<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // ربط التصنيفات بمقالة موجودة
    public function attachTags($article_id, Request $request)
    {
        $article = Article::find($article_id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        // التحقق من صحة البيانات
        $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        // ربط التصنيفات (يضيف فقط بدون حذف القديمة)
        $article->tags()->attach($request->tag_ids);

        return response()->json([
            'success' => true,
            'message' => 'Tags attached successfully',
            'data' => $article->load('tags')
        ]);
    }

    // مزامنة التصنيفات (يستبدل القديمة بالجديدة)
    public function syncTags($article_id, Request $request)
    {
        $article = Article::find($article_id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        // مزامنة التصنيفات (يحذف القديمة ويضع الجديدة)
        $article->tags()->sync($request->tag_ids);

        return response()->json([
            'success' => true,
            'message' => 'Tags synced successfully',
            'data' => $article->load('tags')
        ]);
    }

    // إنشاء مقالة جديدة مع التصنيفات
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // ربط التصنيفات إذا كانت موجودة
        if ($request->has('tag_ids')) {
            $article->tags()->attach($request->tag_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => $article->load('tags')
        ], 201);
    }

    // حذف تصنيف من المقالة
    public function detachTag($article_id, $tag_id)
    {
        $article = Article::find($article_id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $article->tags()->detach($tag_id);

        return response()->json([
            'success' => true,
            'message' => 'Tag detached successfully',
            'data' => $article->load('tags')
        ]);
    }

    // حذف جميع التصنيفات من المقالة
    public function detachAllTags($article_id)
    {
        $article = Article::find($article_id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $article->tags()->detach();

        return response()->json([
            'success' => true,
            'message' => 'All tags detached successfully',
            'data' => $article
        ]);
    }
}
