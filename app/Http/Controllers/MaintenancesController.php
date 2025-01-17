<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class MaintenancesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:43', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:41', ['only' => ['add']]);
        $this->middleware('permission:42', ['only' => ['edit', 'status']]);
        $this->middleware('permission:44', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list($id = null)
    {
        $vessel = null;
        if ($id) {
            $vessel = Vessel::withTrashed()->where('id', $id)->first();
        }

        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')],
        ];

        if ($vessel) {
            array_push($breadcrumbs, ['name' => $vessel->name]);
        }
        array_push($breadcrumbs, ['name' => __('locale.Maintenances')]);

        $vessels = Vessel::all();

        return view('content.maintenances-list', [
            'breadcrumbs' => $breadcrumbs,
            'vessel' => $vessel,
            'vessels' => $vessels
        ]);
    }

    public function list_api(Request $request, $id = null)
    {

        $data = [];
        $search_clm = ['name', 'vessel.name', 'description'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Maintenance::with(['vessel' => function ($q) {
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
            $query->where('vessel_id', $id);
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
            'vessel' => 'required|numeric',
            'name' => 'required|string',
            'start' => 'required|string',
            'end' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $item = new Maintenance;

        $item->vessel_id = $params['vessel'];
        $item->name = $params['name'];
        $item->start_at = Carbon::parse($params['start'])->toDateTimeString();
        $item->end_at = Carbon::parse($params['end'])->toDateTimeString();
        $item->description = $params['description'];

        $files = $request->file('files', []);
        $filesArr = [];
        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $file, $fileName);
                $filesArr[] = $fileName;
            }
        }

        $item->files = json_encode($filesArr);

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'name' => 'required|string',
            'start' => 'required|string',
            'end' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Maintenance::withTrashed()->where('id', $id)->first();

        $item->vessel_id = $params['vessel'];
        $item->name = $params['name'];
        $item->start_at = Carbon::parse($params['start']);
        $item->end_at = Carbon::parse($params['end']);
        $item->description = $params['description'];

        $files = $request->file('files', []);
        $filesArr = [];
        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $file, $fileName);
                $filesArr[] = $fileName;
            }
        }

        $item->files = json_encode($filesArr);

        $item->save();

        return response()->success();
    }

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = Maintenance::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = Maintenance::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }
}
