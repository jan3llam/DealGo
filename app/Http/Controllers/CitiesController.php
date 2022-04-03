<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{

    public function getCities($id = null, Request $request)
    {

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        $cities = City::query();
        if ($id) {
            $cities->where('country_id', $id);
        }

        return response()->success($cities->skip(($page_number - 1) * $page_size)->take($page_size)->get());
    }
}
