@extends('layouts.admin')

@section('title', 'Create Subscription Transaction')

@section('js')
<script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation/subscription_transaction.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_subscription_transaction" action="{{ route('subs-trans.store') }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('date') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Date:
                </label>
                <div class="col-lg-6">
                    <div class="input-group date" id="date">
                        <input type="text" name="date" class="form-control m-input" readonly="true" value="{{ old('date') }}" placeholder="Enter date" aria-describedby="date-error">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                    <div id="date-error" class="form-control-feedback">{{ $errors->first('date') }}</div>
                </div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('payment_method') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Payment Method:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="payment_method" class="form-control m-input" placeholder="Enter payment method" value="Offline/Cash" aria-describedby="payment_method-error">
                    <div id="payment_method-error" class="form-control-feedback">{{ $errors->first('payment_method') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            @if (Auth::user()->child()->first() instanceof \App\Admin)
                <div class="form-group m-form__group row {{ $errors->first('owner_id') ? 'has-danger' : '' }}">
                    <label class="col-lg-2 col-form-label">
                        Owner:
                    </label>
                    <div class="col-lg-6">
                        <div class="m-form__control">
                            <select class="form-control" name="owner_id" aria-describedby="owner_id-error">
                                @foreach ($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ $owner->id == old('owner_id') ? 'selected' : '' }}>
                                        {{ $owner->accounts()->first()->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="owner_id-error" class="form-control-feedback">{{ $errors->first('owner_id') }}</div>
                        <a class="m-link" href="/owner/create">+ Create owner</a>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            @endif
            <div class="form-group m-form__group row {{ $errors->first('subscription_plan_id') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Subscription Plan:
                </label>
                <div class="col-lg-6">
                    <div class="m-form__control">
                        <select class="form-control" name="subscription_plan_id" aria-describedby="subscription_plan_id-error">
                            @foreach ($subscription_plans as $subscription_plan)
                                <option value="{{ $subscription_plan->id }}" {{ $subscription_plan->id == old('subscription_plan_id') ? 'selected' : '' }}>
                                    {{ $subscription_plan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="subscription_plan_id-error" class="form-control-feedback">{{ $errors->first('subscription_plan_id') }}</div>
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