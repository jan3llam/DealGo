<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\gType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class TenantsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:11', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:9', ['only' => ['add']]);
        $this->middleware('permission:10', ['only' => ['edit', 'status']]);
        $this->middleware('permission:12', ['only' => ['bulk_delete', 'delete']]);
    }

    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Tenants')]
        ];

        $countries = Country::all();
        $types = gType::all();
        return view('content.tenants-list', ['breadcrumbs' => $breadcrumbs, 'countries' => $countries, 'types' => $types]);
    }

    public function list_api(Request $request)
    {

        $data = [];
        $search_clm = ['user.contact_name', 'user.phone', 'user.full_name', 'user.email', 'user.city.name_ar', 'user.city.name_en', 'user.city.name_fr',
            'user.city.country.name_ar', 'user.city.country.name_en', 'user.city.country.name_fr'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $params = $request->all();
        $query = Tenant::query();

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_status = isset($params['status']) ? $params['status'] : 1;
        $per_page = isset($params['length']) ? $params['length'] : 10;

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
                    $item = explode('.', $item);
                    if (sizeof($item) == 4) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->whereHas($item[1], function ($que) use ($item, $search_val) {
                                $que->whereHas($item[2], function ($quer) use ($item, $search_val) {
                                    $quer->where($item[3], 'like', '%' . $search_val . '%');
                                });
                            });
                        })->get();
                    } elseif (sizeof($item) == 3) {
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
                    $query->whereHas('user', function ($q) {
                        $q->where('status', 1)->withoutTrashed();
                    })->withoutTrashed();
                    break;
                }
                case 2:
                {
                    $query->whereHas('user', function ($q) {
                        $q->onlyTrashed();
                    })->onlyTrashed();
                    break;
                }
                case 0:
                {
                    $query->whereHas('user', function ($q) {
                        $q->where('status', 0)->withoutTrashed();
                    })->withoutTrashed();
                    break;
                }
            }
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip($page)
            ->take($per_page)->orderBy($order_field, $order_sort)
            ->with(['user' => function ($q) {
                $q->withTrashed();
            }, 'user.city.state.country', 'goods_types'])->get();

        $data['meta']['draw'] = $request->input('draw');
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $total;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function add(Request $request)
    {
        $fileName = null;
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required_if:type,1',
            'commercial' => 'required_if:type,1',
            'company' => 'required_if:type,1',
            'license' => 'required_if:type,1',
            'type' => 'required|numeric',
            'contact' => 'required|string',
            'zip' => 'required|string',
            'province' => 'required|string',
            'address_1' => 'required|string',
            'address_2' => 'nullable|string',
            'city' => 'required|numeric',
            'password' => 'required',
            'email' => 'required|unique:users,email',
            'phone' => 'required',
            'gtype' => 'required',
            'legal' => 'required|file',
        ]);

        if ($validator->fails()) {
            if (isset($validator->failed()['email']['Unique'])) {
                return response()->error('alreadyExist');
            }
            return response()->error('missingParameters', $validator->failed());
        }

        if ($request->hasFile('legal')) {
            $extension = $request->file('legal')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('legal'), $fileName);
        }

        $item = new User;
        $tenant = new Tenant;
        $tenant->save();

        $item->legal_file = $fileName;

        if ($request->type == 1) {

            if ($request->hasFile('company')) {
                $fileName = null;
                $extension = $request->file('company')->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $request->file('company'), $fileName);
                $item->company_file = $fileName;
            }

            if ($request->hasFile('license')) {
                $fileName = null;
                $extension = $request->file('license')->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $request->file('license'), $fileName);
                $item->license_file = $fileName;
            }


            $item->full_name = $params['name'];
            $item->commercial_number = $params['commercial'];

        }
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->contact_name = $params['contact'];
        $item->password = bcrypt($params['password']);
        $item->city_id = $params['city'];
        $item->type = $params['type'];
        $item->zip_code = $params['zip'];
        $item->province = $params['province'];
        $item->address_1 = $params['address_1'];
        $item->address_2 = $params['address_2'];
        $item->userable_id = $tenant->id;
        $item->userable_type = Tenant::class;

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
        $item->verified = 1;
        $item->secret = Str::random(40);

        $item->save();

        $gtypes = explode(',', $request->input('gtype', null));
        foreach ($gtypes as $type) {
            $tenant->goods_types()->attach($type);
        }

        $data = [
            'username' => $item->email,
            'secret' => $item->secret,
            'email' => $item->email,
            'first_name' => $item->contact_name,
            'last_name' => '',
            'custom_json' => 'none',
        ];

        Http::withHeaders([
            'PRIVATE-KEY' => env('CHATENGINE_PROJECT_KEY'),
        ])->post('https://api.chatengine.io/users/', $data);

        return response()->success();
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $fileName = null;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required_if:type,1',
            'commercial' => 'required_if:type,1',
            'type' => 'required|numeric',
            'contact' => 'required|string',
            'zip' => 'required|string',
            'province' => 'required|string',
            'address_1' => 'required|string',
            'address_2' => 'nullable|string',
            'city' => 'required|numeric',
            'email' => 'required',
            'phone' => 'required',
            'gtype' => 'required',
        ]);

        if ($validator->fails()) {
            if (isset($validator->failed()['email']['Unique'])) {
                return response()->error('alreadyExist');
            }
            return response()->error('missingParameters');
        }

        if ($request->hasFile('legal')) {
            $extension = $request->file('legal')->getClientOriginalExtension();
            $fileName = Str::random(18) . '.' . $extension;
            Storage::disk('public_images')->putFileAs('', $request->file('legal'), $fileName);
        }

        $itemT = Tenant::withTrashed()->where('id', $id)->first();
        $item = $itemT->user;

        if ($request->hasFile('legal')) {
            $item->legal_file = $fileName;
        }

        if ($request->type == 1) {

            if ($request->hasFile('company')) {
                $fileName = null;
                $extension = $request->file('company')->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $request->file('company'), $fileName);
                $item->company_file = $fileName;
            }

            if ($request->hasFile('license')) {
                $fileName = null;
                $extension = $request->file('license')->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $request->file('license'), $fileName);
                $item->license_file = $fileName;
            }


            $item->full_name = $params['name'];
            $item->commercial_number = $params['commercial'];

        }
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->contact_name = $params['contact'];
        if ($request->has('password')) {
            $item->password = bcrypt($params['password']);
        }
        $item->city_id = $params['city'];
        $item->type = $params['type'];
        $item->zip_code = $params['zip'];
        $item->province = $params['province'];
        $item->address_1 = $params['address_1'];
        $item->address_2 = $params['address_2'];

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

        $gtypes = explode(',', $request->input('gtype', null));
        $itemT->goods_types()->detach();
        foreach ($gtypes as $type) {
            $itemT->goods_types()->attach($type);
        }

        $item->files = json_encode($filesArr);
        $item->status = 1;

        $itemT->save();
        $item->save();

        return response()->success();
    }

    public function bulk_delete(Request $request)
    {
        foreach ($request->input('ids', []) as $id) {
            $item = Tenant::withTrashed()->where('id', $id)->first();
            if ($item) {
                $item_user = $item->user;
                $item_user->status = 0;
                $item->save();
                $item_user->save();
                $item_user->delete();
                $item->delete();
            }
        }
        return response()->success();
    }

    public function delete($id)
    {

        $item = Tenant::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item_user = $item->user;
            $item_user->status = 0;
            $item->save();
            $item_user->save();
            $item_user->delete();
            $item->delete();
        }

        return response()->success();
    }

    public function status($id)
    {
        $item = Tenant::withTrashed()->where('id', $id)->first();
        if ($item) {

            if ($item->user()->withTrashed()->first()->status === 0 && $item->deleted_at !== null) {
                $item_user = $item->user()->withTrashed()->first();
                $item_user->restore();
                $item->restore();
            }
            $item = $item->user()->withTrashed()->first();
            $item->status = $item->status == 1 ? 0 : 1;
            $item->save();
        }

        return response()->success();
    }
}
