<?php

namespace App\Http\Controllers;

use App\Models\vType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class VesselsTypesController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Vessels types"]
        ];

        $types = vType::all();
        return view('content.vessels-types-list', ['breadcrumbs' => $breadcrumbs, 'types' => $types]);
    }

    public function list_api()
    {
        return response()->success(vType::withTrashed()->with('parent')->withCount('vessels')->get());
    }

    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [

            'name' => 'required|string',
            'parent' => 'nullable',
            'dwt' => 'required|string',
            'draught' => 'required|string',
            'loa' => 'required|string',
            'geared' => 'required|string',
            'holds' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        if ($request->hasFile('legal')) {
            $extension = $request->file('legal')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('legal'), $fileName);
        }

        $item = new vType;

        $parent = $request->input('parent', null);
        $parent = $parent === 'null' ? null : $parent;
        $item->name = $params['name'];
        $item->parent_id = $parent;
        $item->dwt = $params['dwt'];
        $item->draught = $params['draught'];
        $item->loa = $params['loa'];
        $item->geared = $params['geared'];
        $item->holds = $params['holds'];
        $item->description = $params['description'];

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [

            'name' => 'required|string',
            'parent' => 'nullable',
            'dwt' => 'required|string',
            'draught' => 'required|string',
            'loa' => 'required|string',
            'geared' => 'required|string',
            'holds' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = vType::find($id);

        $item->name = $params['name'];
        $item->parent_id = $request->input('parent', null);
        $item->dwt = $params['dwt'];
        $item->draught = $params['draught'];
        $item->loa = $params['loa'];
        $item->geared = $params['geared'];
        $item->holds = $params['holds'];
        $item->description = $params['description'];

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {

        $item = vType::find($id);

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }
}
