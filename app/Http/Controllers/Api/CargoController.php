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
            'loadingPort.*.confirme_type' => ['nullable', 'between:1,2'],
            'loadingPort.*.sea_river' => ['nullable', 'between:1,2'],
            'loadingPort.*.port_type' => ['required', 'between:1,2'],
            'loadingPort.*.NAABSA' => ['nullable'],
            'loadingPort.*.geo_id' => ['required','exists:geo_areas,id'],
            'loadingPort.*.sea_draft' => ['nullable'],
            'loadingPort.*.air_draft' => ['nullable'],
            'loadingPort.*.beam_restriction' => ['nullable'],
            'loadingPort.*.port_id' => ['required', 'exists:ports,id'],
            'loadingPort.*.loading_conditions' => ['nullable', 'between:1,3'],
            'loadingPort.*.mtone_value' => ['required_if:loading_conditions,1'],
            'loadingPort.*.SSHINC' => ['nullable'],
            'loadingPort.*.SSHEX' => ['nullable'],
            'loadingPort.*.FHINC' => ['nullable'],
            'loadingPort.*.FHEX' => ['nullable'],
            'loadingPort.*.LoadRequests.*.goods_id' => ['required','exists:goods_types,id'],
            'loadingPort.*.LoadRequests.*.stowage_factor' => ['nullable'],
            'loadingPort.*.LoadRequests.*.cbm_cbft' => ['nullable', 'between:1,2'],
            'loadingPort.*.LoadRequests.*.min_cbm_cbft' => ['nullable'],
            'loadingPort.*.LoadRequests.*.max_cbm_cbft' => ['nullable'],
            'loadingPort.*.LoadRequests.*.min_weight' => ['nullable'],
            'loadingPort.*.LoadRequests.*.max_weight' => ['nullable'],
            'loadingPort.*.LoadRequests.*.min_sqm' => ['nullable'],
            'loadingPort.*.LoadRequests.*.max_sqm' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }
        $files = $request->file('files', []);
        $data = $this->cargoService->addCargo($params,$files);
        return response()->success();
    }
}
