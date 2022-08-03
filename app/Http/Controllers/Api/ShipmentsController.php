<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use App\Models\Shipment;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Validator;

class ShipmentsController extends Controller
{
    public function list($id, Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user_id = auth('api')->user()->id;
        }

        $query = Shipment::with([
            'vessel' => function ($q) {
                $q->with('owner', function ($qu) {
                    $qu->with('user');
                });
            },
            'contract.tenant' => function ($q) {
                $q->with('user');
            },
            'port_from', 'port_to',
        ])->whereHas('vessel', function ($q) use ($user_id) {
            $q->whereHas('owner', function ($qu) use ($user_id) {
                $qu->whereHas('user', function ($que) use ($user_id) {
                    $que->where('id', $user_id);
                });
            });
        })->orWhereHas('contract', function ($q) use ($user_id) {
            $q->whereHas('tenant', function ($qu) use ($user_id) {
                $qu->whereHas('user', function ($que) use ($user_id) {
                    $que->where('id', $user_id);
                });
            });
        })->whereHas('port_from')->whereHas('port_to');
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $vessel_id = $request->input('vessel', null);
        $search_val = $request->input('keyword', '');
        $search_clm = ['port_from.name', 'port_to.name', 'vessel.name', 'contract_id'];
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

        if ($vessel_id) {
            $vessel = Vessel::find($vessel_id);
            if ($vessel) {
                $query->where('vessel_id', $vessel->id);
            }
        }
        if ($id) {
            $contract = Contract::find($id);
            if ($contract) {
                $query->where('contract_id', $contract->id);
            }
        }

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get()->each(function ($items) {
                $items->append(['goods_types']);
            });;

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
            $user_id = auth('api')->user()->id;
        }

        $query = Shipment::with([
            'vessel' => function ($q) {
                $q->with('owner', function ($qu) {
                    $qu->with('user');
                });
            },
            'contract.tenant' => function ($q) {
                $q->with('user');
            },
            'port_from', 'port_to',
        ])->whereHas('vessel', function ($q) use ($user_id) {
            $q->whereHas('owner', function ($qu) use ($user_id) {
                $qu->whereHas('user', function ($que) use ($user_id) {
                    $que->where('id', $user_id);
                });
            });
        })->orWhereHas('contract', function ($q) use ($user_id) {
            $q->whereHas('tenant', function ($qu) use ($user_id) {
                $qu->whereHas('user', function ($que) use ($user_id) {
                    $que->where('id', $user_id);
                });
            });
        })->whereHas('port_from')->whereHas('port_to');


        $query->where('id', $id);

        $data = $query->first()->append(['goods_types']);

        return response()->success($data);
    }
}
