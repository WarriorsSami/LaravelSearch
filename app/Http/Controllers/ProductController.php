<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function frontend()
    {
        return Product::all();
    }

    public function backend(Request $request)
    {
        $query = Product::query();

        // search items
        if ($s = $request->input('search'))
        {
            $query->whereRaw("title LIKE ?", ["%{$s}%"])
                ->orWhereRaw("description LIKE ?", ["%{$s}%"]);
        }

        // sort items
        if ($s = $request->input('sort'))
        {
            $query->orderBy('price', $s);
        }

        // paginate items
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);

        // count items after applying search and sort filters
        $total = $query->count();

        $result = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return [
            "data" => $result,
            "total" => $total,
            "page" => $page,
            "last_page" => ceil($total / $perPage),
        ];
    }
}
