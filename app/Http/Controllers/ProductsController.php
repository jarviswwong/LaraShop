<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Product;
use function foo\func;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * 获取商品下SKU并格式化:
     * "[symbols;] => [price, stock]"
     * @param Product $product
     * @return mixed
     */
    public function _getProductSkuItems(Product $product)
    {
        return $product->skus
            ->mapWithKeys(function ($item) {
                return [$item['attributes'] => ['price' => $item['price'], 'stock' => $item['stock']]];
            });
    }

    /**
     * 获取该产品下所有已有的商品属性并格式化:
     * "[[ attr_id, items => [[symbol,value]] ]]"
     * @param Product $product
     * @return array
     */
    public function _getProductAttrValues(Product $product)
    {
        $skus_attrs = $product->skus_attributes;
        $attr_values = $product->attr_values;
        $result = collect([]);
        foreach ($skus_attrs as $skus_attr) {
            $collect = collect(['attr_id' => $skus_attr->id]);
            $items = collect([]);
            $attr_values->where('attr_id', $skus_attr->id)
                ->each(function ($item) use ($items) {
                    $attrs = collect([]);
                    $attrs->put('symbol', $item->symbol);
                    $attrs->put('value', $item->value);
                    $items->push($attrs);
                });
            $collect->put('items', $items);
            $result->push($collect);
        }
        return $result;
    }

    public function index(Request $request)
    {
        $builder = Product::where('on_sale', true);

        // Deal with Search Request
        if ($search = $request->input('search', '')) {
            $like = '%' . $search . '%';

            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        //Deal with Order Request
        if ($order = $request->input('order', '')) {
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $matchs)) {
                if (in_array($matchs[1], ['price', 'sold_count', 'rating'])) {
                    $builder->orderBy($matchs[1], $matchs[2]);
                }
            }
        }


        $products = $builder->paginate(16);
        return view('products.index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'order' => $order,
            ]
        ]);
    }

    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未开售');
        }

        $favored = false;
        if ($user = $request->user()) {
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        $sku_items = $this->_getProductSkuItems($product);
        $attr_values = $this->_getProductAttrValues($product);
        $symbolArr = $attr_values->pluck('items')
            ->map(function ($item) {
                return $item->pluck('symbol');
            })
            ->toJson();

        return view('products.show',
            [
                'product' => $product,
                'sku_items' => $sku_items,
                'attr_values' => $attr_values,
                'symbolArr' => $symbolArr,
                'favored' => $favored,
            ]
        );
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);
        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);
        return [];
    }

    /**
     * 收藏商品界面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }

    /**
     * test function
     * @param Product $product
     */
    public function getSkuItems(Product $product)
    {
        $attr_values = $this->_getProductAttrValues($product);
        dd(
            $attr_values->pluck('items')
                ->map(function ($item) {
                    return $item->pluck('symbol');
                })
                ->toJson()
        );
    }
}
