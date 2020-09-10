<?php
  
namespace App\Http\Controllers;
use DB;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
  
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest()->paginate(5);
  
        return view('products.index',compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
            'file'   => 'required| mimes:pdf',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

     if ($request->hasFile('image', 'file')) {
            // uploading image
            $postImage = $request->file('image');
            $imageName = $postImage->getClientOriginalName();
            $directory = 'public/images/';
            $imgUrl = $directory.$imageName;
            $postImage->move($directory, $imageName);

            $postImage1 = $request->file('file');
            $imageName1 = $postImage1->getClientOriginalName();
            $directory1 = 'public/file/';
            $imgUrl1 = $directory1.$imageName1;
            $postImage1->move($directory1, $imageName1);
            // data sent to database
            $product = new Product();
            $product->name = $request->name;
            $product->detail = $request->detail;
            $product->image = $imgUrl;
            $product->file = $imgUrl1;
            $product->save();
            return redirect()->route('products.index')
                        ->with('success','Product created successfully.');
        }
        $product = new Product();
        $product->name = $request->name;
        $product->detail = $request->detail;
        $product->save();
        return redirect()->route('products.index')
                        ->with('success','Product created successfully.');
        
    }
   
    public function show(Product $product)
    {
        return view('products.show',compact('product'));
    }
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('products.edit',compact('product'));
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
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
  
        $product->update($request->all());
  
        return redirect()->route('products.index')
                        ->with('success','Product updated successfully');
    }
        
    public function destroy(Product $product)
    {
        $product->delete();
  
        return redirect()->route('products.index')
                        ->with('success','Product deleted successfully');
    }

    public function downloadRepo(Request $request, $id)
    {   
        $repo = Product::find($id);
        // dd();

        $pathToFile = public_path('../' . $repo->file);
        $name = time().'-'.'File.pdf';
        $headers = ['Content-Type: application/pdf'];
        return response()->download($pathToFile, $name, $headers);
    }
}