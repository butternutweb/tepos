<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return view('pages.product.index')->with('sub_categories', \App\SubCategory::join('category', 'sub_category.category_id', 'category.id')
            ->select('sub_category.*')
            ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
            ->get());
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.product.index')->with('sub_categories', \App\SubCategory::all());
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return view('pages.product.create')->with('sub_categories', \App\SubCategory::join('category', 'sub_category.category_id', 'category.id')
            ->select('sub_category.*')
            ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
            ->get());
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.product.create')->with('sub_categories', \App\SubCategory::all());
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'sku' => ['nullable', 'string', 'max:30'],
                'note' => ['nullable', 'string', 'max:50'],
                'capital_price' => ['nullable', 'integer', 'min:0'],
                'sub_category_id' => ['required', 'integer']
            ], [
                'sku.string' => 'The SKU must be a string.',
                'sku.max' => 'The SKU may not be greater than :max characters.',
                'capital_price.min' => 'The capital price must be at least :min.',
                'sub_category_id.required' => 'The sub category field is required.',
                'sub_category_id.integer' => 'The sub category must be an integer.'
            ]);
    
            $sub_category = \App\SubCategory::join('category', 'sub_category.category_id', 'category_id')->where('sub_category.id', $request->sub_category_id)->select('sub_category.*')->first();
            
            if ($sub_category === NULL) {
                return redirect()->back()->withInput($request->except('sub_category_id'))->with('error', 'Data does not exist.');
            }
    
            try {
                $product = new \App\Product;
                $product->name = $request->name;
                $product->sku = $request->sku;
                $product->note = $request->note;
                $product->capital_price = $request->capital_price;
                $product->subCategory()->associate($sub_category);
                $product->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('product.index'))->with('success', 'Successfully created product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'sku' => ['nullable', 'string', 'max:30'],
                'note' => ['nullable', 'string', 'max:50'],
                'capital_price' => ['nullable', 'integer', 'min:0'],
                'sub_category_id' => ['required', 'integer'],
            ], [
                'sku.string' => 'The SKU must be a string.',
                'sku.max' => 'The SKU may not be greater than :max characters.',
                'capital_price.min' => 'The capital price must be at least :min.',
                'sub_category_id.required' => 'The sub category field is required.',
                'sub_category_id.integer' => 'The sub category must be an integer.',
            ]);

            $sub_category = \App\SubCategory::find($request->sub_category_id);
            
            if ($sub_category === NULL) {
                return redirect()->back()->withInput($request->except('sub_category_id'))->with('error', 'Data does not exist.');
            }

            try {
                $product = new \App\Product;
                $product->name = $request->name;
                $product->sku = $request->sku;
                $product->note = $request->note;
                $product->capital_price = $request->capital_price;
                $product->subCategory()->associate($sub_category);
                $product->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('product.index'))->with('success', 'Successfully created product.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('product.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $product = \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')
                ->join('category', 'sub_category.category_id', 'category.id')
                ->select('product.*')
                ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where('product.id', $id)
                ->first();
            
            if ($product === NULL) {
                return redirect()->route('product.index')->with('error', 'Data does not exist.');
            }
    
            return view('pages.product.edit')->with('product', $product)->with('sub_categories', \App\SubCategory::join('category', 'sub_category.category_id', 'category.id')
            ->select('sub_category.*')
            ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
            ->get());
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $product = \App\Product::find($id);
            
            if ($product === NULL) {
                return redirect()->route('product.index')->with('error', 'Data does not exist.');
            }

            return view('pages.product.edit')->with('product', $product)->with('sub_categories', \App\SubCategory::all());
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'sku' => ['nullable', 'string', 'max:30'],
                'note' => ['nullable', 'string', 'max:50'],
                'capital_price' => ['nullable', 'integer', 'min:0'],
                'sub_category_id' => ['required', 'integer']
            ], [
                'sku.string' => 'The SKU must be a string.',
                'sku.max' => 'The SKU may not be greater than :max characters.',
                'capital_price.min' => 'The capital price must be at least :min.',
                'sub_category_id.required' => 'The sub category field is required.',
                'sub_category_id.integer' => 'The sub category must be an integer.'
            ]);
    
            $sub_category = \App\SubCategory::join('category', 'sub_category.category_id', 'category.id')
                ->select('sub_category.*')
                ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where('sub_category.id', $request->sub_category_id)
                ->first();
            
            if ($sub_category === NULL) {
                return redirect()->back()->withInput($request->except('sub_category_id'))->with('error', 'Data does not exist.');
            }
    
            $product = \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')
                ->join('category', 'sub_category.category_id', 'category.id')
                ->select('product.*')
                ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where('product.id', $id)
                ->first();
    
            if ($product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $product->name = $request->name;
                $product->sku = $request->sku;
                $product->note = $request->note;
                $product->capital_price = $request->capital_price;
                $product->subCategory()->associate($sub_category);
                $product->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('product.index'))->with('success', 'Successfully edited product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'sku' => ['nullable', 'string', 'max:30'],
                'note' => ['nullable', 'string', 'max:50'],
                'capital_price' => ['nullable', 'integer', 'min:0'],
                'sub_category_id' => ['required', 'integer'],
            ], [
                'sku.string' => 'The SKU must be a string.',
                'sku.max' => 'The SKU may not be greater than :max characters.',
                'capital_price.min' => 'The capital price must be at least :min.',
                'sub_category_id.required' => 'The sub category field is required.',
                'sub_category_id.integer' => 'The sub category must be an integer.',
            ]);

            $sub_category = \App\SubCategory::find($request->sub_category_id);
            
            if ($sub_category === NULL) {
                return redirect()->back()->withInput($request->except('sub_category_id'))->with('error', 'Data does not exist.');
            }

            $product = \App\Product::find($id);

            if ($product === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $product->name = $request->name;
                $product->sku = $request->sku;
                $product->note = $request->note;
                $product->capital_price = $request->capital_price;
                $product->subCategory()->associate($sub_category);
                $product->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('product.index'))->with('success', 'Successfully edited product.');
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $product = \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')
                ->join('category', 'sub_category.category_id', 'category.id')
                ->select('product.*')
                ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where('product.id', $id)
                ->first();
            
            if ($product === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
    
            try {
                $product->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('product.index'))->with('success', 'Successfully deleted product.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $product = \App\Product::find($id);

            if ($product === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }

            try {
                $product->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('product.index'))->with('success', 'Successfully deleted product.');
        }

        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'product';
            $field = 'id';
            $m_search_product = '';
            $sub_category_id = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_product'])) {
                $m_search_product = $request->input('datatable')['query']['m_search_product'];
            }
    
            if (!empty($request->input('datatable')['query']['sub_category_id'])) {
                $sub_category_id = $request->input('datatable')['query']['sub_category_id'];
            }
    
            $total = \Illuminate\Support\Facades\DB::table('product')
                ->selectRaw('product.id, product.name, product.sku, product.note, product.capital_price, sub_category.name as sub_category, sub_category.id as sub_category_id, sub_category.category_id as category_id')
                ->join('sub_category', 'product.sub_category_id', 'sub_category.id')
                ->join('category', 'sub_category.category_id', 'category.id')
                ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where(function($query) use ($sub_category_id) {
                    if ($sub_category_id != '') {
                        $query->where('sub_category.id', $sub_category_id);
                    }
                })
                ->where(function($query) use ($m_search_product) {
                    if ($m_search_product != '') {
                        $query->where('product.name', 'like', '%' . $m_search_product . '%');
                        $query->orWhere('product.sku', 'like', '%' . $m_search_product . '%');
                        $query->orWhere('product.note', 'like', '%' . $m_search_product . '%');
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
                'data' => \Illuminate\Support\Facades\DB::table('product')
                    ->selectRaw('product.id, product.name, product.sku, product.note, product.capital_price, sub_category.name as sub_category, sub_category.id as sub_category_id, sub_category.category_id as category_id')
                    ->join('sub_category', 'product.sub_category_id', 'sub_category.id')
                    ->join('category', 'sub_category.category_id', 'category.id')
                    ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                    ->where(function($query) use ($sub_category_id) {
                        if ($sub_category_id != '') {
                            $query->where('sub_category.id', $sub_category_id);
                        }
                    })
                    ->where(function($query) use ($m_search_product) {
                        if ($m_search_product != '') {
                            $query->where('product.name', 'like', '%' . $m_search_product . '%');
                            $query->orWhere('product.sku', 'like', '%' . $m_search_product . '%');
                            $query->orWhere('product.note', 'like', '%' . $m_search_product . '%');
                        }
                    })
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'product';
            $field = 'id';
            $m_search_product = '';
            $sub_category_id = '';
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

            if (!empty($request->input('datatable')['query']['m_search_product'])) {
                $m_search_product = $request->input('datatable')['query']['m_search_product'];
            }

            if (!empty($request->input('datatable')['query']['sub_category_id'])) {
                $sub_category_id = $request->input('datatable')['query']['sub_category_id'];
            }

            $total = \Illuminate\Support\Facades\DB::table('product')
                ->selectRaw('product.id, product.name, product.sku, product.note, product.capital_price, sub_category.name as sub_category, sub_category.id as sub_category_id, sub_category.category_id as category_id')
                ->join('sub_category', 'product.sub_category_id', 'sub_category.id')
                ->where(function($query) use ($sub_category_id) {
                    if ($sub_category_id != '') {
                        $query->where('sub_category.id', $sub_category_id);
                    }
                })
                ->where(function($query) use ($m_search_product) {
                    if ($m_search_product != '') {
                        $query->where('product.name', 'like', '%' . $m_search_product . '%');
                        $query->orWhere('product.sku', 'like', '%' . $m_search_product . '%');
                        $query->orWhere('product.note', 'like', '%' . $m_search_product . '%');
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
                'data' => \Illuminate\Support\Facades\DB::table('product')
                    ->selectRaw('product.id, product.name, product.sku, product.note, product.capital_price, sub_category.name as sub_category, sub_category.id as sub_category_id, sub_category.category_id as category_id')
                    ->join('sub_category', 'product.sub_category_id', 'sub_category.id')
                    ->where(function($query) use ($sub_category_id) {
                        if ($sub_category_id != '') {
                            $query->where('sub_category.id', $sub_category_id);
                        }
                    })
                    ->where(function($query) use ($m_search_product) {
                        if ($m_search_product != '') {
                            $query->where('product.name', 'like', '%' . $m_search_product . '%');
                            $query->orWhere('product.sku', 'like', '%' . $m_search_product . '%');
                            $query->orWhere('product.note', 'like', '%' . $m_search_product . '%');
                        }
                    })
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }

    public function bulkDelete(Request $request) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $product = \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')
                    ->join('category', 'sub_category.category_id', 'category.id')
                    ->select('product.*')
                    ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                    ->where('product.id', $id)
                    ->first();
                
                if ($product === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $product = \App\Product::join('sub_category', 'product.sub_category_id', 'sub_category.id')
                    ->join('category', 'sub_category.category_id', 'category.id')
                    ->select('product.*')
                    ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                    ->where('product.id', $id)
                    ->first();
    
                try {
                    if ($product !== NULL) {
                        $product->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted products.'], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'ids' => ['array']
            ]);

            foreach($request->ids as $id) {
                $product = \App\Product::find($id);
                
                if ($product === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }

            foreach($request->ids as $id) {
                $product = \App\Product::find($id);

                try {
                    if ($product !== NULL) {
                        $product->delete();
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
