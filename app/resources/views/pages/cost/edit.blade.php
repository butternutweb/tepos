@extends('layouts.admin')

@section('title', 'Edit Cost')

@section('js')
<script src="{{ asset('js/form-validation/cost.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_cost" action="{{ route('cost.update', [$store_id, $cost->id]) }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('name') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Name:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="name" class="form-control m-input" placeholder="Enter name" value="{{ old('name') ? old('name') : $cost->name }}" aria-describedby="name-error">
                    <div id="name-error" class="form-control-feedback">{{ $errors->first('name') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('amount') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Amount:
                </label>
                <div class="col-lg-6">
                    <div class="input-group m-input-group">
                        <span class="input-group-addon">
                            Rp
                        </span>
                        <input type="number" name="amount" class="form-control m-input" placeholder="Enter amount" value="{{ old('amount') ? ('amount') : $cost->amount }}" aria-describedby="amount-error">
                    </div>
                    <div id="amount-error" class="form-control-feedback">{{ $errors->first('amount') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('date') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Date:
                </label>
                <div class="col-lg-6">
                    <div class="input-group date" id="date">
                        <input type="text" name="date" class="form-control m-input" readonly="true" value="{{ old('date') ? old('date') : explode(" ", $cost->date)[0] }}" placeholder="Enter date" aria-describedby="date-error">
                        <span class="input-group-addon">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                    <div id="date-error" class="form-control-feedback">{{ $errors->first('date') }}</div>
                </div>
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