<?php

namespace App\Http\Controllers\Api;

use App\Models\vType;
use Illuminate\Http\Request;
use Validator;

class VesselsTypesController extends Controller
{
    public function list(Request $request)
    {
        $query = vType::withoutTrashed();
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $order_field = 'created_at';
        $order_sort = 'desc';

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
