<?php

namespace App\Http\Controllers;

use App\Mail\PasswordEmail;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

class AdminsController extends Controller
{
    public function list()
    {
        $breadcrumbs = [
            ['link' => "home", 'name' => "الرئيسية"], ['name' => "المدراء"]
        ];

        return view('content.admins-list', ['breadcrumbs' => $breadcrumbs]);
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
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = new Admin;
        $item->name = $params['name'];
        $item->email = $params['email'];
        $item->password = bcrypt($params['password']);
        $item->active = 1;

        Mail::to($request->input('email'))->send(new PasswordEmail($item->password));

        $item->save();

        return response()->success();
    }

    public function update($id, Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Admin::find($id);
        if ($item) {
            $item->name = $params['name'];
            $item->email = $params['email'];
            $item->password = $params['password'];

            Mail::to($request->input('email'))->send(new PasswordEmail($item->password));

            $item->save();
        }

        return response()->success();
    }

    public function delete($id)
    {

        $item = Admin::find($id);

        if ($item) {
            $item->delete();
            $item->save();
        }

        return response()->success();
    }

    public function status($id)
    {

        $item = Admin::find($id);
        if ($item) {
            $item->active = $item->active == 1 ? 0 : 1;
        }
        $item->save();

        return response()->success();
    }
}
