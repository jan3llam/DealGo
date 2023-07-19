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
    public function listCargo()
    {
        $ships = ShippingRequest::select(
            'id',
            'name',
            'port_from',
            'port_to',
            'date_from',
            'date_to',
            'contract'
        )->get();
        return $ships;
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
}
