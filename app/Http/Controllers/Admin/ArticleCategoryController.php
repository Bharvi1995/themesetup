<?php

namespace App\Http\Controllers\Admin;

use App\ArticlesCategorie;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticleCategoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->ArticlesCategorie = new ArticlesCategorie();

        $this->moduleTitleS = 'Articles categories';
        $this->moduleTitleP = 'admin.article-categories';
    }

    public function index()
    {
        $data = $this->ArticlesCategorie->getData();
        return view('admin.articlesCategories.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.articlesCategories.create');
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
            'title' => 'required',
            'meta_keyword' => 'required',
            'meta_description' => 'required'
        ]);

        $input['slug'] = \Str::slug($input['title'], "_");
        $categories = $this->ArticlesCategorie->storeData($input);

        notificationMsg('success', 'Category created Successfully!');

        return redirect()->route('article-categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = $this->ArticlesCategorie->findData($id);
        return view('admin.articlesCategories.edit', compact('categories'));
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
            'title' => 'required',
            'meta_keyword' => 'required',
            'meta_description' => 'required'
        ]);

        $input['slug'] = \Str::slug($input['title'], "_");
        $this->ArticlesCategorie->updateData($id, $input);

        notificationMsg('success', 'Category updated successfully!');

        return redirect()->route('article-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->ArticlesCategorie->deleteData($id);

        notificationMsg('success', 'Category deleted successfully!');

        return redirect()->route('article-categories.index');
    }
}
