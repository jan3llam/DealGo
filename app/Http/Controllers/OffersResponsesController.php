<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\gType;
use App\Models\Offer;
use App\Models\OfferResponse;
use App\Models\OfferResponsePayment;
use App\Models\Port;
use App\Models\Shipment;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class OffersResponsesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:62', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:61', ['only' => ['add']]);
        $this->middleware('permission:63', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')]
        ];

        $offer = Offer::withTrashed()->where('id', $id)->first();

        if ($offer) {
            array_push($breadcrumbs, ['name' => $offer->name . ' (#' . $offer->id . ')']);
        }
        array_push($breadcrumbs, ['name' => __('locale.OffersResponses')]);

        $offers = Offer::all();
        $tenants = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->get();

        $vessels = Vessel::all();
        $ports = Port::all();
        $types = gType::all();

        return view('content.offers-responses-list', [
            'breadcrumbs' => $breadcrumbs,
            'offers' => $offers,
            'offer' => $offer,
            'tenants' => $tenants,
            'ports' => $ports,
            'types' => $types,
            'vessels' => $vessels,
        ]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['tenant.user.contact_name', 'tenant.user.phone', 'tenant.user.email'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $offer_id = $request->input('offer_id', null);
        $params = $request->all();
        $query = OfferResponse::with([
            'payments' => function ($query) {
                $query->sum('value');
            },
            'tenant' => function ($q) {
                $q->withTrashed()->with('user');
            },
            'offer.port_from' => function ($q) {
                $q->withTrashed();
            },
            'port_to' => function ($q) {
                $q->withTrashed();
            }, 'routes', 'goods_types.good_type'
        ]);

        if ($offer_id) {
            $query->where('offer_id', $offer_id);
        }

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_status = isset($params['status']) ? $params['status'] : 1;
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

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip($page)
            ->take($per_page)->orderBy($order_field, $order_sort)->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $total;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }


    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'contract' => 'required|string',
            'routes' => 'required_if:contract,2,3,4',
            'goods' => 'required_if:contract,1,3',
            'description' => 'required|string',
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'port_to' => 'required',
            'tenant' => 'required|numeric',
            'offer' => 'required|numeric',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new OfferResponse;

        $item->name = $params['name'];
        $item->offer_id = $params['offer'];
        $item->tenant_id = $params['tenant'];
        $item->port_to = $params['port_to'];
        $item->date_from = Carbon::parse($params['date_from'])->toDateString();
        $item->date_to = Carbon::parse($params['date_to'])->toDateString();
        $item->description = $params['description'];
        $item->contract = $params['contract'];

        $files = $request->file('files', []);
        $filesArr = [];
        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $file, $fileName);
                $filesArr[] = $fileName;
            }
        }

        $item->files = json_encode($filesArr);

        $item->save();

        if ($request->contract == 1 || $request->contract == 3) {

            $goods = $request->input('goods', []);
            foreach ($goods as $index => $good) {
                if (!Route::find($good['gtype'])) {
                    return response()->error('objectNotFound');
                }
                $item->goods_types()->attach($good['gtype'], ['weight' => $good['weight']]);
            }
        }
        if ($request->contract != 1) {

            $routes = $request->input('routes', []);
            foreach ($routes as $index => $route) {
                if (!Route::find($route)) {
                    return response()->error('objectNotFound');
                }
                $item->routes()->attach($route, ['order' => $index]);
            }
        }

        $payments = $request->input('payments', []);
        $paymentsArr = [];
        foreach ($payments as $payment) {
            $fileName = null;

            $paymentItem = new OfferResponsePayment;
            $paymentItem->value = $payment['value'];
            $paymentItem->date = $payment['date'];
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

        $item = OfferResponse::withTrashed()->where('id', $id)->first();

        if ($item) {

            $item->delete();
        }

        return response()->success();
    }

    public function approve($id)
    {

        $item = OfferResponse::withTrashed()->where('id', $id)->first();

        $offer = $item->offer;
        if ($item && $offer->approved == 0) {
            $offer->approved = 1;
            $offer->save();

            $contract = new Contract;
            $contract->owner_id = $item->offer->vessel->owner->id;
            $contract->tenant_id = $item->tenant->id;
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
        } else {
            return response()->error('operationNotPermitted');
        }

        return response()->success();
    }
}
