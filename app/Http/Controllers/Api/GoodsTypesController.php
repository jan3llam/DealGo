<?php

namespace App\Http\Controllers\Api;

use App\Models\gType;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Validator;

class GoodsTypesController extends Controller
{
    public function list_parent(Request $request)
    {
        $query = gType::withoutTrashed()->whereNull('parent_id')->with('children');
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $vessel_id = $request->input('vessel', null);
        $order_field = 'created_at';
        $order_sort = 'desc';

        if ($vessel_id) {
            $vessel = Vessel::find($vessel_id);
            if ($vessel) {
                $query->whereIn('id', $vessel->type->goods_types->pluck('id'));
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

    public function list(Request $request)
    {
        $query = gType::withoutTrashed();
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $vessel_id = $request->input('vessel', null);
        $order_field = 'created_at';
        $order_sort = 'desc';

        if ($vessel_id) {
            $vessel = Vessel::find($vessel_id);
            if ($vessel) {
                $query->whereIn('id', $vessel->type->goods_types->pluck('id'));
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
