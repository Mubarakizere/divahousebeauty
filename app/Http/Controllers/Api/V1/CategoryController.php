<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

/**
 * @group Categories
 *
 * APIs for retrieving categories
 */
class CategoryController extends Controller
{
    /**
     * List categories
     *
     * Get a list of all categories.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Electronics",
     *       "slug": "electronics"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    /**
     * Get category details
     *
     * Get details of a specific category by its ID or slug.
     *
     * @urlParam category required The ID or slug of the category. Example: 1
     *
     * @response 200 {
     *   "data": {
     *       "id": 1,
     *       "name": "Electronics",
     *       "slug": "electronics"
     *   }
     * }
     * @response 404 {
     *   "message": "Record not found."
     * }
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }
}
