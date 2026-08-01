<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\ProductResource;
use App\Models\Product;

/**
 * @group Products
 *
 * APIs for retrieving products
 */
class ProductController extends Controller
{
    /**
     * List products
     *
     * Get a paginated list of products.
     * 
     * @queryParam page int The page number. Example: 1
     * @queryParam category_id int Filter by category ID. Example: 2
     * @queryParam search string Search term. Example: phone
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Product Name",
     *       "slug": "product-name",
     *       "description": "Product Description",
     *       "express_price": 1000,
     *       "standard_price": 800,
     *       "formatted_price": "800 RWF",
     *       "formatted_express_price": "1000 RWF",
     *       "image_urls": ["http://example.com/image.jpg"],
     *       "in_stock": true,
     *       "is_new": true,
     *       "is_on_sale": false,
     *       "sale_price": null,
     *       "average_rating": 4.5,
     *       "review_count": 10,
     *       "category_id": 1,
     *       "brand_id": 1
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('category_id')) {
            $query->inCategory($request->category_id);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $products = $query->paginate(15);

        return ProductResource::collection($products);
    }

    /**
     * Get product details
     *
     * Get details of a specific product by its ID or slug.
     *
     * @urlParam product required The ID or slug of the product. Example: 1
     *
     * @response 200 {
     *   "data": {
     *       "id": 1,
     *       "name": "Product Name"
     *   }
     * }
     * @response 404 {
     *   "message": "Record not found."
     * }
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}
