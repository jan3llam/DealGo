<?php

namespace App\Http\Controllers\Api;

use App\Models\Port;
use Illuminate\Http\Request;
use Validator;

class PortsController extends Controller
{
    public function list(Request $request)
    {
        $data = Port::withoutTrashed()->where('status', 1);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $search_val = $request->input('keyword', 1);
        $search_clm = ['city.name', 'city.country.name', 'name'];

        if ($search_val) {
            $data->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
                    $item = explode('.', $item);
                    if (sizeof($item) == 3) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->whereHas($item[1], function ($que) use ($item, $search_val) {
                                $que->where($item[2], 'like', '%' . $search_val . '%');
                            });
                        })->get();
                    } elseif (sizeof($item) == 2) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->where($item[1], 'like', '%' . $search_val . '%');
                        })->get();
                    } elseif (sizeof($item) == 1) {
                        $q->orWhere($item[0], 'like', '%' . $search_val . '%');
                    }

                }
            });
        }

        return response()->success($data->skip(($page_number - 1) * $page_size)->take($page_size)->get());
    }
}
