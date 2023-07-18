<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCargoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            'tenant' => ['required', 'numeric'],
            'address_commission' => ['nullable', 'string'],
            'broker_commission' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'LoadingPorts.*.confirme_type' => ['nullable', 'between:1,2'],
            'LoadingPorts.*.sea_river' => ['nullable', 'between:1,2'],
            'LoadingPorts.*.port_type' => ['required', 'between:1,2'],
            'LoadingPorts.*.NAABSA' => ['nullable'],
            'LoadingPorts.*.geo_id' => ['required'], //'exists:geo_areas,id'
            'LoadingPorts.*.sea_draft' => ['nullable'],
            'LoadingPorts.*.air_draft' => ['nullable'],
            'LoadingPorts.*.beam_restriction' => ['nullable'],
            'LoadingPorts.*.loading_conditions' => ['nullable', 'between:1,2'],
            'LoadingPorts.*.mtone_value' => ['nullable'],
            'LoadingPorts.*.SSHINC' => ['nullable'],
            'LoadingPorts.*.SSHEX' => ['nullable'],
            'LoadingPorts.*.FHINC' => ['nullable'],
            'LoadingPorts.*.FHEX' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.goods_id' => ['required'],
            'LoadingPorts.*.LoadRequests.*.stowage_factor' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.min_weight' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.max_weight' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.cbm_cbft' => ['nullable', 'between:1,2'],
            'LoadingPorts.*.LoadRequests.*.min_cbm_cbft' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.max_cbm_cbft' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.min_sqm' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.max_sqm' => ['nullable'],
            'LoadingPorts.*.LoadRequests.*.max_sqm' => ['nullable'],
        ];
    }
}
