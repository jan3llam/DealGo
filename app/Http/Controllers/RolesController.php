<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Validator;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:15', ['only' => ['list', 'list_api']]);
        $this->middleware('permission:13', ['only' => ['add']]);
        $this->middleware('permission:14', ['only' => ['edit', 'status']]);
        $this->middleware('permission:16', ['only' => ['bulk_delete', 'delete']]);
    }

    private $guard_name = 'admins';

    public function list()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => __('locale.Home')], ['name' => __('locale.Roles')]
        ];

        $roles = Role::with('users')->withCount('users')->get();
        $permissions = Permission::all();
        return view('content.roles-list', ['breadcrumbs' => $breadcrumbs, 'roles' => $roles, 'permissions' => $permissions]);
    }

    public function list_api()
    {
        return response()->success(Role::all());
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

        $permissions = $request->input('permissions');
        $perms = [];
        foreach (array_values($permissions) as $index => $item) {
            array_push($perms, array_keys($item)[0]);
        }
        $item = Role::findById($id);
        if ($item) {
            $item->name = $params['name'];
            $item->description = $params['description'];
            $item->syncPermissions($perms);
            $item->save();
        }

        return redirect()->route('admin.roles')->with('success', 'Success');
    }


    public function delete($id)
    {

        $item = Role::where('id', $id)->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }
}
