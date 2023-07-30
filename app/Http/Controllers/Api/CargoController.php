<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddCargoRequest;
use App\Services\CargoService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;
use App\Models\Offer;
use App\Models\Request as ShippingRequest;
use App\Models\RequestResponse;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CargoController extends Controller
{
    protected $cargoService;

    public function __construct(CargoService $cargoService)
    {
        $this->cargoService = $cargoService;
    }

    public function list(Request $request)
    {

        if (!auth('api')->check()) {
            return response()->error('notAuthorized');
        }
        $user_id = auth('api')->user()->id;

        $data = [];
        $search_clm = ['port_from.name', 'port_to.name', 'port_from.city.name_ar', 'port_from.city.name_en', 'port_to.city.name_ar', 'port_to.city.name_en', 'tenant.user.contact_name'];
        $order_field = 'created_at';
        $order_sort = 'desc';
        $now = Carbon::parse(date('Y-m-d', strtotime(now())))->toDateString();

        $builder = ShippingRequest::whereHas('port_to')->whereHas('port_from')
            ->whereHas('tenant', function ($q) {
                $q->whereHas('user');
            })
            ->with(['port_to', 'port_from', 'tenant.user', 'routes', 'goods_types','loadRequest','portRequest'])
            ->withCount([
                'responses' => function (Builder $q) {
                    $q->whereHas('vessels')->whereHas('request_goods_types');
                }
            ]);


        $search_val = $request->input('keyword', null);
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $tenant = $request->input('tenant', null);
        $port_to = $request->input('port_to', null);
        $port_from = $request->input('port_from', null);
        $date_from = $request->input('date_from', null);
        $date_to = $request->input('date_to', null);
        $is_mine = $request->input('is_mine', 0);


        if ($search_val) {
            $builder->where(function ($q) use ($search_clm, $search_val) {
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
        $builder->when($port_from, function ($query) use ($port_from) {
            $query->where('port_from', $port_from);
        });

        $builder->when($port_to, function ($query) use ($port_to) {
            $query->where('port_to', $port_to);
        });


        $builder->when($date_from, function ($query) use ($date_from) {
            $from = Carbon::parse(date('Y-m-d', strtotime($date_from)))->toDateString();
            $query->where('date_from', '>=', $from);
        });
        $builder->when($date_to, function ($query) use ($date_to) {
            $to = Carbon::parse(date('Y-m-d', strtotime($date_to)))->toDateString();
            $query->whereDate('date_to', '<=', $to);
        });

        if(!$date_from && !$date_to){
            $builder->whereDate('date_to','>=',$now);
        }

        $builder->when($tenant, function ($query) use ($tenant) {
            $query->whereHas('tenant', function ($q) use ($tenant) {
                $q->where('id', $tenant);
            });
        });

        $builder->when($is_mine, function ($query) use ($user_id) {
            $query->whereHas('tenant', function ($q) use ($user_id) {
                $q->where('id', auth('api')->user()->userable->id);
            });
        });


        $total = $builder->count();


        $data['data'] = $builder->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy($order_field, $order_sort)->get();


        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;
        $data['data'] = $data['data']->toArray();

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

        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'prompt' => 'nullable',
            'spot' => 'nullable',
            'dead_spot' => 'nullable',
            'vessel_category' => 'required|between:1,6',
            'vessel_category_json' => 'nullable|string',
            'sole_part' => 'required|between:1,2',
            'part_type' => 'nullable|string',
            'contract' => 'required',
            'min_weight' => 'nullable|numeric',
            'max_weight' => 'nullable|numeric',
            'min_cbm' => 'nullable|numeric',
            'max_cbm' => 'nullable|numeric',
            'min_cbft' => 'nullable|numeric',
            'max_cbft' => 'nullable|numeric',
            'min_sqm' => 'nullable|numeric',
            'max_sqm' => 'nullable|numeric',
            'port_from' => 'required|numeric',
            'port_to' => 'required|numeric',
            'address_commission' => 'nullable|string',
            'broker_commission' => 'nullable|string',
            'description' => 'nullable|string',
            'LoadingPorts.*.confirme_type' => 'nullable|between:1,2',
            'LoadingPorts.*.sea_river' => 'nullable|between:1,2',
            'LoadingPorts.*.port_type' => 'required|between:1,2',
            'LoadingPorts.*.NAABSA' => 'nullable',
            'LoadingPorts.*.geo_id' => 'nullable|exists:local_areas,id',
            'LoadingPorts.*.sea_draft' => 'nullable',
            'LoadingPorts.*.air_draft' => 'nullable',
            'LoadingPorts.*.beam_restriction' => 'nullable',
            'LoadingPorts.*.port_id' => 'required',
            'exists:ports,id',
            'LoadingPorts.*.loading_conditions' => 'nullable|between:1,3',
            'LoadingPorts.*.mtone_value' => 'required_if:loading_conditions,1',
            'LoadingPorts.*.SSHINC' => 'nullable',
            'LoadingPorts.*.SSHEX' => 'nullable',
            'LoadingPorts.*.FHINC' => 'nullable',
            'LoadingPorts.*.FHEX' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.goods_id' => 'required|exists:goods_types,id',
            'LoadingPorts.*.LoadRequests.*.stowage_factor' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.cbm_cbft' => 'nullable|between:1,2',
            'LoadingPorts.*.LoadRequests.*.min_cbm_cbft' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.max_cbm_cbft' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.min_weight' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.max_weight' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.min_sqm' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.max_sqm' => 'nullable',
        ]);


        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }

        DB::beginTransaction();
        try {
            $params['date_from'] = Carbon::parse($params['date_from'])->toDateString();
            $params['date_to'] = Carbon::parse($params['date_to'])->toDateString();
            $params['tenant_id'] = $user->userable->id;
            $files = $request->file('files', []);

            $filesArr = [];
            if ($files) {
                foreach ($files as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = Str::random(18) . '.' . $extension;
                    Storage::disk('public_images')->putFileAs('', $file, $fileName);
                    $filesArr[] = $fileName;
                }
            }

            $params['files'] = json_encode($filesArr);
            $ship = ShippingRequest::create(Arr::except($params, [
                'LoadingPorts'
            ]));

            foreach ($params['LoadingPorts'] as $port) {
                $ship->portRequest()->create($port);
                foreach ($port['LoadRequests'] as $load) {
                    $load = $ship->loadRequest()->create($load);
                }
            }

            DB::commit();

            return response()->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }

    }

    public function update(Request $request, $id)
    {
        $user = User::whereHasMorph('userable', [Tenant::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string',
            'date_from' => 'required|string',
            'date_to' => 'required|string',
            'prompt' => 'nullable',
            'spot' => 'nullable',
            'dead_spot' => 'nullable',
            'vessel_category' => 'required|between:1,6',
            'vessel_category_json' => 'nullable|string',
            'sole_part' => 'required|between:1,2',
            'part_type' => 'nullable|string',
            'contract' => 'required',
            'min_weight' => 'nullable|numeric',
            'max_weight' => 'nullable|numeric',
            'min_cbm' => 'nullable|numeric',
            'max_cbm' => 'nullable|numeric',
            'min_cbft' => 'nullable|numeric',
            'max_cbft' => 'nullable|numeric',
            'min_sqm' => 'nullable|numeric',
            'max_sqm' => 'nullable|numeric',
            'port_from' => 'required|numeric',
            'port_to' => 'required|numeric',
            'address_commission' => 'nullable|string',
            'broker_commission' => 'nullable|string',
            'description' => 'nullable|string',
            'LoadingPorts.*.confirme_type' => 'nullable|between:1,2',
            'LoadingPorts.*.sea_river' => 'nullable|between:1,2',
            'LoadingPorts.*.port_type' => 'required|between:1,2',
            'LoadingPorts.*.NAABSA' => 'nullable',
            'LoadingPorts.*.geo_id' => 'nullable|exists:local_areas,id',
            'LoadingPorts.*.sea_draft' => 'nullable',
            'LoadingPorts.*.air_draft' => 'nullable',
            'LoadingPorts.*.beam_restriction' => 'nullable',
            'LoadingPorts.*.port_id' => 'required',
            'exists:ports,id',
            'LoadingPorts.*.loading_conditions' => 'nullable|between:1,3',
            'LoadingPorts.*.mtone_value' => 'required_if:loading_conditions,1',
            'LoadingPorts.*.SSHINC' => 'nullable',
            'LoadingPorts.*.SSHEX' => 'nullable',
            'LoadingPorts.*.FHINC' => 'nullable',
            'LoadingPorts.*.FHEX' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.goods_id' => 'required|exists:goods_types,id',
            'LoadingPorts.*.LoadRequests.*.stowage_factor' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.cbm_cbft' => 'nullable|between:1,2',
            'LoadingPorts.*.LoadRequests.*.min_cbm_cbft' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.max_cbm_cbft' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.min_weight' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.max_weight' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.min_sqm' => 'nullable',
            'LoadingPorts.*.LoadRequests.*.max_sqm' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }
        $files = $request->file('files', []);
        try{
            $this->cargoService->updateCargo(
                $params,
                $files,
                $id
            );
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }
        return response()->success();
    }

    public function getByOwnerId(Request $request,$id){

        $shipping_requests = ShippingRequest::where('tenant_id',$id)->paginate(10);

        if(count($shipping_requests) == 0){
            return response()->noContent();
        }

        return response()->json(array("code" => 1, "message" => "success", "data" => $shipping_requests), 200);
    }

}
