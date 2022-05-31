<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Owner;
use App\Models\User;
use App\Models\Vessel;
use App\Models\vType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class VesselsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:23', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:21', ['only' => ['add']]);
        $this->middleware('permission:22', ['only' => ['edit', 'status']]);
        $this->middleware('permission:24', ['only' => ['bulk_delete', 'delete']]);
        $this->middleware('permission:65', ['only' => ['track', 'check_ps07']]);
    }

    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Vessels')]
        ];

        $countries = Country::all();
        $owners = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->get();
        $types = vType::all();
        return view('content.vessels-list', [
            'breadcrumbs' => $breadcrumbs,
            'countries' => $countries,
            'types' => $types,
            'owners' => $owners,
        ]);
    }

    public function track($id = null)
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Track vessels')]
        ];

        $vessels = Vessel::where('status', 1)->get();

        $vessel = null;
        if ($id) {
            $vessel = $id;
        }

        return view('content.vessels-track', [
            'breadcrumbs' => $breadcrumbs,
            'vessels' => $vessels,
            'vessel_id' => $vessel,
        ]);
    }

    public function check_ps07($id, Request $request)
    {
        $vessel = Vessel::withTrashed()->where('id', $id)->first();
        $mmsi = $vessel->mmsi;

        $response = Http::get('https://services.marinetraffic.com/api/exportvessel/' . env('MARINETRAFFIC_API_KEY_PS07'), [
            'v' => 5,
            'mmsi' => $mmsi,
            'protocol' => 'json',
            'timespan' => '1200'
        ]);
        if ($response->successful()) {
            if ($data = json_decode($response->getBody()->getContents())) {
                return response()->success([
                    'name' => $vessel->name,
                    'rotation' => $data[0][4],
                    'latitude' => $data[0][1],
                    'longitude' => $data[0][2]
                ]);
            } else {
                return response()->error('objectNotFound');
            }
        }
        $data = json_decode($response->getBody()->getContents())->errors[0];
        return response()->customError($data->code, $data->detail);

    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['build_year', 'imo', 'mmsi', 'country.name', 'owner.user.name', 'type.name'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Vessel::query();

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
                    $query->where('status', 1)->withoutTrashed();

                    break;
                }
                case 2:
                {
                    $query->onlyTrashed();
                    break;
                }
                case 0:
                {
                    $query->where('status', 0)->withoutTrashed();
                    break;
                }
            }
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip(($page) * $per_page)
            ->with(['country', 'owner' => function ($q) {
                $q->withTrashed()->with(['user' => function ($qu) {
                    $qu->withTrashed();
                }]);
            }, 'type' => function ($q) {
                $q->withTrashed();
            }])->withCount(['crew', 'maintenance'])->take($per_page)->orderBy($order_field, $order_sort)->get();


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

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = Vessel::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->status = 0;
                $item->save();
                $item->delete();
            }
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
