<?php

namespace App\Http\Controllers\Api;

use App\Models\About;
use App\Models\Advantage;
use App\Models\Client;
use App\Models\Post;
use App\Models\Service;
use App\Models\Slider;

class HomepageController extends Controller
{
    public function get()
    {
        $data = [];

        $data['advantages'] = Advantage::withoutTrashed()->get();
        $data['services'] = Service::withoutTrashed()->get();
        $data['clients'] = Client::withoutTrashed()->get();
        $data['about'] = About::withoutTrashed()->get();
        $data['slider'] = Slider::withoutTrashed()->get();
        $data['posts'] = Post::withoutTrashed()->with('classification')->orderBy('created_at', 'desc')->limit(6)->get();

        return response()->success($data);
    }
}
