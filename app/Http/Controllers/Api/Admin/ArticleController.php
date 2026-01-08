<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ArticleResource;
use App\Models\Article;
use App\Models\ArticleDetail;
use App\Traits\StatusResponser;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use StatusResponser;

    public function index()
    {
        $articles = Article::query();

        $articles = $this->whereClause($articles);
        $articles = $this->loadRelations($articles);
        $articles = $this->sortingAndLimit($articles);

        return $this->apiSuccessResponse(ArticleResource::collection($articles), 'Data Get Successfully!');
    }

    public function show(Article $Article)
    {
        if (isset($_GET['withArticleDetail']) && $_GET['withArticleDetail'] == '1') {
            $Article = $Article->loadMissing('articleDetail');
        }

        return $this->apiSuccessResponse(new ArticleResource($Article), 'Data Get Successfully!');
    }

    public function store(Request $request)
    {
        $validationRule = [];
        $errorMessages = [];
        $validationRule = array_merge($validationRule, ['language' => ['required']]);
        $errorMessages = array_merge($errorMessages, ['language' . '.required' => 'Language is required']);
        $validationRule = array_merge($validationRule, ['agency' => ['required', 'string']]);
        $errorMessages = array_merge($errorMessages, ['agency' . '.required' => 'Agency is required']);
        $validationRule = array_merge($validationRule, ['title' => ['required', 'string']]);
        $errorMessages = array_merge($errorMessages, ['title' . '.required' => 'Title is required']);
        $validationRule = array_merge($validationRule, ['description' => ['required', 'string']]);
        $errorMessages = array_merge($errorMessages, ['description' . '.required' => 'Description is required']);

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );
        $Article = Article::create([
            'agency' => $request->agency,
            'added_by' => $request->added_by,
        ]);

        if ($Article) {
            ArticleDetail::create([
                'article_id' => $Article->id,
                'language_id' => $request->language,
                'title' => $request->title,
                'description' => $request->description,
            ]);
            return $this->apiSuccessResponse(new ArticleResource($Article), 'Article has been added successfully.');
        }
        return $this->errorResponse();
    }

    public function update(Request $request, Article $Article)
    {
        $validationRule = [];
        $errorMessages = [];
        $validationRule = array_merge($validationRule, ['language' => ['required']]);
        $errorMessages = array_merge($errorMessages, ['language' . '.required' => 'Language is required']);
        $validationRule = array_merge($validationRule, ['agency' => ['required', 'string']]);
        $errorMessages = array_merge($errorMessages, ['agency' . '.required' => 'Agency is required']);
        $validationRule = array_merge($validationRule, ['title' => ['required', 'string']]);
        $errorMessages = array_merge($errorMessages, ['title' . '.required' => 'Title is required']);
        $validationRule = array_merge($validationRule, ['description' => ['required', 'string']]);
        $errorMessages = array_merge($errorMessages, ['description' . '.required' => 'Description is required']);

        $this->validate(
            $request,
            $validationRule,
            $errorMessages
        );


        $Article->update([
            'agency' => $request->agency,
            'added_by' => $request->added_by,
        ]);
        
        $ArticleDetail = ArticleDetail::whereArticleId($Article->id)->exists();
        if ($ArticleDetail) {
            ArticleDetail::whereArticleId($Article->id)->update([
                'article_id' => $Article->id,
                'language_id' => $request->language,
                'title' => $request->title,
                'description' => $request->description,
            ]);
        } else {
            ArticleDetail::create([
                'article_id' => $Article->id,
                'language_id' => $request->language,
                'title' => $request->title,
                'description' => $request->description,
            ]);
        }

        if ($Article) {
            return $this->apiSuccessResponse(new ArticleResource($Article), 'Article has been updated successfully.');
        }
        return $this->errorResponse();
    }

    public function destroy(Article $Article)
    {
        if ($Article->articleDetail()->delete() && $Article->delete()) {
            return $this->apiSuccessResponse(new ArticleResource($Article), 'Article has been deleted successfully.');
        }
        return $this->errorResponse();
    }

    protected function loadRelations($articles)
    {
        // $defaultLang = getDefaultLanguage();
        // $articles = $articles->with(['articleDetail' => function ($q) use ($defaultLang) {
        //     $q->where('language_id', $defaultLang->id);
        // }]);
        $articles = $articles->with('articleDetail');
        return $articles;
    }

    protected function sortingAndLimit($articles)
    {
        $sortType = ['ASC', 'asc', 'DESC', 'desc'];
        $sortBy = ['id'];
        if (isset($_GET['sortBy']) && $_GET['sortBy'] != '' && isset($_GET['sortType']) && $_GET['sortType'] != '' && in_array($_GET['sortBy'], $sortBy) && in_array($_GET['sortType'], $sortType)) {
            $articles = $articles->OrderBy($_GET['sortBy'], $_GET['sortType']);
        }

        if (isset($_GET['limit']) && $_GET['limit'] != '') {
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return $articles->paginate($limit);
    }

    protected function whereClause($articles)
    {
        if (isset($_GET['searchParam']) && $_GET['searchParam'] != '') {
            $articles = $articles->whereHas('articleDetail', function ($q) {
                $q->where('id', 'LIKE', '%' . $_GET['searchParam'] . '%');
            });
        }
        return $articles;
    }
}
