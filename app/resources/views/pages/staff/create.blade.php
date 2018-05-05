@extends('layouts.admin')

@section('title', 'Create Staff')

@section('js')
<script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation-method.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation/staff.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    @if (Auth::user()->child()->first() instanceof \App\Admin)
        <form id="m_admin_create_staff" action="{{ route('staff.store', $store_id) }}" method="post" class="m-form m-form--label-align-right">
    @elseif (Auth::user()->child()->first() instanceof \App\Owner)
        <form id="m_owner_create_staff" action="{{ route('staff.store', $store_id) }}" method="post" class="m-form m-form--label-align-right">
    @endif
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('username') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Username:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="username" class="form-control m-input" placeholder="Enter username" value="{{ old('username') }}" aria-describedby="username-error">
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
                    <input type="email" name="email" class="form-control m-input" placeholder="Enter email" value="{{ old('email') }}" aria-describedby="email-error">
                    <div id="email-error" class="form-control-feedback">{{ $errors->first('email') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('name') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Name:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="name" class="form-control m-input" placeholder="Enter name" value="{{ old('name') }}" aria-describedby="name-error">
                    <div id="name-error" class="form-control-feedback">{{ $errors->first('name') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('phone') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Phone:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="phone" class="form-control m-input" placeholder="Enter phone" value="{{ old('phone') }}" aria-describedby="phone-error">
                    <div id="phone-error" class="form-control-feedback">{{ $errors->first('phone') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            @if (Auth::user()->child()->first() instanceof \App\Admin)
                <div class="form-group m-form__group row {{ $errors->first('status_id') ? 'has-danger' : '' }}">
                    <label class="col-lg-2 col-form-label">
                        Status:
                    </label>
                    <div class="col-lg-6">
                        <div class="m-form__control">
                            <select class="form-control" name="status_id" aria-describedby="status_id-error">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" {{ $status->id == old('status_id') ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="status_id-error" class="form-control-feedback">{{ $errors->first('status_id') }}</div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            @endif
            <div class="form-group m-form__group row {{ $errors->first('salary') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Salary:
                </label>
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            Rp
                        </span>
                        <input type="number" name="salary" class="form-control m-input" placeholder="Enter salary" value="{{ old('salary') }}" aria-describedby="salary-error">
                    </div>
                    <div id="salary-error" class="form-control-feedback">{{ $errors->first('salary') }}</div>
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