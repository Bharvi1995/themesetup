<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\ArticlesCategorie;
use App\ArticlesTag;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class ArticleController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->Article = new Article();

        $this->moduleTitleS = 'Articles';
        $this->moduleTitleP = 'admin.article';
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $data = $this->Article->getData($input);
        return view('admin.articles.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ArticlesCategorie::all();
        $tags = ArticlesTag::all();
        return view('admin.articles.create', compact('categories', 'tags'));
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
            'meta_description' => 'required',
            'category_id' => 'required'
        ]);

        $input['slug'] = \Str::slug($input['title'], "_");
        $input['tag_id'] = $input['tag_id'] ? implode(",", $input['tag_id']) : null;
        $user = auth()->guard('admin')->user();

        if($request->hasFile('image')){
            $imageName = time().rand(0, 10000000000000).pathinfo(rand(111111111111,999999999999), PATHINFO_FILENAME);
            $imageName = $imageName.'.'.$request->file('image')->getClientOriginalExtension();
            $filePath = 'uploads/'.$user->name.'-'.$user->id.'/'. $imageName;
            Storage::disk('s3')->put($filePath,file_get_contents($request->file('image')->getRealPath()),'public');
            $input['image'] = $filePath;
        }

        $this->Article->storeData($input);

        notificationMsg('success','Article Create Successfully!');

        return redirect()->route('article.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article = $this->Article->findData($id);
        $article->tag_id = $article->tag_id ? explode(",", $article->tag_id) : $article->tag_id;
        $categories = ArticlesCategorie::all();
        $tags = ArticlesTag::all();
        return view('admin.articles.edit', compact('article', 'categories', 'tags'));
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

        $article = $this->Article->findData($id);

        $input['slug'] = \Str::slug($input['title'], "_");
        $input['tag_id'] = $input['tag_id'] ? implode(",", $input['tag_id']) : null;
        $user = auth()->guard('admin')->user();

        if($request->hasFile('image')){
            if (!empty($article->image)) {
                Storage::disk('s3')->delete($article->image);
            }
            $imageName = time().rand(0, 10000000000000).pathinfo(rand(111111111111,999999999999), PATHINFO_FILENAME);
            $imageName = $imageName.'.'.$request->file('image')->getClientOriginalExtension();
            $filePath = 'uploads/'.$user->name.'-'.$user->id.'/'. $imageName;
            Storage::disk('s3')->put($filePath,file_get_contents($request->file('image')->getRealPath()),'public');
            $input['image'] = $filePath;
        }

        $this->Article->updateData($id, $input);

        notificationMsg('success','Articles Update Successfully!');

        return redirect()->route('article.index');
    }

    public function view($slug)
    {
        $article = $this->Article->findWithSlugData($slug);
        $article->tags = ArticlesTag::whereIn('id', $article->tag_id ? explode(",", $article->tag_id) : [])
            ->pluck('title')
            ->toArray();

        $article->tags = $article->tags ? implode(", ", $article->tags) : $article->tags;

        return view('admin.articles.show', compact('article'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = $this->Article->findData($id);
        if(!empty($article->image)){
            Storage::disk('s3')->delete($article->image);
        }

        $this->Article->deleteData($id);

        notificationMsg('success','Articles Delete Successfully!');

        return redirect()->route('article.index');
    }
}
