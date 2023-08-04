<?php

namespace App\Http\Controllers\Api;

use App\Models\Owner;
use App\Models\Port;
use App\Models\Tenant;
use App\Models\User;
use App\Models\VoyageCalculation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class VoyageController extends Controller
{
    public function get_distance(Request $request)
    {

        $def_port_from = "Bleckede";
        $def_port_to = "Goa";
        require_once "ports.php";

        $params = $request->all();
        $validator = Validator::make($params, [
            'port_from' => 'required',
            'port_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->failed());
        }
        $fromPort = Port::withTrashed()->where('id', $params['port_from'])->first();
        $toPort = Port::withTrashed()->where('id', $params['port_to'])->first();
        $fromPortKey = ucfirst(strtolower($fromPort->name_translation));
        $toPortKey = ucfirst(strtolower($toPort->name_translation));

        // coordinates of from port

        // $fromPortKey_lat = ($fromPort->latitude < 0) ? ($fromPort->latitude * -1) . "S" : ($fromPort->latitude) . "N";
        // $fromPortKey_long = ($fromPort->longitude < 0) ? ($fromPort->longitude * -1) . "W" : ($fromPort->longitude) . "E";
        // $fromPortKey_coords = $fromPortKey_lat . "%20" . $fromPortKey_long;

        // coordinates of to port
        // $toPortKey_lat = ($toPort->latitude < 0) ? ($toPort->latitude * -1) . "S" : ($toPort->latitude) . "N";
        // $toPortKey_long = ($toPort->longitude < 0) ? ($toPort->longitude * -1) . "W" : ($toPort->longitude) . "E";
        // $toPortKey_coords = $toPortKey_lat . "%20" . $toPortKey_long;

        // Next 2 line of code are for demo of NEA
        $portFrom = array_key_exists($fromPortKey, $ports) ? $ports[$fromPortKey] : $def_port_from;
        $portTo = array_key_exists($toPortKey, $ports) ? $ports[$toPortKey] : $def_port_to;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.netpas.net/nea/v7/json/get_distance/?pincode=DEMO&access_code=aldrobi.molham%40gmail.com&piracy_code=001&ports=' . $portFrom . '&ports=' . $portTo . '&canal_pass_code=011&use_local_eca=false',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $response_array = json_decode($response, true);

        curl_close($curl);

        $data['distance'] = $response_array['total_distance'];
        $data['message'] = $response_array['message'];
        return response()->success($data);
    }

    public function getById(int $id)
    {

        $data = VoyageCalculation::findOrFail($id)->where(['id' => $id, 'deleted_at' => null, 'user_id' => auth('api')->user()->id])->get();

        return response()->success($data);
    }

    public function getAll(Request $request)
    {

        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);


        $voyages = VoyageCalculation::where('deleted_at', null)->whereHas('user')->where('user_id', auth('api')->user()->id);

        $total = $voyages->count();

        $data['data'] = $voyages->skip(($page_number - 1) * $page_size)
            ->take($page_size)->orderBy('created_at', 'desc')->get();

        $data['meta']['total'] = $total;
        $data['meta']['count'] = $data['data']->count();
        $data['meta']['page_number'] = $page_number;


        return response()->success($data);
    }

    public function store(Request $request)
    {
        $user = User::whereHasMorph('userable', [Tenant::class,Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required','string',Rule::unique('voyage_calculations', 'name')->where('user_id', $user->id)],
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->errors());
        }
        DB::beginTransaction();
        try {

            $voyage = VoyageCalculation::create([
                'name' => $request->name,
                'details' => $request->details,
                'user_id' => $user->id
            ]);

            DB::commit();

            return response()->success();
        } catch (\Exception $e) {
            DB::rollBack();
            if($e->getCode() == 23000){
                return response()->json(array("code" => $e->getCode(), "message" => "name already taken", "data" => null), 200);
            }
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::whereHasMorph('userable', [Tenant::class,Owner::class])->where('status', 1)->where('id', auth('api')->user()->id)->first();

        if (!$user) {
            return response()->error('notAuthorized');
        }
        $validator = Validator::make($request->all(), [
            'name' => ['required','string',Rule::unique('voyage_calculations', 'name')->where('user_id', $user->id)->ignore($id)],
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->error('missingParameters', $validator->errors());
        }
        DB::beginTransaction();
        try {
            $voyage = VoyageCalculation::findOrFail($id);

            $voyage->update([
                'name' => $request->name,
                'details' => $request->details,
                'user_id' => $user->id
            ]);

            $voyage->save();

            DB::commit();

            return response()->success();
        } catch (\Exception $e) {
            DB::rollBack();
            if($e->getCode() == 23000){
                return response()->json(array("code" => $e->getCode(), "message" => "name already taken", "data" => null), 200);
            }
            return response()->json(array("code" => $e->getCode(), "message" => $e->getMessage(), "data" => null), 200);
        }
    }


    public function delete(int $id)
    {

        $voyage = VoyageCalculation::where('id', $id)->first();
        $voyage->deleted_at = Carbon::now();
        $voyage->save();

        return response()->success();
    }
}

