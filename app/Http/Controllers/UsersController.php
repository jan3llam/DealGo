<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class UsersController extends Controller
{

    public function check_field(Request $request)
    {
        $email = $request->input('email', null);
        $phone = $request->input('phone', null);
        $zip = $request->input('zip', null);
        if ($email) {
            $user = User::withTrashed()->where('email', $email)->first();
            if ($user) {
                return response()->error('alreadyExist');
            }
            return response()->success();
        } elseif ($phone) {
            $user = User::withTrashed()->where('phone', $phone)->first();
            if ($user) {
                return response()->error('alreadyExist');
            }
            return response()->success();
        } elseif ($zip) {
            $user = User::withTrashed()->where('zip_code', $zip)->first();
            if ($user) {
                return response()->error('alreadyExist');
            }
            return response()->success();
        }
        return response()->error('alreadyExist');
    }
}
