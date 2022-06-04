<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\Offer;
use App\Models\OfferResponse;
use App\Models\OfferResponseGoodsType;
use App\Models\OfferResponsePayment;
use App\Models\Owner;
use App\Models\Shipment;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class OffersResponsesController extends Controller
{
    public function list($id, Request $request)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $offer = Offer::find($id);
        if (!$offer || $offer->vessel->owner->id !== $user->owner->id) {
            return response()->error('objectNotFound');
        }

        $data = [];
        $search_clm = [];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $query = OfferResponse::with(['tenant', 'port_to',])
            ->whereHas('port_to')
            ->whereHas('goods_types');

        if ($id) {
            $query->where('offer_id', $id);
        }

        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $filter_status = $request->input('status', 1);
        $port_to = $request->input('port_to', null);

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

        if ($filter_status !== null) {
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

        if ($port_to) {
            $query->where('port_to', $port_to);
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
        $user_id = null;
        if (auth('api')->check()) {
            $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

            $user_id = isset($user->userable) ? $user->userable->id : null;
        }

        $data['data'] = OfferResponse::where('id', $id)->whereHas('offer', function ($q) use ($user_id) {
            $q->whereHas('vessel', function ($qu) use ($user_id) {
                $qu->whereHas('owner', function ($que) use ($user_id) {
                    $que->where('id', $user_id);
                });
            });
        })
            ->whereHas('tenant')
            ->whereHas('port_to')
            ->with(['payments', 'port_to', 'routes', 'goods_types.good_type', 'offer.port_from'])
            ->first();

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
            'payments' => 'required',
            'description' => 'required|string',
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'port_to' => 'required',
            'offer' => 'required|numeric',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new OfferResponse;

        $item->name = $params['name'];
        $item->offer_id = $params['offer'];
        $item->tenant_id = $user->userable->id;
        $item->port_to = $params['port_to'];
        $item->date_from = Carbon::parse($params['date_from'])->toDateString();
        $item->date_to = Carbon::parse($params['date_to'])->toDateString();
        $item->description = $params['description'];
        $item->contract = $params['contract'];
        $item->files = json_encode($request->input('files', []));

        $item->save();

        if ($request->contract == 1 || $request->contract == 3) {

            $goods = $request->input('goods', []);
            foreach ($goods as $index => $good) {
                $good_item = new OfferResponseGoodsType;
                $good_item->good_id = $good['gtype'];
                $good_item->weight = $good['weight'];
                $item->goods_types()->save($good_item);
            }
        }
        if ($request->contract != 1) {

            $routes = $request->input('routes', []);
            foreach ($routes as $index => $route) {
                $item->routes()->attach($route, ['order' => $index]);
            }
        }

        $payments = $request->input('payments', []);
        $paymentsArr = [];
        foreach ($payments as $payment) {
            $fileName = null;

            $paymentItem = new OfferResponsePayment;
            $paymentItem->value = $payment['value'];
            $paymentItem->date = Carbon::parse($payment['date'])->toDateString();
            $paymentItem->description = $payment['description'];
            $paymentItem->file = $fileName;

            array_push($paymentsArr, $paymentItem);
        }
        $paymentItem = new OfferResponsePayment;
        $paymentItem->value = $request->down_value;
        $paymentItem->date = null;
        $paymentItem->is_down = 1;
        $paymentItem->description = $request->down_description;
        array_push($paymentsArr, $paymentItem);

        $item->payments()->saveMany($paymentsArr);

        return response()->success();
    }

    public function delete($id)
    {
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $item = OfferResponse::where('id', $id)->first();

        if ($item) {

            $item->delete();
        }

        return response()->success();
    }

    public function approve($id)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $item = OfferResponse::where('id', $id)->first();

        if ($item->offer->vessel->owner->id !== $user->owner->id) {
            return response()->error('objectNotFound');
        }

        if ($item) {
            $contract = new Contract;
            $contract->owner_id = $item->offer->vessel->owner->id;
            $contract->tenant_id = $item->userable->id;
            $contract->type = $item->contract;
            $contract->date_from = $item->date_from;
            $contract->date_to = $item->date_to;
            $contract->total = $item->total();
            $contract->origin_id = $item->id;
            $contract->origin_type = OfferResponse::class;

            $contract->save();

            $shipment = new Shipment;
            $shipment->vessel_id = $item->offer->vessel->id;
            $shipment->port_from = $item->offer->port_from;
            $shipment->port_to = $item->port_to;
            $shipment->date = $item->date_to;

            $contract->shipments()->saveMany([$shipment]);

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

            $item->offer->responses->where('id', '!=', $item->id)->each(function ($i) {
                $i->status = 2;
                $i->save();
            });;
        }

        return response()->success();
    }
}
