@extends('layouts.admin')

@section('title', 'Edit Owner')

@section('js')
<script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation-method.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation/owner.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_admin_edit_owner" action="{{ route('owner.update', $owner->id) }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('username') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Username:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="username" class="form-control m-input" placeholder="Enter username" value="{{ old('username') ? old('username') : $owner->accounts()->first()->username }}" aria-describedby="username-error">
                    <div id="username-error" class="form-control-feedback">{{ $errors->first('username') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('password') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Password:
                </label>
                <div class="col-lg-6">
                    <input type="password" name="password" class="form-control m-input" placeholder="Enter password" value="{{ old('password') }}" aria-describedby="password-error">
                    <div id="password-error" class="form-control-feedback">{{ $errors->first('password') }}</div>
                    <span class="m-form__help">
                        Leave it blank if you don't want change password.
                    </span>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('password_confirmation') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Password Confirmation:
                </label>
                <div class="col-lg-6">
                    <input type="password" name="password_confirmation" class="form-control m-input" placeholder="Enter password confirmation" value="{{ old('password_confirmation') }}" aria-describedby="password_confirmation-error">
                    <div id="password_confirmation-error" class="form-control-feedback">{{ $errors->first('password_confirmation') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('email') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Email:
                </label>
                <div class="col-lg-6">
                    <input type="email" name="email" class="form-control m-input" placeholder="Enter email" value="{{ old('email') ? old('email') : $owner->accounts()->first()->email }}" aria-describedby="email-error">
                    <div id="email-error" class="form-control-feedback">{{ $errors->first('email') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('name') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Name:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="name" class="form-control m-input" placeholder="Enter name" value="{{ old('name') ? old('name') : $owner->accounts()->first()->name }}" aria-describedby="name-error">
                    <div id="name-error" class="form-control-feedback">{{ $errors->first('name') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('phone') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Phone:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="phone" class="form-control m-input" placeholder="Enter phone" value="{{ old('phone') ? old('phone') : $owner->accounts()->first()->phone }}" aria-describedby="phone-error">
                    <div id="phone-error" class="form-control-feedback">{{ $errors->first('phone') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('status_id') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Status:
                </label>
                <div class="col-lg-6">
                    <div class="m-form__control">
                        <select class="form-control" name="status_id" aria-describedby="status_id-error">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ old('status_id') ? ($status->id == old('status_id') ? 'selected' : '') : ($status->id == $owner->accounts()->first()->status()->first()->id ? $owner->accounts()->first()->status()->first()->id : '') }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="status_id-error" class="form-control-feedback">{{ $errors->first('status_id') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions m-form__actions">
                <div class="row">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-6">
                        <input type="submit" value="Submit" class="btn btn-primary">
                        {{ method_field('put') }}
                        {{ csrf_field() }}
                        <input type="reset" value="Reset" class="btn btn-secondary">
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection