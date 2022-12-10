<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StatesController extends Controller
{

    public function getStates(Request $request, $id = null)
    {

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        $states = State::query();
        if ($id) {
            $states->where('country_id', $id);
        }

        return response()->success($states->skip(($page_number - 1) * $page_size)->take($page_size)->get());
    }
}
