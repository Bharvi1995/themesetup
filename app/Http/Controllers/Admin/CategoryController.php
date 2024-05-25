<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use App\Categories;

use Illuminate\Http\Request;

class CategoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->categories = new Categories;

        $this->moduleTitleS = 'Categories';
        $this->moduleTitleP = 'admin.categories';

        //view()->share('moduleTitleP',$this->moduleTitleP);
        //view()->share('moduleTitleS',$this->moduleTitleS);
    }
    public function index()
    {
        $data = $this->categories->getData();

        return view('admin.categories.index', compact('data'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
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
            'name' => 'required'
        ]);

        $categories = $this->categories->storeData($input);

        notificationMsg('success', 'Category created Successfully!');

        return redirect()->route('categories.index');
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
        $categories = $this->categories->findData($id);
        return view('admin.categories.edit', compact('categories'));
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
        $input = $request->all();
        $this->validate($request, [
            'name' => 'required'
        ]);
        $categories = $this->categories->findData($id);

        $this->categories->updateData($id, $input);

        notificationMsg('success', 'Category update successfully!');

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->categories->deleteData($id);

        notificationMsg('success', 'Category deleted Successfully!');

        return redirect()->route('categories.index');
    }
}
