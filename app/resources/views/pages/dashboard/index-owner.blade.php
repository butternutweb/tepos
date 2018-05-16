@extends('layouts.admin')

@section('title', 'Dashboard')

@section('js')
<script src="{{ asset('js/toastr.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/dashboard-owner.js') }}" type="text/javascript"></script>
@endsection

@section('content')
@if (Auth::check())
    @if (Auth::user()->child()->first() instanceof \App\Owner)
    <div class="m-portlet">
        <div class="m-portlet__body  m-portlet__body--no-padding">
            <div class="row m-row--no-padding m-row--col-separator-xl">			
                <div class="col-xl-4">
                    <!--begin:: Widgets/Stats2-1 -->
                    <div class="m-widget1">
                        <div class="m-widget1__item total_sales">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h3 class="m-widget1__title">Total Transaction</h3>
                                    <span class="m-widget1__desc total_sales_desc"></span>
                                </div>
                                <div class="col m--align-right">
                                    <span class="m-widget1__number total_sales_number m--font-brand"></span>
                                </div>
                            </div>
                        </div>
                        <div class="m-widget1__item profit">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h3 class="m-widget1__title">This Month Profit</h3>
                                    <span class="m-widget1__desc profit_desc"></span>
                                </div>
                                <div class="col m--align-right">
                                    <span class="m-widget1__number profit_number m--font-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="m-widget1__item product_stats">
                            <div class="row m-row--no-padding align-items-center">
                                <div class="col">
                                    <h3 class="m-widget1__title">Products in Stores</h3>
                                    <span class="m-widget1__desc product_stats_desc">{{Auth::user()->child()->first()->stores->count()}} Stores Active</span>
                                </div>
                                <div class="col m--align-right">
                                    <span class="m-widget1__number product_stats_number m--font-success"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end:: Widgets/Stats2-1 -->			
                </div>
                <div class="col-xl-4">
                    <!--begin:: Widgets/Daily Sales-->
                    <div class="m-widget14">
                        <div class="m-widget14__header m--margin-bottom-10">
                            <h3 class="m-widget14__title">
                                Daily Sales              
                            </h3>
                            <span class="m-widget14__desc">
                            Check out each column for more details
                            </span>
                        </div>
                        <div class="m-widget14__chart">
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div></div>
                                </div>
                            </div>
                            <canvas id="m_chart_daily_sales" width="334" height="120" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                    <!--end:: Widgets/Daily Sales-->
                </div>
                <div class="col-xl-4">
                    <!--begin:: Popular Products-->
                    <div class="m-widget14">
                        <div class="m-widget14__header pb-0 mb-0">
                            <h3 class="m-widget14__title">
                                Popular product            
                            </h3>
                            <span class="m-widget14__desc">
                                of the last 30 day
                            </span>
                        </div>
                        <div class="m-widget14__chart">
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div></div>
                                </div>
                            </div>
                            <canvas id="m_chart_popular" width="334" height="145" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
    			</div>
            </div>
        </div>
    </div>
    @endif
@endif
@endsection