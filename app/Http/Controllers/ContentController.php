<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Validator;

class ContentController extends Controller
{
    public function view()
    {
        $breadcrumbs = [
            ['link' => "admin/home", 'name' => "الرئيسية"], ['name' => "الصفحات الثابتة"]
        ];

        $content = Content::all();

        return view('content.static-view', ['breadcrumbs' => $breadcrumbs, 'content' => $content]);
    }

    public function submit(Request $request)
    {
        $params = $request->all();

        unset($params['_token']);

        foreach ($params as $index => $param) {
            $item = Content::where('identifier', $index)->first();
            $item->content_ar = $item->content_en = $param;
            $item->save();
        }

        return redirect()->back()
            ->with('success', __('api.codes.success.message'))
            ->withInput();
    }
}
