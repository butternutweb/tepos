<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
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
                return redirect()->route('store.index')->with('error', 'Need higher subscription plan to access this staff.');
            }

            return view('pages.staff.create')->with('store_id', $store_id);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }

            return view('pages.staff.create')->with('store_id', $store_id)->with('statuses', \App\Status::whereIn('name', ['Active', 'Inactive'])->get());
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
                'username' => ['required', 'string', 'between:4,50', 'alpha_num'],
                'password' => ['required', 'string', 'confirmed', 'min:4'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'name' => ['required', 'string', 'max:30'],
                'phone' => ['required', 'string', 'max:30'],
                'salary' => ['nullable', 'integer', 'min:0'],
            ]);

            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $store_id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this staff.');
            }
    
            if (\App\Account::where('username', \Illuminate\Support\Facades\Auth::user()->username . '_' . $request->username)->withTrashed()->exists()) {
                return redirect()->back()->withInput($request->except('username', 'password', 'password_confirmation'))
                    ->with('error', 'Username is already existed.');
            }

            if (\App\Account::where('email', $request->email)->withTrashed()->exists()) {
                return redirect()->back()->withInput($request->except('email', 'password', 'password_confirmation'))
                    ->with('error', 'Email is already existed.');
            }
    
            try {
                $account = new \App\Account;
                $account->username = \Illuminate\Support\Facades\Auth::user()->username . '_' . $request->username;
                $account->password = bcrypt($request->password);
                $account->email = $request->email;
                $account->name = $request->name;
                $account->phone = $request->phone;
                $account->status()->associate(\App\Status::where('name', 'Active')->first());
    
                $staff = new \App\Staff;
                $staff->salary = $request->salary;
                $staff->store()->associate($store);
                $staff->save();
                $staff->accounts()->save($account);
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully created staff.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'username' => ['required', 'string', 'between:4,50'],
                'password' => ['required', 'string', 'confirmed', 'min:4'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'name' => ['required', 'string', 'max:30'],
                'phone' => ['required', 'string', 'max:30'],
                'status_id' => ['required', 'integer'],
                'salary' => ['nullable', 'integer', 'min:0'],
            ], [
                'status_id.required' => 'The status field is required.',
                'status_id.integer' => 'The status must be an integer.',
            ]);

            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Data does not exist.');
            }

            $status = \App\Status::where('id', $request->status_id)->whereIn('name', ['Active', 'Inactive'])->first();

            if ($status === NULL) {
                return redirect()->back()->withInput($request->except('status_id', 'password', 'password_confirmation'))->with('error', 'Data does not exist.');
            }

            if (\App\Account::where('username', $store->owner()->first()->accounts()->first()->username . '_' . $request->username)->withTrashed()->exists()) {
                return redirect()->back()->withInput($request->except('username', 'password', 'password_confirmation'))
                    ->with('error', 'Username is already existed.');
            }

            if (\App\Account::where('email', $request->email)->withTrashed()->exists()) {
                return redirect()->back()->withInput($request->except('email', 'password', 'password_confirmation'))
                    ->with('error', 'Email is already existed.');
            }

            try {
                $account = new \App\Account;
                $account->username = $store->owner()->first()->accounts()->first()->username . '_' . $request->username;
                $account->password = bcrypt($request->password);
                $account->email = $request->email;
                $account->name = $request->name;
                $account->phone = $request->phone;
                $account->status()->associate($status);

                $staff = new \App\Staff;
                $staff->salary = $request->salary;
                $staff->store()->associate($store);
                $staff->save();
                $staff->accounts()->save($account);
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully created staff.');
        }

        return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Permission denied.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($store_id, $id)
    {
        return redirect()->route('staff.edit', [$store_id, $id]);
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
                return redirect()->route('store.index')->with('error', 'Need higher subscription plan to access this staff.');
            }
            
            $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();
            
            if ($staff === NULL) {
                return redirect()->route('store.edit', $store_id)->with('error', 'Data does not exist.');
            }
    
            return view('pages.staff.edit')->with('staff', $staff)->with('store_id', $store_id);
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->route('store.index')->with('error', 'Data does not exist.');
            }

            $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();

            if ($staff === NULL) {
                return redirect()->route('store.edit', $store_id)->with('error', 'Data does not exist.');
            }

            return view('pages.staff.edit')->with('staff', $staff)->with('store_id', $store_id)->with('statuses', \App\Status::whereIn('name', ['Active', 'Inactive'])->get());;
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
                'username' => ['required', 'string', 'between:4,50', 'alpha_num'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'name' => ['required', 'string', 'max:30'],
                'phone' => ['required', 'string', 'max:30'],
                'salary' => ['nullable', 'integer', 'min:0'],
            ]);

            if ($request->password !== NULL) {
                $this->validate($request, [
                    'password' => ['nullable', 'string', 'confirmed', 'min:4'],
                ]);
            }

            $store = \App\Store::where('id', $store_id)->where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Data does not exist.');
            }

            $last_subscription_transaction = \Illuminate\Support\Facades\Auth::user()->child()->first()->transactions()->where('subs_end', '>=', \Carbon\Carbon::now())->orderBy('subs_end', 'asc')->first();

            if ($last_subscription_transaction === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need subscribe to access this feature.');
            }

            $store = \App\Store::where('owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)->take($last_subscription_transaction->plan()->first()->store_number)->get()->where('id', $store_id)->first();
            
            if ($store === NULL) {
                return redirect()->back()->withInput()->with('error', 'Need higher subscription plan to access this staff.');
            }

            $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();
            
            if ($staff === NULL) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Data does not exist.');
            }

            if (\App\Account::where('username', \Illuminate\Support\Facades\Auth::user()->username . '_' . $request->username)->where('id', '!=', $staff->accounts()->first()->id)->withTrashed()->exists()) {
                return redirect()->back()->withInput($request->except('username', 'password', 'password_confirmation'))
                    ->with('error', 'Username is already existed.');
            }

            if (\App\Account::where('email', $request->email)->where('id', '!=', $staff->accounts()->first()->id)->withTrashed()->exists()) {
                return redirect()->back()->withInput($request->except('email', 'password', 'password_confirmation'))
                    ->with('error', 'Email is already existed.');
            }
    
            try {
                $account = $staff->accounts()->first();
                $account->username = \Illuminate\Support\Facades\Auth::user()->username . '_' . $request->username;

                if ($request->password !== NULL) {
                    $account->password = bcrypt($request->password);
                }

                $account->email = $request->email;
                $account->name = $request->name;
                $account->phone = $request->phone;
                $account->status()->associate(\App\Status::where('name', 'Active')->first());
    
                $staff->salary = $request->salary;
                $staff->store()->associate($store);
                $staff->save();
                $staff->accounts()->save($account);
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully edited staff.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'username' => ['required', 'string', 'between:4,50'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'name' => ['required', 'string', 'max:30'],
                'phone' => ['required', 'string', 'max:30'],
                'status_id' => ['required', 'integer'],
                'salary' => ['nullable', 'integer', 'min:0'],
            ], [
                'status_id.required' => 'The status field is required.',
                'status_id.integer' => 'The status must be an integer.',
            ]);

            if ($request->password !== NULL) {
                $this->validate($request, [
                    'password' => ['nullable', 'string', 'confirmed', 'min:4'],
                ]);
            }

            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Data does not exist.');
            }

            $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();
            
            if ($staff === NULL) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->with('error', 'Data does not exist.');
            }

            $status = \App\Status::where('id', $request->status_id)->whereIn('name', ['Active', 'Inactive'])->first();

            if ($status === NULL) {
                return redirect()->back()->withInput($request->except('status_id', 'password', 'password_confirmation'))->with('error', 'Data does not exist.');
            }

            if (\App\Account::where('username', $store->owner()->first()->accounts()->first()->username . '_' . $request->username)->where('id', '!=', $staff->accounts()->first()->id)->withTrashed()->exists()) {
                return redirect()->back()->withInput($request->except('username', 'password', 'password_confirmation'))
                    ->with('error', 'Username is already existed.');
            }

            if (\App\Account::where('email', $request->email)->where('id', '!=', $staff->accounts()->first()->id)->withTrashed()->exists()) {
                return redirect()->back()->withInput($request->except('email', 'password', 'password_confirmation'))
                    ->with('error', 'Email is already existed.');
            }

            try {
                $account = $staff->accounts()->first();
                $account->username = $store->owner()->first()->accounts()->first()->username . '_' . $request->username;

                if ($request->password !== NULL) {
                    $account->password = bcrypt($request->password);
                }

                $account->email = $request->email;
                $account->name = $request->name;
                $account->phone = $request->phone;
                $account->status()->associate($status);

                $staff->salary = $request->salary;
                $staff->store()->associate($store);
                $staff->save();
                $staff->accounts()->save($account);
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully edited staff.');
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

            $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();
            
            if ($staff === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
            
            try {
                $staff->accounts()->first()->delete();
                $staff->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return response()->back()->with('error', 'Something wrong with the database.');
            }
    
            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully deleted staff.');
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $store = \App\Store::find($store_id);
            
            if ($store === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
            
            $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();

            if ($staff === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }
            
            try {
                $staff->accounts()->first()->delete();
                $staff->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return response()->back()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('store.edit', $store_id))->with('success', 'Successfully deleted staff.');
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
                return response()->json(['error' => 'Need higher subscription plan to access this staff.'], 403);
            }

            $perpage = -1;
            $sort = 'asc';
            $table_name = 'staff';
            $field = 'id';
            $m_search_staff = '';
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
    
            if (!empty($request->input('datatable')['query']['m_search_staff'])) {
                $m_search_staff = $request->input('datatable')['query']['m_search_staff'];
            }
    
            if (!empty($request->input('datatable')['query']['status_id'])) {
                $status_id = $request->input('datatable')['query']['status_id'];
            }

            $total = \Illuminate\Support\Facades\DB::table('staff')
                ->selectRaw('staff.id, account.username, account.email, account.name, account.phone, staff.salary, status.name as status')
                ->join('account', 'staff.id', 'account.child_id')
                ->where('account.child_type', 'Staff')
                ->where('account.deleted_at', NULL)
                ->where('staff.store_id', $store_id)
                ->join('status', 'account.status_id', 'status.id')
                ->join('store', 'staff.store_id', 'store.id')
                ->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                ->where(function($query) use ($status_id) {
                    if ($status_id != '') {
                        $query->where('status.id', $status_id);
                    }
                })
                ->where(function($query) use ($m_search_staff) {
                    if ($m_search_staff != '') {
                        $query->where('account.username', 'like', '%' . $m_search_staff . '%');
                        $query->orWhere('account.email', 'like', '%' . $m_search_staff . '%');
                        $query->orWhere('account.name', 'like', '%' . $m_search_staff . '%');
                        $query->orWhere('account.phone', 'like', '%' . $m_search_staff . '%');
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
                'data' => \Illuminate\Support\Facades\DB::table('staff')
                    ->selectRaw('staff.id, account.username, account.email, account.name, account.phone, staff.salary, status.name as status')
                    ->join('account', 'staff.id', 'account.child_id')
                    ->where('account.child_type', 'Staff')
                    ->where('account.deleted_at', NULL)
                    ->where('staff.store_id', $store_id)
                    ->join('status', 'account.status_id', 'status.id')
                    ->join('store', 'staff.store_id', 'store.id')
                    ->where('store.owner_id', \Illuminate\Support\Facades\Auth::user()->child()->first()->id)
                    ->where(function($query) use ($status_id) {
                        if ($status_id != '') {
                            $query->where('status.id', $status_id);
                        }
                    })
                    ->where(function($query) use ($m_search_staff) {
                        if ($m_search_staff != '') {
                            $query->where('account.username', 'like', '%' . $m_search_staff . '%');
                            $query->orWhere('account.email', 'like', '%' . $m_search_staff . '%');
                            $query->orWhere('account.name', 'like', '%' . $m_search_staff . '%');
                            $query->orWhere('account.phone', 'like', '%' . $m_search_staff . '%');
                        }
                    })
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
            $table_name = 'staff';
            $field = 'id';
            $m_search_staff = '';
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

            if (!empty($request->input('datatable')['query']['m_search_staff'])) {
                $m_search_staff = $request->input('datatable')['query']['m_search_staff'];
            }

            if (!empty($request->input('datatable')['query']['status_id'])) {
                $status_id = $request->input('datatable')['query']['status_id'];
            }

            $total = \Illuminate\Support\Facades\DB::table('staff')
                ->selectRaw('staff.id, account.username, account.email, account.name, account.phone, staff.salary, status.name as status')
                ->join('account', 'staff.id', 'account.child_id')
                ->where('account.child_type', 'Staff')
                ->where('account.deleted_at', NULL)
                ->where('staff.store_id', $store_id)
                ->join('status', 'account.status_id', 'status.id')
                ->where(function($query) use ($status_id) {
                    if ($status_id != '') {
                        $query->where('status.id', $status_id);
                    }
                })
                ->where(function($query) use ($m_search_staff) {
                    if ($m_search_staff != '') {
                        $query->where('account.username', 'like', '%' . $m_search_staff . '%');
                        $query->orWhere('account.email', 'like', '%' . $m_search_staff . '%');
                        $query->orWhere('account.name', 'like', '%' . $m_search_staff . '%');
                        $query->orWhere('account.phone', 'like', '%' . $m_search_staff . '%');
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
                'data' => \Illuminate\Support\Facades\DB::table('staff')
                    ->selectRaw('staff.id, account.username, account.email, account.name, account.phone, staff.salary, status.name as status')
                    ->join('account', 'staff.id', 'account.child_id')
                    ->where('account.child_type', 'Staff')
                    ->where('account.deleted_at', NULL)
                    ->where('staff.store_id', $store_id)
                    ->join('status', 'account.status_id', 'status.id')
                    ->where(function($query) use ($status_id) {
                        if ($status_id != '') {
                            $query->where('status.id', $status_id);
                        }
                    })
                    ->where(function($query) use ($m_search_staff) {
                        if ($m_search_staff != '') {
                            $query->where('account.username', 'like', '%' . $m_search_staff . '%');
                            $query->orWhere('account.email', 'like', '%' . $m_search_staff . '%');
                            $query->orWhere('account.name', 'like', '%' . $m_search_staff . '%');
                            $query->orWhere('account.phone', 'like', '%' . $m_search_staff . '%');
                        }
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
                $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();
                
                if ($staff === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }
    
            foreach($request->ids as $id) {
                $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();
    
                try {
                    if ($staff !== NULL) {
                        $staff->accounts()->first()->delete();
                        $staff->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }
    
            return response()->json(['success' => 'Successfully deleted staffs.'], 200);
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
                $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();
                
                if ($staff === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }

            foreach($request->ids as $id) {
                $staff = \App\Staff::where('id', $id)->where('store_id', $store_id)->first();

                try {
                    if ($staff !== NULL) {
                        $staff->accounts()->first()->delete();
                        $staff->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }

            return response()->json(['success' => 'Successfully deleted staffs.'], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }
}
