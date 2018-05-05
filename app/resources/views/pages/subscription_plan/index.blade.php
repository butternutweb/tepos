@extends('layouts.admin')

@section('title', 'Subscription Plan List')

@section('js')
<script src="{{ asset('js/datatable/subscription_plan.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__body">
        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
            <div class="row align-items-center">
                <div class="col-xl-6 order-2 order-xl-1">
                    <div class="m-input-icon m-input-icon--left">
                        <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="m_search_subscription_plan">
                        <span class="m-input-icon__icon m-input-icon__icon--left">
                            <span>
                                <i class="la la-search"></i>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                    <a href="{{ route('subs-plan.create') }}" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air">
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
        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 collapse" id="m_datatable_group_action_form_subscription_plan">
            <div class="row align-items-center">
                <div class="col-xl-12">
                    <div class="m-form__group m-form__group--inline">
                        <div class="m-form__label m-form__label-no-wrap">
                            <label class="m--font-bold m--font-danger-">
                                Selected
                                <span id="m_datatable_selected_number_subscription_plan"></span>
                                records:
                            </label>
                        </div>
                        <div class="m-form__control">
                            <div class="btn-toolbar">
                                <button class="btn btn-sm btn-danger" type="button"  data-toggle="modal" data-target="#m_bulk_delete_subscription_plan_modal">
                                    Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="m_datatable" id="m_datatable_subscription_plan"></div>
    </div>
</div>
<div class="modal fade" id="m_delete_subscription_plan_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <form id="m_delete_subscription_plan" action="" method="post">
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
<div class="modal fade" id="m_bulk_delete_subscription_plan_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                <button id="m_bulk_delete_subscription_plan" type="button" class="btn btn-danger" data-dismiss="modal">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection