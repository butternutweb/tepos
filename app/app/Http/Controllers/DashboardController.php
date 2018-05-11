<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function getIndex() {
        if (Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.dashboard.index-admin');
        }
        if (Auth::user()->child()->first() instanceof \App\Owner) {
            $transactionSum = 0;
            $transactionAmountSum = 0;
            $monthProfit = 0;
            $monthTransaction = 0;
            $status = \App\Status::where('name', 'Completed')->first()->id;
            $owner = Auth::user()->child()->first();
            //iterate through all transactions of this account
            foreach ($owner->stores as $store){
                foreach ($store->staffs as $staff){
                    $completedTransactions = $staff->transactions->where('status_id',$status)->all();
                    $transactionSum += count($completedTransactions);
                    foreach ($completedTransactions as $transaction){
                        if (\Carbon\Carbon::now()->month == \Carbon\Carbon::parse($transaction->date)->month){
                            $monthProfit += $transaction->amount();
                            $monthTransaction++;
                        };
                        $transactionAmountSum += $transaction->amount();
                    };
                };
            };

            //calculate this month profit and product in store,
            //profit = transactionprofit - costs - staff salaries
            $productActive =0;
            foreach ($owner->stores as $store){
                foreach ($store->costs as $key => $cost) {
                    if (\Carbon\Carbon::now()->month == \Carbon\Carbon::parse($cost->date)->month){
                        $monthProfit -= $cost->amount;
                    }
                };
                foreach ($store->staffs as $staff){
                    $monthProfit -= $staff->salary;
                };
                $productActive += $store->products->count();
            }
            
            $data = [
                'transactionSum'=>$transactionSum,
                'transactionTotal'=>money_short($transactionAmountSum),
                'monthProfit' => number_format($monthProfit,0,',','.'),
                'monthTransaction' => $monthTransaction,
                'productActive' => $productActive,
            ];
            return view('pages.dashboard.index-owner',$data);
        }

        return redirect()->route('transaction.index');
    }
}
