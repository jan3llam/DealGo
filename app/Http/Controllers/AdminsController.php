<?php

namespace App\Http\Controllers;

use App\Mail\PasswordEmail;
use App\Models\Admin;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Validator;

class AdminsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "home", 'name' => "Home"], ['name' => "Administrators"]
        ];

        $countries = Country::all();
        $roles = Role::all();

        return view('content.admins-list', [
            'breadcrumbs' => $breadcrumbs,
            'countries' => $countries,
            'roles' => $roles
        ]);
    }

    public function list_api()
    {
        return response()->success(Admin::all());
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

        Mail::to($request->input('email'))->send(new PasswordEmail($item->password));

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
            'password' => 'required',
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
            $item->password = bcrypt($params['password']);

            Mail::to($request->input('email'))->send(new PasswordEmail($item->password));

            $item->save();

            $role = $request->input('role', null);

            if ($role) {
                $item->syncRoles([$role]);
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
