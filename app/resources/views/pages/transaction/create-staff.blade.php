@extends('layouts.staff')

@section('title', 'Create Transaction')

@section('js')
<script src="{{ asset('js/pages/transaction-staff.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__body">
        <div class="m-form m-form--label-align-right">
            <div class="form-group m-form__group row">
                <div class="col-lg-6">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Amount Paid</label>
                        <div class="col-lg-8">
                            <input id="trAmount" type="number" name="amount" class="form-control m-input" placeholder="Rp.">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Transaction Note:</label>
                        <div class="col-lg-8">
                            <textarea id="trNote" name="note" class="form-control m-input" placeholder="transaction Note" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h4 class="mt-5">Total: Rp.<span id="amount_paid">{{ session('sales_total') ?: 0}}</span></h4>
                </div>
            </div>
            <div class="row align-items-center">
                @if (session('error_message'))
                    {{ session('error_message') }}
                @else
                    @foreach ($products as $product)
                        <div class="col-md-3 m--align-center" style="margin-bottom: 25px">
                            <div class="col-border-item">
                                <h5 style="min-height: 50px">{{ $product->name }}</h5>
                                
                                <div class="m-bootstrap-touchspin-brand" style="margin-top: 5px">
                                    <div style="font-weight:500">Rp <span class="price">{{number_format($product->selling_price,0,',','.')}}</span></div>
                                    <div class="input-group bootstrap-touchspin">
                                        <input type="text" class="m_touchspin form-control" value="{{ session('_product_id_' . $product->id) ?: 0}}" style="display: block;">
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