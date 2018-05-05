@extends('layouts.admin')

@section('title', 'Profile')

@section('js')
<script src="{{ asset('js/toastr.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-xl-3 col-lg-4">
        <div class="m-portlet m-portlet--full-height  ">
            <div class="m-portlet__body">
                <div class="m-card-profile">
                    <div class="m-card-profile__title m--hide">
                        Your Profile
                    </div>
                    <div class="m-card-profile__pic">
                        <div class="m-card-profile__pic-wrapper">
                            <img src="img/users/staff_icon.png" alt="" />
                        </div>
                    </div>
                    <div class="m-card-profile__details">
                        <div class="row">
                            <div class="col-lg-12">
                                <span class="m-card-profile__name">
                                    {{ Auth::user()->name }}
                                </span>
                            </div>
                            <div class="col-lg-12">
                                <span class="m-card-profile__email m-link">
                                    {{ Auth::user()->username }}
                                </span>
                            </div>
                            <div class="col-lg-12">
                                <span class="m-card-profile__email m-link">
                                    {{ Auth::user()->email }}
                                </span>
                            </div>
                            <div class="col-lg-12">
                                <span class="m-card-profile__email m-link">
                                    {{ Auth::user()->phone }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-9 col-lg-8">
        <div class="m-portlet m-portlet--full-height m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left m-tabs-line--primary" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link {{ old('action') ? (old('action') == 'change-profile' ? 'active' : '') : 'active' }}" data-toggle="tab" href="#m_user_profile_tab_1" role="tab">
                                <i class="flaticon-share m--hide"></i>
                                Update Profile
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link {{ old('action') == 'change-password' ? 'active' : '' }}" data-toggle="tab" href="#m_user_profile_tab_2" role="tab">
                                Update Password
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link {{ old('action') == 'change-email' ? 'active' : '' }}" data-toggle="tab" href="#m_user_profile_tab_3" role="tab">
                                Update Email
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane {{ old('action') ? (old('action') == 'change-profile' ? 'active' : '') : 'active' }}" id="m_user_profile_tab_1">
                    <form class="m-form m-form--fit m-form--label-align-right" action="{{ route('profile.index') }}" method="post" id="m_update_profile">
                        <div class="m-portlet__body">
                            <div class="form-group m-form__group row {{ $errors->first('name') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    Name
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="text" value="{{ old('name') ? old('name') : Auth::user()->name }}" name="name" placeholder="Enter name" aria-describedby="name-error">
                                    <div id="name-error" class="form-control-feedback">{{ $errors->first('name') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                            <div class="form-group m-form__group row {{ $errors->first('phone') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    Phone
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="text" value="{{ old('phone') ? old('phone') : Auth::user()->phone }}" name="phone" placeholder="Entern phone" aria-describedby="phone-error">
                                    <div id="phone-error" class="form-control-feedback">{{ $errors->first('phone') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </div>
                        <div class="m-portlet__foot m-portlet__foot--fit">
                            <div class="m-form__actions">
                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-6">
                                        <input type="submit" class="btn btn-accent m-btn m-btn--air m-btn--custom" value="Save Changes">
                                        <input name="action" value="change-profile" type="hidden">
                                        {{ csrf_field() }}
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane {{ old('action') == 'change-password' ? 'active' : '' }}" id="m_user_profile_tab_2">
                    <form class="m-form m-form--fit m-form--label-align-right" action="{{ route('profile.index') }}" method="post" id="m_update_password">
                        <div class="m-portlet__body">
                            <div class="form-group m-form__group row {{ $errors->first('old_password') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    Old Password
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="password" name="old_password" placeholder="Enter old password" value="{{ old('old_password') }}" aria-describedby="old_password-error">
                                    <div id="old_password-error" class="form-control-feedback">{{ $errors->first('new_password') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                            <div class="form-group m-form__group row {{ $errors->first('new_password') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    New Password
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="password" name="new_password" placeholder="Enter new password" value="{{ old('new_password') }}" aria-describedby="new_password-error">
                                    <div id="new_password-error" class="form-control-feedback">{{ $errors->first('new_password') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                            <div class="form-group m-form__group row {{ $errors->first('new_password_confirmation') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    New Password Confirmation
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="password" name="new_password_confirmation" placeholder="Enter new password confirmation" value="{{ old('new_password_confirmation') }}" aria-describedby="new_password_confirmation-error">
                                    <div id="new_password_confirmation" class="form-control-feedback">{{ $errors->first('new_password_confirmation') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </div>
                        <div class="m-portlet__foot m-portlet__foot--fit">
                            <div class="m-form__actions">
                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-6">
                                        <input type="submit" class="btn btn-accent m-btn m-btn--air m-btn--custom" value="Save Changes">
                                        <input name="action" value="change-password" type="hidden">
                                        {{ csrf_field() }}
                                        &nbsp;&nbsp;
                                        <input type="reset" class="btn btn-secondary m-btn m-btn--air m-btn--custom" value="Reset">
                                    </div>
                                    <div class="col-md-4"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane {{ old('action') == 'change-email' ? 'active' : '' }}" id="m_user_profile_tab_3">
                    <form class="m-form m-form--fit m-form--label-align-right" action="{{ route('profile.index') }}" method="post" id="m_update_email">
                        <div class="m-portlet__body">
                            <div class="form-group m-form__group row {{ $errors->first('old_email') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    Old Email
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="email" name="old_email" placeholder="Enter old email" value="{{ old('old_email') }}" aria-describedby="old_email-error">
                                    <div id="old_email-error" class="form-control-feedback">{{ $errors->first('old_email') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                            <div class="form-group m-form__group row {{ $errors->first('new_email') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    New Email
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="email" name="new_email" placeholder="Enter new email" value="{{ old('new_email') }}" aria-describedby="new_email-error">
                                    <div id="new_email-error" class="form-control-feedback">{{ $errors->first('new_email') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                            <div class="form-group m-form__group row {{ $errors->first('new_email_confirmation') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    New Email Confirmation
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="email" name="new_email_confirmation" placeholder="Enter new email confirmation" value="{{ old('new_email_confirmation') }}" aria-describedby="new_email_confirmation-error">
                                    <div id="new_email_confirmation-error" class="form-control-feedback">{{ $errors->first('new_email_confirmation') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                            <div class="form-group m-form__group row {{ $errors->first('password') ? 'has-danger' : '' }}">
                                <label for="example-text-input" class="col-md-2 col-form-label">
                                    Password
                                </label>
                                <div class="col-md-6">
                                    <input class="form-control m-input" type="password" name="password" placeholder="Enter password" value="{{ old('password') }}" aria-describedby="password-error">
                                    <div id="password-error" class="form-control-feedback">{{ $errors->first('password') }}</div>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                        </div>
                        <div class="m-portlet__foot m-portlet__foot--fit">
                            <div class="m-form__actions">
                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-6">
                                        <input type="submit" class="btn btn-accent m-btn m-btn--air m-btn--custom" value="Save Changes">
                                        <input name="action" value="change-email" type="hidden">
                                        {{ csrf_field() }}
                                        &nbsp;&nbsp;
                                        <input type="reset" class="btn btn-secondary m-btn m-btn--air m-btn--custom" value="Reset">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection