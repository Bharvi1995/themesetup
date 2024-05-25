<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use App\TechnologyPartner;

use Illuminate\Http\Request;

class TechnologyPartnersController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->technologypartner = new TechnologyPartner;

        $this->moduleTitleS = 'TechnologyPartner';
        $this->moduleTitleP = 'admin.technologyPartner';

        //view()->share('moduleTitleP',$this->moduleTitleP);
        //view()->share('moduleTitleS',$this->moduleTitleS);
    }
    public function index()
    {
        $data = $this->technologypartner->getData();

        return view('admin.technologyPartner.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.technologyPartner.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'name' => 'required|unique:technology_partners,name'
        ]);

        $technologypartner = $this->technologypartner->storeData($input);

        notificationMsg('success', 'Integration Preference created successfully!');

        return redirect()->route('integration-preference.index');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // we set "OTHERS" as default with id 1 in database, so it won't be edited or deleted
        if ($id == 1) {
            notificationMsg('error', 'Integration Preference OTHERS can not be changed!');

            return redirect()->route('integration-preference.index');
        }

        $technologypartner = $this->technologypartner->findData($id);

        return view('admin.technologyPartner.edit', compact('technologypartner'));
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
        // we set "OTHERS" as default with id 1 in database, so it won't be edited or deleted
        if ($id == 1) {
            notificationMsg('error', 'Integration Preference OTHERS can not be changed!');

            return redirect()->route('integration-preference.index');
        }

        $input = $request->all();

        $this->validate($request, [
            'name' => 'required|unique:technology_partners,name'
        ]);

        $technologypartner = $this->technologypartner->findData($id);

        $this->technologypartner->updateData($id, $input);

        notificationMsg('success', 'Integration Preference updated successfully!');

        return redirect()->route('integration-preference.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // we set "OTHERS" as default with id 1 in database, so it won't be edited or deleted
        if ($id == 1) {
            notificationMsg('error', 'Integration Preference OTHERS can not be changed!');

            return redirect()->route('integration-preference.index');
        }

        TechnologyPartner::find($id)->delete();

        notificationMsg('success', 'Integration Preference deleted successfully!');

        return redirect()->route('integration-preference.index');
    }
}
