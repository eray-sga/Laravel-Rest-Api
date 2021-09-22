<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->apiResponse(ResultType::Success, Category::all(), 'Categories fetched', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
    

        $category->save();

        return $this->apiResponse(ResultType::Success, $category, 'Category created', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->apiResponse(ResultType::Success, $category,'Category fetched',200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
    

        $category->save();

        return $this->apiResponse(ResultType::Success, $category,'Category updated',200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->apiResponse(ResultType::Success, null,'Category deleted',200);
    }

    //id ve name değerlerini çeker array olarak.
    public function custom1(){
        return Category::pluck('id','name');

    }

    public function report1(){
        //ilişkili tablomuzdan veri çekiyoruz.
        return DB::table('product_categories as pc')
        ->selectRaw('c.name, COUNT(*) as total')
        ->join('categories as c','c.id','=','pc.category_id')
        ->join('products as p','p.id','=','pc.product_id')
        ->groupBy('c.name')
        ->orderByRaw('COUNT(*) DESC')
        ->get();
    }
}
