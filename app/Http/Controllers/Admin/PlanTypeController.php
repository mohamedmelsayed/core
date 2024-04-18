<?php

namespace App\Http\Controllers\Admin;
use App\Models\PlanType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlanTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!hasRole("super")&&!hasRole('subscription-manager')) {
            $notify[] = ['fail', 'Not Authorized'];
            return redirect('/admin')->withNotify($notify);
        }
        $pageTitle = "Subscription Plan Types";
        $planTypes     = PlanType::orderBy("level","asc")->paginate(getPaginate());
        return view('admin.plan-types.index', compact('pageTitle', 'planTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = 0) {
        $request->validate([
            'name'        => 'required|unique:plan_types,name,' . $id,
            'level'       => 'required|numeric|gt:-1',
        ]);

        if ($id == 0) {
            $plan         = new PlanType();
            $notification = 'Plan created successfully';
            $oldImage     = null;
        } else {
            $plan         = PlanType::findOrFail($id);
            $notification = 'Plan updated successfully';
        }

       

        $plan->name        = $request->name;
        $plan->level     = $request->level;

        $plan->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
