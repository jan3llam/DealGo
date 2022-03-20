<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Port;
use App\Models\Request as ShipmentRequest;
use App\Models\RequestResponse;
use App\Models\RequestResponsePayment;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class RequestsResponsesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:59', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:58', ['only' => ['add']]);
        $this->middleware('permission:60', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')]
        ];

        $request = ShipmentRequest::withTrashed()->where('id', $id)->first();

        if ($request) {
            array_push($breadcrumbs, ['name' => $request->name . ' (#' . $request->id . ')']);
        }
        array_push($breadcrumbs, ['name' => __('locale.Requests Responses')]);

        $requests = ShipmentRequest::all();
        $owners = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->get();

        $ports = Port::all();
        $vessels = Vessel::all();

        return view('content.requests-responses-list', [
            'breadcrumbs' => $breadcrumbs,
            'requests' => $requests,
            'request' => $request,
            'owners' => $owners,
            'ports' => $ports,
            'vessels' => $vessels,
        ]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['owner.user.contact_name'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $request_id = $request->input('request_id', null);
        $params = $request->all();
        $query = RequestResponse::with([
            'payments' => function ($query) {
                $query->sum('value');
            },
            'owner' => function ($q) {
                $q->withTrashed()->with('user', function ($qu) {
                    $qu->withTrashed();
                });
            },
        ]);

        if ($request_id) {
            $query->where('request_id', $request_id);
        }

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
            'owner' => 'required|numeric',
            'request' => 'required|numeric',
            'date' => 'required',
            'commercial' => 'required_if:type,1',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new RequestResponse;

        $item->request_id = $params['request'];
        $item->owner_id = $params['owner'];
        $item->date = $params['date'];
        $item->description = $params['description'];

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


        if ($item->request->contract == 1) {

            $routes = $request->input('routes', []);
            foreach ($routes as $index => $route) {
                $item->routes()->attach($route, ['order' => $index]);
            }
        }

        $vessels = $request->input('vessels', []);
        foreach ($vessels as $vessel) {
            $item->vessels()->attach($vessel->id, ['request_good_id' => $vessel->request_good_id]);
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

        return response()->success();
    }

    public function delete($id)
    {

        $item = RequestResponse::withTrashed()->where('id', $id)->first();

        if ($item) {

            $item->delete();
        }

        return response()->success();
    }
}
