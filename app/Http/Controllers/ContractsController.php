<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Validator;

class ContractsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:66', ['only' => ['list', 'list_api']]);
    }

    public function list($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Contracts')]
        ];

        return view('content.contracts-list', ['breadcrumbs' => $breadcrumbs]);
    }

    public function list_api(Request $request)
    {
        $data = [];
        $search_clm = ['tenant.user.contact_name', 'owner.user.contact_name'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $params = $request->all();
        $query = Contract::withCount('shipments')->with([
            'tenant' => function ($q) {
                $q->withTrashed()->with('user');
            },
            'owner' => function ($q) {
                $q->withTrashed()->with('user');
            },
        ]);
        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_trashed = isset($params['trashed']) ? $params['trashed'] : 0;
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

        if ($filter_trashed) {
            $query->onlyTrashed();
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
