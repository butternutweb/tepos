@extends('layouts.login')

@section('title', 'Forgot Password')

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
                        Forgotten Password ?
                    </h3>
                    <div class="m-login__desc">
                        Enter your email to reset your password:
                    </div>
                </div>
                <form id="m_login_forgot_password" class="m-login__form m-form" action="{{ route('auth.forgot') }}" method="POST">
                    <div class="form-group m-form__group {{ $errors->first('email') ? 'has-danger' : '' }}">
                        <input class="form-control m-input" type="text" placeholder="Enter email" name="email" autocomplete="off" aria-describedby="email-error">
                        <div id="email-error" class="form-control-feedback">{{ $errors->first('email') }}</div>
                    </div>
                    <div class="m-login__form-action">
                        <input type="submit" class="btn btn-focus btn-focus2 m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primaryr" value="Request">
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>
@endsection