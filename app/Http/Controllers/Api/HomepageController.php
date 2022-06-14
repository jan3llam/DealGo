<?php

namespace App\Http\Controllers\Api;

use App\Models\About;
use App\Models\Advantage;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Owner;
use App\Models\Post;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\Slider;
use App\Models\Tenant;
use App\Models\User;

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
        $data['statistics']['owners_count'] = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->count();
        $data['statistics']['tenants_count'] = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->count();
        $data['statistics']['contracts_count'] = Contract::count();
        $data['statistics']['shipments_count'] = Shipment::count();
        $data['posts'] = Post::withoutTrashed()->with('classification')->orderBy('created_at', 'desc')->limit(6)->get();

        return response()->success($data);
    }
}
