@extends('layouts.admin')

@section('title', 'Edit Product')

@section('js')
<script src="{{ asset('js/form-validation/store_product.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_store_product" action="{{ route('store_.product.update', [$store_id, $store_product->pivot->id]) }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('selling_price') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Selling Price:
                </label>
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            Rp
                        </span>
                        <input type="text" name="selling_price" class="form-control m-input" placeholder="Enter selling price" value="{{ old('selling_price') ? old('selling_price') : $store_product->pivot->selling_price }}" aria-describedby="selling_price-error">
                    </div>
                    <div id="selling_price-error" class="form-control-feedback">{{ $errors->first('selling_price') }}</div>
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
                                <option value="{{ $product->id }}" {{ old('product_id') ? ($product->id == old('product_id') ? 'selected' : '') : ($product->id == $store_product->pivot->product_id ? 'selected' : '') }}>
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