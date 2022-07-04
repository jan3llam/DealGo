<?php

namespace App\Http\Controllers\Api;

use App\Models\ContractPayment;
use App\Models\Owner;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class PaymentsController extends Controller
{
    public function list(Request $request)
    {
        $user = null;
        $user_id = null;

        if (auth('api')->check()) {
            $user_id = auth('api')->user()->id;
            $user = User::find(auth('api')->user()->id)->userable;
        }

        $query = ContractPayment::with('contract.owner.user')
            ->whereHas('contract', function ($q) use ($user_id, $user) {
                if ($user->userable instanceof Tenant) {
                    $q->whereHas('tenant', function ($qu) use ($user_id) {
                        $qu->whereHas('user', function ($que) use ($user_id) {
                            $que->where('id', $user_id);
                        });
                    });
                } elseif ($user->userable instanceof Owner) {
                    $q->whereHas('owner', function ($qu) use ($user_id) {
                        $qu->whereHas('user', function ($que) use ($user_id) {
                            $que->where('id', $user_id);

                        });
                    });
                }
            });

        if ($request->input('paid', null) !== null) {
            if ($request->input('paid', null) == 0) {
                $query->whereNull('submit_date');
            } else {
                $query->whereNotNull('submit_date');
            }
        }

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        $total = $query->limit($page_size)->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy('created_at', 'desc')->get()->each(function ($items) {
                $items->append(['full_value', 'remaining_value']);
            });

        $statistics = ContractPayment::whereHas('contract', function ($q) use ($user_id, $user) {
            if ($user->userable instanceof Tenant) {
                $q->whereHas('tenant', function ($qu) use ($user_id) {
                    $qu->whereHas('user', function ($que) use ($user_id) {
                        $que->where('id', $user_id);
                    });
                });
            } elseif ($user->userable instanceof Owner) {
                $q->whereHas('owner', function ($qu) use ($user_id) {
                    $qu->whereHas('user', function ($que) use ($user_id) {
                        $que->where('id', $user_id);

                    });
                });
            }
        })->get();

        $fullPayments = 0;
        $duePayments = 0;

        foreach ($statistics as $item) {
            if (!$item->submit_date) {
                $duePayments += $item->value;
            }
            $fullPayments += $item->value;
        }

        $data['statistics']['full'] = $fullPayments;
        $data['statistics']['remaining'] = $duePayments;

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();


        return response()->success($data);
    }
}
