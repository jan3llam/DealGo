<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class UsersController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "الرئيسية"], ['name' => "المستخدمين"]
        ];

        return view('content.users-list', ['breadcrumbs' => $breadcrumbs]);
    }

    public function view($id)
    {
        $breadcrumbs = [
            ['link' => "home", 'name' => "الرئيسية"], ['link' => "users", 'name' => "المستخدمين"]
        ];

        $user = User::withTrashed()->where('id', $id)->first();

        return view('content.user-view', [
            'breadcrumbs' => $breadcrumbs,
            'object' => $user
        ]);
    }

    public function update(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = User::find($request->object_id);
        if ($item) {
            $item->name = $params['name'];
            $item->email = $params['email'];
            $item->gsm = $params['phone'];

            $item->save();
        }

        return redirect()->route('admin.user', $request->object_id);
    }

    public function list_api()
    {
        return response()->success(User::all());
    }

    public function check_field(Request $request)
    {
        $email = $request->input('email', null);
        $phone = $request->input('phone', null);
        $zip = $request->input('zip', null);
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                return response()->error('alreadyExist');
            }
            return response()->success();
        } elseif ($phone) {
            $user = User::where('phone', $phone)->first();
            if ($user) {
                return response()->error('alreadyExist');
            }
            return response()->success();
        } elseif ($zip) {
            $user = User::where('zip_code', $zip)->first();
            if ($user) {
                return response()->error('alreadyExist');
            }
            return response()->success();
        }
        return response()->error('alreadyExist');
    }

    public function delete($id)
    {

        $item = User::withTrashed()->where('id', $id)->first();

        if ($item) {
            $item->delete();
            $item->save();
        }

        return response()->success();
    }

    public function status($id)
    {

        $item = User::withTrashed()->where('id', $id)->first();
        if ($item) {
            $item->active = $item->active == 1 ? 0 : 1;
        }
        $item->save();

        return response()->success();
    }
}
