<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Http\Request;
use Validator;

class PortsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Ports"]
        ];

        $countries = Country::all();

        return view('content.ports-list', ['breadcrumbs' => $breadcrumbs, 'countries' => $countries]);
    }

    public function list_api()
    {
        return response()->success(Port::withTrashed()->with('city.country')->get());
    }

    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'city' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new Port;

        $item->name = $params['name'];
        $item->city_id = $params['city'];

        $item->status = 1;

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'city' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Port::withTrashed()->where('id', $id)->first();

        $item->name = $params['name'];
        $item->city_id = $params['city'];

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {

        $item = Port::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }


    public function status($id)
    {
        $item = Port::withTrashed()->where('id', $id)->first();
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
