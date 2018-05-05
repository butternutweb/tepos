@extends('layouts.admin')

@section('title', 'Create Sub Category')

@section('js')
<script src="{{ asset('js/form-validation/sub_category.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <form id="m_sub_category" action="{{ route('sub-category.store', $category_id) }}" method="post" class="m-form m-form--label-align-right">
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