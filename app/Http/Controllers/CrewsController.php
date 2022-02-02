<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Crew;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class CrewsController extends Controller
{
    public function list($id = null)
    {
        $vessel = null;
        if ($id) {
            $vessel = Vessel::withTrashed()->where('id', $id)->first();
        }
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"],
        ];

        if ($vessel) {
            array_push($breadcrumbs, ['name' => $vessel->name]);
        }
        array_push($breadcrumbs, ['name' => 'Vessel crew']);

        $countries = Country::all();
        $vessels = Vessel::all();

        return view('content.crews-list', [
            'breadcrumbs' => $breadcrumbs,
            'countries' => $countries,
            'vessel' => $vessel,
            'vessels' => $vessels
        ]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['user.name', 'user.username', 'user.gsm', 'user.email'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Crew::query();

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
            ->take($per_page)->orderBy($order_field, $order_sort)
            ->with(['city.country'])->get();


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
            'vessel' => 'required|numeric',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job' => 'required|string',
            'birth' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|numeric',
            'email' => 'required|unique:crews,email',
            'phone' => 'required',
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            if (isset($validator->failed()['email']['Unique'])) {
                return response()->error('alreadyExist');
            }
            return response()->error('missingParameters', $validator->failed());
        }

        if ($request->hasFile('file')) {
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('file'), $fileName);
        }

        $item = new Crew;

        $item->file = $fileName;

        $item->vessel_id = $params['vessel'];
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->first_name = $params['first_name'];
        $item->last_name = $params['last_name'];
        $item->city_id = $params['city'];
        $item->job_title = $params['job'];
        $item->dob = $params['birth'];
        $item->address = $params['address'];

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
            'vessel' => 'required|numeric',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job' => 'required|string',
            'birth' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|numeric',
            'email' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            if (isset($validator->failed()['email']['Unique'])) {
                return response()->error('alreadyExist');
            }
            return response()->error('missingParameters', $validator->failed());
        }

        if ($request->hasFile('file')) {
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('file'), $fileName);
        }

        $item = Crew::withTrashed()->where('id', $id)->first();

        if ($request->hasFile('file')) {
            $item->file = $fileName;
        }

        $item->vessel_id = $params['vessel'];
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->first_name = $params['first_name'];
        $item->last_name = $params['last_name'];
        $item->city_id = $params['city'];
        $item->job_title = $params['job'];
        $item->dob = $params['birth'];
        $item->address = $params['address'];

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
        $item->status = 1;

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {

        $item = Crew::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }


    public function status($id)
    {
        $item = Crew::withTrashed()->where('id', $id)->first();
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
