<?php

namespace App\Http\Controllers\Admin;

use App\ArticlesTag;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticleTagController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->ArticlesTag = new ArticlesTag();

        $this->moduleTitleS = 'Articles tags';
        $this->moduleTitleP = 'admin.article-tags';
    }

    public function index()
    {
        $data = $this->ArticlesTag->getData();
        return view('admin.articlesTags.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.articlesTags.create');
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
        $this->ArticlesTag->storeData($input);

        notificationMsg('success','Articles tag Create Successfully!');

        return redirect()->route('article-tags.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tags = $this->ArticlesTag->findData($id);
        return view('admin.articlesTags.edit', compact('tags'));
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
        $this->ArticlesTag->updateData($id, $input);

        notificationMsg('success','Articles tag Update Successfully!');

        return redirect()->route('article-tags.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->ArticlesTag->deleteData($id);

        notificationMsg('success','Articles tag Delete Successfully!');

        return redirect()->route('article-tags.index');
    }
}
