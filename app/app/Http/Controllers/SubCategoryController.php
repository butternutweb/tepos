<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($category_id)
    {
        return redirect()->route('category.edit', $category_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($category_id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $category = \App\Category::where('id', $category_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return redirect()->route('category.index')->with('error', 'Data does not exist.');
            }
    
            return view('pages.sub_category.create')->with('category_id', $category_id);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $category = \App\Category::find($category_id);
            
            if ($category === NULL) {
                return redirect()->route('category.index')->with('error', 'Data does not exist.');
            }
    
            return view('pages.sub_category.create')->with('category_id', $category_id);
        }

        return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($category_id, Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50']
            ]);
    
            $category = \App\Category::where('id', $category_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $sub_category = new \App\SubCategory;
                $sub_category->name = $request->name;
                $sub_category->category()->associate($category);
                $sub_category->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('category.edit', $category_id))->with('success', 'Successfully created sub category.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50']
            ]);
    
            $category = \App\Category::find($category_id);
            
            if ($category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $sub_category = new \App\SubCategory;
                $sub_category->name = $request->name;
                $sub_category->category()->associate($category);
                $sub_category->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('category.edit', $category_id))->with('success', 'Successfully created sub category.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($category_id, $id)
    {
        return redirect()->route('sub-category.edit', [$category_id, $id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($category_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $category = \App\Category::where('id', $category_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return redirect()->route('category.index')->with('error', 'Data does not exist.');
            }

            $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
            
            if ($sub_category === NULL) {
                return redirect()->route('category.edit', $category_id)->withInput()->with('error', 'Data does not exist.');
            }

            return view('pages.sub_category.edit')->with('category_id', $category_id)->with('sub_category', $sub_category);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $category = \App\Category::find($category_id);
            
            if ($category === NULL) {
                return redirect()->route('category.index')->with('error', 'Data does not exist.');
            }

            $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
            
            if ($sub_category === NULL) {
                return redirect()->route('category.edit', $category_id)->withInput()->with('error', 'Data does not exist.');
            }

            return view('pages.sub_category.edit')->with('category_id', $category_id)->with('sub_category', $sub_category);
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
    public function update(Request $request, $category_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50']
            ]);

            $category = \App\Category::where('id', $category_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
            
            if ($sub_category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $sub_category->name = $request->name;
                $sub_category->category()->associate($category);
                $sub_category->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('category.edit', $category_id))->with('success', 'Successfully edited sub category.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50']
            ]);

            $category = \App\Category::find($category_id);
            
            if ($category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
            
            if ($sub_category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $sub_category->name = $request->name;
                $sub_category->category()->associate($category);
                $sub_category->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('category.edit', $category_id))->with('success', 'Successfully edited sub category.');
        }

        return redirect()->back()->withInput()->with('error', 'Permission denied.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($category_id, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $category = \App\Category::where('id', $category_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }

            $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
            
            if ($sub_category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $sub_category->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('category.edit', $category_id))->with('success', 'Successfully deleted sub category.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $category = \App\Category::find($category_id);
            
            if ($category === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }

            $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
            
            if ($sub_category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $sub_category->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('category.edit', $category_id))->with('success', 'Successfully deleted sub category.');
        }

        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request, $category_id) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $category = \App\Category::where('id', $category_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }
    
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'sub_category';
            $field = 'id';
            $m_search_sub_category = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_sub_category'])) {
                $m_search_sub_category = $request->input('datatable')['query']['m_search_sub_category'];
            }
    
            $total = DB::table('sub_category')
                ->selectRaw('sub_category.id, sub_category.name')
                ->where('sub_category.category_id', $category_id)
                ->where('sub_category.name', 'like', '%' . $m_search_sub_category . '%')->count();
    
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
                'data' => DB::table('sub_category')
                    ->selectRaw('sub_category.id, sub_category.name')
                    ->where('sub_category.category_id', $category_id)
                    ->where('sub_category.name', 'like', '%' . $m_search_sub_category . '%')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $category = \App\Category::find($category_id);
            
            if ($category === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }
    
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'sub_category';
            $field = 'id';
            $m_search_sub_category = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_sub_category'])) {
                $m_search_sub_category = $request->input('datatable')['query']['m_search_sub_category'];
            }
    
            $total = DB::table('sub_category')
                ->selectRaw('sub_category.id, sub_category.name')
                ->where('sub_category.category_id', $category_id)
                ->where('sub_category.name', 'like', '%' . $m_search_sub_category . '%')->count();
    
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
                'data' => DB::table('sub_category')
                    ->selectRaw('sub_category.id, sub_category.name')
                    ->where('sub_category.category_id', $category_id)
                    ->where('sub_category.name', 'like', '%' . $m_search_sub_category . '%')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }

    public function bulkDelete(Request $request, $category_id) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $category = \App\Category::where('id', $category_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }
            
            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
                
                if ($sub_category === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
                
                try {
                    if ($sub_category !== NULL) {
                        $sub_category->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted sub categories.'], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $category = \App\Category::find($category_id);
            
            if ($category === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }
            
            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
                
                if ($sub_category === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $sub_category = \App\SubCategory::where('id', $id)->where('category_id', $category_id)->first();
                
                try {
                    if ($sub_category !== NULL) {
                        $sub_category->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted sub categories.'], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }
}
