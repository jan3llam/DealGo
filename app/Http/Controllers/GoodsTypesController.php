<?php

namespace App\Http\Controllers;

use App\Models\gType;
use App\Models\vType;
use Illuminate\Http\Request;
use Validator;

class GoodsTypesController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Goods types"]
        ];

        $gTypes = gType::all();
        $vTypes = vType::all();
        return view('content.goods-types-list', ['breadcrumbs' => $breadcrumbs, 'gTypes' => $gTypes, 'vTypes' => $vTypes]);
    }

    public function list_api()
    {
        return response()->success(gType::withTrashed()->with(['parent', 'vessels_types'])->get());
    }

    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'parent' => 'nullable',
            'vtype' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new gType;

        $parent = $request->input('parent', null);
        $parent = $parent === 'null' ? null : $parent;
        $item->name = $params['name'];
        $item->parent_id = $parent;
        $item->save();

        $vtypes = explode(',', $request->input('vtype', null));
        foreach ($vtypes as $type) {
            $item->vessels_types()->attach($type);
        }


        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'parent' => 'nullable',
            'vtype' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = gType::find($id);

        $parent = $request->input('parent', null);
        $parent = $parent === 'null' ? null : $parent;
        $item->name = $params['name'];
        $item->parent_id = $parent;

        $item->save();

        $item->vessels_types()->detach();
        $vtypes = $request->input('vtype', null);
        if (!is_integer($vtypes)) {
            $vtypes = explode(',', $request->input('vtype', null));
            foreach ($vtypes as $type) {
                $item->vessels_types()->attach($type);
            }
        } else {
            $item->vessels_types()->attach($vtypes);
        }


        return response()->success();
    }

    public function delete($id)
    {

        $item = gType::find($id);

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }
}
