<?php

namespace App\Http\Controllers\Api;

use App\Models\Owner;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;

class VesselsController extends Controller
{

    public function list(Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();
            $user_id = isset($user->userable) ? $user->userable->id : null;
        }

        $data = [];
        $search_clm = ['build_year', 'imo', 'mmsi', 'country.name', 'owner.user.name', 'type.name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $query = Vessel::query();

        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $filter_status = $request->input('status', null);
        $owner = $request->input('owner', null);
        $country = $request->input('country', null);
        $vType = $request->input('type', null);
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

        if ($country) {
            $query->whereHas('country', function ($q) use ($country) {
                $q->where('id', $country);
            });
        }

        if ($owner) {
            $query->whereHas('owner', function ($q) use ($owner) {
                $q->where('id', $owner);
            });
        }

        if ($vType) {
            $query->whereHas('type', function ($q) use ($vType) {
                $q->where('id', $vType);
            });
        }

        if ($is_mine && auth('api')->check()) {
            $query->where('owner_id', $user_id);
            if ($filter_status !== null) {
                switch ($filter_status) {
                    case 1:
                    {
                        $query->where('status', 1)->withoutTrashed();
                        break;
                    }
                    case 2:
                    {
                        $query->onlyTrashed();
                        break;
                    }
                    case 0:
                    {
                        $query->where('status', 0)->withoutTrashed();
                        break;
                    }
                }
            }
        } else {
            $query->where('status', 1);
        }

        $total = $query->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->whereHas('country')->whereHas('type')
            ->whereHas('owner', function ($q) {
                $q->whereHas('user');
            })->with(['country', 'type.goods_types', 'owner.user', 'shipments.contract', 'shipments.port_from', 'shipments.port_to'])
            ->withCount(['crew', 'maintenance'])
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);

    }

    public function check_ps07($id, Request $request)
    {
        $vessel = Vessel::withTrashed()->where('id', $id)->first();
        $mmsi = $vessel->mmsi;

        $response = Http::get('https://services.marinetraffic.com/api/exportvessel/' . env('MARINETRAFFIC_API_KEY_PS07'), [
            'v' => 5,
            'mmsi' => $mmsi,
            'protocol' => 'json',
            'timespan' => '1200'
        ]);
        if ($response->successful()) {
            if ($data = json_decode($response->getBody()->getContents())) {
                return response()->success([
                    'id' => $vessel->id,
                    'name' => $vessel->name,
                    'rotation' => $data[0][4],
                    'latitude' => $data[0][1],
                    'longitude' => $data[0][2]
                ]);
            } else {
                return response()->error('objectNotFound');
            }
        }
        $data = json_decode($response->getBody()->getContents())->errors[0];
        return response()->customError($data->code, $data->detail);

    }


    public function add(Request $request)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'type' => 'required|numeric',
            'country' => 'required|numeric',
            'imo' => 'required|string|unique:vessels,imo',
            'mmsi' => 'required|string|unique:vessels,imo',
            'capacity' => 'required',
            'build' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            if (isset($validator->failed()['imo']['Unique']) || isset($validator->failed()['mmsi']['Unique'])) {
                return response()->error('alreadyExist');
            }
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new Vessel;


        $item->name = $params['name'];
        $item->type_id = $params['type'];
        $item->owner_id = $user->userable->id;
        $item->image = $params['image'];
        $item->country_id = $params['country'];
        $item->imo = $params['imo'];
        $item->mmsi = $params['mmsi'];
        $item->capacity = $params['capacity'];
        $item->build_year = $params['build'];
        $item->status = $request->input('status', 1);

        $item->save();

        return response()->success();
    }

    public function update($id, Request $request)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'type' => 'required|numeric',
            'country' => 'required|numeric',
            'imo' => 'required|string',
            'mmsi' => 'required|string',
            'capacity' => 'required',
            'build' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Vessel::withTrashed()->where('id', $id)->where('owner_id', $user->userable->id)->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }

        $item->name = $params['name'];
        $item->image = $params['image'];
        $item->type_id = $params['type'];
        $item->country_id = $params['country'];
        $item->imo = $params['imo'];
        $item->mmsi = $params['mmsi'];
        $item->capacity = $params['capacity'];
        $item->build_year = $params['build'];

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        $item = Vessel::withTrashed()->where('id', $id)->where('owner_id', $user->userable->id)->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }

    public function status($id)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        $item = Vessel::withTrashed()->where('id', $id)->where('owner_id', $user->userable->id)->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }

        if ($item) {

            if ($item->status === 0 && $item->deleted_at !== null) {
                $item->restore();
            }
            $item->status = $item->status == 1 ? 0 : 1;
            $item->save();
        }

        return response()->success();
    }
}
