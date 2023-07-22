<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\GlobalArea;
use Illuminate\Http\Request;
use Validator;

class GlobalAreasController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:35', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:33', ['only' => ['add']]);
        $this->middleware('permission:34', ['only' => ['edit', 'status']]);
        $this->middleware('permission:36', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.GlobalAreas')]
        ];

         $languages = Language::withoutTrashed()->get();

        return view('content.global-areas-list', ['breadcrumbs' => $breadcrumbs, 'languages' => $languages]);
    }



    
    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['id', 'name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = GlobalArea::query();

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
                    $query->where('status', 1)->withoutTrashed();
                    break;
                }
                case 2:
                {
                    $query->onlyTrashed();
                    break;
                }
                case 0:
                {
                    $query->where('status', 0)->withoutTrashed();;
                    break;
                }
            }
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip($page)
            ->with('localareas')->take($per_page)->orderBy($order_field, $order_sort)->get();


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
            'unlocode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new GlobalArea;

        $item->name = $params['name'];
        $item->unlocode = $params['unlocode'];

        $item->status = 1;

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|array',
            'unlocode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = GlobalArea::withTrashed()->where('id', $id)->first();

        $item->name = $params['name'];
        $item->unlocode = $params['unlocode'];

        $item->save();

        return response()->success();
    }

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = GlobalArea::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = GlobalArea::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }

    public function status($id)
    {
        $item = GlobalArea::withTrashed()->where('id', $id)->first();
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
