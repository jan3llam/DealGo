<?php

namespace App\Http\Controllers\Api;

use App\Models\Owner;
use App\Models\Port;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
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
        $search_clm = ['city.name_ar', 'city.name_en', 'city.name_fr', 'city.country.name_ar', 'city.country.name_en', 'city.country.name_fr', 'name'];
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
        $port = $request->input('port');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $goods_types = $request->input('goods_types', []);
//        return response()->success($goods_types);
        $search_clm = ['city.name', 'city.country.name', 'name', 'offers.vessel.name'];

        $user = null;

        if (auth('api')->check()) {
            $user = User::find(auth('api')->user()->id)->userable;
        }

        $query = Port::where('status', 1);

        if ($user instanceof Tenant) {
            $query->whereHas('offers', function ($qu) {
                $qu->where('approved', 0);
            })->with(['offers' => function ($q) {
                $q->where('approved', 0)->with(['vessel.type.goods_types', 'offers.vessel.owner.user']);
            }]);
            if ($date_from) {
                $query->whereHas('offers', function ($q) use ($date_from) {
                    $q->where('date_from', '<=', $date_from)->where('date_to', '>=', $date_from);
                });
            }
            if (!empty($goods_types)) {
                $query->whereHas('offers.vessel.type.goods_types', function ($q) use ($goods_types) {
                    $q->whereIn('goods_types.id', $goods_types);
                });
            }
        } elseif ($user instanceof Owner) {
            $query->whereHas('requests', function ($qu) {
                $qu->where('approved', 0);
            })->with(['requests' => function ($q) {
                $q->where('approved', 0)->with(['port_to', 'tenant.user', 'routes', 'goods_types']);
            }]);
            if ($date_from) {
                $query->whereHas('requests', function ($q) use ($date_from) {
                    $q->where('date_from', '<=', $date_from)->where('date_to', '>=', $date_from);
                });
            }
            if (!empty($goods_types)) {
                $query->whereHas('requests.goods_types', function ($q) use ($goods_types) {
                    $q->whereIn('goods_types.id', $goods_types);
                });
            }
        } else {
            $query->where(function ($q) {
                $q->orWhereHas('requests', function ($qu) {
                    $qu->where('approved', 0);
                })->orWhereHas('offers', function ($qu) {
                    $qu->where('approved', 0);
                });
            })->with(['requests' => function ($q) {
                $q->where('approved', 0)->with(['port_to', 'tenant.user', 'routes', 'goods_types']);
            }, 'offers' => function ($q) {
                $q->where('approved', 0)->with(['vessel.type.goods_types', 'offers.vessel.owner.user']);
            }]);

            if ($date_from) {
                $query->whereHas('requests', function ($q) use ($date_from) {
                    $q->where('date_from', '<=', Carbon::parse($date_from)->toDateString())->where('date_to', '>=', Carbon::parse($date_from)->toDateString());
                });
                $query->whereHas('offers', function ($q) use ($date_from) {
                    $q->where('date_from', '<=', Carbon::parse($date_from)->toDateString())->where('date_to', '>=', Carbon::parse($date_from)->toDateString());
                });
            }
            if (!empty($goods_types)) {
                $query->whereHas('offers.vessel.type.goods_types', function ($q) use ($goods_types) {
                    $q->whereIn('goods_types.id', $goods_types);
                });
                $query->whereHas('requests.goods_types', function ($q) use ($goods_types) {
                    $q->whereIn('goods_types.id', $goods_types);
                });
            }

        }


        if ($port) {
            $query->where('ports.id', $port);
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
