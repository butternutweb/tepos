<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Owner) {
            return view('pages.subscription_plan.index-owner')->with('subscription_plans', \App\SubscriptionPlan::orderBy('price', 'asc')->get());
        }

        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.subscription_plan.index');
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            return view('pages.subscription_plan.create');
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:30'],
                'store_number' => ['required', 'integer', 'min:0'],
                'duration_day' => ['required', 'integer', 'min:0'],
                'price' => ['required', 'integer', 'min:0'],
            ], [
                'store_number.required' => 'The store number field is required.',
                'store_number.integer' => 'The store number must be an integer.',
                'store_number.min' => 'The store number must be at least :min.',
                'duration_day.required' => 'The duration field is required.',
                'duration_day.integer' => 'The duration must be an integer.',
                'duration_day.min' => 'The duration must be at least :min.'
            ]);

            try {
                $subscription_plan = new \App\SubscriptionPlan;
                $subscription_plan->name = $request->name;
                $subscription_plan->store_number = $request->store_number;
                $subscription_plan->duration_day = $request->duration_day;
                $subscription_plan->price = $request->price;
                $subscription_plan->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('subs-plan.index'))->with('success', 'Successfully created subscription plan.');
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
        return redirect()->route('subs-plan.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $subscription_plan = \App\SubscriptionPlan::find($id);
            
            if ($subscription_plan === NULL) {
                return redirect()->route('subs-plan.index')->with('error', 'Data does not exist.');
            }

            return view('pages.subscription_plan.edit')->with('subscription_plan', $subscription_plan);
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:30'],
                'store_number' => ['required', 'integer', 'min:0'],
                'duration_day' => ['required', 'integer', 'min:0'],
                'price' => ['required', 'integer', 'min:0'],
            ], [
                'store_number.required' => 'The store number field is required.',
                'store_number.integer' => 'The store number must be an integer.',
                'store_number.min' => 'The store number must be at least :min.',
                'duration_day.required' => 'The duration field is required.',
                'duration_day.integer' => 'The duration must be an integer.',
                'duration_day.min' => 'The duration must be at least :min.'
            ]);

            $subscription_plan = \App\SubscriptionPlan::find($id);

            if ($subscription_plan === NULL) {
                return redirect()->back()->withInput()->with('error', 'Data does not exist.');
            }

            try {
                $subscription_plan->name = $request->name;
                $subscription_plan->store_number = $request->store_number;
                $subscription_plan->duration_day = $request->duration_day;
                $subscription_plan->price = $request->price;
                $subscription_plan->save();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->withInput()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('subs-plan.index'))->with('success', 'Successfully edited subscription plan.');
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
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $subscription_plan = \App\SubscriptionPlan::find($id);

            if ($subscription_plan === NULL) {
                return redirect()->back()->with('error', 'Data does not exist.');
            }

            try {
                $subscription_plan->delete();
            }
            catch (\Illuminate\Database\QueryException $e) {
                return redirect()->back()->with('error', 'Something wrong with the database.');
            }

            return redirect()->intended(route('subs-plan.index'))->with('success', 'Successfully deleted subscription plan.');
        }

        return redirect()->route('dashboard.index')->with('error', 'Permission denied.');
    }

    public function indexAjax(Request $request) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $perpage = -1;
            $sort = 'asc';
            $table_name = 'subs_plan';
            $field = 'id';
            $m_search_subscription_plan = '';
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

            if (!empty($request->input('datatable')['query']['m_search_subscription_plan'])) {
                $m_search_subscription_plan = $request->input('datatable')['query']['m_search_subscription_plan'];
            }

            $total = DB::table('subs_plan')
                ->selectRaw('subs_plan.id, subs_plan.name, subs_plan.store_number, subs_plan.duration_day, subs_plan.price')
                ->where('subs_plan.name', 'like', '%' . $m_search_subscription_plan . '%')
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
                'data' => DB::table('subs_plan')
                    ->selectRaw('subs_plan.id, subs_plan.name, subs_plan.store_number, subs_plan.duration_day, subs_plan.price')
                    ->where('subs_plan.name', 'like', '%' . $m_search_subscription_plan . '%')
                    ->orderBy($field, $sort)->skip(($page-1)*$perpage)->take($perpage)->get()
            ], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }

    public function bulkDelete(Request $request) {
        if (\Illuminate\Support\Facades\Auth::user()->child()->first() instanceof \App\Admin) {
            $this->validate($request, [
                'ids' => ['array']
            ]);

            foreach($request->ids as $id) {
                $subscription_plan = \App\SubscriptionPlan::find($id);

                if ($subscription_plan === NULL) {
                    return response()->json(['error' => 'Data does not exist.'], 404);
                }
            }

            foreach($request->ids as $id) {
                $subscription_plan = \App\SubscriptionPlan::find($id);

                try {
                    if ($subscription_plan !== NULL) {
                        $subscription_plan->delete();
                    }
                }
                catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['error' => 'Something wrong with the database.'], 500);
                }
            }

            return response()->json(['success' => 'Successfully deleted subscription plans.'], 200);
        }

        return response()->json(['error' => 'Permission denied.'], 403);
    }
}
