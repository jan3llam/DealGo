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
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Vessels Types"]
        ];

        $types = vType::all();
        return view('content.vessels-types-list', ['breadcrumbs' => $breadcrumbs, 'types' => $types]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['name', 'parent.name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = vType::with('parent')->withCount('vessels');

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_status = isset($params['status']) ? $params['status'] : 1;
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

        if ($filter_status !== null) {
            switch ($filter_status) {
                case 1:
                {
                    $query->withoutTrashed();
                    break;
                }
                case 2:
                {
                    $query->onlyTrashed();
                    break;
                }
            }
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

        $item = vType::withTrashed()->where('id', $id)->first();
        $parent = $request->input('parent');

        $item->name = $params['name'];
        $item->parent_id = $parent ? null : $parent;
        $item->dwt = $params['dwt'];
        $item->draught = $params['draught'];
        $item->loa = $params['loa'];
        $item->geared = $params['geared'];
        $item->holds = $params['holds'];
        $item->description = $params['description'];

        $item->save();

        return response()->success();
    }

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = vType::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = vType::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }
}
