<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * show the dashboard index page
     * 
     * @return Illuminate\Http\Response
     */
    function getIndex() {
        if (Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.dashboard.index-admin');
        }
        if (Auth::user()->child()->first() instanceof \App\Owner) {
            
            return view('pages.dashboard.index-owner');
        }

        return redirect()->route('transaction.index');
    }

    /**
     * ajax function to get the last transaction by date
     * 
     * @return Illuminate\Http\Response
     */
    function getDashboardData(){
        $user = Auth::user();
        if ($user->child()->first() instanceof \App\Owner) {
            $owner = $user->child()->first();
            $xTransactions=collect([]);
            $xProducts=collect([]);
            $transactionSum = 0;
            $transactionAmountSum = 0;
            $monthProfit = 0;
            $monthTransaction = 0;
            $status = \App\Status::where('name', 'Completed')->first()->id;
            foreach ($owner->stores as $store){
                foreach ($store->staffs as $staff){
                    $completedTransactions = $staff->transactions->where('status_id',$status)->all();
                    $transactionSum += count($completedTransactions);
                    foreach($completedTransactions as $transaction){
                        $transDate = Carbon::parse($transaction->date);
                        if ($transDate->gt(Carbon::now()->subDays(9)->startOfDay())){
                            $transAm=$transaction->amount();
                            $xTransactions->push(['date'=> Carbon::parse($transaction->date)->format('M d'), 'value' => $transAm['value'], 'qty' => $transAm['qty']]);
                        };
                        if (Carbon::now()->month == $transDate->month){
                            $monthProfit += $transaction->amount()['value'];
                            $monthTransaction++;
                        };
                        if ($transDate->gt(Carbon::now()->subDays(30)->startOfDay())){
                            foreach ($transaction->products as $product){
                                $xProducts->push(['name'=>$product->name,'qty'=>$product->pivot->qty]);
                            };
                        };
                        $transactionAmountSum += $transaction->amount()['value'];
                    };
                };
            };
            
            // get the daily sales in the past 10 days
            $step = \Carbon\CarbonInterval::day();
            $period = new \DatePeriod(Carbon::now()->subDays(9)->startOfDay(), $step, Carbon::now()->addDay()->startOfDay());

            $range = [];
            $sales = [];
            $sold = [];
            foreach ($period as $day) {
                $carbonDate = Carbon::parse($day)->format('M d');
                $range[] = $carbonDate;
                if ($xTransactions->groupBy('date')->has($carbonDate)){
                    $transactions = $xTransactions->groupBy('date')[$carbonDate];
                    $sales[] = $transactions->sum('value');
                    $sold[] = $transactions->sum('qty');
                } else {
                    $sales[] = 0;
                    $sold[] = 0;
                };
                
            };

            // get the popular product
            $popProducts = $xProducts->groupBy('name')->sortByDesc(function ($product, $key) {
                return count($product);
            });
            $sortedProduct = [];
            $names = [];
            $othersQty=0;
            foreach ($popProducts as $keyName => $product){
                if (count($sortedProduct)<4){
                    $names[] = $keyName;
                    $sortedProduct[] = $product->sum('qty');
                } else {
                    $othersQty += $product->sum('qty');
                };
            };
            if (count($sortedProduct)>=4){
                $names[] = 'others';
                $sortedProduct[] = $othersQty;
            }

            //calculate this month profit and product in store,
            //profit = transactionprofit - costs - staff salaries
            $productActive =0;
            foreach ($owner->stores as $store){
                foreach ($store->costs as $key => $cost) {
                    if (Carbon::now()->month == Carbon::parse($cost->date)->month){
                        $monthProfit -= $cost->amount;
                    }
                };
                foreach ($store->staffs as $staff){
                    $monthProfit -= $staff->salary;
                };
                $productActive += $store->products->count();
            }
            
            $response = [
                'labels' => $range,
                'sales' => $sales,
                'qty' => $sold,
                'names'=> $names,
                'sortedProduct'=> $sortedProduct,
                'transactionSum'=>$transactionSum,
                'transactionTotal'=>money_short($transactionAmountSum),
                'monthProfit' => number_format($monthProfit,0,',','.'),
                'monthTransaction' => $monthTransaction,
                'productActive' => $productActive
            ];
            return response()->json($response);
        }
    }
}
