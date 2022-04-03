<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function getCountries(Request $request)
    {
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        return response()->success(Country::skip(($page_number - 1) * $page_size)->take($page_size)->get());
    }
}
