<?php

namespace App\Http\Controllers;

use App\Models\gType;
use App\Models\Owner;
use App\Models\Port;
use App\Models\Request as ShippingRequest;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class RequestsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Shipping requests"]
        ];

        $owners = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->get();
        $tenants = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->get();
        $ports = Port::all();
        $types = gType::all();

        return view('content.requests-list', [
            'breadcrumbs' => $breadcrumbs,
            'types' => $types,
            'owners' => $owners,
            'tenants' => $tenants,
            'ports' => $ports
        ]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['port_from.name', 'port_to.name', 'port.city.name', 'port.city.country.name', 'tenant.name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = ShippingRequest::query();

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
            ->take($per_page)->orderBy($order_field, $order_sort)
            ->with([
                'tenant' => function ($q) {
                    $q->withTrashed()->with('user');
                },
                'port_from' => function ($q) {
                    $q->withTrashed();
                },
                'port_to' => function ($q) {
                    $q->withTrashed();
                },
                'owner' => function ($q) {
                    $q->withTrashed()->with('user');
                }])->withCount('responses')->get();


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
            'owner' => 'nullable',
            'tenant' => 'required|numeric',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new ShippingRequest;

        $item->name = $params['name'];
        $item->tenant_id = $params['tenant'];
        $item->owner_id = $params['owner'] === 'null' ? null : $params['owner'];
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

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

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
            'owner' => 'nullable',
            'tenant' => 'required|numeric',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = ShippingRequest::withTrashed()->where('id', $id)->first();

        $item->name = $params['name'];
        $item->tenant_id = $params['tenant'];
        $item->owner_id = $params['owner'] === 'null' ? null : $params['owner'];
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
            $item->goods_types()->detach();
            $goods = $request->input('goods', []);
            foreach ($goods as $index => $good) {
                $item->goods_types()->attach($good['gtype'], ['weight' => $good['weight']]);
            }
        }
        if ($request->contract != 1) {
            $item->routes()->detach();
            $routes = $request->input('routes', []);
            foreach ($routes as $index => $route) {
                $item->routes()->attach($route, ['ord   er' => $index]);
            }
        }

        return response()->success();
    }

    public function delete($id)
    {

        $item = ShippingRequest::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }


    public function status($id)
    {
        $item = ShippingRequest::withTrashed()->where('id', $id)->first();
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
