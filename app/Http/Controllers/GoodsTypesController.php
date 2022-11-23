<?php

namespace App\Http\Controllers;

use App\Models\gType;
use App\Models\Language;
use App\Models\vType;
use Illuminate\Http\Request;
use Validator;

class GoodsTypesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:31', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:29', ['only' => ['add']]);
        $this->middleware('permission:30', ['only' => ['edit', 'status']]);
        $this->middleware('permission:32', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Goods types')]
        ];

        $gTypes = gType::all();
        $vTypes = vType::all();
        $languages = Language::withoutTrashed()->get();

        return view('content.goods-types-list', [
            'breadcrumbs' => $breadcrumbs,
            'gTypes' => $gTypes,
            'vTypes' => $vTypes,
            'languages' => $languages
        ]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['parent.name', 'name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = gType::query();

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_status = isset($params['status']) ? $params['status'] : 1;
        $per_page = isset($params['length']) ? $params['length'] : 10;

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
                    $item = explode('.', $item);
                    if (sizeof($item) == 4) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->whereHas($item[1], function ($que) use ($item, $search_val) {
                                $que->whereHas($item[2], function ($quer) use ($item, $search_val) {
                                    $quer->where($item[3], 'like', '%' . $search_val . '%');
                                });
                            });
                        })->get();
                    } elseif (sizeof($item) == 3) {
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

        $data['data'] = $query->skip($page)
            ->take($per_page)->orderBy($order_field, $order_sort)
            ->with(['parent' => function ($q) {
                $q->withTrashed();
            }, 'vessels_types'])->get();


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
            'name' => 'required|array',
            'parent' => 'nullable',
            'vtype' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = gType::withTrashed()->where('id', $id)->first();

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

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = gType::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = gType::withTrashed()->where('id', $id)->first();

        if ($item) {
            if ($item->children()->count() > 0) {
                foreach ($item->children() as $sub) {
                    $sub->delete();
                    if ($sub->children()->count() > 0) {
                        foreach ($sub->children() as $sub2) {
                            $sub2->delete();
                        }
                    }
                }
            }
            $item->delete();
        }

        return response()->success();
    }
}
