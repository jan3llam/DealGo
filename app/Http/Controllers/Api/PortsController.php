<?php

namespace App\Http\Controllers\Api;

use App\Models\Owner;
use App\Models\Port;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class PortsController extends Controller
{
    public function list(Request $request)
    {
        $query = Port::withoutTrashed()->where('status', 1);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $search_val = $request->input('keyword', '');
        $search_clm = ['city.name', 'city.country.name', 'name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

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

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function list_map(Request $request)
    {

        $search_val = $request->input('keyword', '');
        $search_clm = ['city.name', 'city.country.name', 'name'];

        $user = null;

        if (auth('api')->check()) {
            $user = User::find(auth('api')->user()->id)->userable;
        }

        $query = Port::where('status', 1);

        if ($user instanceof Tenant) {
            $query->whereHas('offers')->with(['offers.vessel.type.goods_types', 'offers.vessel.owner.user']);
        } elseif ($user instanceof Owner) {
            $query->whereHas('requests')->with(['requests.port_to', 'requests.tenant.user', 'requests.routes', 'requests.goods_types']);
        } else {
            $query->whereHas('requests')->whereHas('offers')->with(['requests.port_to', 'requests.tenant.user', 'requests.routes', 'requests.goods_types', 'offers.vessel.type.goods_types', 'offers.vessel.owner.user']);
        }

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

        $data['data'] = $query->get()->toArray();

        return response()->success($data);
    }
}
