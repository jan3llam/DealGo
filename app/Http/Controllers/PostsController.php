<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Language;
use App\Models\Post;
use Illuminate\Http\Request;
use Validator;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:109', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:107', ['only' => ['add']]);
        $this->middleware('permission:108', ['only' => ['edit', 'status']]);
        $this->middleware('permission:110', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list($id = null)
    {
        $classification = null;
        if ($id) {
            $classification = Classification::withTrashed()->where('id', $id)->first();
        }

        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Content')]
        ];

        if ($classification) {
            array_push($breadcrumbs, ['name' => $classification->name]);
        }
        array_push($breadcrumbs, ['name' => __('locale.Posts')]);

        $classifications = Classification::withoutTrashed()->get();
        $posts = Post::withoutTrashed()->get();
        $languages = Language::withoutTrashed()->get();

        return view('content.posts-list', [
            'breadcrumbs' => $breadcrumbs,
            'classification' => $classification,
            'languages' => $languages,
            'classifications' => $classifications,
            'posts' => $posts
        ]);
    }

    public function list_api($id = null, Request $request)
    {

        $data = [];
        $search_clm = ['name', 'classification.name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Post::with(['classification' => function ($q) {
            $q->withTrashed();
        }]);

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
            $query->where('classification_id', $id);
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip(($page) * $per_page)
            ->take($per_page)->orderBy($order_field, $order_sort)->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }
}
