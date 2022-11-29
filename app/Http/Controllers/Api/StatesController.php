<?php

namespace App\Http\Controllers\Api;

use App\Models\State;
use Illuminate\Http\Request;

class StatesController extends Controller
{
    public function getStates(Request $request, $id = null)
    {
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        $query = State::query();

        if ($id) {
            $query->where('country_id', $id);
        }

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }
}
