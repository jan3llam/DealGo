<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\Owner;
use App\Models\Request as ShippingRequest;
use App\Models\RequestResponse;
use App\Models\RequestResponsePayment;
use App\Models\Shipment;
use App\Models\Tenant;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Validator;

class RequestsResponsesController extends Controller
{

    public function list($id, Request $request)
    {
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $shipping = ShippingRequest::find($id);
        if (!$shipping || $shipping->tenant->id !== $user->userable->id) {
            return response()->error('objectNotFound');
        }

        $data = [];
        $search_clm = ['owner.user.contact_name'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $params = $request->all();

        $query = RequestResponse::with([
            'payments' => function ($query) {
                $query->sum('value');
            },
            'owner' => function ($q) {
                $q->withTrashed()->with('user', function ($qu) {
                    $qu->withTrashed();
                });
            }, 'vessels', 'request_goods_types.good_type'
        ])->whereHas('vessels')->whereHas('request_goods_types');

        if ($id) {
            $query->where('request_id', $id);
        }

        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $filter_status = $request->input('status', 0);

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

        if ($filter_status) {
            switch ($filter_status) {
                case 0:
                {
                    $query->where('status', 0);
                    break;
                }
                case 1:
                {
                    $query->where('status', 1);
                    break;
                }
                case 2:
                {
                    $query->where('status', 2);
                    break;
                }
                case 3:
                {
                    $query->onlyTrashed();
                    break;
                }
            }
        }

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function get($id, Request $request)
    {

        if (auth('api')->check()) {
            $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

            $user_id = isset($user->userable) ? $user->userable->id : null;
        }

        $data['data'] = RequestResponse::where('id', $id)->whereHas('request', function ($q) use ($user_id) {
            $q->whereHas('tenant', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });
        })
            ->whereHas('owner')
            ->with(['payments', 'request.port_to', 'request.port_from', 'owner.user', 'routes', 'vessels', 'request_goods_types.good_type'])
            ->first();


        return response()->success($data);
    }

    public function list_mine(Request $request)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $data = [];
        $search_clm = ['owner.user.contact_name'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $params = $request->all();

        $query = RequestResponse::with([
            'payments' => function ($query) {
                $query->sum('value');
            },
            'owner' => function ($q) {
                $q->withTrashed()->with('user', function ($qu) {
                    $qu->withTrashed();
                });
            }, 'vessels', 'request.goods_types'
        ])->whereHas('vessels')->whereHas('request.goods_types');

        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $filter_status = $request->input('status', 0);

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

        if ($filter_status) {
            switch ($filter_status) {
                case 0:
                {
                    $query->where('status', 0);
                    break;
                }
                case 1:
                {
                    $query->where('status', 1);
                    break;
                }
                case 2:
                {
                    $query->where('status', 2);
                    break;
                }
                case 3:
                {
                    $query->onlyTrashed();
                    break;
                }
            }
        }

        $query->whereHas('owner', function ($q) {
            $q->where('id', auth('api')->user()->userable->id);
        });

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['data'] = $data['data']->toArray();

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
            'request' => 'required|numeric',
            'payments' => 'required',
            'date' => 'required',
            'description' => 'required|string',
            'vessels' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new RequestResponse;

        $item->request_id = $params['request'];
        $item->owner_id = $user->userable->id;
        $item->date = $params['date'];
        $item->description = $params['description'];
        $item->files = json_encode($request->input('files', []));

        $item->save();


        if ($item->request->contract == 1) {

            $routes = $request->input('routes', []);
            foreach ($routes as $index => $route) {
                $item->routes()->attach($route, ['order' => $index]);
            }
        }

        $vessels = $request->input('vessels', []);

        foreach ($vessels as $index => $vessel) {
            $item->vessels()->attach($vessel, ['request_good_id' => $index]);
        }

        $payments = $request->input('payments', []);
        $paymentsArr = [];
        foreach ($payments as $payment) {
            $fileName = null;

            $paymentItem = new RequestResponsePayment;
            $paymentItem->value = $payment['value'];
            $paymentItem->date = $payment['date'];
            $paymentItem->description = $payment['description'];

            $paymentItem->file = $fileName;

            array_push($paymentsArr, $paymentItem);
        }
        $paymentItem = new RequestResponsePayment;
        $paymentItem->value = $request->down_value;
        $paymentItem->date = null;
        $paymentItem->is_down = 1;
        $paymentItem->description = $request->down_description;
        array_push($paymentsArr, $paymentItem);

        $item->payments()->saveMany($paymentsArr);

        try {
            Helper::sendNotification('responseRequest', [], $item->request->tenant->user->id, ['id' => $item->id, 'origin_id' => $item->request_id, 'action' => 'response_request']);
        } catch (\Exception $e) {
            return response()->success();
        }
        return response()->success();
    }


    public function approve($id)
    {
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $item = RequestResponse::where('id', $id)->first();

        if ($item->request->tenant->id !== $user->userable->id) {
            return response()->error('objectNotFound');
        }
        $shipping_request = $item->request;

        if ($item && $shipping_request->approved == 0) {
            $shipping_request->approved = 1;
            $shipping_request->save();

            $contract = new Contract;
            $contract->owner_id = $item->owner->id;
            $contract->tenant_id = $item->request->tenant->id;
            $contract->type = $item->request->contract;
            $contract->date_from = $item->request->date_from;
            $contract->date_to = $item->request->date_to;
            $contract->total = $item->total();
            $contract->origin_id = $item->id;
            $contract->origin_type = RequestResponse::class;

            $contract->save();

            $cVessels = [];

            foreach ($item->vessels as $vessel) {
                $shipment = new Shipment;
                $shipment->vessel_id = $vessel->id;
                $shipment->port_from = $item->request->port_from;
                $shipment->port_to = $item->request->port_to;
                $shipment->date = $item->date;
                array_push($cVessels, $shipment);
            }

            $contract->shipments()->saveMany($cVessels);

            $cPayments = [];

            foreach ($item->payments as $originPayment) {

                $payment = new ContractPayment;
                $payment->value = $originPayment->value;
                $payment->date = $originPayment->date;
                $payment->is_down = $originPayment->is_down;
                $payment->description = $originPayment->description;
                $payment->file = $originPayment->file;

                array_push($cPayments, $payment);
            }
            $contract->payments()->saveMany($cPayments);

            $item->status = 1;
            $item->save();

            $item->request->responses->where('id', '!=', $item->id)->each(function ($i) {
                $i->status = 2;
                $i->save();
            });;
        } else {
            return response()->error('operationNotPermitted');
        }

        try {
            Helper::sendNotification('responseApproval', [], $contract->owner->user->id, ['id' => $contract->id, 'action' => 'approve']);
        } catch (\Exception $e) {
            return response()->success();
        }

        return response()->success();
    }

    public function delete($id)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $item = RequestResponse::where('id', $id)->where('owner_id', $user->owner->id)->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }
        $item->delete();

        return response()->success();
    }
}
