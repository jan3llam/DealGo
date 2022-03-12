<?php

namespace App\Http\Controllers;

use App\Models\gType;
use App\Models\Offer;
use App\Models\OfferResponse;
use App\Models\OfferResponsePayment;
use App\Models\Port;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class OffersResponsesController extends Controller
{
    public function list($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"]
        ];

        $offer = Offer::withTrashed()->where('id', $id)->first();

        if ($offer) {
            array_push($breadcrumbs, ['name' => $offer->name . ' (#' . $offer->id . ')']);
        }
        array_push($breadcrumbs, ['name' => 'Responses']);

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
        $search_clm = ['user.name', 'user.username', 'user.gsm', 'user.email'];
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
            'port_from' => function ($q) {
                $q->withTrashed();
            },
            'port_to' => function ($q) {
                $q->withTrashed();
            },
        ]);

        if ($offer_id) {
            $query->where('offer_id', $offer_id);
        }

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_trashed = isset($params['trashed']) ? $params['trashed'] : 0;
        $per_page = isset($params['length']) ? $params['length'] : 10;

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
//                    $item = explode('.', $item);
//                    $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
//                        $qu->where($item[1], 'like', '%' . $search_val . '%');
//                    })->get();
                    $q->orWhere($item[1], 'like', '%' . $search_val . '%');
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

        $data['data'] = $query->skip(($page) * $per_page)
            ->take($per_page)->orderBy($order_field, $order_sort)->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
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
            'port_from' => 'required',
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
        $item->port_from = $params['port_from'];
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
                $item->goods_types()->attach($good['gtype'], ['weight' => $good['weight']]);
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

    public function update(Request $request)
    {
        $id = $request->object_id;

        $fileName = null;

        $params = $request->all();
        $validator = Validator::make($params, [
            'offer' => 'required|numeric',
            'date' => 'required',
            'commercial' => 'required_if:type,1',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        if ($request->hasFile('legal')) {
            $extension = $request->file('legal')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('legal'), $fileName);
        }

        $item = OfferResponse::withTrashed()->where('id', $id)->first();

        $item->legal_file = $fileName;

        if ($request->type == 1) {

            if ($request->hasFile('company')) {
                $fileName = null;
                $extension = $request->file('company')->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $request->file('company'), $fileName);
                $item->company_file = $fileName;
            }

            if ($request->hasFile('license')) {
                $fileName = null;
                $extension = $request->file('license')->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $request->file('license'), $fileName);
                $item->license_file = $fileName;
            }


            $item->full_name = $params['name'];
            $item->commercial_number = $params['commercial'];

        }
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->contact_name = $params['contact'];
        $item->password = bcrypt($params['password']);
        $item->city_id = $params['city'];
        $item->type = $params['type'];
        $item->zip_code = $params['zip'];
        $item->address_1 = $params['address_1'];
        $item->address_2 = $params['address_2'];

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
        $item->status = 1;

        $item->save();

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


    public function status($id)
    {
        $item = OfferResponse::withTrashed()->where('id', $id)->first();
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
