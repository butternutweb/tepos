@extends('layouts.admin')

@section('title', 'Edit Product')

@section('js')
<script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation/transaction_product.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_transaction_product" action="{{ route('transaction_.product.update', [$transaction_id, $transaction_product->pivot->id]) }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('qty') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Quantity:
                </label>
                <div class="col-lg-6">
                    <input type="number" name="qty" class="form-control m-input" placeholder="Enter quantity" value="{{ old('qty') ? old('qty') : $transaction_product->pivot->qty }}" aria-describedby="qty-error">
                    <div id="qty-error" class="form-control-feedback">{{ $errors->first('qty') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('note') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Note:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="note" class="form-control m-input" placeholder="Enter note" value="{{ old('note') ? old('note') : $transaction_product->pivot->note }}" aria-describedby="note-error">
                    <div id="note-error" class="form-control-feedback">{{ $errors->first('note') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('product_id') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Product:
                </label>
                <div class="col-lg-6">
                    <div class="m-form__control">
                        <select class="form-control" name="product_id" aria-describedby="product_id-error">
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') ? ($product->id == old('product_id') ? 'selected' : '') : ($product->id == $transaction_product->pivot->product_id ? 'selected' : '') }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="product_id-error" class="form-control-feedback">{{ $errors->first('product_id') }}</div>
                    <a class="m-link" href="{{ route('product.create') }}">+ Create product</a>
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