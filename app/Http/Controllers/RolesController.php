<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Validator;

class RolesController extends Controller
{
    private $guard_name = 'admins';

    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "Home"], ['name' => "Roles"]
        ];

        $roles = Role::all();
        $permissions = Permission::all();
        return view('content.roles-list', ['breadcrumbs' => $breadcrumbs, 'roles' => $roles, 'permissions' => $permissions]);
    }

    public function list_api()
    {
        return response()->success(Role::withTrashed()->get());
    }

    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'description' => 'required|string',
            'permissions' => 'required|array',
        ]);

        $permissions = $request->input('permissions');
        $perms = [];
        foreach (array_values($permissions) as $index => $item) {
            array_push($perms, array_keys($item)[0]);
        }
        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }
        try {
            $item = Role::create(['guard_name' => $this->guard_name, 'name' => $params['name'], 'description' => $params['description']]);
            $item->syncPermissions($perms);
        } catch (\Exception $e) {
            return redirect()->route('admin.roles')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.roles')->with('success', 'Success');
    }

    public function update(Request $request)
    {
        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'description' => 'required|string',
            'permissions' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $item = Role::findById($id);
        if ($item) {
            $item->name = $params['name'];
            $item->description = $params['description'];
            $item->syncPermissions($params['permissions']);
            $item->save();
        }

        return response()->success();
    }


    public function delete($id)
    {

        $item = Role::find($id);

        if ($item) {
            $item->status = 0;
            $item->save();
            $item->delete();
        }

        return response()->success();
    }
}
