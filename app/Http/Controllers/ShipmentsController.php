<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Validator;

class ShipmentsController extends Controller
{
    public function list($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => "Ports"]
        ];

        return view('content.shipments-list', ['breadcrumbs' => $breadcrumbs]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['user.name', 'user.username', 'user.gsm', 'user.email'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $offer_id = $request->input('offer_id', null);
        $params = $request->all();
        $query = Offer::with([
            'payments' => function ($query) {
                $query->sum('value');
            },
            'owner' => function ($q) {
                $q->withTrashed()->with('user');
            },
        ]);

        if ($offer_id) {
            $query->where('offer_id', $offer_id);
        }

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_trashed = isset($params['trashed']) ? $params['trashed'] : 0;
        $per_page = isset($params['length']) ? $params['length'] : 10;

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
//                    $item = explode('.', $item);
//                    $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
//                        $qu->where($item[1], 'like', '%' . $search_val . '%');
//                    })->get();
                    $q->orWhere($item[1], 'like', '%' . $search_val . '%');
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

    public function delete($id)
    {

        $item = Shipment::withTrashed()->where('id', $id)->first();

        if ($item) {

            $item->delete();
        }

        return response()->success();
    }


    public function status($id)
    {
        $item = Shipment::withTrashed()->where('id', $id)->first();
        if ($item) {

            if ($item->status === 0 && $item->deleted_at !== null) {
                $item->restore();
            }
            $item->status = $item->status == 1 ? 0 : 1;
            $item->save();
        }

        return response()->success();
    }
}
