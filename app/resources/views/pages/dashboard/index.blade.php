@extends('layouts.admin')

@section('title', 'Dashboard')

@section('js')
<script src="{{ asset('js/toastr.js') }}" type="text/javascript"></script>
@endsection

@section('content')
@if (Auth::check())
    Logged in
    @if (Auth::user()->child()->first() instanceof \App\Admin)
        Admin
    @endif
    @if (Auth::user()->child()->first() instanceof \App\Owner)
        Owner
    @endif
    @if (Auth::user()->child()->first() instanceof \App\Staff)
        Staff
    @endif
@endif
@endsection