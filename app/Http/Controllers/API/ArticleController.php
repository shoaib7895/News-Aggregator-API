<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     description="Article Model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the article"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the article"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Content of the article"
 *     ),
 *     @OA\Property(
 *         property="published_at",
 *         type="string",
 *         format="date-time",
 *         description="Publication date"
 *     ),
 *     @OA\Property(
 *         property="category",
 *         type="string",
 *         description="Category of the article"
 *     ),
 *     @OA\Property(
 *         property="source",
 *         type="string",
 *         description="Source of the article"
 *     )
 * )
 */
class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get list of articles",
     *     tags={"Articles"},
     *     security={{"apiKey":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article")),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Generate a unique cache key based on the request parameters
        $cacheKey = 'articles_' . md5(serialize($request->all()));

        // Cache the articles list for 60 minutes
        $articles = Cache::remember($cacheKey, 60, function () use ($request) {
            $articles = Article::query();

            if ($request->has('keyword')) {
                $articles->where('title', 'like', '%' . $request->keyword . '%')
                        ->orWhere('content', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('date')) {
                $articles->whereDate('published_at', $request->date);
            }

            if ($request->has('category')) {
                $articles->where('category', $request->category);
            }

            if ($request->has('source')) {
                $articles->where('source', $request->source);
            }

            return $articles->orderBy('published_at', 'desc')->paginate(10);
        });

        return response()->json($articles);
    }


    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get a specific article by ID",
     *     tags={"Articles"},
     *     security={{"apiKey":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Article"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function show($id)
    {
        // Cache the article for 60 minutes
        $article = Cache::remember("article_{$id}", 60, function () use ($id) {
            return Article::findOrFail($id);
        });

        return response()->json($article);
    }
}
