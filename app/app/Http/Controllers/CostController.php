<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CostController extends Controller
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
                return redirect()->route('store.index')->with('error', 'Need higher subscription plan to access this cost.');
            }
    
            return view('pages.cost.create')->with('store_id', $store_id);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }
    
            return view('pages.cost.create')->with('store_id', $store_id);
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
                'name' => ['required', 'string', 'max:50'],
                'amount' => ['required', 'integer', 'min:0'],
                'date' => ['required', 'date'],
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
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this cost.');
            }
    
            try {
                $cost = new \App\Cost;
                $cost->name = $request->name;
                $cost->amount = $request->amount;
                $cost->date = $request->date;
                $cost->store()->associate($store);
                $cost->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully created cost.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'amount' => ['required', 'integer', 'min:0'],
                'date' => ['required', 'date'],
            ]);
    
            $store = \App\Store::find($store_id);
    
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $cost = new \App\Cost;
                $cost->name = $request->name;
                $cost->amount = $request->amount;
                $cost->date = $request->date;
                $cost->store()->associate($store);
                $cost->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully created cost.');
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
        return redirect()->route('cost.edit', [$store_id, $id]);
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
                return redirect()->route('store.index')->with('error', 'Need higher subscription plan to access this cost.');
            }
    
            $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
            
            if ($cost === NULL) {
                return redirect()->route('store.edit', $store_id)->with('error', 'Data does not exist.');
            }
    
            return view('pages.cost.edit')->with('cost', $cost)->with('store_id', $store_id);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }
    
            $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
            
            if ($cost === NULL) {
                return redirect()->route('store.edit', $store_id)->with('error', 'Data does not exist.');
            }

            return view('pages.cost.edit')->with('cost', $cost)->with('store_id', $store_id);
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
                'name' => ['required', 'string', 'max:50'],
                'amount' => ['required', 'integer', 'min:0'],
                'date' => ['required', 'date'],
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
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this cost.');
            }
    
            $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
    
            if ($cost === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $cost->name = $request->name;
                $cost->amount = $request->amount;
                $cost->date = $request->date;
                $cost->store()->associate($store);
                $cost->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully edited cost.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:50'],
                'amount' => ['required', 'integer', 'min:0'],
                'date' => ['required', 'date'],
            ]);
    
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
    
            if ($cost === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }
    
            try {
                $cost->name = $request->name;
                $cost->amount = $request->amount;
                $cost->date = $request->date;
                $cost->store()->associate($store);
                $cost->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully edited cost.');
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
                return redirect()->back()->with('error', 'Data does not exist.');
            }
    
            $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
            
            if ($cost === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
    
            try {
                $cost->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully deleted cost.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
    
            $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
            
            if ($cost === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
    
            try {
                $cost->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully deleted cost.');
        }
    
        return redirect()->back()->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request, $store_id) {
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
                return response()->json(['error' => 'Need higher subscription plan to access this cost.'], 403);
            }
    
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'cost';
            $field = 'id';
            $m_search_cost = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_cost'])) {
                $m_search_cost = $request->input('datatable')['query']['m_search_cost'];
            }
    
            $total = DB::table('cost')
                ->selectRaw('cost.id, cost.name, cost.amount, cost.date')
                ->where('cost.store_id', $store_id)
                ->where(function($query) use ($m_search_cost) {
                    $query->where('cost.name', 'like', '%' . $m_search_cost . '%');
                    $query->orWhere('cost.amount', 'like', '%' . $m_search_cost . '%');
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
                'data' => DB::table('cost')
                    ->selectRaw('cost.id, cost.name, cost.amount, cost.date')
                    ->where('cost.store_id', $store_id)
                    ->where(function($query) use ($m_search_cost) {
                        $query->where('cost.name', 'like', '%' . $m_search_cost . '%');
                        $query->orWhere('cost.amount', 'like', '%' . $m_search_cost . '%');
                    })
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::where('id', $store_id);
            
            if ($store === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }
    
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'cost';
            $field = 'id';
            $m_search_cost = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_cost'])) {
                $m_search_cost = $request->input('datatable')['query']['m_search_cost'];
            }
    
            $total = DB::table('cost')
                ->selectRaw('cost.id, cost.name, cost.amount, cost.date')
                ->where('cost.store_id', $store_id)
                ->where(function($query) use ($m_search_cost) {
                    $query->where('cost.name', 'like', '%' . $m_search_cost . '%');
                    $query->orWhere('cost.amount', 'like', '%' . $m_search_cost . '%');
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
                'data' => DB::table('cost')
                    ->selectRaw('cost.id, cost.name, cost.amount, cost.date')
                    ->where('cost.store_id', $store_id)
                    ->where(function($query) use ($m_search_cost) {
                        $query->where('cost.name', 'like', '%' . $m_search_cost . '%');
                        $query->orWhere('cost.amount', 'like', '%' . $m_search_cost . '%');
                    })
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
                $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
                
                if ($cost === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
                
                try {
                    if ($cost !== NULL) {
                        $cost->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted costs.'], 200);
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
                $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
                
                if ($cost === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $cost = \App\Cost::where('id', $id)->where('store_id', $store_id)->first();
                
                try {
                    if ($cost !== NULL) {
                        $cost->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted costs.'], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }
}
