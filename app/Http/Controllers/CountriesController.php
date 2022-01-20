<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountriesController extends Controller
{
    public function getCountries()
    {
        return response()->success(Country::withTrashed()->get());
    }
}
