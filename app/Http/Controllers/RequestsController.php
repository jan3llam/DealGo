<?php

namespace App\Http\Controllers;

use App\Models\gType;
use App\Models\Owner;
use App\Models\Port;
use App\Models\Request as ShippingRequest;
use App\Models\Tenant;
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

        $owners = Owner::all();
        $tenants = Tenant::all();
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

    public function list_api()
    {
        return response()->success(ShippingRequest::withTrashed()->with(['tenant', 'port_from', 'port_to', 'owner'])->get());
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
                $item->goods_types()->attach($good->gtype, ['weight' => $good->gtype]);
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

        $fileName = null;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required_if:type,1',
            'commercial' => 'required_if:type,1',
            'company' => 'required_if:type,1|string',
            'license' => 'required_if:type,1|file',
            'type' => 'required|numeric',
            'contact' => 'required|string',
            'zip' => 'required|string',
            'address_1' => 'required|string',
            'address_2' => 'nullable|string',
            'city' => 'required|numeric',
            'password' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'legal' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        if ($request->hasFile('legal')) {
            $extension = $request->file('legal')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('legal'), $fileName);
        }

        $item = ShippingRequest::withTrashed()->where('id', $id)->first();

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

        $item = ShippingRequest::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->status = 0;
            $item->save();
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
