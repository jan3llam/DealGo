<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Owner;
use App\Models\Vessel;
use App\Models\vType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class VesselsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Vessels"]
        ];

        $countries = Country::all();
        $owners = Owner::all();
        $types = vType::all();
        return view('content.vessels-list', [
            'breadcrumbs' => $breadcrumbs,
            'countries' => $countries,
            'types' => $types,
            'owners' => $owners,
        ]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['user.name', 'user.username', 'user.gsm', 'user.email'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Vessel::query();

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
            ->with(['country', 'owner'])->take($per_page)->orderBy($order_field, $order_sort)->get();


        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function add(Request $request)
    {
        $fileName = null;
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'type' => 'required|numeric',
            'owner' => 'required|numeric',
            'country' => 'required|numeric',
            'imo' => 'required|string',
            'mmsi' => 'required|string',
            'capacity' => 'required',
            'build' => 'required',
            'image' => 'required|file',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('image'), $fileName);
        }

        $item = new Vessel;

        $item->image = $fileName;

        $item->name = $params['name'];
        $item->type_id = $params['type'];
        $item->owner_id = $params['owner'];
        $item->country_id = $params['country'];
        $item->imo = $params['imo'];
        $item->mmsi = $params['mmsi'];
        $item->capacity = $params['capacity'];
        $item->build_year = $params['build'];
        $item->status = 1;

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $fileName = null;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'type' => 'required|numeric',
            'owner' => 'required|numeric',
            'country' => 'required|numeric',
            'imo' => 'required|string',
            'mmsi' => 'required|string',
            'capacity' => 'required',
            'build' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('image'), $fileName);
        }
        $item = Vessel::withTrashed()->where('id', $id)->first();

        if ($request->hasFile('image')) {
            $item->image = $fileName;
        }

        $item->name = $params['name'];
        $item->type_id = $params['type'];
        $item->owner_id = $params['owner'];
        $item->country_id = $params['country'];
        $item->imo = $params['imo'];
        $item->mmsi = $params['mmsi'];
        $item->capacity = $params['capacity'];
        $item->build_year = $params['build'];

        $item->status = 1;

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {

        $item = Vessel::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }


    public function status($id)
    {
        $item = Vessel::withTrashed()->where('id', $id)->first();
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
