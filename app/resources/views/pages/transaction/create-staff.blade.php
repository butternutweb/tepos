@extends('layouts.staff')

@section('title', 'Create Transaction')

@section('js')
<script src="{{ asset('js/pages/transaction-staff.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__body">
        <div class="m-form m-form--label-align-right">
            <div class="row align-items-center">
                @if (session('error_message'))
                    {{ session('error_message') }}
                @else
                    @foreach (\App\SubCategory::find(Request::input('sub_category'))->products()->get() as $product)
                        <div class="col-md-3 m--align-center" style="margin-bottom: 25px">
                            <div class="col-border-item">
                                <h5 style="margin-bottom: 20px; min-height: 50px">{{ $product->name }}</h5>
                                <div class="m-bootstrap-touchspin-brand" style="margin-top: 25px">
                                    <div class="input-group bootstrap-touchspin">
                                        <input type="text" class="m_touchspin form-control" value="{{ session('_product_id_' . $product->id) ? session('_product_id_' . $product->id) : 0}}" style="display: block;" readonly>
                                        <span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span>
                                    </div>
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection