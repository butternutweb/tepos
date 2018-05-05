@extends('layouts.admin')

@section('title', 'Create Subscription Plan')

@section('js')
<script src="{{ asset('js/form-validation/subscription_plan.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_subscription_plan" action="{{ route('subs-plan.store') }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
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
            <div class="form-group m-form__group row {{ $errors->first('store_number') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Store Number:
                </label>
                <div class="col-lg-6">
                    <div class="input-group">
                        <input type="number" name="store_number" class="form-control m-input" placeholder="Enter store number" value="{{ old('store_number') }}" aria-describedby="store_number-error">
                        <span class="input-group-addon">
                            store(s)
                        </span>
                    </div>
                    <div id="store_number-error" class="form-control-feedback">{{ $errors->first('store_number') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('duration_day') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Duration:
                </label>
                <div class="col-lg-6">
                    <div class="input-group">
                        <input type="number" name="duration_day" class="form-control m-input" placeholder="Enter duration" value="{{ old('duration_day') }}" aria-describedby="duration_day-error">
                        <span class="input-group-addon">
                            day(s)
                        </span>
                    </div>
                    <div id="duration_day-error" class="form-control-feedback">{{ $errors->first('duration_day') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('price') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Price:
                </label>
                <div class="col-lg-6">
                    <div class="input-group m-input-group">
                        <span class="input-group-addon">
                            Rp
                        </span>
                        <input type="number" name="price" class="form-control m-input" placeholder="Enter price" value="{{ old('price') }}" aria-describedby="price-error">
                    </div>
                    <div id="price-error" class="form-control-feedback">{{ $errors->first('price') }}</div>
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