@extends('layouts.staff')

@section('title', 'Create Transaction')

@section('js')
<script src="{{ asset('js/pages/transaction-staff.js') }}" type="text/javascript"></script>
<script>
    $('#print-btn').click(function(){
        window.open("{{route('transaction.invoice',$id)}}", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes");
    })
</script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__body">
        <div class="m-form m-form--label-align-right">
            <div class="row align-items-center">
                @if (session('error_message'))
                    {{ session('error_message') }}
                @else
                    @foreach ($products as $product)
                        <div class="col-md-3 m--align-center" style="margin-bottom: 25px">
                            <div class="col-border-item">
                                <h5 style="min-height: 50px">{{ $product->name }}</h5>
                                <span style="font-weight:500">Rp {{number_format($product->selling_price,0,',','.')}}</span>
                                <div class="m-bootstrap-touchspin-brand" style="margin-top: 5px">
                                    <div class="input-group bootstrap-touchspin">
                                        <input type="text" class="m_touchspin form-control" value="{{ session('_product_id_' . $product->id) ? session('_product_id_' . $product->id) : ($transaction->products()->where('product.id', $product->id)->first() ? $transaction->products()->where('product.id', $product->id)->first()->pivot->qty : 0) }}" name="demo1" style="display: block;">
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