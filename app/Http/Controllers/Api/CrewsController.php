<?php

namespace App\Http\Controllers\Api;

use App\Models\Crew;
use App\Models\Owner;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Validator;

class CrewsController extends Controller
{

    public function list($id, Request $request)
    {
        $user_id = null;

        if (auth('api')->check()) {
            $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();
            $user_id = isset($user->userable) ? $user->userable->id : null;
        }

        $data = [];
        $search_clm = ['first_name', 'last_name', 'job_title', 'email', 'phone', 'city.name', 'vessel.name', 'address'];
        $order_field = 'created_at';
        $order_sort = 'desc';

        $query = Crew::whereHas('vessel', function ($q) use ($id, $user_id) {
            $q->where('id', $id)->withoutTrashed()->whereHas('owner', function ($qu) use ($user_id) {
                $qu->where('id', $user_id);
            });
        })->with(['city.country']);

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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job' => 'required|string',
            'birth' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|numeric',
            'email' => 'required|unique:crews,email',
            'phone' => 'required',
            'file' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        $vessel = Vessel::withTrashed()->where('id', $params['vessel'])->where('owner_id', $user->userable->id)->first();

        if (!$vessel) {
            return response()->error('objectNotFound');
        }

        $item = new Crew;

        $item->vessel_id = $vessel->id;
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->first_name = $params['first_name'];
        $item->last_name = $params['last_name'];
        $item->city_id = $params['city'];
        $item->job_title = $params['job'];
        $item->dob = $params['birth'];
        $item->address = $params['address'];
        $item->file = $params['file'];
        $item->files = json_encode($request->input('files', []));
        $item->status = 1;

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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job' => 'required|string',
            'birth' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|numeric',
            'email' => 'required|unique:crews,email',
            'phone' => 'required',
            'file' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters');
        }

        $vessel = Vessel::withTrashed()->where('id', $params['vessel'])->where('owner_id', $user->userable->id)->first();

        if (!$vessel) {
            return response()->error('objectNotFound');
        }

        $item = Crew::withTrashed()->where('id', $id)->whereHas('vessel', function ($q) use ($vessel) {
            $q->where('id', $vessel->id);
        })->first();

        $item->vessel_id = $vessel->id;
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->first_name = $params['first_name'];
        $item->last_name = $params['last_name'];
        $item->city_id = $params['city'];
        $item->job_title = $params['job'];
        $item->dob = $params['birth'];
        $item->address = $params['address'];
        $item->status = 1;
        $item->file = $params['file'];
        $item->files = json_encode($request->input('files', []));

        $item->save();

        return response()->success();
    }

    public function delete($id)
    {
        $user = User::whereHasMorph('userable', [Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $item = Crew::withTrashed()->where('id', $id)->whereHas('vessel', function ($q) use ($user) {
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

        $item = Crew::withTrashed()->where('id', $id)->whereHas('vessel', function ($q) use ($user) {
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
