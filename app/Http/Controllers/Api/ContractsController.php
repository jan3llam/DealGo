<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use App\Models\ContractPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class ContractsController extends Controller
{
    public function list(Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user_id = auth('api')->user()->id;
        }

        $query = Contract::withCount('shipments')->with([
            'tenant' => function ($q) {
                $q->with('user');
            },
            'owner' => function ($q) {
                $q->with('user');
            }, 'shipments.vessel', 'shipments.port_from', 'shipments.port_to', 'origin.parent'
        ])->whereHas('owner', function ($q) use ($user_id) {
            $q->whereHas('user', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });
        })->orWhereHas('tenant', function ($q) use ($user_id) {
            $q->whereHas('user', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });
        });

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $search_val = $request->input('keyword', '');
        $search_clm = ['tenant.user.contact_name', 'owner.user.contact_name'];
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
            ->take($page_size)->orderBy($order_field, $order_sort)->get()->each(function ($items) {
                $items->append(['full_value', 'remaining_value', 'goods_types']);
            });

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

        $query = Contract::withCount('shipments')->with([
            'tenant' => function ($q) {
                $q->with('user');
            },
            'owner' => function ($q) {
                $q->with('user');
            }, 'shipments.vessel', 'shipments.port_from', 'shipments.port_to', 'origin.parent'
        ])->whereHas('owner', function ($q) use ($user_id) {
            $q->whereHas('user', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });
        })->orWhereHas('tenant', function ($q) use ($user_id) {
            $q->whereHas('user', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });
        });

        $query->where('id', $id);

        $data = $query->first()->each(function ($items) {
            $items->append(['full_value', 'remaining_value', 'goods_types']);
        });

        return response()->success($data);
    }

    public function payments($id, Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user_id = auth('api')->user()->id;
        }

        $contract = Contract::find($id)->whereHas('owner', function ($q) use ($user_id) {
            $q->whereHas('user', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });
        })->orWhereHas('tenant', function ($q) use ($user_id) {
            $q->whereHas('user', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });
        })->first();

        if (!$contract) {
            return response()->error('objectNotFound');
        }

        $payments = $request->input('payment', []);
        $new_payments = $request->input('payments', []);
        $original_payments = $contract->payments;

        foreach ($payments as $index => $payment) {
            $item = $original_payments->where('id', $index)->first();
            if ($item->is_down) {
                $item->paid = $item->value;
            } else {
                $item->paid = $payment['value'];
            }
            if (isset($payment['date'])) {
                $item->submit_date = Carbon::parse($payment['date'])->toDateTimeString();
            }
            if (isset($payment['next'])) {
                $item->date = Carbon::parse($payment['next'])->toDateTimeString();
            }
            $item->save();
        }

        foreach ($new_payments as $payment) {

            $item = new ContractPayment;

            $item->value = $payment->value;
            $item->paid = $payment->paid;
            $item->description = $payment->description;
            $item->submit_date = Carbon::parse($payment['date'])->toDateTimeString();
            $item->date = Carbon::parse($payment['next'])->toDateTimeString();

            $contract->payments()->save($item);
        }

        return response()->success();
    }

}
