<?php

namespace App\Http\Controllers\Api;

use App\Models\ContractPayment;
use Illuminate\Http\Request;
use Validator;

class PaymentsController extends Controller
{
    public function list(Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user_id = auth('api')->user()->id;
        }

        $query = ContractPayment::with('contract.owner')
            ->whereHas('contract', function ($q) use ($user_id) {
                $q->whereHas('tenant', function ($qu) use ($user_id) {
                    $qu->whereHas('user', function ($que) use ($user_id) {
                        $que->where('id', $user_id);
                    });
                });
            });

        if ($request->input('paid', null) !== null) {
            $query->where('paid', $request->input('paid', null));
        }

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->get()->each(function ($items) {
                $items->append(['full_value', 'remaining_value']);
            });

        $statistics = ContractPayment::with('contract.owner')
            ->whereHas('contract', function ($q) use ($user_id) {
                $q->whereHas('tenant', function ($qu) use ($user_id) {
                    $qu->whereHas('user', function ($que) use ($user_id) {
                        $que->where('id', $user_id);
                    });
                });
            })->get()->each(function ($items) {
                $items->append(['full_value', 'remaining_value']);
            });

        $data['statistics']['full'] = $statistics->sum('full_value');
        $data['statistics']['remaining'] = $statistics->sum('remaining_value');

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();


        return response()->success($data);
    }
}
