@extends('layouts.admin')

@section('title', 'Edit Category')

@section('js')
<script src="{{ asset('js/datatable/category-edit.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    @if (Auth::user()->child()->first() instanceof \App\Admin)
        <form id="m_admin_category" action="{{ route('category.update', $category->id) }}" method="post" class="m-form m-form--label-align-right">
    @elseif (Auth::user()->child()->first() instanceof \App\Owner)
        <form id="m_owner_category" action="{{ route('category.update', $category->id) }}" method="post" class="m-form m-form--label-align-right">
    @endif
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('name') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Name:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="name" class="form-control m-input" placeholder="Enter name" value="{{ old('name') ? old('name') : $category->name }}" aria-describedby="name-error">
                    <div id="name-error" class="form-control-feedback">{{ $errors->first('name') }}</div>
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
                                    <option value="{{ $owner->id }}" {{ old('owner_id') ? ($owner->id == old('owner_id') ? 'selected' : '') : ($owner->id == $category->owner()->first()->id ? 'selected' : '') }}>
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
            @endif
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <h5>Sub Category</h5>
            </div>
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-6 order-2 order-xl-1">
                        <div class="m-input-icon m-input-icon--left">
                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="m_search_sub_category">
                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                <span>
                                    <i class="la la-search"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                        <a href="{{ route('sub-category.create', $category->id) }}" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air">
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
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 collapse" id="m_datatable_group_action_form_sub_category">
                <div class="row align-items-center">
                    <div class="col-xl-12">
                        <div class="m-form__group m-form__group--inline">
                            <div class="m-form__label m-form__label-no-wrap">
                                <label class="m--font-bold m--font-danger-">
                                    Selected
                                    <span id="m_datatable_selected_number_sub_category"></span>
                                    records:
                                </label>
                            </div>
                            <div class="m-form__control">
                                <div class="btn-toolbar">
                                    <button class="btn btn-sm btn-danger" type="button"  data-toggle="modal" data-target="#m_bulk_delete_sub_category_modal">
                                        Delete Selected
                                    </button>
                                </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m_datatable" id="m_datatable_sub_category" style="margin-bottom: 10px;"></div>
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
                    <div class="col-lg-4"></div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" id="m_delete_sub_category_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <form id="m_delete_sub_category" action="" method="post">
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
<div class="modal fade" id="m_bulk_delete_sub_category_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <button id="m_bulk_delete_sub_category" type="button" class="btn btn-danger" data-dismiss="modal">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
<div id="category-id" style="display: none;">{{ $category->id }}</div>
@endsection