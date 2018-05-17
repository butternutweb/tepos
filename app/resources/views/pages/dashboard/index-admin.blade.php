@extends('layouts.admin')

@section('title', 'Dashboard')

@section('js')
<script src="{{ asset('js/toastr.js') }}" type="text/javascript"></script>
@endsection

@section('content')
@if (Auth::check())
    @if (Auth::user()->child()->first() instanceof \App\Admin)
    
    @endif
@endif
@endsection