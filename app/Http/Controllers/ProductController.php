<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(){
        $products = Product::with('category')->get();
        $category = Category::all();
        return view('welcome',compact('products','category'));
    }

    public function create(){
        $category = Category::all();
        return view('index',compact('category'));
    }

    public function store(Request $request){
        Product::create([
            'name'=>$request->name,
            'price'=>$request->price,
            'description'=>$request->description,
            'category_id'=>$request->category_id
        ]);
        return 
            "<script>
                alert('Product added successfully');
                window.location.href='/';
            </script>";
    }

    public function update(Request $request){
        $product = Product::findOrFail($request->id);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description'=>$request->description,
            'category_id'=>$request->category_id
        ]);

        return
            "<script>
                alert('Product Update Successfully');
                window.location.href='/';
            </script>";
    }

    public function destory(Request $request){

        $product = Product::findOrFail($request->id);
        $product->delete();

        return 
        "<script>
            alert('Product delete Successfully');
            window.location.href='/';
        </script>";
    }
}


