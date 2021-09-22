<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductWithCategoriesResource;
use App\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return Product::all();
        //return response()->json(Product::all(), 200);
        //return response(Product::all(), 200);
        //return response(Product::paginate(10), 200);
        $offset = $request->has('offset') ? $request->query('offset') : 0;
        $limit = $request->has('limit') ? $request->query('limit') : 10;

        $list = Product::query()->with('categories'); //ilişkili tablodan veri çekme
        if($request->has('q')) //filtreleme, arama işlemleri yapar q parametresi üzerinden.
            $list->where('name','like','%' . $request->query('q') . '%');

        if($request->has('sortBy'))
            $list->orderBy($request->query('sortBy'), $request->query('sort','DESC'));
            //sortBy parametresi gönderirsek belirttiğimiz sütuna göre descending sıralar.

        return response($list->offset($offset)->limit($limit)->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$input = $request->all();
        //$product = Product::create($input);
        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->price = $request->price;
        $product->description = $request->description;

        $product->save();

        return response([
            'data' => $product,
            'message' => "product created"
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $product = Product::findOrFail($id);
            return $this->apiResponse(ResultType::Success, $product, 'Product found', 200);
            
        }
        catch(ModelNotFoundException $exception){
            return $this->apiResponse(ResultType::Error, null, 'Product not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->price = $request->price;
        $product->description = $request->description;

        $product->save();

        return response([
            'data' => $product,
            'message' => "product updated"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response([
            "message" => "product deleted"
        ], 200);
    }

    public function custom1(){
        //return Product::select('id','name')->orderBy('created_at','desc')->take(10)->get();
        //kolon isimlerini istediğimiz gibi özelleştirdik.
        return Product::selectRaw('id product_id, name product_name')->orderBy('created_at','desc')->take(10)->get();
    }

    public function custom2(){
        //map metodu ile de sütunlarımızın isimlerini ve hatta değerlerini özelleştirip dönebiliriz.
        $products = Product::orderBy('created_at','desc')->take(10)->get();

        $mapped = $products->map(function($product) {
            return [
                '_id' => $product['id'],
                'product_name' => $product['name'],
                'product_price' => $product['price'] * 1.03
            ];
        });

        return $mapped->all();
    }

    public function custom3(){
        $products = Product::paginate(10);

        return ProductResource::collection($products);
    }

    public function listwithcategories(){
        $products = Product::paginate(10);

        return ProductWithCategoriesResource::collection($products);
    }
}
