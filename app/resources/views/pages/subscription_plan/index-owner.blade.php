@extends('layouts.admin')

@section('title', 'Subscription Plan List')

@section('js')
<script type="text/javascript" src="{{$clientSnapUrl}}" data-client-key="{{$clientKey}}"></script>
<script type="text/javascript" src="{{ asset('js/pages/subs_plan.js') }}"></script>   
 @endsection

@section('content')
<div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__body">
        <div class="m-pricing-table-1">
            <div class="m-pricing-table-1__items row">
                @foreach ($subscription_plans as $idx => $subscription_plan)
                <div class="m-pricing-table-1__item col-lg-4">
                        <div class="m-pricing-table-1__visual">
                            <div class="m-pricing-table-1__hexagon1"></div>
                            <div class="m-pricing-table-1__hexagon2"></div>
                            <span class="m-pricing-table-1__icon m--font-brand">
                                <i class="fa flaticon-piggy-bank"></i>
                            </span>
                        </div>
                        <span class="m-pricing-table-1__price">
                            <span class="m-pricing-table-1__label">
                                Rp
                            </span>
                            {{ $subscription_plan->price }}
                        </span>
                        <h2 class="m-pricing-table-1__subtitle">
                            {{ $subscription_plan->name }}
                        </h2>
                        <span class="m-pricing-table-1__description">
                            @if ($subscription_plan->store_number > 1)
                                {{ $subscription_plan->store_number }} Stores
                            @else
                                {{ $subscription_plan->store_number }} Store
                            @endif
                            @if ($subscription_plan->duration_day > 1)
                                <br> {{ $subscription_plan->duration_day }} Days
                            @else
                                <br> {{ $subscription_plan->duration_day }} Day
                            @endif
                        </span>
                        <div class="m-pricing-table-1__btn">
                            <button type="button" data-plan="{!!$subscription_plan->id!!}" class="btn btn-brand m-btn m-btn--custom m-btn--pill m-btn--wide m-btn--uppercase m-btn--bolder m-btn--sm btn-purchase">
                                Purchase
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection