<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class ArticlesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:73', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:71', ['only' => ['add']]);
        $this->middleware('permission:72', ['only' => ['edit', 'status']]);
        $this->middleware('permission:74', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list($id = null)
    {
        $category = null;
        if ($id) {
            $category = Category::withTrashed()->where('id', $id)->first();
        }

        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')],
        ];

        if ($category) {
            array_push($breadcrumbs, ['name' => $category->name]);
        }
        array_push($breadcrumbs, ['name' => __('locale.Articles')]);

        $categories = Category::withoutTrashed()->get();
        $articles = Article::withoutTrashed()->get();

        return view('content.articles-list', [
            'breadcrumbs' => $breadcrumbs,
            'category' => $category,
            'categories' => $categories,
            'articles' => $articles
        ]);
    }

    public function list_api($id = null, Request $request)
    {

        $data = [];
        $search_clm = ['name', 'category.name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Article::with(['category' => function ($q) {
            $q->withTrashed();
        }, 'related']);

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_status = isset($params['status']) ? $params['status'] : 1;
        $per_page = isset($params['length']) ? $params['length'] : 10;

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

        if ($sort_field) {
            $order_field = $sort_field;
            $order_sort = $params['direction'];
        }

        if ($filter_status !== null) {
            switch ($filter_status) {
                case 1:
                {
                    $query->withoutTrashed();
                    break;
                }
                case 2:
                {
                    $query->onlyTrashed();
                    break;
                }
            }
        }

        if ($id) {
            $query->where('category_id', $id);
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip($page)
            ->take($per_page)->orderBy($order_field, $order_sort)->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $total;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'created_at' => 'required|string',
            'updated_at' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new Article;

        $item->category_id = $request->input('category', null) === 'null' ? null : $request->input('category', null);
        $item->name = $params['name'];
        $item->description = $params['description'];
        $item->created_at = Carbon::parse($params['created_at'])->toDateTimeString();
        $item->updated_at = Carbon::parse($params['updated_at'])->toDateTimeString();

        $item->save();

        $related = explode(',', $request->input('related', null));

        foreach ($related as $i) {
            if ($i) {
                $item->related()->attach($i);
            }
        }

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'created_at' => 'required|string',
            'updated_at' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Article::withTrashed()->where('id', $id)->first();

        $item->category_id = $request->input('category', null) === 'null' ? null : $request->input('category', null);
        $item->name = $params['name'];
        $item->description = $params['description'];
        $item->created_at = Carbon::parse($params['created_at'])->toDateTimeString();
        $item->updated_at = Carbon::parse($params['updated_at'])->toDateTimeString();

        $item->save();

        $item->related()->detach();
        $related = explode(',', $request->input('related', null));
        foreach ($related as $i) {
            if ($i) {
                $item->related()->attach($i);
            }
        }

        return response()->success();
    }

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = Article::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = Article::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }
}
