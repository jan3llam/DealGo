<?php

namespace App\Http\Controllers\Api;

use App\Models\gType;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Validator;

class GoodsTypesController extends Controller
{
    public function list(Request $request)
    {
        $data = gType::withoutTrashed();
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $vessel_id = $request->input('vessel', null);

        if ($vessel_id) {
            $vessel = Vessel::find($vessel_id);
            if ($vessel) {
                $data->whereIn('id', $vessel->type->goods_types->pluck('id'));
            }
        }

        return response()->success($data->skip(($page_number - 1) * $page_size)->take($page_size)->get());
    }
}
