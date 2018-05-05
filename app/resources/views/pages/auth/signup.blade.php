@extends('layouts.login')

@section('title', 'Sign Up')

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
                        Sign Up
                    </h3>
                    <div class="m-login__desc">
                        Enter your details to create your account:
                    </div>
                </div>
                <form id="m_login_signup" class="m-login__form m-form" action="{{ route('auth.signup') }}" method="POST">
                    <div class="form-group m-form__group {{ $errors->first('username') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="text" placeholder="Enter username" name="username" value="{{ old('username') }}" aria-describedby="username-error">
                        <div id="username-error" class="form-control-feedback">{{ $errors->first('username') }}</div>
                    </div>
                    <div class="form-group m-form__group {{ $errors->first('email') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="text" placeholder="Enter email" name="email" autocomplete="off" value="{{ old('email') }}" aria-describedby="email-error">
                        <div id="email-error" class="form-control-feedback">{{ $errors->first('email') }}</div>
                    </div>
                    <div class="form-group m-form__group {{ $errors->first('email_confirmation') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="text" placeholder="Enter email confirmation" name="email_confirmation" autocomplete="off" value="{{ old('email_confirmation') }}" aria-describedby="email_confirmation-error">
                        <div aria-describedby="email_confirmation-error" class="form-control-feedback">{{ $errors->first('email_confirmation') }}</div>
                    </div>
                    <div class="form-group m-form__group {{ $errors->first('password') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="password" placeholder="Enter password" name="password" value="{{ old('password') }}" aria-describedby="password-error">
                        <div id="password-error" class="form-control-feedback">{{ $errors->first('password') }}</div>
                    </div>
                    <div class="form-group m-form__group {{ $errors->first('password_confirmation') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="password" placeholder="Enter password confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" aria-describedby="password_confirmation-error">
                        <div id="password_confirmation-error" class="form-control-feedback">{{ $errors->first('password_confirmation') }}</div>
                    </div>
                    <div class="form-group m-form__group {{ $errors->first('name') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="text" placeholder="Enter name" name="name" value="{{ old('name') }}" aria-describedby="name-error">
                        <div id="name-error" class="form-control-feedback">{{ $errors->first('name') }}</div>
                    </div>
                    <div class="form-group m-form__group {{ $errors->first('phone') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="tel" placeholder="Enter phone" name="phone" value="{{ old('phone') }}" aria-describedby="phone-error">
                        <div id="phone-error" class="form-control-feedback">{{ $errors->first('phone') }}</div>
                    </div>
                    <div class="row form-group m-form__group m-login__form-sub {{ $errors->first('agree') ? 'has-danger' : '' }}">
                        <div class="col m--align-left">
                            <label class="m-checkbox m-checkbox--focus">
                                <input type="checkbox" name="agree" value="1" aria-describedby="agree-error"> I Agree the
                                <a href="#" class="m-link m-link--focus">
                                    terms and conditions
                                </a>.
                                <span></span>
                            </label>
                            <div id="agree-error" class="form-control-feedback">{{ $errors->first('agree') }}</div>
                        </div>
                    </div>
                    <div class="m-login__form-action">
                        <input type="Submit" class="btn btn-focus btn-focus2 m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn" value="Sign Up">
                        {{ csrf_field() }}
                    </div>
                    <div class="m-login__account">
                        <span class="m-login__account-msg">
                            Already have an account?
                        </span>
                        &nbsp;&nbsp;
                        <a href="{{ route('auth.login') }}" class="m-link m-link--light m-login__account-link">
                            Log In
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection