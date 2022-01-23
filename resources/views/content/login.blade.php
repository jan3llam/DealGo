@extends('layouts/fullLayoutMaster')

@section('title', 'Login')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-basic px-2">
        <div class="auth-inner my-2">
            <!-- Login basic -->
            <div class="card mb-0">
                <div class="card-body">
                    <a href="#" class="brand-logo">
                        <img src="{{asset('images/logo/logo.png')}}" alt="logo">
                        <h2 class="brand-text text-primary ms-1"></h2>
                    </a>

                    <h4 class="card-title mb-1">Welcome to {{env('APP_NAME')}}! 👋</h4>
                    <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>

                    <form class="auth-login-form mt-2" action="{{route('admin.login')}}" method="POST">
                        @csrf
                        <div class="mb-1">
                            <label for="login-email" class="form-label">Email</label>
                            <input type="email" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   id="login-email" name="email" tabindex="1" autofocus
                                   placeholder="john@example.com" aria-describedby="login-email"/>
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">{{$errors->first('email') }}</div>

                            @endif
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="login-password">Password</label>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" id="login-password" name="password" tabindex="2"
                                       class="form-control form-control-merge {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                       aria-describedby="login-password"/>
                                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">{{$errors->first('password') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" tabindex="3"/>
                                <label class="form-check-label" for="remember-me"> Remember me </label>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100" tabindex="4">Login</button>
                    </form>
                </div>
            </div>
            <!-- /Login basic -->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{asset(mix('js/scripts/pages/auth-login.js'))}}"></script>
@endsection
