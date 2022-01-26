<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class MaintenancesController extends Controller
{
    public function list($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Vessels maintenances"]
        ];

        $vessels = Vessel::all();
        $vessel = null;
        if ($id) {
            $vessel = Vessel::withTrashed()->where('id', $id)->first();
        }

        return view('content.maintenances-list', [
            'breadcrumbs' => $breadcrumbs,
            'vessel' => $vessel,
            'vessels' => $vessels
        ]);
    }

    public function list_api()
    {
        return response()->success(Maintenance::withTrashed()->get());
    }

    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'name' => 'required|string',
            'start' => 'required|string',
            'end' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new Maintenance;

        $item->vessel_id = $params['vessel'];
        $item->name = $params['name'];
        $item->start_at = Carbon::parse($params['start']);
        $item->end_at = Carbon::parse($params['end']);
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

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'name' => 'required|string',
            'start' => 'required|string',
            'end' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Maintenance::withTrashed()->where('id', $id)->first();

        $item->vessel_id = $params['vessel'];
        $item->name = $params['name'];
        $item->start_at = Carbon::parse($params['start']);
        $item->end_at = Carbon::parse($params['end']);
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

        return response()->success();
    }

    public function delete($id)
    {

        $item = Maintenance::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }
}
