<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
        }

        return view('pages.owner.index')->with('statuses', \App\Status::whereIn('name', ['Active', 'Inactive'])->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
        }

        return view('pages.owner.create')->with('statuses', \App\Status::whereIn('name', ['Active', 'Inactive'])->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return redirect()->back()->withInput()->with('error', 'Permission denied.');
        }

        $this->validate($request, [
            'username' => ['required', 'string', 'between:4,50', 'alpha_num'],
            'password' => ['required', 'string', 'confirmed', 'min:4'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:30'],
            'status_id' => ['required', 'integer'],
        ], [
            'status_id.required' => 'The status field is required.',
            'status_id.integer' => 'The status must be an integer.',
        ]);

        $status = \App\Status::where('id', $request->status_id)->whereIn('name', ['Active', 'Inactive'])->first();

        if ($status === NULL) {
            return redirect()->back()->withInput($request->except('status_id', 'password', 'password_confirmation'))->with('error', 'Data does not exist.');
        }

        if (\App\Account::where('username', $request->username)->withTrashed()->exists()) {
            return redirect()->back()->withInput($request->except('username', 'password', 'password_confirmation'))
                ->with('error', 'Username is already existed.');
        }

        if (\App\Account::where('email', $request->email)->withTrashed()->exists()) {
            return redirect()->back()->withInput($request->except('email', 'email_confirmation', 'password', 'password_confirmation'))
                ->with('error', 'Email is already existed.');
        }

        try {
            $account = new \App\Account;
            $account->username = $request->username;
            $account->password = bcrypt($request->password);
            $account->email = $request->email;
            $account->name = $request->name;
            $account->phone = $request->phone;
            $account->status()->associate($status);

            $owner = new \App\Owner;
            $owner->save();
            $owner->accounts()->save($account);
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('owner.index'))->with('success', 'Successfully created owner.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('owner.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
        }

        $owner = \App\Owner::find($id);
        
        if ($owner === NULL) {
            return redirect()->route('owner.index')->with('error', 'Data does not exist.');
        }

        return view('pages.owner.edit')->with('owner', $owner)->with('statuses', \App\Status::whereIn('name', ['Active', 'Inactive'])->get());
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
        if (!\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $this->validate($request, [
            'username' => ['required', 'string', 'between:4,50', 'alpha_num'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:30'],
            'status_id' => ['required', 'integer'],
        ], [
            'status_id.required' => 'The status field is required.',
            'status_id.integer' => 'The status must be an integer.',
        ]);

        if ($request->password !== NULL) {
            $this->validate($request, [
                'password' => ['nullable', 'string', 'confirmed', 'min:4'],
            ]);
        }

        $owner = \App\Owner::find($id);
        
        if ($owner === NULL) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Data does not exist.');
        }

        $status = \App\Status::where('id', $request->status_id)->whereIn('name', ['Active', 'Inactive'])->first();

        if ($status === NULL) {
            return redirect()->back()->withInput($request->except('status_id', 'password', 'password_confirmation'))->with('error', 'Data does not exist.');
        }

        if (\App\Account::where('username', $request->username)->where('id', '!=', $owner->accounts()->first()->id)->withTrashed()->exists()) {
            return redirect()->back()->withInput($request->except('username', 'password', 'password_confirmation'))
                ->with('error', 'Username is already existed.');
        }

        if (\App\Account::where('email', $request->email)->where('id', '!=', $owner->accounts()->first()->id)->withTrashed()->exists()) {
            return redirect()->back()->withInput($request->except('email', 'email_confirmation', 'password', 'password_confirmation'))
                ->with('error', 'Email is already existed.');
        }

        try {
            $account = $owner->accounts()->first();

            if ($request->password !== NULL) {
                $account->password = bcrypt($request->password);
            }

            $account->username = $request->username;
            $account->password = bcrypt($request->password);
            $account->email = $request->email;
            $account->name = $request->name;
            $account->phone = $request->phone;
            $account->status()->associate($status);

            $owner->save();
            $owner->accounts()->save($account);
        }
        catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('owner.index'))->with('success', 'Successfully edited owner.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $owner = \App\Owner::find($id);

        if ($owner === NULL) {
            return redirect()->back()->with('error', 'Data does not exist.');
        }
        
        try {
            foreach ($owner->staffs()->withTrashed()->get() as $staff) {
                $staff->accounts()->withTrashed()->first()->forceDelete();
                $staff->forceDelete();
            }

            $owner->accounts()->first()->forceDelete();
            $owner->delete();
        }
        catch (\Illuminate\Database\QueryException $e) {
            return response()->back()->with('error', 'Something wrong with the database.');
        }

        return redirect()->intended(route('owner.index'))->with('success', 'Successfully deleted owner.');
    }

    public function indexAjax(Request $request)
    {
        if (!\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $perpage = -1;
        $sort = 'asc';
        $table_name = 'owner';
        $field = 'id';
        $m_search_owner = '';
        $status_id = '';
        $total = \App\Owner::count();
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

        if (!empty($request->input('datatable')['query']['m_search_owner'])) {
            $m_search_owner = $request->input('datatable')['query']['m_search_owner'];
        }

        if (!empty($request->input('datatable')['query']['status_id'])) {
            $status_id = $request->input('datatable')['query']['status_id'];
        }

        $total = \Illuminate\Support\Facades\DB::table('owner')
            ->selectRaw('owner.id, account.username, account.email, account.name, account.phone, status.name as status')
            ->join('account', 'owner.id', 'account.child_id')
            ->where('account.child_type', 'Owner')
            ->where('account.deleted_at', NULL)
            ->join('status', 'account.status_id', 'status.id')
            ->where(function($query) use ($status_id) {
                if ($status_id != '') {
                    $query->where('status.id', $status_id);
                }
            })
            ->where(function($query) use ($m_search_owner) {
                if ($m_search_owner != '') {
                    $query->where('account.username', 'like', '%' . $m_search_owner . '%');
                    $query->orWhere('account.email', 'like', '%' . $m_search_owner . '%');
                    $query->orWhere('account.name', 'like', '%' . $m_search_owner . '%');
                    $query->orWhere('account.phone', 'like', '%' . $m_search_owner . '%');
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
            'data' => \Illuminate\Support\Facades\DB::table('owner')
                ->selectRaw('owner.id, account.username, account.email, account.name, account.phone, status.name as status')
                ->join('account', 'owner.id', 'account.child_id')
                ->where('account.child_type', 'Owner')
                ->where('account.deleted_at', NULL)
                ->join('status', 'account.status_id', 'status.id')
                ->where(function($query) use ($status_id) {
                    if ($status_id != '') {
                        $query->where('status.id', $status_id);
                    }
                })
                ->where(function($query) use ($m_search_owner) {
                    if ($m_search_owner != '') {
                        $query->where('account.username', 'like', '%' . $m_search_owner . '%');
                        $query->orWhere('account.email', 'like', '%' . $m_search_owner . '%');
                        $query->orWhere('account.name', 'like', '%' . $m_search_owner . '%');
                        $query->orWhere('account.phone', 'like', '%' . $m_search_owner . '%');
                    }
                })
                ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
        ], 200);
    }

    public function bulkDelete(Request $request) {
        if (!\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }

        $this->validate($request, [
            'ids' => ['array']
        ]);

        foreach($request->ids as $id) {
            $owner = \App\Owner::find($id);
            
            if ($owner === NULL) {
                return response()->json(['error' => 'Data does not exist.'], 404);
            }
        }

        foreach($request->ids as $id) {
            $owner = \App\Owner::find($id);

            try {
                if ($owner !== NULL) {
                    foreach ($owner->staffs()->withTrashed()->get() as $staff) {
                        $staff->accounts()->withTrashed()->first()->forceDelete();
                        $staff->forceDelete();
                    }
    
                    $owner->accounts()->first()->forceDelete();
                    $owner->delete();
                }
            }
            catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['error' => 'Something wrong with the database.'], 500);
            }
        }

        return response()->json(['success' => 'Successfully deleted owners.'], 200);
    }
}
