<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function getCities($id = null, Request $request)
    {
        $query = City::query();
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $order_field = 'created_at';
        $order_sort = 'desc';

        if ($id) {
            $query->where('country_id', $id);
        }
        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }
}
