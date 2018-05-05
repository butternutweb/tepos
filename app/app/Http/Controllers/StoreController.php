<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return view('pages.store.index');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.store.index')->with('owners', \App\Owner::all());
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
            $storeNumber = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->count();

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->route('store.index')->with('error', 'Need subscribe to access this feature.');
            }

            if ($storeNumber >= $last_subscription_transaction->plan()->first()->store_number) {
                return redirect()->route('store.index')->with('error', 'Store maximum limit.');
            }

            return view('pages.store.create');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.store.create')->with('owners', \App\Owner::all());
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
                'name' => ['required', 'string', 'max:50']
            ]);

            $storeNumber = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->count();

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }

            if ($storeNumber >= $last_subscription_transaction->plan()->first()->store_number) {
                return redirect()->route('store.index')->with('error', 'Store maximum limit.');
            }

            try {
                $store = new \App\Store;
                $store->name = $request->name;
                $store->owner()->associate(\Illuminate\Support\Facades\Auth::user()->child()->first());
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('store.index'))->with('success', 'Successfully created store.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'owner_id' => ['required', 'integer']
            ], [
                'owner_id.required' => 'The owner field is required.',
                'owner_id.integer' => 'The owner must be an integer.'
            ]);
    
            $owner = \App\Owner::find($request->owner_id);

            if ($owner === NULL) {
                return redirect()->back()->withInput($request->except('owner_id'))->with('error', 'Data does not exist.');
            }

            $storeNumber = \App\Store::where('owner_id', $request->owner_id)->count();

            $last_subscription_transaction = $owner->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }

            if ($storeNumber >= $last_subscription_transaction->plan()->first()->store_number) {
                return redirect()->back()->withInput($request->except('owner_id'))->with('error', 'Store maximum limit.');
            }
    
            try {
                $store = new \App\Store;
                $store->name = $request->name;
                $store->owner()->associate($owner);
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.index'))->with('success', 'Successfully created store.');
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
        return redirect()->route('store.edit', $id);
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
            $store = \App\Store::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->route('store.index')->with('error', 'Need subscribe to access this feature.');
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $id)->first();
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Need higher subscription plan to access this store.');
            }
    
            return view('pages.store.edit')->with('store', $store);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($id);
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }

            return view('pages.store.edit')->with('store', $store)->with('owners', \App\Owner::All())->with('statuses', \App\Status::whereIn('name', ['Active', 'Inactive'])->get());
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

            $store = \App\Store::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this store.');
            }

            try {
                $store->name = $request->name;
                $store->owner()->associate(\Illuminate\Support\Facades\Auth::user()->child()->first());
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.index'))->with('success', 'Successfully edited store.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'owner_id' => ['required', 'integer']
            ], [
                'owner_id.required' => 'The owner field is required.',
                'owner_id.integer' => 'The owner must be an integer.'
            ]);
    
            $store = \App\Store::find($id);
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            $owner = \App\Owner::find($request->owner_id);
            
            if ($owner === NULL) {
                return redirect()->back()->withInput($request->except('owner_id'))->with('error', 'Data does not exist.');
            }
    
            try {
                $store->name = $request->name;
                $store->owner()->associate($owner);
                $store->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.index'))->with('success', 'Successfully edited store.');
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
            $store = \App\Store::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();

            if ($store === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
    
            try {
                $store->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.index'))->with('success', 'Successfully deleted store.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($id);
            
            if ($store === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
    
            try {
                $store->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.index'))->with('success', 'Successfully deleted store.');
        }

        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'store';
            $field = 'id';
            $m_search_store = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_store'])) {
                $m_search_store = $request->input('datatable')['query']['m_search_store'];
            }

            $total = DB::table('store')
                ->selectRaw('store.id, store.name')
                ->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where('store.name', 'like', '%' . $m_search_store . '%')
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
                'data' => DB::table('store')
                    ->selectRaw('store.id, store.name')
                    ->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                    ->where('store.name', 'like', '%' . $m_search_store . '%')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'store';
            $field = 'id';
            $m_search_store = '';
            $owner_id = '';
            $total = \App\Store::count();
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
    
            if (!empty($request->input('datatable')['query']['m_search_store'])) {
                $m_search_store = $request->input('datatable')['query']['m_search_store'];
            }
    
            if (!empty($request->input('datatable')['query']['owner_id'])) {
                $owner_id = $request->input('datatable')['query']['owner_id'];
            }
    
            $total = DB::table('store')
                ->selectRaw('store.id, store.name, owner.id as owner_id, account.username as owner')
                ->join('owner', 'store.owner_id', '=', 'owner.id')
                ->join('account', 'owner.id', '=', 'account.child_id')
                ->where('account.child_type', 'Owner')
                ->where(function($query) use ($owner_id) {
                    if ($owner_id != '') {
                        $query->where('owner.id', $owner_id);
                    }
                })
                ->where('store.name', 'like', '%' . $m_search_store . '%')
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
                'data' => DB::table('store')
                    ->selectRaw('store.id, store.name, owner.id as owner_id, account.username as owner')
                    ->join('owner', 'store.owner_id', '=', 'owner.id')
                    ->join('account', 'owner.id', '=', 'account.child_id')
                    ->where('account.child_type', 'Owner')
                    ->where(function($query) use ($owner_id) {
                        if ($owner_id != '') {
                            $query->where('owner.id', $owner_id);
                        }
                    })
                    ->where('store.name', 'like', '%' . $m_search_store . '%')
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
                $store = \App\Store::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
                
                if ($store === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $store = \App\Store::where('id', $id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
                
                try {
                    if ($store !== NULL) {
                        $store->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted stores.'], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'ids' => ['array']
            ]);
    
            foreach($request->ids as $id) {
                $store = \App\Store::find($id);
                
                if ($store === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $store = \App\Store::find($id);
                
                try {
                    if ($store !== NULL) {
                        $store->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted stores.'], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }
}
