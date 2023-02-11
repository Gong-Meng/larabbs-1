<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Transformers\CategoryTransformer;
use Dingo\Api\Transformer\Factory;

class CategoriesController extends Controller
{
    public function index(Request $request, Factory $transformerFactory)
    {
        if ($request->include == 'children') {
            $categories = Category::defaultOrder()->get()->toTree();
            // 关闭 Dingo 的预加载
            $transformerFactory->disableEagerLoading();
        } else {
            $categories = Category::whereIsRoot()->defaultOrder()->get();
        }

        return $this->response->collection($categories, new CategoryTransformer());
    }
}
