@extends('layouts.login')

@section('title', 'Login')

@section('js')
<script src="{{ asset('js/toastr.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--singin m-login--2 m-login-2--skin-2">
        <div class="m-grid__item m-grid__item--fluid m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__head">
                    <h3 class="m-login__title">
                        Sign In
                    </h3>
                </div>
                <form id="m_login_signin" class="m-login__form m-form" action="{{ route('auth.login') }}" method="POST">
                    <div class="form-group m-form__group {{ $errors->first('username') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="text" placeholder="Enter username" name="username" autocomplete="off" aria-describedby="username-error" value="{{ old('username') }}">
                        <div id="username-error" class="form-control-feedback">{{ $errors->first('username') }}</div>
                    </div>
                    <div class="form-group m-form__group {{ $errors->first('password') ? 'has-danger' : '' }}">
                        <input class="form-control m-input m-login__form-input--last" type="password" placeholder="Enter password" name="password" aria-describedby="password-error" value="{{ old('password') }}">
                        <div id="password-error" class="form-control-feedback">{{ $errors->first('username') }}</div>
                    </div>
                    <div class="row m-login__form-sub">
                        <div class="col m--align-left m-login__form-left">
                            <label class="m-checkbox m-checkbox--focus">
                                <input type="checkbox" name="remember_me"> Remember Me
                                <span></span>
                            </label>
                        </div>
                        <div class="col m--align-right m-login__form-right">
                            <a href="{{ route('auth.forgot') }}" class="m-link">
                                Forget Password ?
                            </a>
                        </div>
                    </div>
                    <div class="m-login__form-action">
                        <input type="submit" class="btn btn-focus btn-focus2 m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary" value="Sign In">
                        {{ csrf_field() }}
                    </div>
                </form>
                <div class="m-login__account">
                    <span class="m-login__account-msg">
                        Don't have an account yet ?
                    </span>
                    &nbsp;&nbsp;
                    <a href="{{ route('auth.signup') }}" class="m-link m-link--light m-login__account-link">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection