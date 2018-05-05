<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function getIndex() {
        if (Auth::user()->child()->first() instanceof \App\User || Auth::user()->child()->first() instanceof \App\Owner) {
            return view('pages.dashboard.index');
        }

        return redirect()->route('transaction.index');
    }
}
