<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Owner;
use App\Models\Vessel;
use App\Models\vType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class VesselsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Vessels"]
        ];

        $countries = Country::all();
        $owners = Owner::all();
        $types = vType::all();
        return view('content.vessels-list', [
            'breadcrumbs' => $breadcrumbs,
            'countries' => $countries,
            'types' => $types,
            'owners' => $owners,
        ]);
    }

    public function list_api()
    {
        return response()->success(Vessel::withTrashed()->with(['country', 'owner'])->get());
    }

    public function add(Request $request)
    {
        $fileName = null;
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'type' => 'required|numeric',
            'owner' => 'required|numeric',
            'country' => 'required|numeric',
            'imo' => 'required|string',
            'mmsi' => 'required|string',
            'capacity' => 'required',
            'build' => 'required',
            'image' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('image'), $fileName);
        }

        $item = new Vessel;

        $item->image = $fileName;

        $item->name = $params['name'];
        $item->type_id = $params['type'];
        $item->owner_id = $params['owner'];
        $item->country_id = $params['owner'];
        $item->imo = $params['imo'];
        $item->mmsi = $params['mmsi'];
        $item->capacity = $params['capacity'];
        $item->build_year = $params['build'];
        $item->status = 1;

        $item->save();

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

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('image'), $fileName);
        }
        $item = Vessel::find($id);

        $item->image = $fileName;

        $item->name = $params['name'];
        $item->type_id = $params['type'];
        $item->owner_id = $params['owner'];
        $item->country_id = $params['owner'];
        $item->imo = $params['imo'];
        $item->mmsi = $params['mmsi'];
        $item->capacity = $params['capacity'];
        $item->build_year = $params['build'];

        $item->status = 1;

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {

        $item = Vessel::find($id);

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }


    public function status($id)
    {
        $item = Vessel::withTrashed()->where('id', $id)->first();
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
