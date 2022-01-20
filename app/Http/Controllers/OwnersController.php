<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class OwnersController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Vessels owners"]
        ];

        $countries = Country::all();
        return view('content.owners-list', ['breadcrumbs' => $breadcrumbs, 'countries' => $countries]);
    }

    public function list_api()
    {
        return response()->success(Owner::withTrashed()->with('city.country')->withCount('vessels')->get());
    }

    public function add(Request $request)
    {
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
            return response()->error('missingParameters', $validator->failed());
        }

        if ($request->hasFile('legal')) {
            $extension = $request->file('legal')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('legal'), $fileName);
        }

        $item = new Owner;

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

        $item = Owner::find($id);

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

        $item = Owner::find($id);

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }


    public function status($id)
    {
        $item = Owner::withTrashed()->where('id', $id)->first();
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
