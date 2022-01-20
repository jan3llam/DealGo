<?php

namespace App\Http\Controllers;

use App\Models\City;

class CitiesController extends Controller
{

    public function getCities($id = null)
    {
        $cities = City::query();
        if ($id) {
            $cities->where('country_id', $id);
        }
        return response()->success($cities->get());
    }
}
