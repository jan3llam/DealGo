<?php

namespace App\Http\Controllers\Api;

use App\Models\vType;
use Illuminate\Http\Request;
use Validator;

class VesselsTypesController extends Controller
{
    public function list(Request $request)
    {
        $data = vType::withoutTrashed();
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        return response()->success($data->skip(($page_number - 1) * $page_size)->take($page_size)->get());
    }
}
