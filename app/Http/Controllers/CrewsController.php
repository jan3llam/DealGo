<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Crew;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class CrewsController extends Controller
{
    public function list($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Vessels crew"]
        ];

        $countries = Country::all();
        $vessels = Vessel::all();
        $vessel = null;
        if ($id) {
            $vessel = Vessel::withTrashed()->where('id', $id)->first();
        }

        return view('content.crews-list', [
            'breadcrumbs' => $breadcrumbs,
            'countries' => $countries,
            'vessel' => $vessel,
            'vessels' => $vessels
        ]);
    }

    public function list_api()
    {
        return response()->success(Crew::withTrashed()->with('city.country')->get());
    }

    public function add(Request $request)
    {
        $fileName = null;
        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job' => 'required|string',
            'birth' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|numeric',
            'email' => 'required',
            'phone' => 'required',
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        if ($request->hasFile('file')) {
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('file'), $fileName);
        }

        $item = new Crew;

        $item->file = $fileName;

        $item->vessel_id = $params['vessel'];
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->first_name = $params['first_name'];
        $item->last_name = $params['last_name'];
        $item->city_id = $params['city'];
        $item->job_title = $params['job'];
        $item->dob = $params['birth'];
        $item->address = $params['address'];

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
            'vessel' => 'required|numeric',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job' => 'required|string',
            'birth' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|numeric',
            'email' => 'required',
            'phone' => 'required',
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        if ($request->hasFile('file')) {
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('file'), $fileName);
        }

        $item = Crew::withTrashed()->where('id', $id)->first();

        $item->file = $fileName;

        $item->vessel_id = $params['vessel'];
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->first_name = $params['first_name'];
        $item->last_name = $params['last_name'];
        $item->city_id = $params['city'];
        $item->job_title = $params['job'];
        $item->dob = $params['birth'];
        $item->address = $params['address'];

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

        $item = Crew::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }


    public function status($id)
    {
        $item = Crew::withTrashed()->where('id', $id)->first();
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
