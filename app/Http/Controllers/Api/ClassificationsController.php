<?php

namespace App\Http\Controllers\Api;

use App\Models\Classification;
use Illuminate\Http\Request;

class ClassificationsController extends Controller
{
    public function list(Request $request)
    {
        $search_clm = ['name', 'parent.name'];

        $params = $request->all();
        $query = Classification::withCount('articles')->withCount('children')->with('parent');

        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
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

        if (isset($request->parent) && $request->parent) {
            $query->where('parent_id', $request->parent);
        }

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->get();

        $data['meta']['page_number'] = $page_number;
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $total;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }
}
