<?php

namespace App\Http\Controllers\Api;

use App\Models\gType;
use App\Models\Vessel;
use App\Models\vType;
use Illuminate\Http\Request;
use Validator;

class GoodsTypesController extends Controller
{
    public function list_parent(Request $request)
    {
        $query = gType::withoutTrashed()->whereNull('parent_id')->with('children');
        $search_clm = ['name', 'parent.name', 'children.name'];
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $keyword = $request->input('keyword', null);
        $vessel_id = $request->input('vessel', null);
        $order_field = 'created_at';
        $order_sort = 'desc';

        if ($vessel_id) {
            $vessel = Vessel::find($vessel_id);
            if ($vessel) {
                $query->whereIn('id', $vessel->type->goods_types->pluck('id'));
            }
        }

        if ($keyword) {
            $query->where(function ($q) use ($search_clm, $keyword) {
                foreach ($search_clm as $item) {
                    $item = explode('.', $item);
                    if (sizeof($item) == 3) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $keyword) {
                            $qu->whereHas($item[1], function ($que) use ($item, $keyword) {
                                $que->where($item[2], 'like', '%' . $keyword . '%');
                            });
                        })->get();
                    } elseif (sizeof($item) == 2) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $keyword) {
                            $qu->where($item[1], 'like', '%' . $keyword . '%');
                        })->get();
                    } elseif (sizeof($item) == 1) {
                        $q->orWhere($item[0], 'like', '%' . $keyword . '%');
                    }

                }
            });
        }


        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();


        return response()->success($data);
    }

    public function list(Request $request)
    {
        $query = gType::withoutTrashed();
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $vessel_id = $request->input('vessel', null);
        $vtype = $request->input('vtype', null);
        $order_field = 'created_at';
        $order_sort = 'desc';

        if ($vessel_id) {
            $vessel = Vessel::find($vessel_id);
            if ($vessel) {
                $query->whereIn('id', $vessel->type->goods_types->pluck('id'));
            }
        }

        if ($vtype) {
            $type = vType::find($vtype);
            if ($type) {
                $query->whereIn('id', $type->goods_types->pluck('id'));
            }
        }

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();


        return response()->success($data);
    }
}
