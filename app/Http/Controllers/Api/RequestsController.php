<?php

namespace App\Http\Controllers\Api;

use App\Models\Offer;
use App\Models\Request as ShippingRequest;
use App\Models\RequestResponse;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Validator;

class RequestsController extends Controller
{
    public function list(Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user_id = auth('api')->user()->id;
        }

        $data = [];
        $search_clm = ['port_from.name', 'port_to.name', 'port.city.name', 'port.city.country.name', 'tenant.user.contact_name'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $now = Carbon::parse(date('Y-m-d', strtotime(now())))->toDateString();

        $query = ShippingRequest::whereHas('port_to')->whereHas('port_from')
            ->whereHas('tenant', function ($q) {
                $q->whereHas('user');
            })
            ->with(['port_to', 'port_from', 'tenant.user', 'routes', 'goods_types'])
            ->withCount(['responses' => function (Builder $q) {
                $q->whereHas('vessels')->whereHas('request_goods_types');
            }]);

        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $tenant = $request->input('tenant', null);
        $port_to = $request->input('port_to', null);
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


        if ($port_to) {
            $query->where('port_to', $port_to);
        }

        if ($date_from) {
            $from = Carbon::parse(date('Y-m-d', strtotime($date_from)))->toDateString();
            $query->where('date_from', '>=', $from);
        }

        if ($date_to) {
            $to = Carbon::parse(date('Y-m-d', strtotime($date_to)))->toDateString();
            $query->where('date_to', '<=', $to);
        } else {
            $query->where('date_to', '>=', $now);
        }

        if ($tenant) {
            $query->whereHas('tenant', function ($q) use ($tenant) {
                $q->where('id', $tenant);
            });
        }


        if ($is_mine && auth('api')->check()) {
            $query->whereHas('tenant', function ($q) use ($user_id) {
                $q->where('id', auth('api')->user()->userable->id);
            });
        }
//        else {
//        $query->where('date_to', '>=', $now);
//                ->where('date_from', '<=', $now);
//        }

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
            $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

            $user_id = isset($user->tenant) ? $user->tenant->id : null;
        }

        $data['request'] = ShippingRequest::where('id', $id)
            ->whereHas('port_to')->whereHas('port_from')
            ->whereHas('tenant', function ($q) {
                $q->whereHas('user');
            })->with(['tenant.user', 'port_from', 'port_to', 'request_goods_types.good_type', 'routes'])
            ->withCount(['responses' => function (Builder $q) {
                $q->whereHas('vessels')->whereHas('request_goods_types')->where('status', 0);
            }])
            ->first();

        if ($data['request']->tenant->id === $user_id) {
            $data['responses'] = RequestResponse::where('request_id', $data['request']->id)
                ->whereHas('owner')
                ->with(['payments', 'routes', 'goods_types'])->get();
        }

        return response()->success($data);
    }

    public function add(Request $request)
    {
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $params = $request->all();
        $validator = Validator::make($params, [
            'contract' => 'required|string',
            'routes' => 'required_if:contract,2,3,4',
            'goods' => 'required_if:contract,1,3',
            'description' => 'required|string',
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'port_from' => 'required',
            'port_to' => 'required',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new ShippingRequest;

        $item->name = $params['name'];
        $item->tenant_id = $user->userable->id;
        $item->owner_id = null;
        $item->port_from = $params['port_from'];
        $item->port_to = $params['port_to'];
        $item->date_from = Carbon::parse($params['date_from'])->toDateString();
        $item->date_to = Carbon::parse($params['date_to'])->toDateString();
        $item->description = $params['description'];
        $item->contract = $params['contract'];
        $item->files = json_encode($request->input('files', []));
        $item->matrix = json_encode($request->input('matrix', []));

        $item->save();

        $goods = $request->input('goods', []);
        foreach ($goods as $index => $good) {
            $item->goods_types()->attach($good['gtype'], ['weight' => $good['weight']]);
        }

        if ($request->contract != 1) {

            $routes = $request->input('routes', []);
            foreach ($routes as $index => $route) {
                $item->routes()->attach($route, ['order' => $index]);
            }
        }

        return response()->success();
    }

    public function suggest(Request $request)
    {
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $params = $request->all();
        $validator = Validator::make($params, [
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'port_from' => 'required',
            'port_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $items = Offer::where('port_from', $request->port_from)
            ->where('date_from', Carbon::parse($request->date_from)->toDateTimeString())
            ->where('date_to', Carbon::parse($request->date_to)->toDateTimeString())->count();

        return response()->success($items);
    }

    public function delete($id)
    {
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $item = ShippingRequest::where('id', $id)->whereHas('vessel.owner', function ($q) use ($user) {
            $q->where('id', $user->userable->id);
        })->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }
        $item->delete();

        return response()->success();
    }
}
