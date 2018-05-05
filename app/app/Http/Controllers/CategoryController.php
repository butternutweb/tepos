<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return view('pages.category.index');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.category.index')->with('owners', \App\Owner::all());
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
            return view('pages.category.create');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.category.create')->with('owners', \App\Owner::all());
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
            ]);
    
            try {
                $category = new \App\Category;
                $category->name = $request->name;
                $category->owner()->associate(\Illuminate\Support\Facades\Auth::user()->child()->first());
                $category->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('category.index'))->with('success', 'Successfully created category.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'owner_id' => ['required', 'integer'],
            ], [
                'owner_id.required' => 'The owner field is required.',
                'owner_id.integer' => 'The owner must be an integer.',
            ]);
    
            $owner = \App\Owner::find($request->owner_id);
            
            if ($owner === NULL) {
                return redirect()->back()->withInput($request->except('owner_id'))->with('error', 'Data does not exist.');
            }
    
            try {
                $category = new \App\Category;
                $category->name = $request->name;
                $category->owner()->associate($owner);
                $category->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('category.index'))->with('success', 'Successfully created category.');
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
        return redirect()->route('category.edit', $id);
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
            $category = \App\Category::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();

            if ($category === NULL) {
                return redirect()->route('category.index')->with('error', 'Data does not exist.');
            }

            return view('pages.category.edit')->with('category', $category);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $category = \App\Category::find($id);

            if ($category === NULL) {
                return redirect()->route('category.index')->with('error', 'Data does not exist.');
            }

            return view('pages.category.edit')->with('category', $category)->with('owners', \App\Owner::all());
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
            ]);

            $category = \App\Category::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $category->name = $request->name;
                $category->owner()->associate(\Illuminate\Support\Facades\Auth::user()->child()->first());
                $category->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('category.index'))->with('success', 'Successfully edited category.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'owner_id' => ['required', 'integer'],
            ], [
                'owner_id.required' => 'The owner field is required.',
                'owner_id.integer' => 'The owner must be an integer.',
            ]);
    
            $owner = \App\Owner::find($request->owner_id);
            
            if ($owner === NULL) {
                return redirect()->back()->withInput($request->except('owner_id'))->with('error', 'Data does not exist.');
            }

            $category = \App\Category::find($id);
            
            if ($category === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $category->name = $request->name;
                $category->owner()->associate($owner);
                $category->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('category.index'))->with('success', 'Successfully edited category.');
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
            $category = \App\Category::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($category === NULL) {
                return response()->back()->with('error', 'Data does not exist.');
            }

            try {
                $category->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('category.index'))->with('success', 'Successfully deleted category.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $category = \App\Category::find($id);
            
            if ($category === NULL) {
                return response()->back()->with('error', 'Data does not exist.');
            }

            try {
                $category->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('category.index'))->with('success', 'Successfully deleted category.');
        }

        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'category';
            $field = 'id';
            $m_search_category = '';
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

            if (!empty($request->input('datatable')['query']['m_search_category'])) {
                $m_search_category = $request->input('datatable')['query']['m_search_category'];
            }

            $total = DB::table('category')
                ->selectRaw('category.id, category.name')
                ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where('category.name', 'like', '%' . $m_search_category . '%')
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
                'data' => DB::table('category')
                    ->selectRaw('category.id, category.name')
                    ->where('category.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                    ->where('category.name', 'like', '%' . $m_search_category . '%')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'category';
            $field = 'id';
            $m_search_category = '';
            $owner_id = '';
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

            if (!empty($request->input('datatable')['query']['m_search_category'])) {
                $m_search_category = $request->input('datatable')['query']['m_search_category'];
            }

            if (!empty($request->input('datatable')['query']['owner_id'])) {
                $owner_id = $request->input('datatable')['query']['owner_id'];
            }

            $total = DB::table('category')
                ->selectRaw('category.id, category.name, account.id as owner_id, account.username as owner')
                ->join('owner', 'category.owner_id', 'owner.id')
                ->join('account', 'owner.id', 'account.child_id')
                ->where('account.child_type', 'Owner')
                ->where('category.name', 'like', '%' . $m_search_category . '%')
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
                'data' => DB::table('category')
                    ->selectRaw('category.id, category.name, account.id as owner_id, account.username as owner')
                    ->join('owner', 'category.owner_id', 'owner.id')
                    ->join('account', 'owner.id', 'account.child_id')
                    ->where('account.child_type', 'Owner')
                    ->where('category.name', 'like', '%' . $m_search_category . '%')
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
                $category = \App\Category::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
                
                if ($category === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }

            foreach($request->ids as $id) {
                $category = \App\Category::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
                
                try {
                    if ($category !== NULL) {
                        $category->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }

            return response()->json(['success' => 'Successfully deleted categories.'], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'ids' => ['array']
            ]);

            foreach($request->ids as $id) {
                $category = \App\Category::find($id);
                
                if ($category === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }

            foreach($request->ids as $id) {
                $category = \App\Category::find($id);
                
                try {
                    if ($category !== NULL) {
                        $category->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }

            return response()->json(['success' => 'Successfully deleted categories.'], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }
}
