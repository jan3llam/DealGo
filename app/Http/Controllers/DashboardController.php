<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    // home
    public function home()
    {
        $breadcrumbs = [
            ['name' => "Home"]
        ];

        return view('content.home', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
