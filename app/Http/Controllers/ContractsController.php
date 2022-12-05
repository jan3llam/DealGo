<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractPayment;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Validator;

class ContractsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:66', ['only' => ['list', 'list_api']]);
    }

    public function list($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Contracts')]
        ];

        return view('content.contracts-list', ['breadcrumbs' => $breadcrumbs]);
    }

    public function list_api(Request $request)
    {
        $data = [];
        $search_clm = ['tenant.user.contact_name', 'owner.user.contact_name'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $params = $request->all();
        $query = Contract::withCount('shipments')->with([
            'tenant' => function ($q) {
                $q->withTrashed()->with('user', function ($qu) {
                    $qu->withTrashed();
                });
            },
            'owner' => function ($q) {
                $q->withTrashed()->with('user', function ($qu) {
                    $qu->withTrashed();
                });
            }, 'payments' => function ($q) {
                $q->orderBy('id', 'desc');
            }
        ]);
        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_trashed = isset($params['trashed']) ? $params['trashed'] : 0;
        $per_page = isset($params['length']) ? $params['length'] : 10;

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

        if ($sort_field) {
            $order_field = $sort_field;
            $order_sort = $params['direction'];
        }

        if ($filter_trashed) {
            $query->onlyTrashed();
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip($page)
            ->take($per_page)->orderBy($order_field, $order_sort)->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $total;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function payments(Request $request)
    {
        $id = $request->object_id;

        $contract = Contract::find($id);

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

            $item->value = $payment['value'];
            $item->paid = $payment['paid'];
            $item->description = $payment['description'];
            $item->submit_date = Carbon::parse($payment['date'])->toDateTimeString();
            $item->date = Carbon::parse($payment['next'])->toDateTimeString();

            $contract->payments()->save($item);
        }
        Helper::sendNotification('paymentChanged', [], $contract->owner->user->id, ['id' => $contract->id, 'action' => 'payment']);
        Helper::sendNotification('paymentChanged', [], $contract->tenant->user->id, ['id' => $contract->id, 'action' => 'payment']);

        return response()->success();
    }


}
