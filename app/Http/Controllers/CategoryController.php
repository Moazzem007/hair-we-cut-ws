<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $categories= Category::all();
        return view('admin.product.category',compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $row = new Category();
        $row->category_name=$request->name;
        $result= $row->save();
            if($result)
            {
                return redirect()->route('category.index')->with('message','Category Add Successful');
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
     public function destroy($id)
     {
         //
         $id = Category::where('id',$id)->firstOrFail();
         $result = $id->delete();
         if ($result) {
             return redirect()->route('category.index')->with('message','Category Delete SuccessFull');
         }
 
     }



     public function categoryforapi()
    {
        try {
            
            $category = Category::all();
            return response()->json($category);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'Error' => $e->getMessage()
            ]);
        }
       
    }
}
