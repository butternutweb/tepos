@extends('layouts.admin')

@section('title', 'Create Product')

@section('js')
<script src="{{ asset('js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-validation/product.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_product" action="{{ route('product.store') }}" method="post" class="m-form m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group row {{ $errors->first('name') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Name:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="name" class="form-control m-input" placeholder="Enter name" value="{{ old('name') }}" aria-describedby="name-error">
                    <div id="name-error" class="form-control-feedback">{{ $errors->first('name') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('sku') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    SKU:
                </label>
                <div class="col-lg-6">
                    <input type="text" name="sku" class="form-control m-input" placeholder="Enter SKU" value="{{ old('sku') }}" aria-describedby="sku-error">
                    <div id="sku-error" class="form-control-feedback">{{ $errors->first('sku') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
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
            <div class="form-group m-form__group row {{ $errors->first('capital_price') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Capital Price:
                </label>
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            Rp
                        </span>
                        <input type="number" name="capital_price" class="form-control m-input" placeholder="Enter capital price" value="{{ old('capital_price') }}" aria-describedby="capital_price-error">
                    </div>
                    <div id="capital_price-error" class="form-control-feedback">{{ $errors->first('capital_price') }}</div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="form-group m-form__group row {{ $errors->first('sub_category_id') ? 'has-danger' : '' }}">
                <label class="col-lg-2 col-form-label">
                    Sub Category:
                </label>
                <div class="col-lg-6">
                    <div class="m-form__control">
                        <select class="form-control" name="sub_category_id" aria-describedby="sub_category_id-error">
                            @foreach ($sub_categories as $sub_category)
                                <option value="{{ $sub_category->id }}" {{ $sub_category->id == old('sub_category_id') ? 'selected' : '' }}>
                                    {{ $sub_category->category()->first()->name }} - {{ $sub_category->name }}
                                    @if (Auth::user()->first()->child()->first() instanceof \App\Admin)
                                        ({{ $sub_category->category()->first()->owner()->first()->accounts()->first()->username }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="sub_category_id-error" class="form-control-feedback">{{ $errors->first('sub_category_id') }}</div>
                    {{--sK edit--}}
                    <a class="m-link" href="{{ route('category.index') }}">category list</a>
                    {{-- -------- --}}
                </div>
                <div class="col-md-4">
                    
                </div>
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