<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Validator;

class SettingsController extends Controller
{
    public function view()
    {
        $breadcrumbs = [
            ['link' => "home", 'name' => "الرئيسية"], ['name' => "الإعدادات"]
        ];

        $settings = Settings::all();

        return view('content.settings-view', ['breadcrumbs' => $breadcrumbs, 'settings' => $settings]);
    }

    public function submit(Request $request)
    {
        $params = $request->all();

        unset($params['_token']);

        foreach ($params as $index => $param) {
            $item = Settings::where('key', $index)->first();
            $item->value = $param;
            $item->save();
        }

        return redirect()->back()
            ->with('success', __('api.codes.success.message'))
            ->withInput();
    }
}
