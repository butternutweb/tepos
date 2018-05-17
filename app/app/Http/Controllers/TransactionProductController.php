<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($transaction_id)
    {
        return redirect()->route('transaction.edit', $transaction_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($transaction_id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->route('transaction.edit', $transaction_id)->with('error', 'Need subscribe to access this feature.');
            }
        
            $stores = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->whereIn('staff.store_id', $stores)->first();

            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Need higher subscription plan to access this product.');
            }

            return view('pages.transaction_product.create')
            ->with('products', \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $transaction->staff()->first()->store()->first()->owner_id)->whereNotIn('product.id', $transaction->products()->pluck('product_id')->toArray())->join('store_product', 'product.id', 'store_product.product_id')->get())
            ->with('transaction_id', $transaction_id);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $transaction = \App\Transaction::find($transaction_id);

            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            return view('pages.transaction_product.create')
            ->with('products', \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $transaction->staff()->first()->store()->first()->owner_id)->whereNotIn('product.id', $transaction->products()->pluck('product_id')->toArray())->join('store_product', 'product.id', 'store_product.product_id')->get())
            ->with('transaction_id', $transaction_id);
        }

        return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $transaction_id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'qty' => ['required', 'integer', 'min:0'],
                'note' => ['nullable', 'string', 'max:50'],
                'product_id' => ['required', 'integer'],
            ], [
                'product_id.required' => 'The product field is required.',
                'product_id.integer' => 'The product must be an integer.'
            ]);

            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }
        
            $stores = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->whereIn('staff.store_id', $stores)->first();

            if ($transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this product.');
            }

            $product = \App\Product::where('product.id', $request->product_id)->join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $transaction->staff()->first()->store()->first()->owner_id)->join('store_product', 'product.id', 'store_product.product_id')->first();
            
            if ($product === NULL) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Data does not exist.');
            }

            if ($transaction->products()->wherePivot('product_id', $product->id)->exists()) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Product is already existed.');
            }

            try {
                $transaction->products()->attach($product, ['qty' => $request->qty, 'note' => $request->note]);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.edit', $transaction_id))->with('success', 'Successfully created product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'qty' => ['required', 'integer', 'min:0'],
                'note' => ['nullable', 'string', 'max:50'],
                'product_id' => ['required', 'integer'],
            ], [
                'product_id.required' => 'The product field is required.',
                'product_id.integer' => 'The product must be an integer.'
            ]);

            $transaction = \App\Transaction::find($transaction_id);
            
            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $product = \App\Product::where('product.id', $request->product_id)->join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $transaction->staff()->first()->store()->first()->owner_id)->join('store_product', 'product.id', 'store_product.product_id')->first();
            
            if ($product === NULL) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Data does not exist.');
            }

            if ($transaction->products()->wherePivot('product_id', $product->id)->exists()) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Product is already existed.');
            }

            try {
                $transaction->products()->attach($product, ['qty' => $request->qty, 'note' => $request->note]);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.edit', $transaction_id))->with('success', 'Successfully created product.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($transaction_id, $id)
    {
        return redirect()->route('transaction_.product.edit', [$transaction_id, $id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($transaction_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();

            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->route('transaction.edit', $transaction_id)->with('error', 'Need subscribe to access this feature.');
            }
        
            $stores = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->whereIn('staff.store_id', $stores)->first();

            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Need higher subscription plan to access this product.');
            }

            $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
            
            if ($transaction_product === NULL) {
                return redirect()->route('transaction.edit', $transaction_id)->with('error', 'Data does not exist.');
            }

            return view('pages.transaction_product.edit')
            ->with('products', \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $transaction->staff()->first()->store()->first()->owner_id)->whereNotIn('product.id', $transaction->products()->wherePivot('product_id', '!=', $transaction_product->pivot->product_id)->pluck('product_id')->toArray())->join('store_product', 'product.id', 'store_product.product_id')->get())
            ->with('transaction_product', $transaction_product)
            ->with('transaction_id', $transaction_id);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $transaction = \App\Transaction::find($transaction_id);

            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
            
            if ($transaction_product === NULL) {
                return redirect()->route('transaction.edit', $transaction_id)->with('error', 'Data does not exist.');
            }

            return view('pages.transaction_product.edit')
            ->with('products', \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $transaction->staff()->first()->store()->first()->owner_id)->whereNotIn('product.id', $transaction->products()->wherePivot('product_id', '!=', $transaction_product->pivot->product_id)->pluck('product_id')->toArray())->join('store_product', 'product.id', 'store_product.product_id')->get())
            ->with('transaction_product', $transaction_product)
            ->with('transaction_id', $transaction_id);
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
    public function update(Request $request, $transaction_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'qty' => ['required', 'integer', 'min:0'],
                'note' => ['nullable', 'string', 'max:50'],
                'product_id' => ['required', 'integer'],
            ], [
                'product_id.required' => 'The product field is required.',
                'product_id.integer' => 'The product must be an integer.'
            ]);

            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();

            if ($transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }
        
            $stores = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->whereIn('staff.store_id', $stores)->first();

            if ($transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this product.');
            }

            $transaction_product = $transaction->products()->wherePivot('id', $id)->first();

            if ($transaction_product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $product = \App\Product::where('product.id', $request->product_id)->join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $transaction->staff()->first()->store()->first()->owner_id)->join('store_product', 'product.id', 'store_product.product_id')->first();
               
            if ($product === NULL) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Data does not exist.');
            }

            if ($transaction->products()->wherePivot('product_id', $product->id)->wherePivot('product_id', '!=', $transaction_product->pivot->product_id)->exists()) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Product is already existed.');
            }

            try {
                $transaction->products()->updateExistingPivot($product->id, ['qty' => $request->qty, 'note' => $request->note]);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.edit', $transaction_id))->with('success', 'Successfully edited product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'qty' => ['required', 'integer', 'min:0'],
                'note' => ['nullable', 'string', 'max:50'],
                'product_id' => ['required', 'integer'],
            ], [
                'product_id.required' => 'The product field is required.',
                'product_id.integer' => 'The product must be an integer.'
            ]);

            $transaction = \App\Transaction::find($transaction_id);
            
            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
            
            if ($transaction_product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $product = \App\Product::where('product.id', $request->product_id)->join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $transaction->staff()->first()->store()->first()->owner_id)->join('store_product', 'product.id', 'store_product.product_id')->first();
            
            if ($product === NULL) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Data does not exist.');
            }

            if ($transaction->products()->wherePivot('product_id', $product->id)->wherePivot('product_id', '!=', $transaction_product->pivot->product_id)->exists()) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Product is already existed.');
            }

            try {
                $transaction->products()->updateExistingPivot($product->id, ['qty' => $request->qty, 'note' => $request->note]);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.edit', $transaction_id))->with('success', 'Successfully edited product.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($transaction_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
            
            if ($transaction_product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $transaction->products()->detach($transaction_product->pivot->product_id);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.edit', $transaction_id))->with('success', 'Successfully deleted product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $transaction = \App\Transaction::find($transaction_id);
            
            if ($transaction === NULL) {
                return redirect()->route('transaction.index')->with('error', 'Data does not exist.');
            }

            $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
            
            if ($transaction_product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $transaction->products()->detach($transaction_product->pivot->product_id);
                $transaction->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('transaction.edit', $transaction_id))->with('success', 'Successfully deleted product.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request, $transaction_id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($transaction === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return response()->json(['error' => 'Need subscribe to access this feature.'], 403);
            }
        
            $stores = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->pluck('id')->toArray();

            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->whereIn('staff.store_id', $stores)->first();

            if ($transaction === NULL) {
                return response()->json(['error' => 'Need higher subscription plan to access this product.'], 403);
            }

            $perpage = -1;
            $sort = 'asc';
            $table_name = 'transaction_product';
            $field = 'id';
            $m_search_transaction_product = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_transaction_product'])) {
                $m_search_transaction_product = $request->input('datatable')['query']['m_search_transaction_product'];
            }
    
            $total = \Illuminate\Support\Facades\DB::table('transaction_product')
                ->selectRaw('transaction_product.id, transaction_product.qty, transaction_product.note, product.id as product_id, product.name as product, store_product.selling_price as price')
                ->join('product', 'transaction_product.product_id', 'product.id')
                ->join('store_product', 'product.id', 'store_product.product_id')
                ->join('store', 'store_product.store_id', 'store.id')
                ->join('transaction', 'transaction_product.transaction_id', 'transaction.id')
                ->where('transaction.id', $transaction_id)
                ->join('staff', 'transaction.staff_id', 'staff.id')
                ->join('store as store2', 'staff.store_id', 'store.id')
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
                'data' => \Illuminate\Support\Facades\DB::table('transaction_product')
                    ->selectRaw('transaction_product.id, transaction_product.qty, transaction_product.note, product.id as product_id, product.name as product, store_product.selling_price as price')
                    ->join('product', 'transaction_product.product_id', 'product.id')
                    ->join('store_product', 'product.id', 'store_product.product_id')
                    ->join('store', 'store_product.store_id', 'store.id')
                    ->join('transaction', 'transaction_product.transaction_id', 'transaction.id')
                    ->where('transaction.id', $transaction_id)
                    ->join('staff', 'transaction.staff_id', 'staff.id')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $transaction = \App\Transaction::find($transaction_id);
            
            if ($transaction === NULL) {
                return response()->json(['error', 'Data does not exist.'], 404);
            }

            $perpage = -1;
            $sort = 'asc';
            $table_name = 'transaction_product';
            $field = 'id';
            $m_search_transaction_product = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_transaction_product'])) {
                $m_search_transaction_product = $request->input('datatable')['query']['m_search_transaction_product'];
            }
    
            $total = \Illuminate\Support\Facades\DB::table('transaction_product')
                ->selectRaw('transaction_product.id, transaction_product.qty, transaction_product.note, product.id as product_id, product.name as product, store_product.selling_price as price')
                ->join('product', 'transaction_product.product_id', 'product.id')
                ->join('store_product', 'product.id', 'store_product.product_id')
                ->join('store', 'store_product.store_id', 'store.id')
                ->join('transaction', 'transaction_product.transaction_id', 'transaction.id')
                ->where('transaction.id', $transaction_id)
                ->join('staff', 'transaction.staff_id', 'staff.id')
                ->join('store as store2', 'staff.store_id', 'store.id')
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
                'data' => \Illuminate\Support\Facades\DB::table('transaction_product')
                    ->selectRaw('transaction_product.id, transaction_product.qty, transaction_product.note, product.id as product_id, product.name as product, store_product.selling_price as price')
                    ->join('product', 'transaction_product.product_id', 'product.id')
                    ->join('store_product', 'product.id', 'store_product.product_id')
                    ->join('store', 'store_product.store_id', 'store.id')
                    ->join('transaction', 'transaction_product.transaction_id', 'transaction.id')
                    ->where('transaction.id', $transaction_id)
                    ->join('staff', 'transaction.staff_id', 'staff.id')
                    ->join('store as store2', 'staff.store_id', 'store.id')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        return redirect()->json(['error' => 'Permission denied.'], 403);
    }

    public function bulkDelete(Request $request, $transaction_id) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $transaction = \App\Transaction::where('transaction.id', $transaction_id)->join('staff', 'transaction.staff_id', 'staff.id')->join('store', 'staff.store_id', 'store.id')->select('transaction.*')->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($transaction === NULL) {
                return response()->json(['error', 'Data does not exist.'], 404);
            }

            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
    
                if ($transaction_product === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
    
                try {
                    if ($transaction_product !== NULL) {
                        $transaction->products()->detach($transaction_product->pivot->product_id);
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted products.'], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $transaction = \App\Transaction::find($transaction_id);
            
            if ($transaction === NULL) {
                return response()->json(['error', 'Data does not exist.'], 404);
            }

            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
    
                if ($transaction_product === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $transaction_product = $transaction->products()->wherePivot('id', $id)->first();
    
                try {
                    if ($transaction_product !== NULL) {
                        $transaction->products()->detach($transaction_product->pivot->product_id);
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted products.'], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }
}
