<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticlesCategorie;
use Illuminate\Http\Request;

class ArticleController extends HomeController
{
    public function __construct()
    {
        parent::__construct();
        $this->Article = new Article();
        $this->ArticlesCategorie = new ArticlesCategorie();
        $this->moduleTitleS = 'Article';
        $this->moduleTitleP = 'front.article';
    }

    public function index($id = null)
    {
        $input['category_id'] = $id;
        $data = $this->Article->getData($input);
        $category = $this->ArticlesCategorie->getData();
        return view('front.article.index',compact('data', 'category'));
    }

    public function view($slug)
    {
        $article = $this->Article->findWithSlugData($slug);
        return view('front.article.show',compact('article'));
    }
}
