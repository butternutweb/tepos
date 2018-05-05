@extends('layouts.admin')

@section('title', 'Create Category')

@section('js')
<script src="{{ asset('js/form-validation/category.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    @if (Auth::user()->child()->first() instanceof \App\Admin)
        <form id="m_admin_category" action="{{ route('category.store') }}" method="post" class="m-form m-form--label-align-right">
    @elseif (Auth::user()->child()->first() instanceof \App\Owner)
        <form id="m_owner_category" action="{{ route('category.store') }}" method="post" class="m-form m-form--label-align-right">
    @endif
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
            @if (Auth::user()->child()->first() instanceof \App\Admin)
                <div class="form-group m-form__group row {{ $errors->first('owner_id') ? 'has-danger' : '' }}">
                    <label class="col-lg-2 col-form-label">
                        Owner:
                    </label>
                    <div class="col-lg-6">
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
            @endif
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