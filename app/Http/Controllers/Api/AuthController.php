<?php

namespace App\Http\Controllers\Api;

use App\Mail\PasswordEmail;
use App\Models\Code;
use App\Models\FCMToken;
use App\Models\Owner;
use App\Models\Tenant;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['signOut']]);
    }

    public function signUp(Request $request)
    {
        $fileName = null;
        $validator = Validator::make($request->all(),
            [
                'business_name' => 'required_if:type,1',
                'commercial' => 'required_if:type,1',
                'company' => 'required_if:type,1',
                'license' => 'required_if:type,1',
                'user_type' => 'required|numeric',
                'type' => 'required|numeric',
                'contact_name' => 'required|string',
                'zip' => 'required|string',
                'province' => 'required|string',
                'address_1' => 'required|string',
                'address_2' => 'nullable|string',
                'city' => 'required|numeric',
                'password' => 'required',
                'email' => 'required|unique:users,email',
                'phone' => 'required|unique:users,phone',
                'legal' => 'required',
            ]);

        if ($validator->fails()) {

            $failedRules = $validator->failed();

            if (isset($failedRules['phone']['Unique']) || isset($failedRules['email']['Unique'])) {
                $field = isset($failedRules['phone']['Unique']) ? 'Phone' : 'Email';
                return response()->error('alreadyExist' . $field);
            }
            return response()->error('missingParameters', $validator->failed());
        }

        $params = $request->all();

        if ($validator->fails()) {
            if (isset($validator->failed()['email']['Unique'])) {
                return response()->error('alreadyExist');
            }
            return response()->error('missingParameters', $validator->failed());
        }

        $user_type = intval($request->user_type);
        $class = null;

        $item = new User;

        if ($user_type === 1) {
            $class = new Owner;
            $class->save();
        } else {
            $class = new Tenant;
            $class->save();
        }

        $item->legal_file = $params['legal'];

        if ($request->type == 1) {

            $item->company_file = $params['company'];
            $item->license_file = $params['license'];
            $item->full_name = $params['business_name'];
            $item->commercial_number = $params['commercial'];

        }
        $item->email = $params['email'];
        $item->phone = $params['phone'];
        $item->contact_name = $params['contact_name'];
        $item->password = bcrypt($params['password']);
        $item->city_id = $params['city'];
        $item->type = $params['type'];
        $item->zip_code = $params['zip'];
        $item->province = $params['province'];
        $item->address_1 = $params['address_1'];
        $item->address_2 = $params['address_2'];
        $item->files = json_encode([]);
        $item->secret = Str::random(40);

        $item->userable_id = $class->id;
        $item->userable_type = $user_type === 1 ? Owner::class : Tenant::class;

        $item->status = 1;
        $item->verified = 0;

        $item->save();

        $data = [
            'username' => $item->email,
            'secret' => $item->secret,
            'email' => $item->email,
            'first_name' => $item->contact_name,
            'last_name' => '',
            'custom_json' => 'none',
        ];

        Http::withHeaders([
            'PRIVATE-KEY' => env('CHATENGINE_PROJECT_KEY'),
        ])->post('https://api.chatengine.io/users/', $data);

        return response()->success();
    }

    public function sendCode(Request $request, $user_id = null)
    {
        if (!$user_id) {
            $validator = Validator::make($request->all(),
                [
                    'identifier' => 'required',
                ]);

            if ($validator->fails()) {

                return response()->error('missingParameters', $validator->failed());
            }

            $identifier = $request->input('identifier');
        }
        $user = $user_id ? User::find($user_id) : User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return response()->error('userNotFound');
        }

        $code = $user->codes->last();

        if ($code && $code->valid && $code->created_at->diffInMinutes(now()) < intval(env('SMS_VALID_MINUTES'))) {
            $code->touch();
            return response()->success(['code' => $code->code]);
        } elseif ($code) {
            $code->valid = false;
            $code->save();
        }

        $verificationCode = mt_rand(1000, 9999);

        $code = new Code;

        $code->code = $verificationCode;
        $code->user_id = $user->id;

        $code->save();
        $user->save();

        Mail::to($user->email)->send(new PasswordEmail($verificationCode));

        return response()->success();

    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'identifier' => 'required',
                'code' => 'required|numeric',

            ]);

        if ($validator->fails()) {

            return response()->error('missingParameters');
        }

        $identifier = $request->input('identifier');
        $userCode = intval($request->input('code'));

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return response()->error('userNotFound');
        }

        $code = $user->codes->last();
        if ($code && $userCode === $code->code && $code->valid && $code->created_at->diffInMinutes(now()) < intval(env('SMS_VALID_MINUTES'))) {
            $user->verified = 1;
            $code->valid = false;
            $code->save();
            $user->save();

        } else {
            return response()->error('codeNotValid');
        }

        if (!$request->has('password') && !$request->has('type')) {
            return response()->success();
        }
        return $this->signIn($request, $user->email);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signIn(Request $request, $user = null)
    {
        $validator = Validator::make($request->all(),
            [
                'identifier' => Rule::requiredIf(function () use ($request, $user) {
                    return !$user;
                }),
                'password' => 'required',
            ]);

        if ($validator->fails()) {

            return response()->error('missingParameters', $validator->failed());
        }

        $credentialsEmail = [
            'email' => $user ? $user : $request->input('identifier'),
            'password' => $request->input('password'),
        ];

        $credentialsGSM = [
            'phone' => $user ? $user : $request->input('identifier'),
            'password' => $request->input('password'),
        ];

        $token = null;

        if ((!$token = auth('api')->attempt($credentialsEmail)) && (!$token = auth('api')->attempt($credentialsGSM))) {
            return response()->error('wrongUsernamePwd');
        }

        if (!auth('api')->user()->status) {
            return response()->error('accountNotActive');
        }

        if (!auth('api')->user()->verified) {
            return response()->error('accountNotVerified');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected
    function respondWithToken($token, $social_account_id = null, $refresh = false)
    {
//        if (!$social_account_id && !$refresh) {
//            Helpers::sendNotification('login', [], auth('api')->user()->id);
//        } elseif ($social_account_id && !$refresh) {
//            Helpers::sendNotification('login', [], $social_account_id);
//        }

//        try {
//            Helper::sendNotification('login', [], auth('api')->user()->id);
//        } catch (\Exception $e) {
//            return response()->success([
//                'access_token' => $token,
//                'token_type' => 'bearer',
//                'user_type' => auth('api')->user()->userable_type,
//                'expires_in' => auth('api')->factory()->getTTL() * 60
//            ]);
//        }

        return response()->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user_type' => auth('api')->user()->userable_type,
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'identifier' => 'required',
                'password' => 'required|min:6',
                'code' => 'required',

            ]);

        if ($validator->fails()) {

            return response()->error('missingParameters', $validator->failed());
        }

        $identifier = $request->input('identifier');
        $userCode = intval($request->input('code'));

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return response()->error('userNotFound');
        }

        $code = $user->codes->last();

        if ($userCode === $code->code && $code && $code->valid && $code->created_at->diffInMinutes(now()) < intval(env('SMS_VALID_MINUTES'))) {
            //$user->has_code = false;
            $code->valid = false;
            $code->save();
            $user->save();
        } else {
//            dd($code->created_at->diffInMinutes(now()) < intval(env('SMS_VALID_MINUTES')));
            return response()->error('codeNotValid');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return $this->signIn($request, $user->email);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signOut()
    {

        FCMToken::where('user_id', auth('api')->user()->id)->delete();
        auth('api')->logout();

        return response()->success();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh(), null, true);
    }
}
