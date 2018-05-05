@extends('layouts.admin')

@section('title', 'Create Transaction')

@section('js')
<script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation/transaction.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_transaction" action="{{ route('transaction.store') }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
            @if (Auth::user()->child()->first() instanceof \App\Admin || Auth::user()->child()->first() instanceof \App\Owner)
                <div class="form-group m-form__group row {{ $errors->first('date') ? 'has-danger' : '' }}">
                    <label class="col-lg-2 col-form-label">
                        Date:
                    </label>
                    <div class="col-lg-6">
                        <div class="input-group date" id="datetime">
                            <input type="text" name="date" class="form-control m-input" readonly="true" value="{{ old('date') }}" placeholder="Enter date" aria-describedby="date-error">
                            <span class="input-group-addon">
                                <i class="la la-calendar glyphicon-th"></i>
                            </span>
                        </div>
                        <div id="date-error" class="form-control-feedback">{{ $errors->first('date') }}</div>
                    </div>
                </div>
            @endif
            <div class="form-group m-form__group row {{ $errors->first('note') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Note:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="note" class="form-control m-input" placeholder="Enter note" value="{{ old('note') }}" aria-describedby="note-error">
                    <div id="note-error" class="form-control-feedback">{{ $errors->first('note') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            @if (Auth::user()->child()->first() instanceof \App\Admin || Auth::user()->child()->first() instanceof \App\Owner)
                <div class="form-group m-form__group row {{ $errors->first('staff_id') ? 'has-danger' : '' }}">
                    <label class="col-lg-2 col-form-label">
                        Staff:
                    </label>
                    <div class="col-lg-6">
                        <div class="m-form__control">
                            <select class="form-control" name="staff_id" aria-describedby="staff_id-error">
                                @foreach ($staffs as $staff)
                                    <option value="{{ $staff->id }}" {{ $staff->id == old('staff_id') ? 'selected' : '' }}>
                                        {{ $staff->accounts()->first()->username }} ({{ $staff->store()->first()->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="staff_id-error" class="form-control-feedback">{{ $errors->first('staff_id') }}</div>
                        <a class="m-link" href="{{ route('store.index') }}">+ Create staff</a>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            @endif
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