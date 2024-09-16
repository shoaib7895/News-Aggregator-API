<?php

namespace App\Http\Controllers\API;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="User Preferences",
 *     description="User preferences management operations"
 * )
 */
class UserPreferenceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Retrieve the user's preferences",
     *     tags={"User Preferences"},
     *     security={{"apiKey":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of preferences",
     *         @OA\JsonContent(
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(Request $request)
    {
        $preferences = $request->user()->preferences;

        return response()->json($preferences);
    }

    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Set or update user preferences",
     *     tags={"User Preferences"},
     *     security={{"apiKey":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Preferences updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'preferred_sources' => 'array|nullable',
            'preferred_categories' => 'array|nullable',
            'preferred_authors' => 'array|nullable',
        ]);

        $user = $request->user();

        // Update or create the user's preferences
        $user->preferences()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json(['message' => 'Preferences updated successfully']);
    }
    /**
     * @OA\Get(
     *     path="/api/personalized-feed",
     *     summary="Generate a personalized news feed based on user preferences",
     *     tags={"User Preferences"},
     *     security={{"apiKey":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of personalized feed",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="source", type="string"),
     *                 @OA\Property(property="category", type="string"),
     *                 @OA\Property(property="author", type="string"),
     *                 @OA\Property(property="published_at", type="string", format="date-time")
     *             )),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No preferences found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No preferences found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function personalizedFeed(Request $request)
    {
        $user = $request->user();
        
        // Cache the personalized feed for this user for 60 minutes
        $cacheKey = 'personalized_feed_' . $user->id;
        $personalizedArticles = Cache::remember($cacheKey, 60, function () use ($user) {
            $preferences = $user->preferences;

            if (!$preferences) {
                return []; // Return an empty array or handle as needed
            }

            // Fetch articles based on user's preferences
            $articlesQuery = Article::query();

            if ($preferences->preferred_sources && count($preferences->preferred_sources) > 0) {
                $articlesQuery->whereIn('source', $preferences->preferred_sources);
            }
            
            if ($preferences->preferred_categories && count($preferences->preferred_categories) > 0) {
                $articlesQuery->whereIn('category', $preferences->preferred_categories);
            }
            
            if ($preferences->preferred_authors && count($preferences->preferred_authors) > 0) {
                $articlesQuery->whereIn('author', $preferences->preferred_authors);
            }

            return $articlesQuery->orderBy('published_at', 'desc')->paginate(10);
        });

        // Return the response outside the cache closure
        if (empty($personalizedArticles)) {
            return response()->json(['message' => 'No articles found'], 404);
        }

        return response()->json($personalizedArticles);
    }
    
}
