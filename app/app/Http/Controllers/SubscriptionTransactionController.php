<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubscriptionTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return view('pages.subscription_transaction.index')->with('subscription_plans', \App\SubscriptionPlan::all());
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.subscription_transaction.index')->with('owners', \App\Owner::all())->with('subscription_plans', \App\SubscriptionPlan::all());
        }

        return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.subscription_transaction.create')->with('owners', \App\Owner::all())->with('subscription_plans', \App\SubscriptionPlan::all());
        }

        return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'date' => ['required', 'date'],
                'payment_method' => ['required', 'string', 'max:30'],
                'owner_id' => ['required', 'integer'],
                'subscription_plan_id' => ['required', 'integer'],
            ], [
                'payment_method.required' => 'The payment method field is required.',
                'payment_method.string' => 'The payment method must be a string.',
                'payment_method.max' => 'The payment method may not be greater than :max characters.',
                'owner_id.required' => 'The owner field is required.',
                'owner_id.integer' => 'The owner must be an integer.',
                'subscription_plan_id.required' => 'The subscription plan field is required.',
                'subscription_plan_id.integer' => 'The subscription plan must be an integer.',
            ]);
    
            $owner = \App\Owner::find($request->owner_id);
            
            if ($owner === NULL) {
                return redirect()->back()->withInput($request->except('owner_id'))->with('error', 'Data does not exist.');
            }
    
            $subscription_plan = \App\SubscriptionPlan::find($request->subscription_plan_id);
            
            if ($subscription_plan === NULL) {
                return redirect()->back()->withInput($request->except('subscription_plan_id'))->with('error', 'Data does not exist.');
            }
    
            try {
                $subscription_transaction = new \App\SubscriptionTransaction;
                $subscription_transaction->date = $request->date;
                $subscription_transaction->payment_method = $request->payment_method;
                $subscription_transaction->payment_status = "received";
                $last_subscription_transaction = $owner->transactions()->orderBy('subs_end', 'desc')->first();

                if ($last_subscription_transaction === NULL) {
                    $subscription_transaction->subs_end = \Carbon\Carbon::now()->addDays($subscription_plan->duration_day);
                }
                else {
                    $subscription_transaction->subs_end = \Carbon\Carbon::parse($last_subscription_transaction->subs_end)->addDays($subscription_plan->duration_day);
                }

                $subscription_transaction->owner()->associate($owner);
                $subscription_transaction->plan()->associate($subscription_plan);
                $subscription_transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('subs-trans.index'))->with('success', 'Successfully created subscription transaction.');
        }

        return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('subs-trans.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $subscription_transaction = \App\SubscriptionTransaction::find($id);

            if ($subscription_transaction === NULL) {
                return redirect()->route('subs-trans.index')->with('error', 'Data does not exist.');
            }

            if (\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($subscription_transaction->subs_end)) < 0) {
                return redirect()->route('subs-trans.index')->with('error', 'Data is outdated.');
            }

            return view('pages.subscription_transaction.edit')->with('subscription_transaction', $subscription_transaction)->with('owners', \App\Owner::all())->with('subscription_plans', \App\SubscriptionPlan::all());
        }

        return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'date' => ['required', 'date'],
                'payment_method' => ['required', 'string', 'max:30'],
                'owner_id' => ['required', 'integer'],
                'subscription_plan_id' => ['required', 'integer'],
            ], [
                'payment_method.required' => 'The payment method field is required.',
                'payment_method.string' => 'The payment method must be a string.',
                'payment_method.max' => 'The payment method may not be greater than :max characters.',
                'owner_id.required' => 'The owner field is required.',
                'owner_id.integer' => 'The owner must be an integer.',
                'subscription_plan_id.required' => 'The subscription plan field is required.',
                'subscription_plan_id.integer' => 'The subscription plan must be an integer.',
            ]);

            $owner = \App\Owner::find($request->owner_id);
            
            if ($owner === NULL) {
                return redirect()->back()->withInput($request->except('owner_id'))->with('error', 'Data does not exist.');
            }

            $subscription_plan = \App\SubscriptionPlan::find($request->subscription_plan_id);
            
            if ($subscription_plan === NULL) {
                return redirect()->back()->withInput($request->except('subscription_plan_id'))->with('error', 'Data does not exist.');
            }

            $subscription_transaction = \App\SubscriptionTransaction::find($id);

            if ($subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            if (\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($subscription_transaction->subs_end)) < 0) {
                return redirect()->back()->withInput()->with('error', 'Data is outdated.');
            }

            try {
                $subscription_transaction->date = $request->date;

                $last_subscription_transactions = $owner->transactions()->where('subs_end', '>=', $subscription_transaction->subs_end)->get();
                
                foreach ($last_subscription_transactions as $last_subscription_transaction) {
                    $last_subscription_transaction->subs_end = \Carbon\Carbon::parse($last_subscription_transaction->subs_end)->addDays($subscription_plan->duration_day - $subscription_transaction->plan()->first()->duration_day);
                    $last_subscription_transaction->save();
                }

                $subscription_transaction->payment_method = $request->payment_method;
                $subscription_transaction->payment_status = "byAdmin";
                $subscription_transaction->owner()->associate($owner);
                $subscription_transaction->plan()->associate($subscription_plan);
                $subscription_transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('subs-trans.index'))->with('success', 'Successfully edited subscription transaction.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $subscription_transaction = \App\SubscriptionTransaction::find($id);
            
            if ($subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            if (\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($subscription_transaction->subs_end)) < 0) {
                return redirect()->back()->withInput()->with('error', 'Data is outdated.');
            }
    
            try {
                $last_subscription_transactions = $subscription_transaction->owner()->first()->transactions()->where('subs_end', '>', $subscription_transaction->subs_end)->get();
                
                foreach ($last_subscription_transactions as $last_subscription_transaction) {
                    $last_subscription_transaction->subs_end = \Carbon\Carbon::parse($last_subscription_transaction->subs_end)->subDays($subscription_transaction->plan()->first()->duration_day);
                    $last_subscription_transaction->save();
                }

                $subscription_transaction->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('subs-trans.index'))->with('success', 'Successfully deleted subscription transaction.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'subs_transaction';
            $field = 'id';
            $m_search_subscription_transaction = '';
            $subscription_plan_id = '';
            $total = 0;
            $pages = 1;
            $page = 1;

            if (!empty($request->input('datatable')['pagination']['perpage']) && is_numeric($request->input('datatable')['pagination']['perpage']) && $request->input('datatable')['pagination']['perpage'] >= 1) {
                $perpage = $request->input('datatable')['pagination']['perpage'];
            }

            if (!empty($request->input('datatable')['sort']['sort']) && ($request->input('datatable')['sort']['sort'] == 'asc' || $request->input('datatable')['sort']['sort'] == 'desc')) {
                $sort = $request->input('datatable')['sort']['sort'];
            }

            if (!empty($request->input('datatable')['sort']['field']) && !Schema::hasColumn($table_name, $request->input('datatable')['sort']['field'])) {
                $field = $request->input('datatable')['sort']['field'];
            }

            if (!empty($request->input('datatable')['query']['m_search_subscription_transaction'])) {
                $m_search_subscription_transaction = $request->input('datatable')['query']['m_search_subscription_transaction'];
            }

            if (!empty($request->input('datatable')['query']['owner_id'])) {
                $owner_id = $request->input('datatable')['query']['owner_id'];
            }

            if (!empty($request->input('datatable')['query']['subscription_plan_id'])) {
                $subscription_plan_id = $request->input('datatable')['query']['subscription_plan_id'];
            }

            $total = DB::table('subs_transaction')
                ->selectRaw('subs_transaction.id, subs_transaction.date, subs_transaction.subs_end, subs_transaction.payment_method, subs_plan.id as subscription_plan_id, subs_plan.name as subscription_plan')
                ->join('owner', 'subs_transaction.owner_id', 'owner.id')
                ->where('owner.id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->join('subs_plan', 'subs_transaction.subs_plan_id', 'subs_plan.id')
                ->where(function($query) use ($subscription_plan_id) {
                    if ($subscription_plan_id != '') {
                        $query->where('subs_plan.id', $subscription_plan_id);
                    }
                })
                ->where(function($query) use ($m_search_subscription_transaction) {
                    $query->where('subs_transaction.payment_method', 'like', '%' . $m_search_subscription_transaction . '%');
                })
                ->count();

            if ($perpage >= 1) {
                $pages = ceil($total/$perpage);
            }

            if (!empty($request->input('datatable')['pagination']['page']) && is_numeric($request->input('datatable')['pagination']['page']) && $request->input('datatable')['pagination']['page'] >= 1) {
                $page = $request->input('datatable')['pagination']['page'];

                if ($page*$perpage > $total) {
                    $page = $pages;
                }
            }

            return response()->json([
                'meta' => [
                    'page' => $page,
                    'pages' => $pages,
                    'perpage' => $perpage,
                    'total' => $total,
                    'sort' => $sort,
                    'field' => $field,
                ],
                'data' => DB::table('subs_transaction')
                    ->selectRaw('subs_transaction.id, subs_transaction.date, subs_transaction.subs_end, subs_transaction.payment_method, subs_transaction.payment_status, subs_plan.id as subscription_plan_id, subs_plan.name as subscription_plan')
                    ->join('owner', 'subs_transaction.owner_id', 'owner.id')
                    ->where('owner.id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                    ->join('subs_plan', 'subs_transaction.subs_plan_id', 'subs_plan.id')
                    ->where(function($query) use ($subscription_plan_id) {
                        if ($subscription_plan_id != '') {
                            $query->where('subs_plan.id', $subscription_plan_id);
                        }
                    })
                    ->where(function($query) use ($m_search_subscription_transaction) {
                        $query->where('subs_transaction.payment_method', 'like', '%' . $m_search_subscription_transaction . '%');
                    })
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'subs_transaction';
            $field = 'id';
            $m_search_subscription_transaction = '';
            $owner_id = '';
            $subscription_plan_id = '';
            $total = 0;
            $pages = 1;
            $page = 1;

            if (!empty($request->input('datatable')['pagination']['perpage']) && is_numeric($request->input('datatable')['pagination']['perpage']) && $request->input('datatable')['pagination']['perpage'] >= 1) {
                $perpage = $request->input('datatable')['pagination']['perpage'];
            }

            if (!empty($request->input('datatable')['sort']['sort']) && ($request->input('datatable')['sort']['sort'] == 'asc' || $request->input('datatable')['sort']['sort'] == 'desc')) {
                $sort = $request->input('datatable')['sort']['sort'];
            }

            if (!empty($request->input('datatable')['sort']['field']) && !Schema::hasColumn($table_name, $request->input('datatable')['sort']['field'])) {
                $field = $request->input('datatable')['sort']['field'];
            }

            if (!empty($request->input('datatable')['query']['m_search_subscription_transaction'])) {
                $m_search_subscription_transaction = $request->input('datatable')['query']['m_search_subscription_transaction'];
            }

            if (!empty($request->input('datatable')['query']['owner_id'])) {
                $owner_id = $request->input('datatable')['query']['owner_id'];
            }

            if (!empty($request->input('datatable')['query']['subscription_plan_id'])) {
                $subscription_plan_id = $request->input('datatable')['query']['subscription_plan_id'];
            }

            $total = DB::table('subs_transaction')
                ->selectRaw('subs_transaction.id, subs_transaction.date, subs_transaction.subs_end, subs_transaction.payment_method, owner.id as owner_id, account.username as owner, subs_plan.id as subscription_plan_id, subs_plan.name as subscription_plan')
                ->join('owner', 'subs_transaction.owner_id', 'owner.id')
                ->join('account', 'owner.id', 'account.child_id')
                ->where('account.child_type', 'Owner')
                ->join('subs_plan', 'subs_transaction.subs_plan_id', 'subs_plan.id')
                ->where(function($query) use ($owner_id) {
                    if ($owner_id != '') {
                        $query->where('owner.id', $owner_id);
                    }
                })
                ->where(function($query) use ($subscription_plan_id) {
                    if ($subscription_plan_id != '') {
                        $query->where('subs_plan.id', $subscription_plan_id);
                    }
                })
                ->where(function($query) use ($m_search_subscription_transaction) {
                    $query->where('subs_transaction.payment_method', 'like', '%' . $m_search_subscription_transaction . '%');
                })
                ->count();

            if ($perpage >= 1) {
                $pages = ceil($total/$perpage);
            }

            if (!empty($request->input('datatable')['pagination']['page']) && is_numeric($request->input('datatable')['pagination']['page']) && $request->input('datatable')['pagination']['page'] >= 1) {
                $page = $request->input('datatable')['pagination']['page'];

                if ($page*$perpage > $total) {
                    $page = $pages;
                }
            }

            return response()->json([
                'meta' => [
                    'page' => $page,
                    'pages' => $pages,
                    'perpage' => $perpage,
                    'total' => $total,
                    'sort' => $sort,
                    'field' => $field,
                ],
                'data' => DB::table('subs_transaction')
                    ->selectRaw('subs_transaction.id, subs_transaction.date, subs_transaction.subs_end, subs_transaction.payment_method, subs_transaction.payment_status, owner.id as owner_id, account.username as owner, subs_plan.id as subscription_plan_id, subs_plan.name as subscription_plan')
                    ->join('owner', 'subs_transaction.owner_id', 'owner.id')
                    ->join('account', 'owner.id', 'account.child_id')
                    ->where('account.child_type', 'Owner')
                    ->join('subs_plan', 'subs_transaction.subs_plan_id', 'subs_plan.id')
                    ->where(function($query) use ($owner_id) {
                        if ($owner_id != '') {
                            $query->where('owner.id', $owner_id);
                        }
                    })
                    ->where(function($query) use ($subscription_plan_id) {
                        if ($subscription_plan_id != '') {
                            $query->where('subs_plan.id', $subscription_plan_id);
                        }
                    })
                    ->where(function($query) use ($m_search_subscription_transaction) {
                        $query->where('subs_transaction.payment_method', 'like', '%' . $m_search_subscription_transaction . '%');
                    })
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }

    public function bulkDelete(Request $request) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'ids' => ['array']
            ]);

            foreach($request->ids as $id) {
                $subscription_transaction = \App\SubscriptionTransaction::find($id);
                
                if ($subscription_transaction === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }

                if (\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($subscription_transaction->subs_end)) < 0) {
                    return response()->json(['error' => 'Data is outdated.'], 403);
                }
            }

            foreach($request->ids as $id) {
                $subscription_transaction = \App\SubscriptionTransaction::find($id);

                $last_subscription_transactions = $subscription_transaction->owner()->first()->transactions()->where('subs_end', '>', $subscription_transaction->subs_end)->get();
                
                foreach ($last_subscription_transactions as $last_subscription_transaction) {
                    $last_subscription_transaction->subs_end = \Carbon\Carbon::parse($last_subscription_transaction->subs_end)->subDays($subscription_transaction->plan()->first()->duration_day);
                    $last_subscription_transaction->save();
                }
                
                try {
                    if ($subscription_transaction !== NULL) {
                        $subscription_transaction->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }

            return response()->json(['success' => 'Successfully deleted subscription plans.'], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }

// ===============================================================================================================
// ===============================================================================================================

    /**
     * function to handle http post notification from midtrans.
     * if transaction happens, or change in transaction status
     * 
     * @param $request
     * @return Illuminate\Http\Response
     */
    public function MidtrNotif(Request $request){
        $status = $request->status_code;

        // if status are 'success', 'pending', fraud challenge, or cancelled by payment gateway
        if ($status == '200'||$status == '201'||$status == '202'){
            
            //challenge status, confirm with payment gateway if the data was correct
            $challenge = \App\Butternut\SnapMidtrans::getOrderStatus($request->transaction_id);
            $owner = \App\Owner::find($request->custom_field1);
            $subs_plan = \App\SubscriptionPlan::find($request->custom_field2);

            //if challenge success, the owner exist and subscription plan exist
            if (($challenge->status_code == $status) && ( $owner !== NULL ) &&( $subs_plan !== NULL )) {
                $now = \Carbon\Carbon::now();

                // check if transaction recorded before
                if (\App\SubscriptionTransaction::where('order_id',$request->order_id)->count() > 0) {
                    $transaction = \App\SubscriptionTransaction::where('order_id',$request->order_id)->first();
                } else {
                    $transaction = new \App\SubscriptionTransaction;
                    $transaction->date = \Carbon\Carbon::now();
                    $transaction->subs_end = $now;
                    $transaction->order_id = $request->order_id;
                    $transaction->owner()->associate($owner);
                    $transaction->plan()->associate($subs_plan);
                    $transaction->payment_method = $request->payment_type;
                };

                $last_transaction=$owner->transactions->where('subs_plan_id', $subs_plan->id)->sortByDesc('subs_end');        
                // if status are success and fraud_status are accept
                if (($status=='200')&&($request->fraud_status=='accept' || $request->fraudstatus===NULL)) {
                    $transStat = $request->transaction_status;
                    
                    // if status are capture and not settlement of credit card 
                    if (($transStat=='settlement' && $transaction->payment_status!='capture')||$transStat=='capture') {
                        // check if owner still has remaining days in his subscription
                        if (($last_transaction->count()>0)&&(\Carbon\Carbon::parse($last_transaction->first()->subs_end)->gt($now)) {
                            $transaction->subs_end = \Carbon\Carbon::parse($last_transaction->first()->subs_end)->addDays($subs_plan->duration_day);
                        } else {
                            $transaction->subs_end = $now->addDays($subs_plan->duration_day);
                        };
                    };
                    $msg = 'subscription added';

                  //if status are canceled and owner is considered subscribed  
                } elseif (\Carbon\Carbon::parse($transaction->subs_end)->gt($now)) {
                    if (($last_transaction->count()>0)&&(\Carbon\Carbon::parse($last_transaction->first()->subs_end)->gt($now)) {
                        $transaction->subs_end = $last_transaction->first()->subs_end;
                    } else {
                        $transaction->subs_end = $now;
                    };
                    $msg = 'subscription cancelled';
                } else {
                    $msg = $request->transaction_status;
                }

                // if fraud_status is challenged admin must go to payment gateway control panel (midtrans.com) to finish the transaction
                $transaction->payment_status = ($request->fraud_status != 'challenge')? $request->transaction_status : "CHALLENGE!";
                $transaction->save();
                return response()->json(['status_code'=>$status,'status_message'=>$msg]);
            } else {
                return response()->json(['status_code'=>$status,'status_message'=>'challenge failed !','param'=>$subs_plan]);
            };
        } else {
            return response()->json(['status_code'=>'200','param'=>$request]);
        };
    }
}
