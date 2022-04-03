<?php

namespace App\Http\Controllers\Api;

use App\Models\About;
use App\Models\Advantage;
use App\Models\Article;
use App\Models\Client;
use App\Models\Service;

class HomepageController extends Controller
{
    public function get()
    {
        $data = [];

        $data['advantages'] = Advantage::withoutTrashed()->get();
        $data['services'] = Service::withoutTrashed()->get();
        $data['clients'] = Client::withoutTrashed()->get();
        $data['about'] = About::withoutTrashed()->get()->each(function ($items) {
            $items->append('description_html');
        });
        $data['articles'] = Article::withoutTrashed()->with('category')->orderBy('created_at', 'desc')->limit(6)->get();

        return response()->success($data);
    }
}
