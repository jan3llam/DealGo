<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Language;
use Illuminate\Http\Request;
use Validator;

class ClassificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:105', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:103', ['only' => ['add']]);
        $this->middleware('permission:104', ['only' => ['edit', 'status']]);
        $this->middleware('permission:106', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list($id = null)
    {
        $classification = null;
        if ($id) {
            $classification = Classification::withTrashed()->where('id', $id)->first();
        }

        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Content')]
        ];

        if ($classification) {
            array_push($breadcrumbs, ['link' => "admin/classifications", 'name' => __('locale.Classifications')]);
            array_push($breadcrumbs, ['name' => $classification->name]);
        } else {
            array_push($breadcrumbs, ['name' => __('locale.Classifications')]);
        }

        $classifications = Classification::withoutTrashed()->get();
        $languages = Language::withoutTrashed()->get();

        return view('content.classifications-list', [
            'breadcrumbs' => $breadcrumbs,
            'languages' => $languages,
            'classification' => $classification,
            'classifications' => $classifications
        ]);
    }

    public function list_api($id = null, Request $request)
    {

        $data = [];
        $search_clm = ['name', 'parent.name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Classification::withCount('posts')->withCount('children')->with('parent');

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

        if ($id) {
            $query->where('parent_id', $id);
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip(($page) * $per_page)
            ->take($per_page)->orderBy($order_field, $order_sort)->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $total;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|array',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new Classification;

        $item->parent_id = $request->input('classification', null) === 'null' ? null : $request->input('classification', null);
        $item->name = $params['name'];
        $item->description = $params['description'];

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|array',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Classification::withTrashed()->where('id', $id)->first();

        $item->parent_id = $request->input('classification', null) === 'null' ? null : $request->input('classification', null);
        $item->name = $params['name'];
        $item->description = $params['description'];

        $item->save();

        return response()->success();
    }

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = Classification::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = Classification::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }
}
