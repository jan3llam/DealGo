<?php

namespace App\Http\Controllers\Api;

use App\Models\Maintenance;
use App\Models\Owner;
use App\Models\User;
use App\Models\Vessel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class MaintenancesController extends Controller
{

    public function list($id, Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();
            $user_id = isset($user->userable) ? $user->userable->id : null;
        }

        $data = [];
        $search_clm = ['name', 'vessel.name', 'description'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $query = Maintenance::whereHas('vessel', function ($q) use ($id, $user_id) {
            $q->where('id', $id)->withoutTrashed()->whereHas('owner', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });;
        });

        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);

        if ($search_val) {
            $query->where(function ($q) use ($search_clm, $search_val) {
                foreach ($search_clm as $item) {
                    $item = explode('.', $item);
                    if (sizeof($item) == 3) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->whereHas($item[1], function ($que) use ($item, $search_val) {
                                $que->where($item[2], 'like', '%' . $search_val . '%');
                            });
                        })->get();
                    } elseif (sizeof($item) == 2) {
                        $q->orWhereHas($item[0], function ($qu) use ($item, $search_val) {
                            $qu->where($item[1], 'like', '%' . $search_val . '%');
                        })->get();
                    } elseif (sizeof($item) == 1) {
                        $q->orWhere($item[0], 'like', '%' . $search_val . '%');
                    }

                }
            });
        }

        $total = $query->count();

        $data['data'] = $query->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function add(Request $request)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'name' => 'required|string',
            'start' => 'required|string',
            'end' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $vessel = Vessel::withTrashed()->where('id', $params['vessel'])->where('owner_id', $user->userable->id)->first();

        if (!$vessel) {
            return response()->error('objectNotFound');
        }

        $item = new Maintenance;

        $item->vessel_id = $vessel->id;
        $item->name = $params['name'];
        $item->start_at = Carbon::parse($params['start']);
        $item->end_at = Carbon::parse($params['end']);
        $item->description = $params['description'];
        $item->files = json_encode($params['files']);

        $item->save();

        return response()->success();
    }

    public function update(Request $request)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $id = $request->object_id;

        $params = $request->all();
        $validator = Validator::make($params, [
            'vessel' => 'required|numeric',
            'name' => 'required|string',
            'start' => 'required|string',
            'end' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $vessel = Vessel::withTrashed()->where('id', $params['vessel'])->where('owner_id', $user->userable->id)->first();

        if (!$vessel) {
            return response()->error('objectNotFound');
        }

        $item = Maintenance::withTrashed()->where('id', $id)->whereHas('vessel', function ($q) use ($vessel) {
            $q->where('id', $vessel->id);
        })->first();
        dd($item);
        if (!$item) {
            return response()->error('objectNotFound');
        }

        $item->vessel_id = $vessel->id;
        $item->name = $params['name'];
        $item->start_at = Carbon::parse($params['start']);
        $item->end_at = Carbon::parse($params['end']);
        $item->description = $params['description'];
        $item->files = json_encode($params['files']);

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $item = Maintenance::withTrashed()->where('id', $id)->whereHas('vessel', function ($q) use ($user) {
            $q->whereHas('owner', function ($qu) use ($user) {
                $qu->where('id', $user->userable->id);
            });
        })->first();

        if ($item) {
            $item->delete();
        }

        return response()->success();
    }

    public function get($id)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        $item = Maintenance::withTrashed()->where('id', $id)->whereHas('vessel', function ($q) use ($user) {
            $q->whereHas('owner', function ($qu) use ($user) {
                $qu->where('id', $user->userable->id);
            });
        })->first();

        if (!$item) {
            return response()->error('objectNotFound');
        }

        return response()->success($item);
    }
}
