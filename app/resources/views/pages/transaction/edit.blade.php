@extends('layouts.admin')

@section('title', 'Edit Transaction')

@section('js')
<script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/datatable/transaction-edit.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation/transaction.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_transaction" action="{{ route('transaction.update', $transaction->id) }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('date') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Date:
                </label>
                <div class="col-lg-6">
                    <div class="input-group date" id="datetime">
                        <input type="text" name="date" class="form-control m-input" readonly="true" value="{{ old('date') ? old('date') : explode(':', $transaction->date)[0] . ':' . explode(':', $transaction->date)[1] }}" placeholder="Enter date" aria-describedby="date-error">
                        <span class="input-group-addon">
                            <i class="la la-calendar glyphicon-th"></i>
                        </span>
                    </div>
                    <div id="date-error" class="form-control-feedback">{{ $errors->first('date') }}</div>
                </div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('staff_id') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Staff:
                </label>
                <div class="col-lg-6">
                    <div class="m-form__control">
                        <select class="form-control" name="staff_id" aria-describedby="staff_id-error">
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}" {{ old('staff_id') ? ($staff->id == old('staff_id') ? 'selected' : '') : ($staff->id == $transaction->staff_id ? 'selected' : '') }}>
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
            <div class="form-group m-form__group row {{ $errors->first('status_id') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Status:
                </label>
                <div class="col-lg-6">
                    <div class="m-form__control">
                        <select class="form-control" name="status_id" aria-describedby="status_id-error">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ old('status_id') ? ($status->id == old('status_id') ? 'selected' : '') : ($status->id == $transaction->status_id ? 'selected' : '') }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="status_id-error" class="form-control-feedback">{{ $errors->first('status_id') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row">
                <label class="col-lg-2 col-form-label">
                    Total:
                </label>
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            Rp
                        </span>
                        <input type="text" class="form-control m-input m-input--solid" value="{{ $total }}" readonly>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row">
                <div class="col-md-12">
                    <h5>
                        Product:
                    </h5>
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-6 order-2 order-xl-1">
                                <div class="m-input-icon m-input-icon--left">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="m_search_transaction_product">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                                <a href="{{ route('transaction_.product.create', $transaction->id) }}" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>
                                            New
                                        </span>
                                    </span>
                                </a>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 collapse" id="m_datatable_group_action_form_transaction_product">
                        <div class="row align-items-center">
                            <div class="col-xl-12">
                                <div class="m-form__group m-form__group--inline">
                                    <div class="m-form__label m-form__label-no-wrap">
                                        <label class="m--font-bold m--font-danger-">
                                            Selected
                                            <span id="m_datatable_selected_number_transaction_product"></span>
                                            records:
                                        </label>
                                    </div>
                                    <div class="m-form__control">
                                        <div class="btn-toolbar">
                                            <button class="btn btn-sm btn-danger" type="button"  data-toggle="modal" data-target="#m_bulk_delete_transaction_product_modal">
                                                Delete Selected
                                            </button>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m_datatable" id="m_datatable_transaction_product"></div>
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
<div class="modal fade" id="m_delete_transaction_product_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Are you sure?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
                <form id="m_delete_transaction_product" action="" method="post">
                    <button type="submit" class="btn btn-danger">
                        DELETE
                    </button>
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="m_bulk_delete_transaction_product_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Are you sure?
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
                <button id="m_bulk_delete_transaction_product" type="button" class="btn btn-danger" data-dismiss="modal">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
<div id="transaction-id" style="display: none;">{{ $transaction->id }}</div>
@endsection