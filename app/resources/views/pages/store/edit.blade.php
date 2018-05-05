@extends('layouts.admin')

@section('title', 'Edit Store')

@section('js')
<script src="{{ asset('js/form-validation/store.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/datatable/store-edit.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    @if (Auth::user()->child()->first() instanceof \App\Admin)
        <form id="m_admin_store" action="{{ route('store.update', $store->id) }}" method="post" class="m-form m-form--label-align-right">
    @elseif (Auth::user()->child()->first() instanceof \App\Owner)
        <form id="m_owner_store" action="{{ route('store.update', $store->id) }}" method="post" class="m-form m-form--label-align-right">
    @endif
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group m-form__group {{ $errors->first('name') ? 'has-danger' : '' }}">
                        <label class="col-form-label">
                            Name:
                        </label>
                        <div>
                            <input type="text" name="name" class="form-control m-input" placeholder="Enter name" value="{{ old('name') ? old('name') : $store->name }}" aria-describedby="name-error">
                            <div id="name-error" class="form-control-feedback">{{ $errors->first('name') }}</div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>
                @if (Auth::user()->child()->first() instanceof \App\Admin)
                    <div class="col-lg-6">
                        <div class="form-group m-form__group {{ $errors->first('owner_id') ? 'has-danger' : '' }}">
                            <label class="col-form-label">
                                Owner:
                            </label>
                            <div>
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
                                <a class="m-link" href="{{ route('owner.create') }}">+ Create owner</a>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="form-group m-form__group row">
                <div class="col-md-6">
                    <h5>
                        Cost:
                    </h5>
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-6 order-2 order-xl-1">
                                <div class="m-input-icon m-input-icon--left">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="m_search_cost">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                                <a href="{{ route('cost.create', $store->id) }}" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air">
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
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 collapse" id="m_datatable_group_action_form_cost">
                        <div class="row align-items-center">
                            <div class="col-xl-12">
                                <div class="m-form__group m-form__group--inline">
                                    <div class="m-form__label m-form__label-no-wrap">
                                        <label class="m--font-bold m--font-danger-">
                                            Selected
                                            <span id="m_datatable_selected_number_cost"></span>
                                            records:
                                        </label>
                                    </div>
                                    <div class="m-form__control">
                                        <div class="btn-toolbar">
                                            <button class="btn btn-sm btn-danger" type="button"  data-toggle="modal" data-target="#m_bulk_delete_cost_modal">
                                                Delete Selected
                                            </button>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m_datatable" id="m_datatable_cost"></div>
                </div>
                <div class="col-md-6">
                    <h5>
                        Staff:
                    </h5>
                    @if (Auth::user()->child()->first() instanceof \App\Admin)
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
                                    <div class="form-group m-form__group row align-items-center">
                                        <div class="col-md-4">
                                            <div class="m-form__group m-form__group--inline">
                                                <div class="m-form__label">
                                                    <label>
                                                        Status:
                                                    </label>
                                                </div>
                                                <div class="m-form__control">
                                                    <select class="form-control m-bootstrap-select m-bootstrap-select--solid" id="m_form_staff_status">
                                                        <option value="">
                                                            All
                                                        </option>
                                                        @foreach ($statuses as $status)
                                                            <option value="{{ $status->id }}">
                                                                {{ $status->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="d-md-none m--margin-bottom-10"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-6 order-2 order-xl-1">
                                <div class="m-input-icon m-input-icon--left">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="m_search_staff">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                                <a href="{{ route('staff.create', $store->id) }}" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air">
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
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 collapse" id="m_datatable_group_action_form_staff">
                        <div class="row align-items-center">
                            <div class="col-xl-12">
                                <div class="m-form__group m-form__group--inline">
                                    <div class="m-form__label m-form__label-no-wrap">
                                        <label class="m--font-bold m--font-danger-">
                                            Selected
                                            <span id="m_datatable_selected_number_staff"></span>
                                            records:
                                        </label>
                                    </div>
                                    <div class="m-form__control">
                                        <div class="btn-toolbar">
                                            <button class="btn btn-sm btn-danger" type="button"  data-toggle="modal" data-target="#m_bulk_delete_staff_modal">
                                                Delete Selected
                                            </button>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m_datatable" id="m_datatable_staff"></div>
                </div>
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
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="m_search_store_product">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                                <a href="{{ route('store_.product.create', $store->id) }}" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air">
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
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 collapse" id="m_datatable_group_action_form_store_product">
                        <div class="row align-items-center">
                            <div class="col-xl-12">
                                <div class="m-form__group m-form__group--inline">
                                    <div class="m-form__label m-form__label-no-wrap">
                                        <label class="m--font-bold m--font-danger-">
                                            Selected
                                            <span id="m_datatable_selected_number_store_product"></span>
                                            records:
                                        </label>
                                    </div>
                                    <div class="m-form__control">
                                        <div class="btn-toolbar">
                                            <button class="btn btn-sm btn-danger" type="button"  data-toggle="modal" data-target="#m_bulk_delete_store_product_modal">
                                                Delete Selected
                                            </button>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m_datatable" id="m_datatable_store_product"></div>
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
<div class="modal fade" id="m_delete_cost_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <form id="m_delete_cost" action="" method="post">
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
<div class="modal fade" id="m_bulk_delete_cost_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <button id="m_bulk_delete_cost" type="button" class="btn btn-danger" data-dismiss="modal">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="m_delete_staff_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <form id="m_delete_staff" action="" method="post">
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
<div class="modal fade" id="m_bulk_delete_staff_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <button id="m_bulk_delete_staff" type="button" class="btn btn-danger" data-dismiss="modal">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="m_delete_store_product_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <form id="m_delete_store_product" action="" method="post">
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
<div class="modal fade" id="m_bulk_delete_store_product_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <button id="m_bulk_delete_store_product" type="button" class="btn btn-danger" data-dismiss="modal">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
<div id="store-id" style="display: none;">{{ $store->id }}</div>
@endsection