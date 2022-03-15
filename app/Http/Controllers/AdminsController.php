<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Validator;

class AdminsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "home", 'name' => __('locale.Home')], ['name' => __('locale.Administrators')]
        ];

        $countries = Country::all();
        $roles = Role::all();

        return view('content.admins-list', [
            'breadcrumbs' => $breadcrumbs,
            'countries' => $countries,
            'roles' => $roles
        ]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['name', 'phone', 'email', 'city.name_ar', 'city.name_en', 'city.name_fr',
            'city.country.name_ar', 'city.country.name_en', 'city.country.name_fr'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Admin::with(['roles', 'city.country']);

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
                    $query->where('status', 0)->withoutTrashed();;
                    break;
                }
            }
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


    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'dealgo_id' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'city' => 'required|numeric',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = new Admin;
        $item->name = $params['name'];
        $item->email = $params['email'];
        $item->dealgo_id = $params['dealgo_id'];
        $item->phone = $params['phone'];
        $item->address = $params['address'];
        $item->city_id = $params['city'];
        $item->password = bcrypt($params['password']);
        $item->status = 1;

//        Mail::to($request->input('email'))->send(new PasswordEmail($item->password));
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

        $role = $request->input('role', null);

        if ($role) {
            $item->assignRole($role);
        }

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'dealgo_id' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'city' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Admin::withTrashed()->where('id', $id)->first();
        if ($item) {
            $item->name = $params['name'];
            $item->email = $params['email'];
            $item->dealgo_id = $params['dealgo_id'];
            $item->phone = $params['phone'];
            $item->address = $params['address'];
            $item->city_id = $params['city'];

            if ($request->has('password')) {
                $item->password = bcrypt($params['password']);
            }

//            Mail::to($request->input('email'))->send(new PasswordEmail($item->password));

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

            $role = $request->input('role', null);

            if ($role) {
                $item->syncRoles([$role]);
            }
        }

        return response()->success();
    }

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = Admin::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item->status = 0;
                $item->save();
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = Admin::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }

    public function status($id)
    {
        $item = Admin::withTrashed()->where('id', $id)->first();
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
