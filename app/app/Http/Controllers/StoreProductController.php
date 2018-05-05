<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StoreProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($store_id)
    {
        return redirect()->route('store.edit', $store_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($store_id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->route('store.edit', $store_id)->with('error', 'Need subscribe to access this feature.');
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $store_id)->first();
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Need higher subscription plan to access this product.');
            }

            return view('pages.store_product.create')->with('products', \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $store->owner_id)->whereNotIn('product.id', $store->products()->pluck('product_id')->toArray())->get())
            ->with('store_id', $store_id);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);

            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }

            return view('pages.store_product.create')->with('products', \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $store->owner_id)->whereNotIn('product.id', $store->products()->pluck('product_id')->toArray())->get())->with('store_id', $store_id);
        }
    
        return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $store_id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'selling_price' => ['required', 'integer', 'min:0'],
                'product_id' => ['required', 'integer']
            ], [
                'selling_price.required' => 'The selling price field is required.',
                'selling_price.integer' => 'The selling price must be an integer.',
                'selling_price.min' => 'The selling price must be at least :min.',
                'product_id.required' => 'The product field is required.',
                'product_id.integer' => 'The product must be an integer.',
            ]);
    
            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $store_id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this product.');
            }
    
            $product = \App\Product::where('product.id', $request->product_id)->join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $store->owner_id)->first();

            if ($product === NULL) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Data does not exist.');
            }

            if ($store->products()->wherePivot('product_id', $product->id)->exists()) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Product is already existed.');
            }
    
            try {
                $store->products()->attach($product, ['selling_price' => $request->selling_price]);
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully created product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'selling_price' => ['required', 'integer', 'min:0'],
                'product_id' => ['required', 'integer']
            ], [
                'selling_price.required' => 'The selling price field is required.',
                'selling_price.integer' => 'The selling price must be an integer.',
                'selling_price.min' => 'The selling price must be at least :min.',
                'product_id.required' => 'The product field is required.',
                'product_id.integer' => 'The product must be an integer.',
            ]);
    
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            $product = \App\Product::where('product.id', $request->product_id)->join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $store->owner_id)->first();
            
            if ($product === NULL) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Data does not exist.');
            }

            if ($store->products()->wherePivot('product_id', $product->id)->exists()) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Product is already existed.');
            }
    
            try {
                $store->products()->attach($product, ['selling_price' => $request->selling_price]);
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                dd($e);
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully created product.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($store_id, $id)
    {
        return redirect()->route('store_.product.edit', [$store_id, $id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($store_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->route('store.edit', $store_id)->with('error', 'Need subscribe to access this feature.');
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $store_id)->first();
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Need higher subscription plan to access this product.');
            }

            $store_product = $store->products()->wherePivot('id', $id)->first();
            
            if ($store_product === NULL) {
                return redirect()->route('store.edit', $store_id)->with('error', 'Data does not exist.');
            }

            return view('pages.store_product.edit')->with('products', \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $store->owner_id)->whereNotIn('product.id', $store->products()->wherePivot('product_id', '!=', $store_product->pivot->product_id)->pluck('product_id')->toArray())->get())
            ->with('store_id', $store_id)
            ->with('store_product', $store_product);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }
            
            $store_product = $store->products()->wherePivot('id', $id)->first();

            if ($store_product === NULL) {
                return redirect()->route('store.edit', $store_id)->with('error', 'Data does not exist.');
            }

            return view('pages.store_product.edit')->with('products', \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $store->owner_id)->whereNotIn('product.id', $store->products()->wherePivot('product_id', '!=', $store_product->pivot->product_id)->pluck('product_id')->toArray())->get())
            ->with('store_id', $store_id)
            ->with('store_product', $store_product);
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
    public function update(Request $request, $store_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'selling_price' => ['required', 'integer', 'min:0'],
                'product_id' => ['required', 'integer']
            ], [
                'selling_price.required' => 'The selling price field is required.',
                'selling_price.integer' => 'The selling price must be an integer.',
                'selling_price.min' => 'The selling price must be at least :min.',
                'product_id.required' => 'The product field is required.',
                'product_id.integer' => 'The product must be an integer.',
            ]);
    
            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $store_id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this product.');
            }

            $store_product = $store->products()->wherePivot('id', $id)->first();
            
            if ($store_product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            $product = \App\Product::where('product.id', $request->product_id)->join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $store->owner_id)->first();
            
            if ($product === NULL) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Data does not exist.');
            }

            if ($store->products()->wherePivot('product_id', $product->id)->wherePivot('product_id', '!=', $store_product->pivot->product_id)->exists()) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Product is already existed.');
            }
    
            try {
                $store->products()->updateExistingPivot($product->id, ['selling_price' => $request->selling_price]);
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully edited product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'selling_price' => ['required', 'integer', 'min:0'],
                'product_id' => ['required', 'integer']
            ], [
                'selling_price.required' => 'The selling price field is required.',
                'selling_price.integer' => 'The selling price must be an integer.',
                'selling_price.min' => 'The selling price must be at least :min.',
                'product_id.required' => 'The product field is required.',
                'product_id.integer' => 'The product must be an integer.',
            ]);
    
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $store_product = $store->products()->wherePivot('id', $id)->first();
            
            if ($store_product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            $product = \App\Product::where('product.id', $request->product_id)->join('sub_category', 'product.sub_category_id', 'sub_category.id')->join('category', 'sub_category.category_id', 'category.id')->select('product.*')->where('category.owner_id', $store->owner_id)->first();
            
            if ($product === NULL) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Data does not exist.');
            }

            if ($store->products()->wherePivot('product_id', $product->id)->wherePivot('product_id', '!=', $store_product->pivot->product_id)->exists()) {
                return redirect()->back()->withInput($request->except('product_id'))->with('error', 'Product is already existed.');
            }
    
            try {
                $store->products()->updateExistingPivot($product->id, ['selling_price' => $request->selling_price]);
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully edited product.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($store_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $store_product = $store->products()->wherePivot('id', $id)->first();
            
            if ($store_product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $store->products()->detach($store_product->pivot->product_id);
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully deleted product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $store_product = $store->products()->wherePivot('id', $id)->first();
            
            if ($store_product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $store->products()->detach($store_product->pivot->product_id);
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully deleted product.');
        }

        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request, $store_id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();

            if ($store === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return response()->json(['error' => 'Need subscribe to access this feature.'], 403);
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $store_id)->first();
            
            if ($store === NULL) {
                return response()->json(['error' => 'Need higher subscription plan to access this product.'], 403);
            }

            $perpage = -1;
            $sort = 'asc';
            $table_name = 'store_product';
            $field = 'id';
            $m_search_store_product = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_store_product'])) {
                $m_search_store_product = $request->input('datatable')['query']['m_search_store_product'];
            }
    
            $total = DB::table('store_product')
                ->selectRaw('store_product.id, product.id as product_id, product.name as product, store_product.selling_price')
                ->join('product', 'store_product.product_id', 'product.id')
                ->where('store_product.store_id', $store_id)
                ->where('product.name', 'like', '%' . $m_search_store_product . '%')
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
                'data' => DB::table('store_product')
                    ->selectRaw('store_product.id, product.id as product_id, product.name as product, store_product.selling_price')
                    ->join('product', 'store_product.product_id', 'product.id')
                    ->where('store_product.store_id', $store_id)
                    ->where('product.name', 'like', '%' . $m_search_store_product . '%')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }

            $perpage = -1;
            $sort = 'asc';
            $table_name = 'store_product';
            $field = 'id';
            $m_search_store_product = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_store_product'])) {
                $m_search_store_product = $request->input('datatable')['query']['m_search_store_product'];
            }
    
            $total = DB::table('store_product')
                ->selectRaw('store_product.id, product.id as product_id, product.name as product, store_product.selling_price')
                ->join('product', 'store_product.product_id', 'product.id')
                ->where('store_product.store_id', $store_id)
                ->where('product.name', 'like', '%' . $m_search_store_product . '%')
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
                'data' => DB::table('store_product')
                    ->selectRaw('store_product.id, product.id as product_id, product.name as product, store_product.selling_price')
                    ->join('product', 'store_product.product_id', 'product.id')
                    ->where('store_product.store_id', $store_id)
                    ->where('product.name', 'like', '%' . $m_search_store_product . '%')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }

    public function bulkDelete(Request $request, $store_id) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }

            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $store_product = $store->products()->wherePivot('id', $id)->first();
                
                if ($store_product === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $store_product = $store->products()->wherePivot('id', $id)->first();
                
                try {
                    if ($store_product !== NULL) {
                        $store->products()->detach($store_product->pivot->product_id);
                        $store->save();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted products.'], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }

            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $store_product = $store->products()->wherePivot('id', $id)->first();
                
                if ($store_product === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $store_product = $store->products()->wherePivot('id', $id)->first();
                
                try {
                    if ($store_product !== NULL) {
                        $store->products()->detach($store_product->pivot->product_id);
                        $store->save();
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
