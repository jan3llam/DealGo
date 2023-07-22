<?php

namespace App\Http\Controllers\Api;

use App\Models\Language;
use App\Models\LocalArea;
use App\Models\GlobalArea;
use Illuminate\Http\Request;
use Validator;

class GlobalAreasController extends Controller
{
    

    public function list(Request $request)
    {
        $query = GlobalArea::withoutTrashed()->where('status', 1);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $search_val = $request->input('keyword', '');
        $search_clm = ['name'];
        $order_field = 'name';
        $order_sort = 'asc';

        if ($search_val) {
            $query->Where($search_clm[0], 'like', '%' . $search_val . '%');
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
