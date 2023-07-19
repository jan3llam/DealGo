<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddCargoRequest;
use App\Services\CargoService;
use Illuminate\Http\Request;
use Validator;

class CargoController extends Controller
{
    protected $cargoService;

    public function __construct(CargoService $cargoService)
    {
        $this->cargoService = $cargoService;
    }

    public function list()
    {
        $data = $this->cargoService->listCargo();
        return response()->success($data);
    }

    public function show($cargo_id)
    {
        $data = $this->cargoService->showCargo($cargo_id);
        return response()->success($data);
    }

    public function delete($cargo_id)
    {
        $data = $this->cargoService->delete($cargo_id);
        return response()->success();
    }

    public function add(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => ['required', 'string'],
            'date_from' => ['required', 'string'],
            'date_to' => ['required', 'string'],
            'prompt' => ['nullable'],
            'spot' => ['nullable'],
            'dead_spot' => ['nullable'],
            'vessel_category' => ['required', 'between:1,6'],
            'vessel_category_json' => ['nullable', 'string'],
            'sole_part' => ['required', 'between:1,2'],
            'part_type' => ['nullable', 'string'],
            'contract' => ['required'],
            'min_weight' => ['nullable', 'numeric'],
            'max_weight' => ['nullable', 'numeric'],
            'min_cbm' => ['nullable', 'numeric'],
            'max_cbm' => ['nullable', 'numeric'],
            'min_cbft' => ['nullable', 'numeric'],
            'max_cbft' => ['nullable', 'numeric'],
            'min_sqm' => ['nullable', 'numeric'],
            'max_sqm' => ['nullable', 'numeric'],
            // 'tenant' => ['required', 'numeric'],
            'port_from' => ['required', 'numeric'],
            'port_to' => ['required', 'numeric'],
            'address_commission' => ['nullable', 'string'],
            'broker_commission' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'LoadingPorts.*.confirme_type' => ['nullable', 'between:1,2'],
            'LoadingPorts.*.sea_river' => ['nullable', 'between:1,2'],
            'LoadingPorts.*.port_type' => ['required', 'between:1,2'],
            'LoadingPorts.*.NAABSA' => ['nullable'],
            'LoadingPorts.*.geo_id' => ['required','exists:geo_areas,id'],
            'LoadingPorts.*.sea_draft' => ['nullable'],
            'LoadingPorts.*.air_draft' => ['nullable'],
            'LoadingPorts.*.beam_restriction' => ['nullable'],
            'LoadingPorts.*.port_id' => ['required', 'exists:ports,id'],
            'LoadingPorts.*.loading_conditions' => ['nullable', 'between:1,3'],
            'LoadingPorts.*.mtone_value' => ['required_if:loading_conditions,1'],
            'LoadingPorts.*.SSHINC' => ['nullable'],
            'LoadingPorts.*.SSHEX' => ['nullable'],
            'LoadingPorts.*.FHINC' => ['nullable'],
            'LoadingPorts.*.FHEX' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.goods_id' => ['required','exists:goods_types,id'],
            'LoadingPorts.*.LoadRequests.*.stowage_factor' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.cbm_cbft' => ['nullable', 'between:1,2'],
            'LoadingPorts.*.LoadRequests.*.min_cbm_cbft' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.max_cbm_cbft' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.min_weight' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.max_weight' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.min_sqm' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.max_sqm' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }
        $files = $request->file('files',  []);
        $data = $this->cargoService->addCargo($params,$files);
        return response()->success();
    }

}
