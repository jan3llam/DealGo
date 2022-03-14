<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    // home
    public function home()
    {
        $breadcrumbs = [
            ['name' => __('locale.Home')]
        ];

        return view('content.home', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
