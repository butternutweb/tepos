<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return view('pages.transaction.index')->with('statuses', \App\Status::whereIn('name', ['Completed', 'On Progress'])->get())
            ->with('staffs', \App\Staff::join('store', 'staff.store_id', 'store.id')->select('staff.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->get());
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.transaction.index')->with('statuses', \App\Status::whereIn('name', ['Completed', 'On Progress'])->get())->with('staffs', \App\Staff::all());
        }

        return view('pages.transaction.index')->with('statuses', \App\Status::whereIn('name', ['Completed', 'On Progress'])->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return redirect()->route('transaction.index')->with('error', 'You are not allowed to see this page.');
        }
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.transaction.create')->with('statuses', \App\Status::whereIn('name', ['Completed', 'On Progress'])->get())->with('staffs', \App\Staff::all());
        }

        $owner = \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->owner()->first();

        $last_subscription_transaction = $owner->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

        if ($last_subscription_transaction === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Need subscribe to access this feature.');
        }

        $stores = \App\Store::where('owner_id', $owner->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

        if (!in_array(\Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->id, $stores)) {
            return redirect()->route('transaction.index')->with('error', 'Need higher subscription plan to access this transaction.');
        }

        if ($request->category === NULL || $request->sub_category === NULL) {
            $categories = \App\Category::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->owner_id)->get();

            if ($request->sub_category === NULL && count($categories) > 0 && count($categories[0]->subCategories()->get()) > 0) {
                return redirect()->route('transaction.create', ['category' => $categories[0]->id, 'sub_category' => $categories[0]->subCategories()->get()[0]]);
            }

            $request->session()->flash('error_message', 'Please contact admin to add products.');
            return view('pages.transaction.create-staff')->with('categories', $categories);
        }

        if (strpos($request->header('referer'), 'create') === false) {
            foreach ($request->session()->all() as $key => $value) {
                if (strpos($key, '_product_id_') === 0) {
                    $request->session()->forget($key);
                }
            }
        }

        $categories = \App\Category::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->owner_id)->get();

        $category = $categories->where('id', $request->category)->first();

        if ($category === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Permission denied.');
        }

        $subCategory = $category->subCategories()->where('id', $request->sub_category)->first();

        if ($subCategory === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Permission denied.');
        }

        $products = $subCategory->products()->join('store_product', 'product.id', 'store_product.product_id')->select('product.*','store_product.selling_price')->get();
        
        if (count($products) <= 0) {
            $request->session()->flash('error_message', 'Please contact admin to add products.');
            return view('pages.transaction.create-staff')->with('categories', $categories);
        }

        return view('pages.transaction.create-staff')->with('categories', $categories)->with('products', $products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'date' => ['required', 'date'],
                'note' => ['max:50'],
                'status_id' => ['required', 'integer'],
                'staff_id' => ['required', 'integer'],
            ], [
               'status_id.required' => 'The status field is required.',
               'status_id.integer' => 'The status must be an integer.',
               'staff_id.required' => 'The staff field is required.',
               'staff_id.integer' => 'The staff must be an integer.'
            ]);
    
            $status = \App\Status::where('id', $request->status_id)->whereIn('name', ['Completed', 'On Progress'])->first();
    
            if ($status === NULL) {
                return redirect()->back()->withInput($request->except('status_id'))->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }
            
            $stores = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();
    
            $staff = \App\Staff::join('store', 'staff.store_id', 'store.id')->select('staff.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->where('staff.id', $request->staff_id)->whereIn('staff.store_id', $stores)->first();
            
            if ($staff === NULL) {
                return redirect()->back()->withInput($request->except('staff_id'))->with('error', 'Data does not exist.');
            }
    
            try {
                $transaction = new \App\Transaction;
    
                $last_invoice = \App\Transaction::join('staff', 'transaction.staff_id', 'staff.id')->selectRaw('convert(substring_index(transaction.invoice, "-", -1), decimal) as invoice')->where('staff.store_id', $staff->store()->first()->id)->orderBy('invoice', 'desc')->first();
    
                if ($last_invoice === NULL) {
                    $transaction->invoice = $staff->store()->first()->id . '-1';
                }
                else {
                    $transaction->invoice = $staff->store()->first()->id . '-' . ($last_invoice->invoice + 1);
                }
    
                $transaction->date = $request->date;
                $transaction->note = $request->note;
                $transaction->staff()->associate($staff);
                $transaction->status()->associate($status);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.index'))->with('success', 'Successfully created transaction.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'date' => ['required', 'date'],
                'note' => ['max:50'],
                'status_id' => ['required', 'integer'],
                'staff_id' => ['required', 'integer'],
            ], [
               'status_id.required' => 'The status field is required.',
               'status_id.integer' => 'The status must be an integer.',
               'staff_id.required' => 'The staff field is required.',
               'staff_id.integer' => 'The staff must be an integer.'
            ]);
    
            $status = \App\Status::where('id', $request->status_id)->whereIn('name', ['Completed', 'On Progress'])->first();
    
            if ($status === NULL) {
                return redirect()->back()->withInput($request->except('status_id'))->with('error', 'Data does not exist.');
            }
    
            $staff = \App\Staff::find($request->staff_id);
            
            if ($staff === NULL) {
                return redirect()->back()->withInput($request->except('staff_id'))->with('error', 'Data does not exist.');
            }
    
            try {
                $transaction = new \App\Transaction;
    
                $last_invoice = \App\Transaction::join('staff', 'transaction.staff_id', 'staff.id')->selectRaw('convert(substring_index(transaction.invoice, "-", -1), decimal) as invoice')->where('staff.store_id', $staff->store()->first()->id)->orderBy('invoice', 'desc')->first();
    
                if ($last_invoice === NULL) {
                    $transaction->invoice = $staff->store()->first()->id . '-1';
                }
                else {
                    $transaction->invoice = $staff->store()->first()->id . '-' . ($last_invoice->invoice + 1);
                }
    
                $transaction->date = $request->date;
                $transaction->note = $request->note;
                $transaction->staff()->associate($staff);
                $transaction->status()->associate($status);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.index'))->with('success', 'Successfully created transaction.');
        }
        
        $this->validate($request, [
            'note' => ['max:50'],
        ]);

        $owner = \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->owner()->first();

        $last_subscription_transaction = $owner->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

        if ($last_subscription_transaction === NULL) {
            return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
        }

        $stores = \App\Store::where('owner_id', $owner->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

        if (!in_array(\Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->id, $stores)) {
            return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this transaction.');
        }

        $status = \App\Status::where('name', 'On Progress')->first();

        if ($status === NULL) {
            return redirect()->back()->withInput($request->except('status_id'))->with('error', 'Data does not exist.');
        }

        try {
            $transaction = new \App\Transaction;

            $last_invoice = \App\Transaction::join('staff', 'transaction.staff_id', 'staff.id')->selectRaw('convert(substring_index(transaction.invoice, "-", -1), decimal) as invoice')->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->id)->orderBy('invoice', 'desc')->first();

            if ($last_invoice === NULL) {
                $transaction->invoice = \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->id . '-1';
            }
            else {
                $transaction->invoice = \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->id . '-' . ($last_invoice->invoice + 1);
            }

            $transaction->date = \Carbon\Carbon::now();
            $transaction->note = $request->note;
            $transaction->staff()->associate(\Illuminate\Support\Facades\Auth::user()->child()->first());
            $transaction->status()->associate($status);
            $transaction->save();

            foreach ($request->session()->all() as $key => $value) {
                if (strpos($key, '_product_id_') === 0) {
                    $product_id = explode('_product_id_', $key)[1];
                    $product_number = $request->session()->pull($key);

                    $product = $transaction->products()->join('store_product', 'product.id', 'store_product.product_id')->select('product.*')->where('product.id', $product_id)->first();

                    if ($product !== NULL) {
                        if ($product_number <= 0) {
                            $transaction->products()->detach($product_id);
                            continue;
                        }

                        $transaction->products()->updateExistingPivot($product_id, ['qty' => $product_number]);
                        continue;
                    }

                    $transaction->products()->attach($product_id, ['qty' => $product_number]);
                }
            }
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('transaction.index'))->with('success', 'Successfully created transaction.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('transaction.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();

            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Need subscribe to access this feature.');
            }
        
            $stores = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

            $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->whereIn('staff.store_id', $stores)->first();

            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Need higher subscription plan to access this transaction.');
            }

            $total = \Illuminate\Support\Facades\DB::table('transaction_product')
                ->selectRaw('sum(store_product.selling_price * transaction_product.qty) as total')
                ->join('product', 'transaction_product.product_id', 'product.id')
                ->join('store_product', 'product.id', 'store_product.product_id')
                ->where('store_product.store_id', $transaction->staff()->first()->store()->first()->id)
                ->where('transaction_product.transaction_id', $transaction->id)->first();

            return view('pages.transaction.edit')->with('statuses', \App\Status::whereIn('name', ['Completed', 'On Progress'])->get())
            ->with('staffs', \App\Staff::join('store', 'staff.store_id', 'store.id')->select('staff.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->get())->with('transaction', $transaction)->with('total', $total->total);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $transaction = \App\Transaction::find($id);
            
            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $total = \Illuminate\Support\Facades\DB::table('transaction_product')
                ->selectRaw('sum(store_product.selling_price * transaction_product.qty) as total')
                ->join('product', 'transaction_product.product_id', 'product.id')
                ->join('store_product', 'product.id', 'store_product.product_id')
                ->where('store_product.store_id', $transaction->staff()->first()->store()->first()->id)
                ->where('transaction_product.transaction_id', $transaction->id)->first();

            return view('pages.transaction.edit')->with('statuses', \App\Status::whereIn('name', ['Completed', 'On Progress'])->get())->with('staffs', \App\Staff::all())->with('transaction', $transaction)->with('total', $total->total);
        }

        $transaction = \App\Transaction::join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.*')->where('transaction.id', $id)->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)->first();
        
        if ($transaction === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
        }

        $owner = \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->owner()->first();

        $last_subscription_transaction = $owner->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

        if ($last_subscription_transaction === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Need subscribe to access this feature.');
        }

        $stores = \App\Store::where('owner_id', $owner->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

        if (!in_array(\Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->id, $stores)) {
            return redirect()->route('transaction.index')->with('error', 'Need higher subscription plan to access this transaction.');
        }

        if ($request->category === NULL || $request->sub_category === NULL) {
            $categories = \App\Category::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->owner_id)->get();

            if ($request->sub_category === NULL && count($categories) > 0 && count($categories[0]->subCategories()->get()) > 0) {
                return redirect()->route('transaction.edit', ['id' => $id, 'category' => $categories[0]->id, 'sub_category' => $categories[0]->subCategories()->get()[0]]);
            }

            $request->session()->flash('error_message', 'Please contact admin to add products.');
            return view('pages.transaction.edit-staff')->with('transaction', $transaction)->with('categories', $categories);
        }

        if (strpos($request->header('referer'), 'edit') === false) {
            foreach ($request->session()->all() as $key => $value) {
                if (strpos($key, '_product_id_') === 0) {
                    $request->session()->forget($key);
                }
            }
        }

        $categories = \App\Category::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->owner_id)->get();

        $category = $categories->where('id', $request->category)->first();

        if ($category === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Permission denied.');
        }

        $subCategory = $category->subCategories()->where('id', $request->sub_category)->first();

        if ($subCategory === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Permission denied.');
        }

        $products = $subCategory->products()->join('store_product', 'product.id', 'store_product.product_id')->select('product.*','store_product.selling_price')->get();
        
        if (count($products) <= 0) {
            $request->session()->flash('error_message', 'Please contact admin to add products.');
            return view('pages.transaction.edit-staff')->with('categories', $categories);
        }

        return view('pages.transaction.edit-staff')->with('categories', $categories)->with('products', $products)->with('transaction', $transaction)->with('id',$id);
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'date' => ['required', 'date'],
                'note' => ['max:50'],
                'status_id' => ['required', 'integer'],
                'staff_id' => ['required', 'integer'],
            ], [
               'status_id.required' => 'The status field is required.',
               'status_id.integer' => 'The status must be an integer.',
               'staff_id.required' => 'The staff field is required.',
               'staff_id.integer' => 'The staff must be an integer.'
            ]);

            $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }
        
            $stores = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

            $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->whereIn('staff.store_id', $stores)->first();

            if ($transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this transaction.');
            }

            $status = \App\Status::where('id', $request->status_id)->whereIn('name', ['Completed', 'On Progress'])->first();
    
            if ($status === NULL) {
                return redirect()->back()->withInput($request->except('status_id'))->with('error', 'Data does not exist.');
            }
    
            $staff = \App\Staff::join('store', 'staff.store_id', 'store.id')->select('staff.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->where('staff.id', $request->staff_id)->first();
            
            if ($staff === NULL) {
                return redirect()->back()->withInput($request->except('staff_id'))->with('error', 'Data does not exist.');
            }
    
            try {
                if ($staff->store()->first()->id != $transaction->staff()->first()->store()->first()->id) {
                    $last_invoice = \App\Transaction::join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.invoice')->where('staff.store_id', $staff->store()->first()->id)->orderBy('transaction.invoice', 'desc')->first();
                    
                    if ($last_invoice === NULL) {
                        $transaction->invoice = $staff->store()->first()->id . '-1';
                    }
                    else {
                        $transaction->invoice = $staff->store()->first()->id . '-' . ((int) explode('-', $last_invoice->invoice)[1] + 1);
                    }
                }
                
                $transaction->date = $request->date;
                $transaction->note = $request->note;
                $transaction->staff()->associate($staff);
                $transaction->status()->associate($status);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.index'))->with('success', 'Successfully edited transaction.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'date' => ['required', 'date'],
                'note' => ['max:50'],
                'status_id' => ['required', 'integer'],
                'staff_id' => ['required', 'integer'],
            ], [
               'status_id.required' => 'The status field is required.',
               'status_id.integer' => 'The status must be an integer.',
               'staff_id.required' => 'The staff field is required.',
               'staff_id.integer' => 'The staff must be an integer.'
            ]);

            $transaction = \App\Transaction::find($id);

            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $status = \App\Status::where('id', $request->status_id)->whereIn('name', ['Completed', 'On Progress'])->first();
    
            if ($status === NULL) {
                return redirect()->back()->withInput($request->except('status_id'))->with('error', 'Data does not exist.');
            }
    
            $staff = \App\Staff::find($request->staff_id);
            
            if ($staff === NULL) {
                return redirect()->back()->withInput($request->except('staff_id'))->with('error', 'Data does not exist.');
            }
    
            try {
                if ($staff->store()->first()->id != $transaction->staff()->first()->store()->first()->id) {
                    $last_invoice = \App\Transaction::join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.invoice')->where('staff.store_id', $staff->store()->first()->id)->orderBy('transaction.invoice', 'desc')->first();
        
                    if ($last_invoice === NULL) {
                        $transaction->invoice = $staff->store()->first()->id . '-1';
                    }
                    else {
                        $transaction->invoice = $staff->store()->first()->id . '-' . ((int) explode('-', $last_invoice->invoice)[1] + 1);
                    }
                }
    
                $transaction->date = $request->date;
                $transaction->note = $request->note;
                $transaction->staff()->associate($staff);
                $transaction->status()->associate($status);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.index'))->with('success', 'Successfully edited transaction.');
        }
        
        $this->validate($request, [
            'note' => ['max:50'],
        ]);

        $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.*')->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)->first();
        
        if ($transaction === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
        }

        $owner = \Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->owner()->first();

        $last_subscription_transaction = $owner->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

        if ($last_subscription_transaction === NULL) {
            return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
        }

        $stores = \App\Store::where('owner_id', $owner->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

        if (!in_array(\Illuminate\Support\Facades\Auth::user()->child()->first()->store()->first()->id, $stores)) {
            return redirect()->route('transaction.index')->with('error', 'Need higher subscription plan to access this transaction.');
        }

        try {
            $transaction->note = $request->note;
            $transaction->save();

            foreach ($request->session()->all() as $key => $value) {
                if (strpos($key, '_product_id_') === 0) {
                    $product_id = explode('_product_id_', $key)[1];
                    $product_number = $request->session()->pull($key);

                    $product = $transaction->products()->join('store_product', 'product.id', 'store_product.product_id')->select('product.*')->where('product.id', $product_id)->first();


                    if ($product !== NULL) {
                        if ($product_number <= 0) {
                            $transaction->products()->detach($product_id);
                            continue;
                        }

                        $transaction->products()->updateExistingPivot($product_id, ['qty' => $product_number]);
                        continue;
                    }

                    $transaction->products()->attach($product_id, ['qty' => $product_number]);
                }
            }
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('transaction.index'))->with('success', 'Successfully edited transaction.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $transaction->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.index'))->with('success', 'Successfully deleted transaction.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $transaction = \App\Transaction::find($id);
            
            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }
    
            try {
                $transaction->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.index'))->with('success', 'Successfully deleted transaction.');
        }

        $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.*')->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)->first();        
        
        if ($transaction === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
        }

        try {
            $transaction->delete();
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('transaction.index'))->with('success', 'Successfully deleted transaction.');
    }

    public function indexAjax(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'transaction';
            $field = 'id';
            $m_search_transaction = '';
            $status_id = '';
            $staff_id = '';
            $total = 0;
            $pages = 1;
            $page = 1;
    
            if (!empty($request->input('datatable')['pagination']['perpage']) && is_numeric($request->input('datatable')['pagination']['perpage']) && $request->input('datatable')['pagination']['perpage'] >= 1) {
                $perpage = $request->input('datatable')['pagination']['perpage'];
            }
    
            if (!empty($request->input('datatable')['sort']['sort']) && ($request->input('datatable')['sort']['sort'] == 'asc' || $request->input('datatable')['sort']['sort'] == 'desc')) {
                $sort = $request->input('datatable')['sort']['sort'];
            }
    
            if (!empty($request->input('datatable')['sort']['field']) && !\Illuminate\Support\Facades\Schema::hasColumn($table_name, $request->input('datatable')['sort']['field'])) {
                $field = $request->input('datatable')['sort']['field'];
            }
    
            if (!empty($request->input('datatable')['query']['m_search_transaction'])) {
                $m_search_transaction = $request->input('datatable')['query']['m_search_transaction'];
            }
    
            if (!empty($request->input('datatable')['query']['status_id'])) {
                $status_id = $request->input('datatable')['query']['status_id'];
            }

            if (!empty($request->input('datatable')['query']['staff_id'])) {
                $staff_id = $request->input('datatable')['query']['staff_id'];
            }
    
            $total = \Illuminate\Support\Facades\DB::table('transaction')
                ->selectRaw('transaction.id, transaction.invoice, transaction.date, transaction.note, status.id as status_id, status.name as status, staff.id as staff_id, account.username as staff, store.id as store_id, store.name as store')
                ->join('status', 'transaction.status_id', 'status.id')
                ->join('staff', 'transaction.staff_id', 'staff.id')
                ->join('store', 'staff.store_id', 'store.id')
                ->join('account', 'staff.id', 'account.child_id')
                ->where('account.child_type', 'Staff')
                ->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where(function($query) use ($staff_id) {
                    if ($staff_id != '') {
                        $query->where('staff.id', $staff_id);
                    }
                })
                ->where(function($query) use ($status_id) {
                    if ($status_id != '') {
                        $query->where('status.id', $status_id);
                    }
                })
                ->where(function($query) use ($m_search_transaction) {
                    if ($m_search_transaction != '') {
                        $query->where('transaction.invoice', 'like', '%' . $m_search_transaction . '%');
                        $query->orWhere('transaction.note', 'like', '%' . $m_search_transaction . '%');
                    }
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
                'data' => \Illuminate\Support\Facades\DB::table('transaction')
                    ->selectRaw('transaction.id, transaction.invoice, transaction.date, transaction.note, status.id as status_id, status.name as status, staff.id as staff_id, account.username as staff, store.id as store_id, store.name as store')
                    ->join('status', 'transaction.status_id', 'status.id')
                    ->join('staff', 'transaction.staff_id', 'staff.id')
                    ->join('store', 'staff.store_id', 'store.id')
                    ->join('account', 'staff.id', 'account.child_id')
                    ->where('account.child_type', 'Staff')
                    ->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                    ->where(function($query) use ($staff_id) {
                        if ($staff_id != '') {
                            $query->where('staff.id', $staff_id);
                        }
                    })
                    ->where(function($query) use ($status_id) {
                        if ($status_id != '') {
                            $query->where('status.id', $status_id);
                        }
                    })
                    ->where(function($query) use ($m_search_transaction) {
                        if ($m_search_transaction != '') {
                            $query->where('transaction.invoice', 'like', '%' . $m_search_transaction . '%');
                            $query->orWhere('transaction.note', 'like', '%' . $m_search_transaction . '%');
                        }
                    })
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'transaction';
            $field = 'id';
            $m_search_transaction = '';
            $status_id = '';
            $staff_id = '';
            $total = 0;
            $pages = 1;
            $page = 1;

            if (!empty($request->input('datatable')['pagination']['perpage']) && is_numeric($request->input('datatable')['pagination']['perpage']) && $request->input('datatable')['pagination']['perpage'] >= 1) {
                $perpage = $request->input('datatable')['pagination']['perpage'];
            }

            if (!empty($request->input('datatable')['sort']['sort']) && ($request->input('datatable')['sort']['sort'] == 'asc' || $request->input('datatable')['sort']['sort'] == 'desc')) {
                $sort = $request->input('datatable')['sort']['sort'];
            }

            if (!empty($request->input('datatable')['sort']['field']) && !\Illuminate\Support\Facades\Schema::hasColumn($table_name, $request->input('datatable')['sort']['field'])) {
                $field = $request->input('datatable')['sort']['field'];
            }

            if (!empty($request->input('datatable')['query']['m_search_transaction'])) {
                $m_search_transaction = $request->input('datatable')['query']['m_search_transaction'];
            }

            if (!empty($request->input('datatable')['query']['status_id'])) {
                $status_id = $request->input('datatable')['query']['status_id'];
            }

            if (!empty($request->input('datatable')['query']['staff_id'])) {
                $staff_id = $request->input('datatable')['query']['staff_id'];
            }

            $total = \Illuminate\Support\Facades\DB::table('transaction')
                ->selectRaw('transaction.id, transaction.invoice, transaction.date, transaction.note, status.id as status_id, status.name as status, staff.id as staff_id, account.username as staff, store.id as store_id, store.name as store')
                ->join('status', 'transaction.status_id', 'status.id')
                ->join('staff', 'transaction.staff_id', 'staff.id')
                ->join('account', 'staff.id', 'account.child_id')
                ->where('account.child_type', 'Staff')
                ->join('store', 'staff.store_id', 'store.id')
                ->where(function($query) use ($staff_id) {
                    if ($staff_id != '') {
                        $query->where('staff.id', $staff_id);
                    }
                })
                ->where(function($query) use ($status_id) {
                    if ($status_id != '') {
                        $query->where('status.id', $status_id);
                    }
                })
                ->where(function($query) use ($m_search_transaction) {
                    if ($m_search_transaction != '') {
                        $query->where('transaction.invoice', 'like', '%' . $m_search_transaction . '%');
                        $query->orWhere('transaction.note', 'like', '%' . $m_search_transaction . '%');
                    }
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
                'data' => \Illuminate\Support\Facades\DB::table('transaction')
                    ->selectRaw('transaction.id, transaction.invoice, transaction.date, transaction.note, status.id as status_id, status.name as status, staff.id as staff_id, account.username as staff, store.id as store_id, store.name as store')
                    ->join('status', 'transaction.status_id', 'status.id')
                    ->join('staff', 'transaction.staff_id', 'staff.id')
                    ->join('account', 'staff.id', 'account.child_id')
                    ->where('account.child_type', 'Staff')
                    ->join('store', 'staff.store_id', 'store.id')
                    ->where(function($query) use ($staff_id) {
                        if ($staff_id != '') {
                            $query->where('staff.id', $staff_id);
                        }
                    })
                    ->where(function($query) use ($status_id) {
                        if ($status_id != '') {
                            $query->where('status.id', $status_id);
                        }
                    })
                    ->where(function($query) use ($m_search_transaction) {
                        if ($m_search_transaction != '') {
                            $query->where('transaction.invoice', 'like', '%' . $m_search_transaction . '%');
                            $query->orWhere('transaction.note', 'like', '%' . $m_search_transaction . '%');
                        }
                    })
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        $perpage = -1;
        $sort = 'asc';
        $table_name = 'transaction';
        $field = 'id';
        $m_search_transaction = '';
        $status_id = '';
        $total = 0;
        $pages = 1;
        $page = 1;

        if (!empty($request->input('datatable')['pagination']['perpage']) && is_numeric($request->input('datatable')['pagination']['perpage']) && $request->input('datatable')['pagination']['perpage'] >= 1) {
            $perpage = $request->input('datatable')['pagination']['perpage'];
        }

        if (!empty($request->input('datatable')['sort']['sort']) && ($request->input('datatable')['sort']['sort'] == 'asc' || $request->input('datatable')['sort']['sort'] == 'desc')) {
            $sort = $request->input('datatable')['sort']['sort'];
        }

        if (!empty($request->input('datatable')['sort']['field']) && !\Illuminate\Support\Facades\Schema::hasColumn($table_name, $request->input('datatable')['sort']['field'])) {
            $field = $request->input('datatable')['sort']['field'];
        }

        if (!empty($request->input('datatable')['query']['m_search_transaction'])) {
            $m_search_transaction = $request->input('datatable')['query']['m_search_transaction'];
        }

        if (!empty($request->input('datatable')['query']['status_id'])) {
            $status_id = $request->input('datatable')['query']['status_id'];
        }

        $total = \Illuminate\Support\Facades\DB::table('transaction')
            ->selectRaw('transaction.id, transaction.invoice, transaction.date, transaction.note, status.id as status_id, status.name as status')
            ->join('status', 'transaction.status_id', 'status.id')
            ->join('staff', 'transaction.staff_id', 'staff.id')
            ->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)
            ->where(function($query) use ($status_id) {
                if ($status_id != '') {
                    $query->where('staff.id', $status_id);
                }
            })
            ->where(function($query) use ($m_search_transaction) {
                if ($m_search_transaction != '') {
                    $query->where('transaction.invoice', 'like', '%' . $m_search_transaction . '%');
                    $query->orWhere('transaction.note', 'like', '%' . $m_search_transaction . '%');
                }
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
            'data' => \Illuminate\Support\Facades\DB::table('transaction')
                ->selectRaw('transaction.id, transaction.invoice, transaction.date, transaction.note, status.id as status_id, status.name as status')
                ->join('status', 'transaction.status_id', 'status.id')
                ->join('staff', 'transaction.staff_id', 'staff.id')
                ->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)
                ->where(function($query) use ($status_id) {
                    if ($status_id != '') {
                        $query->where('staff.id', $status_id);
                    }
                })
                ->where(function($query) use ($m_search_transaction) {
                    if ($m_search_transaction != '') {
                        $query->where('transaction.invoice', 'like', '%' . $m_search_transaction . '%');
                        $query->orWhere('transaction.note', 'like', '%' . $m_search_transaction . '%');
                    }
                })
                ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
        ], 200);
    }

    public function bulkDelete(Request $request) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
    
                if ($transaction === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
    
                try {
                    if ($transaction !== NULL) {
                        $transaction->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted transactions.'], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'ids' => ['array']
            ]);

            foreach($request->ids as $id) {
                $transaction = \App\Transaction::find($id);
                
                if ($transaction === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }

            foreach($request->ids as $id) {
                $transaction = \App\Transaction::find($id);

                try {
                    if ($transaction !== NULL) {
                        $transaction->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }

            return response()->json(['success' => 'Successfully deleted transactions.'], 200);
        }

        $this->validate($request, [
            'ids' => ['array']
        ]);

        foreach($request->ids as $id) {
            $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.*')->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)->first();
            
            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }
        }

        foreach($request->ids as $id) {
            $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.*')->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)->first();

            try {
                if ($transaction !== NULL) {
                    $transaction->delete();
                }
            }
            catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['error' => 'Something wrong with the database.'], 500);
            }
        }

        return response()->json(['success' => 'Successfully deleted transactions.'], 200);
    }

    public function addProduct(Request $request) {
        $request->session()->put('_product_id_' . $request->product_id, $request->product_number);
        return response()->json(['success' => 'Successfully changed product.', 'data' => $request->product_id . ' ' . $request->product_number, 'session' => $request->session()->get('_product_id_' . $request->product_id), 'all_session' => $request->session()->all()], 200);
    }

    public function getCheckout(Request $request, $id) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return redirect()->route('transaction.index')->with('error', 'Permission denied.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return redirect()->route('transaction.index')->with('error', 'Permission denied.');
        }

        $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.*')->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)->first();        
        
        if ($transaction === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
        }

        $total = \Illuminate\Support\Facades\DB::table('transaction_product')
                ->selectRaw('sum(store_product.selling_price * transaction_product.qty) as total')
                ->join('product', 'transaction_product.product_id', 'product.id')
                ->join('store_product', 'product.id', 'store_product.product_id')
                ->where('store_product.store_id', $transaction->staff()->first()->store()->first()->id)
                ->where('transaction_product.transaction_id', $transaction->id)->first();

        return view('pages.transaction.checkout')->with('transaction', $transaction)->with('total', $total->total);
    }

    public function doCheckout(Request $request, $id) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {    
            return redirect()->intended(route('transaction.index'))->with('error', 'Permission denied.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return redirect()->intended(route('transaction.index'))->with('error', 'Permission denied.');
        }

        $transaction = \App\Transaction::where('transaction.id', $id)->join('staff', 'transaction.staff_id', 'staff.id')->select('transaction.*')->where('staff.store_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->store_id)->first();        
        
        if ($transaction === NULL) {
            return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
        }

        $status = \App\Status::where('name', 'Completed')->first();

        if ($status === NULL) {
            return redirect()->back()->withInput($request->except('status_id'))->with('error', 'Data does not exist.');
        }

        try {
            $transaction->status()->associate($status);
            $transaction->save();
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('transaction.index'))->with('success', 'Successfully checked out transaction.');
    }
    
    //==============================================================================================================================
    //==============================================================================================================================

    /**
     * get invoice for a transaction
     */
    public function getInvoice($id) {
        $transaction = \App\Transaction::find($id);
        return view('pages.transaction.invoice',['data'=>$transaction,'amount'=>$transaction->amount()['value']]);

    }
}
