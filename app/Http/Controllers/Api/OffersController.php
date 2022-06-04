<?php

namespace App\Http\Controllers\Api;

use App\Models\Offer;
use App\Models\OfferResponse;
use App\Models\Owner;
use App\Models\Port;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Validator;

class OffersController extends Controller
{
    public function list(Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user_id = auth('api')->user()->id;
        }

        $data = [];
        $search_clm = ['vessel.build_year', 'vessel.imo', 'vessel.mmsi', 'vessel.country.name', 'vessel.type.name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $query = Offer::query();
        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $owner = $request->input('owner', null);
        $port_from = $request->input('port_from', null);
        $date_from = $request->input('date_from', null);
        $date_to = $request->input('date_to', null);
        $is_mine = $request->input('is_mine', 0);

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
                    $item = explode('.', $item);
                    if (sizeof($item) == 3) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->whereHas($item[1], function ($que) use ($item, $search_val) {
                                $que->where($item[2], 'like', '%' . $search_val . '%');
                            });
                        })->get();
                    } elseif (sizeof($item) == 2) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->where($item[1], 'like', '%' . $search_val . '%');
                        })->get();
                    } elseif (sizeof($item) == 1) {
                        $q->orWhere($item[0], 'like', '%' . $search_val . '%');
                    }

                }
            });
        }

        if ($port_from) {
            $query->where('port_from', $port_from);
        }

        if ($date_from) {
            $from = Carbon::parse(date('Y-m-d', strtotime($date_from)))->toDateString();
            $query->where('date_from', '<=', $from);
        }

        if ($date_to) {
            $to = Carbon::parse(date('Y-m-d', strtotime($date_to)))->toDateString();
            $query->where('date_to', '>=', $to);
        }

        if ($owner) {
            $query->whereHas('vessel', function ($q) use ($owner) {
                $q->whereHas('owner', function ($qu) use ($owner) {
                    $qu->whereHas('id', $owner);
                });
            });
        }


        if ($is_mine && auth('api')->check()) {
            $query->whereHas('vessel', function ($q) use ($owner) {
                $q->whereHas('owner', function ($qu) use ($owner) {
                    $qu->where('id', auth('api')->user()->userable->id);
                });
            });
        }

        $query->whereHas('vessel', function ($q) {
            $q->whereHas('owner', function ($qu) {
                $qu->whereHas('user');
            });
        })->whereHas('port_from')
            ->with(['vessel.type.goods_types', 'port_from'])
            ->withCount(['responses' => function (Builder $q) {
                $q->whereHas('port_to')->whereHas('goods_types')->where('status', 0);
            }]);

        $total = $query->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);

    }

    public function get($id, Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

            $user_id = isset($user->userable) ? $user->userable->id : null;
        }

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);


        $data['offer'] = Offer::where('id', $id)
            ->whereHas('port_from')
            ->whereHas('vessel', function ($q) {
                $q->whereHas('owner');
            })->with(['vessel.owner.user', 'vessel.type.goods_types', 'port_from'])
            ->withCount(['responses' => function (Builder $q) {
                $q->whereHas('port_to')->whereHas('goods_types')->where('status', 0);
            }])
            ->first();

        if ($data['offer'] && $data['offer']->vessel->owner->id === $user_id) {
            $data['responses'] = OfferResponse::whereHas('offer', function ($q) use ($id) {
                $q->where('id', $id);
            })
                ->whereHas('tenant')
                ->whereHas('port_to')
                ->whereHas('goods_types')
                ->where('status', 0)
                ->skip(($page_number - 1) * $page_size)
                ->take($page_size)
                ->orderBy('created_at')
                ->with(['payments', 'port_to', 'routes', 'goods_types'])->get();
            $data['meta']['total'] = OfferResponse::whereHas('offer', function ($q) use ($id) {
                $q->where('id', $id);
            })->whereHas('goods_types')->whereHas('tenant')->whereHas('port_to')->where('status', 0)->count();
            $data['meta']['count'] = $data['responses']->count();
            $data['meta']['page_number'] = $page_number;
        }

        return response()->success($data);
    }

    public function add(Request $request)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'description' => 'required|string',
            'port_from' => 'required|string',
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'weight' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $vessel = Vessel::withTrashed()->where('id', $params['vessel'])->where('owner_id', $user->userable->id)->first();
        $port = Port::where('id', $params['port_from'])->first();

        if (!$vessel || !$port) {
            return response()->error('objectNotFound');
        }

        $item = new Offer;

        $item->vessel_id = $vessel->id;
        $item->port_from = $port->id;
        $item->date_from = Carbon::parse($params['date_from'])->toDateString();
        $item->date_to = Carbon::parse($params['date_to'])->toDateString();
        $item->description = $params['description'];
        $item->weight = $params['weight'];

        $item->files = json_encode($request->input('files', []));

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $item = Offer::withTrashed()->where('id', $id)->whereHas('vessel.owner', function ($q) use ($user) {
            $q->where('id', $user->owner->id);
        })->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }
        $item->delete();

        return response()->success();
    }
}
