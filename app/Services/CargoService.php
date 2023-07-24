<?php

namespace App\Services;

use App\Models\LoadRequest;
use Illuminate\Support\Arr;
use App\Models\Request as ShippingRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CargoService
{
    public function listCargo($params, $search_clm, $order_field, $order_sort)
    {
        $ships = ShippingRequest::select(
            'id',
            'name',
            'port_from',
            'port_to',
            'date_from',
            'date_to',
            'contract'
        )->paginate();
        return $ships;
    }

    public function list_api($params, $search_clm, $order_field, $order_sort,$draw)
    {
        $query = ShippingRequest::query();

        $search_val = isset($params['search']) ? $params['search'] : null;
        $sort_field = isset($params['order']) ? $params['order'] : null;
        $page = isset($params['start']) ? $params['start'] : 0;
        $filter_status = isset($params['status']) ? $params['status'] : 1;
        $per_page = isset($params['length']) ? $params['length'] : 10;

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

        if ($sort_field) {
            $order_field = $sort_field;
            $order_sort = $params['direction'];
        }

        if ($filter_status) {
            switch ($filter_status) {
                case 1: {
                        $query->withoutTrashed();
                        break;
                    }
                case 2: {
                        $query->onlyTrashed();
                        break;
                    }
            }
        }

        $total = $query->limit($per_page)->count();

        $data['data'] = $query->skip($page)
            ->take($per_page)->orderBy($order_field, $order_sort)
            ->with(
                [
                    'tenant' => function ($q) {
                        $q->withTrashed()->with('user');
                    },
                    'port_from' => function ($q) {
                        $q->withTrashed();
                    },
                    'port_to' => function ($q) {
                        $q->withTrashed();
                    },
                    'owner' => function ($q) {
                        $q->withTrashed()->with('user');
                    },
                    'goods_types', 'routes'
                ]
            )->withCount('responses')->get();

        $data['meta']['draw'] = $draw;
        $data['meta']['total'] = $total;
        $data['meta']['count'] = $total;
        $data['data'] = $data['data']->toArray();

        return response()->success($data);
    }

    public function showCargo($cargo_id)
    {
        $ship = ShippingRequest::find($cargo_id);
        return $ship;
    }

    public function delete($id)
    {
        $item = ShippingRequest::withTrashed()->where('id', $id)->first();

        if ($item) {
            if ($item->responses()->count() > 0) {
                return response()->error('cannotDelete');
            }
            $item->delete();
        }
    }

    public function addCargo(array $request, $files)
    {
        DB::beginTransaction();
        $request['date_from'] = Carbon::parse($request['date_from'])->toDateString();
        $request['date_to'] = Carbon::parse($request['date_to'])->toDateString();
        $request['tenant_id'] = auth()->user()->id;

        $filesArr = [];
        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $file, $fileName);
                $filesArr[] = $fileName;
            }
        }

        $request['files'] = json_encode($filesArr);
        $ship = ShippingRequest::create(Arr::except($request, [
            'LoadingPorts'
        ]));

        foreach ($request['LoadingPorts'] as $port) {
            $portLoad = $ship->portRequest()->create($port);
            foreach ($port['LoadRequests'] as $load) {
                $load = $ship->loadRequest()->create($load);
            }
        }
        DB::commit();
    }

    public function updateCargo(array $request, $files ,$id)
    {
        DB::beginTransaction();
        $request['date_from'] = Carbon::parse($request['date_from'])->toDateString();
        $request['date_to'] = Carbon::parse($request['date_to'])->toDateString();
        $request['tenant_id'] = auth()->user()->id;

        $filesArr = [];
        if ($files) {
            foreach ($files as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random(18) . '.' . $extension;
                Storage::disk('public_images')->putFileAs('', $file, $fileName);
                $filesArr[] = $fileName;
            }
        }

        $request['files'] = json_encode($filesArr);
        $ship = ShippingRequest::findOrFail($id);
        $ship = $ship->update(Arr::except($request, [
            'LoadingPorts'
        ]));

        foreach ($request['LoadingPorts'] as $port) {
            $portLoad = $ship->portRequest()->create($port);
            foreach ($port['LoadRequests'] as $load) {
                $load = $ship->loadRequest()->create($load);
            }
        }
        DB::commit();
    }
}
