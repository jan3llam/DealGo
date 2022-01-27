<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        return view('content.login');
    }

    public function submit(Request $request)
    {
        $this->validator($request);

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'status' => 1
        ];

        if ($this->guard()->attempt($credentials, $request->filled('remember'))) {
            return redirect()
                ->intended(route('admin.home'));
        }

        return $this->loginFailed();

    }

    private function validator(Request $request)
    {
        //validation rules.
        $rules = [
            'email' => 'required|email|exists:admins,email|min:5|max:191',
            'password' => 'required|string|min:4|max:255',
        ];

        //custom validation error messages.
        $messages = [
            'email.exists' => 'These credentials do not match our records.',
        ];

        //validate the request.
        $request->validate($rules, $messages);
    }

    protected function guard()
    {
        return Auth::guard('admins');
    }

    private function loginFailed()
    {
        return redirect()->back()
            ->with('error', 'Login failed, please try again!')
            ->withInput();
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect()->route('admin.login');
    }
}
