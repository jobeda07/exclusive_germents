<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use App\Models\AccountLedger;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\MultiImg;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductStock;
use App\Models\Unit;
use Carbon\Carbon;
use Image;
use Session;
use Illuminate\Support\Str;
use Auth;
use Illuminate\Support\Collection;
use Milon\Barcode\DNS1D;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /*=================== Start ProductView Methoed ===================*/
    public function ProductView(){
    	if(Auth::guard('admin')->user()->role == '2'){
            $products = Product::where('vendor_id', Auth::guard('admin')->user()->id)->latest()->get();
            //return $products;
        }else{
            $products = Product::latest()->get();
        }

        return view('backend.product.product_view',compact('products'));

    } // end method




    /*=================== Start ProductAdd Methoed ===================*/
    public function ProductAdd(){
    	$categories = Category::where('parent_id', 0)->with('childrenCategories')->orderBy('name_en','asc')->get();
		$brands = Brand::latest()->get();
        $vendors = Vendor::latest()->get();
        $suppliers = Supplier::latest()->get();
        $units = Unit::latest()->get();
        $attributes = Attribute::latest()->get();
    	return view('backend.product.product_add',compact('categories','brands','vendors','suppliers','attributes','units'));

    } // end method

    /*=================== Start StoreProduct Methoed ===================*/
    public function StoreProduct(Request $request){
        $vqtys = 0;
        if ($request->has('vqtys') && is_array($request->vqtys)) {
            foreach ($request->vqtys as $key => $vqty) {
                $vqtys = $vqtys + $vqty;
            }
        }
        if($vqtys > $request->stock_qty){
            $notification = array(
                'message' => 'The Variation quantity will not be greater than the stock quantity',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
        else{
        $request->validate([
            'name_en'           => 'required|max:150',
            'purchase_price'    => 'required|numeric',
            'wholesell_price'   => 'nullable|numeric',
            'discount_price'    => 'nullable|numeric',
            'regular_price'     => 'required|numeric',
            'stock_qty'         => 'required|integer',
            'low_qty'           => 'required|integer',
            'minimum_buy_qty'   => 'required|integer',
            'description_en'    => 'nullable|string',
            'short_description_en'    => 'nullable|string',
            'category_id'       => 'required|integer',
            'unit_weight'       => 'nullable|numeric',
            'unit_id'           => 'nullable|integer',
            'product_sku'  => 'required|max:150|unique:products,product_sku',
            'product_thumbnail' => 'nullable|file',
        ]);

        if(!$request->name_bn){
            $request->name_bn = $request->name_en;
        }

        if(!$request->description_bn){
            $request->description_bn = $request->description_en;
        }


        if(!$request->short_description_bn){
            $request->short_description_bn = $request->short_escription_en;
        }

        // $slug = strtolower(str_replace(' ', '-', $request->name_en));
        if ($request->slug != null) {
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        }else {
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name_en)).'-'.Str::random(5);
        }

        if($request->vendor_id == null || $request->vendor_id==""){
            $request->vendor_id = 0;
        }

        if($request->supplier_id == null || $request->supplier_id==""){
            $request->supplier_id = 0;
        }

        if($request->unit_id == null || $request->unit_id==""){
            $request->unit_id = 0;
        }
        if($request->low_qty > $request->stock_qty){
            $notification = array(
                'message' => 'Low Quantity is Not Geater Then Stock Quantity ',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }

        if($request->hasfile('product_thumbnail')){
            $image = $request->file('product_thumbnail');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            Image::make($image)->resize(900,1200)->save('upload/products/thumbnails/'.$name_gen);
            $save_url = 'upload/products/thumbnails/'.$name_gen;
        }else{
            $save_url = '';
        }
        $checkCode = rand(10000,99999);
        $productCodeCheck = Product::where('product_code', $checkCode)->first();

        if($productCodeCheck){
            $checkCode = rand( 1,100,999);
        }

        if ($request->reseller_price == null || $request->reseller_price == "") {
            $request->reseller_price = 0;

            $default_percentage = get_setting('reseller_discount_percent')->value ?? '0.00';

            if ($default_percentage && $default_percentage > 0) {
                $request->reseller_price = $request->regular_price - ($request->regular_price * $default_percentage) * 1.0 / 100;
            }
        }

        if ($request->reseller_discount_variant == null || $request->reseller_discount_variant == "") {
            $request->reseller_discount_variant = 0;

            $default_percentage = get_setting('reseller_discount_percent')->value ?? '0.00';

            if ($default_percentage && $default_percentage > 0) {
                $request->reseller_discount_variant = $default_percentage;
            }
        }

        $product = Product::create([
            'brand_id'              => $request->brand_id,
            'category_id'           => $request->category_id,
            'vendor_id'             => $request->vendor_id,
            'supplier_id'           => $request->supplier_id,
            'unit_id'               => $request->unit_id,
            'name_en'               => $request->name_en,
            'name_bn'               => $request->name_bn,
            'slug'                  => $slug,
            'product_code'          => $checkCode,
            'reseller_price'        => $request->reseller_price,
            'reseller_discount_variant' => $request->reseller_discount_variant,
            'unit_weight'           => $request->unit_weight,
            'youtube_link'          => $request->youtube_link,
            'purchase_price'        => $request->purchase_price,
            'wholesell_price'       => $request->wholesell_price,
            'wholesell_minimum_qty' => $request->wholesell_minimum_qty,
            'regular_price'         => $request->regular_price,
            'discount_price'        => $request->discount_price,
            'discount_type'         => $request->discount_type,
            'product_sku'           => $request->product_sku,
            'product_code'          => rand(10000,99999),
            'minimum_buy_qty'       => $request->minimum_buy_qty,
            'stock_qty'             => $request->stock_qty,
            'low_qty'               => $request->low_qty,
            'previous_stock'        => $request->stock_qty,
            'description_en'        => $request->description_en,
            'short_description_en'  => $request->short_description_en,
            'description_bn'        => $request->description_bn,
            'short_description_bn'  => $request->short_description_bn,
            'is_featured'           => $request->is_featured ? 1 : 0,
            'is_deals'              => $request->is_deals ? 1 : 0,
            'is_digital'            => $request->is_digital ? 1 : 0,
            'status'                => $request->status ? 1 : 0,
            'top_selling'           => $request->top_selling ? 1 : 0,
            'new_arrival'           => $request->new_arrival ? 1 : 0,
            'show_stock'            => $request->show_stock ? 1 : 0,
            'product_thumbnail'     => $save_url,
            'created_by'            => Auth::guard('admin')->user()->id,
        ]);



        // dd($product);

        /* ========= Start Multiple Image Upload ========= */
        $images = $request->file('multi_img');
        if($images){
            foreach ($images as $img) {
                $make_name = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
                Image::make($img)->resize(900,1200)->save('upload/products/multi-image/'.$make_name);
                $uploadPath = 'upload/products/multi-image/'.$make_name;

                MultiImg::insert([
                    'product_id' => $product->id,
                    'photo_name' => $uploadPath,
                    'created_at' => Carbon::now(),
                ]);
            }
        }
        /* ========= End Multiple Image Upload ========= */

        /* ========= Product Attributes Start ========= */
        $attribute_values = array();
        if($request->has('choice_attributes')){
            foreach ($request->choice_attributes as $key => $attribute)
            {
                $atr = 'choice_options'.$attribute;
                $item['attribute_id'] = $attribute;
                $data = array();

                foreach ($request[$atr] as $key => $value) {
                    array_push($data, $value);
                }
                $item['values'] = $data;
                array_push($attribute_values, $item);
            }
        }
        if (!empty($request->choice_attributes)) {
            $product->attributes = json_encode($request->choice_attributes);
            $product->is_varient = 1;
        
            // Check if the variant-related arrays are present and not empty
            if ($request->has('vnames') && 
                !empty($request->vnames) && 
                !empty($request->vskus) && 
                !empty($request->vprices) && 
                !empty($request->vresellprices) && 
                !empty($request->vqtys)) {
        
                // Ensure all arrays have the same length
                $countVNames = count($request->vnames);
                $countVSkus = count($request->vskus);
                $countVPrices = count($request->vprices);
                $countVResellPrices = count($request->vresellprices);
                $countVQtys = count($request->vqtys);
        
                if ($countVNames !== $countVSkus || 
                    $countVNames !== $countVPrices || 
                    $countVNames !== $countVResellPrices || 
                    $countVNames !== $countVQtys) {
                    return response()->json(['error' => 'Input arrays have mismatched lengths'], 400);
                }
        
                $i = 0;
                foreach ($request->vnames as $key => $name) {
                    // Check if the necessary keys exist in each array before accessing them
                    if (!isset($request->vskus[$i], $request->vprices[$i], $request->vresellprices[$i], $request->vqtys[$i])) {
                        continue;
                    }
        
                    // Generate a unique stock code
                    $stockcheckCode = rand(10000, 99999);
                    $productStockCodeCheck = Product::where('product_code', $stockcheckCode)->first();
                    if ($productStockCodeCheck) {
                        $stockcheckCode = rand(10000, 99999);
                    }
        
                    // Create the product stock
                    $stock = ProductStock::create([
                        'product_id'    => $product->id,
                        'varient'       => $name,
                        'stock_code'    => $stockcheckCode,
                        'sku'           => $request->vskus[$i],
                        'price'         => $request->vprices[$i],
                        'resell_price'  => $request->vresellprices[$i],
                        'qty'           => $request->vqtys[$i],
                        'pre_qty'       => $request->vqtys[$i],
                    ]);
        
                    // Handle variant images
                    if (!empty($request->vimages[$i])) {
                        $image = $request->vimages[$i];
                        if ($image) {
                            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
                            Image::make($image)->resize(900, 1200)->save('upload/products/variations/' . $name_gen);
                            $stock->image = 'upload/products/variations/' . $name_gen;
                        } else {
                            $stock->image = '';
                        }
                    }
        
                    // Save the stock
                    $stock->save();
                    $i++;
                }
            } else {
                return response()->json(['error' => 'Variant data is missing or invalid'], 400);
            }
        } else {
            $product->attributes = json_encode([]);
        }

        $attr_values = collect($attribute_values);
        $attr_values_sorted = $attr_values->sortByDesc('attribute_id');

        $sorted_array = array();
        foreach($attr_values_sorted as $attr){
            array_push($sorted_array, $attr);
        }

        $product->attribute_values = json_encode($sorted_array, JSON_UNESCAPED_UNICODE);
        /* ========= End Product Attributes ========= */

        /* =========== Start Product Tags =========== */
        $product->tags = implode(',', $request->tags);
        /* =========== End Product Tags =========== */
        $product->save();

        //Ledger Entry
        $ledger = AccountLedger::create([
            'account_head_id' => 1,
            'particulars' => $product->name_en,
            'debit' => $product->purchase_price * $product->stock_qty,
            'product_id' => $product->id,
            'type' => 1,
        ]);

        $ledger->balance = get_account_balance() - $product->purchase_price * $product->stock_qty;
        $ledger->save();
        $notification = array(
            'message' => 'Product Inserted Successfully',
            'alert-type' => 'success'
        );
     }
        return redirect()->route('product.all')->with($notification);
    } // end method

    /*=================== Start EditProduct Methoed ===================*/
    public function EditProduct($id){

        $product = Product::findOrFail($id);
        $multiImgs = MultiImg::where('product_id',$id)->get();
        $categories = Category::latest()->get();
        $brands = Brand::latest()->get();
        $vendors = Vendor::latest()->get();
        $suppliers = Supplier::latest()->get();
        $units = Unit::latest()->get();
        $attributes = Attribute::latest()->get();
        return view('backend.product.product_edit',compact('categories','vendors','suppliers','brands','attributes','product','multiImgs','units'));

    } // end method

    /*=================== Start ProductUpdate Methoed ===================*/
    public function ProductUpdate(Request $request, $id){
    // return $request->all();
        $product = Product::find($id);
        $vqtys = 0;
        $product_stocks = $product->stocks;

        if(count( $product_stocks) > 0 ) {
            foreach( $product_stocks as $stock ) {
                 $qty = $stock->id."_qty";
                 $vqtys  +=   $request->$qty;
            }
        }
        if($vqtys > $request->stock_qty){
            $notification = array(
                'message' => 'The Variation quantity will not be greater than the stock quantity',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }else{

            $this->validate($request,[
                'name_en'           => 'required|max:150',
                'purchase_price'    => 'required|numeric',
                'wholesell_price'   => 'nullable|numeric',
                'discount_price'    => 'nullable|numeric',
                'regular_price'     => 'required|numeric',
                'stock_qty'         => 'required|integer',
                'low_qty'           => 'required|integer',
                'description_en'    => 'nullable|string',
                'short_description_en'    => 'nullable|string',
                'category_id'       => 'required|integer',
                'brand_id'          => 'nullable|integer',
                'unit_id'           => 'nullable|integer',
                'unit_weight'       => 'nullable|numeric',
                'product_sku'       => ['required','max:150', Rule::unique('products', 'product_sku')->ignore($product->id),],
            ]);

            if(!$request->name_bn){
                $request->name_bn = $request->name_en;
            }

            if(!$request->description_bn){
                $request->description_bn = $request->description_en;
            }

            if(!$request->short_description_bn){
                $request->short_description_bn = $request->short_description_en;
            }

            if($request->name_en != $product->name_en){
                if ($request->slug != null) {
                    $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
                }else {
                    $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name_en)).'-'.Str::random(5);
                }
            }else{
                $slug = $product->slug;
            }

            if($request->vendor_id == null || $request->vendor_id==""){
                $request->vendor_id = 0;
            }

            if($request->supplier_id == null || $request->supplier_id==""){
                $request->supplier_id = 0;
            }

            if($request->unit_id == null || $request->unit_id==""){
                $request->unit_id = 0;
            }
            if($request->low_qty > $request->stock_qty){
                $notification = array(
                    'message' => 'Low Quantity is Not Geater Then Stock Quantity ',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }

            if($request->hasfile('product_thumbnail')){
                try {
                    if(file_exists($product->product_thumbnail)){
                        unlink($product->product_thumbnail);
                    }
                } catch (Exception $e) {

                }
                $image = $request->file('product_thumbnail');
                $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
                Image::make($image)->resize(900,1200)->save('upload/products/thumbnails/'.$name_gen);
                $save_url = 'upload/products/thumbnails/'.$name_gen;
            }else{
                $save_url = $product->product_thumbnail;
            }

            $pre_stock =$product->stock_qty;

            if ($request->reseller_price == null || $request->reseller_price == "") {
                $request->reseller_price = 0;

                $default_percentage = get_setting('reseller_discount_percent')->value ?? '0.00';

                if ($default_percentage && $default_percentage > 0) {
                    $request->reseller_price = $request->regular_price - ($request->regular_price * $default_percentage) * 1.0 / 100;
                }
            }

            if ($request->reseller_discount_variant == null || $request->reseller_discount_variant == "") {
                $request->reseller_discount_variant = 0;

                $default_percentage = get_setting('reseller_discount_percent')->value ?? '0.00';

                if ($default_percentage && $default_percentage > 0) {
                    $request->reseller_discount_variant = $default_percentage;
                }
            }

            $product->update([
                'brand_id'              => $request->brand_id,
                'category_id'           => $request->category_id,
                'vendor_id'             => $request->vendor_id,
                'supplier_id'           => $request->supplier_id,
                'unit_id'               => $request->unit_id,
                'name_en'               => $request->name_en,
                'name_bn'               => $request->name_bn,
                'slug'                  => $slug,
                'unit_weight'           => $request->unit_weight,
                'product_sku'           => $request->product_sku,
                'youtube_link'          => $request->youtube_link,
                'purchase_price'        => $request->purchase_price,
                'wholesell_price'       => $request->wholesell_price,
                'wholesell_minimum_qty' => $request->wholesell_minimum_qty,
                'regular_price'         => $request->regular_price,
                'discount_price'        => $request->discount_price,
                'discount_type'         => $request->discount_type,
                'reseller_price'        => $request->reseller_price,
                'reseller_discount_variant' => $request->reseller_discount_variant,
                'minimum_buy_qty'       => $request->minimum_buy_qty,
                'stock_qty'             => $request->stock_qty,
                'low_qty'               => $request->low_qty,
                'previous_stock'        => $pre_stock,
                'description_en'        => $request->description_en,
                'short_description_en'  => $request->short_description_en,
                'description_bn'        => $request->description_bn,
                'short_description_bn'  => $request->short_description_bn,
                'is_featured'           => $request->is_featured ? 1 : 0,
                'is_deals'              => $request->is_deals ? 1 : 0,
                'is_digital'            => $request->is_digital ? 1 : 0,
                'status'                => $request->status ? 1 : 0,
                'top_selling'           => $request->top_selling ? 1 : 0,
                'new_arrival'           => $request->new_arrival ? 1 : 0,
                'show_stock'            => $request->show_stock ? 1 : 0,
                'product_thumbnail'     => $save_url,
                'created_by' => Auth::guard('admin')->user()->id,
            ]);


            $pre_stockUpdate = ProductStock::where('product_id', $product->id)->update([
                'pre_qty' => $pre_stock,
            ]);

            /* ========= Product Previous Stock Clear ========= */
            $product_stocks = $product->stocks;
            if(count( $product_stocks) > 0 ) {
                if($request->is_variation_changed){
                    foreach( $product_stocks as $stock ) {
                        // unlink($stock->image);
                        try {
                            if(file_exists($stock->image)){
                                unlink($stock->image);
                            }
                        } catch (Exception $e) {

                        }
                        $stock->delete();
                    }
                }else{
                    foreach( $product_stocks as $stock ) {
                        try {
                            if(file_exists($stock->image)){
                                //unlink($stock->image);
                            }
                            $newImage = $request->file($stock->id."_image");
                            if ($newImage && $newImage->isValid()) {
                                // Generate a new image name and save it
                                $name_gen = hexdec(uniqid()) . '.' . $newImage->getClientOriginalExtension();
                                Image::make($newImage)->resize(900, 1200)->save('upload/products/variations/' . $name_gen);
                                $save_url = 'upload/products/variations/' . $name_gen;

                                $stock->update([
                                    'image' => $save_url,
                                ]);
                            }

                            } catch (Exception $e) {
                        }

                        $variant = $stock->id."_variant";
                        $price = $stock->id."_price";
                        $resell_price = $stock->id."_resell_price";
                        $sku = $stock->id."_sku";
                        $qty = $stock->id."_qty";
                        $image = $stock->id."_image";

                        $stock->update([
                            'sku' => $request->$sku,
                            'price' => $request->$price,
                            'resell_price' => $request->$resell_price,
                            'qty' => $request->$qty,
                        ]);
                    }

                }
            }

            if($request->is_variation_changed){
                /* ========= Product Attributes Start ========= */
                $attribute_values = array();
            if ($request->has('choice_attributes') && is_array($request->choice_attributes)) {
                    foreach ($request->choice_attributes as $key => $attribute) {
                        $atr = 'choice_options' . $attribute;
                        $item['attribute_id'] = $attribute;
                        $data = array();

                        if ($request->has($atr) && is_array($request[$atr])) { // Check if it exists and is an array
                            foreach ($request[$atr] as $key => $value) {
                                array_push($data, $value);
                            }
                        }

                        $item['values'] = $data;
                        array_push($attribute_values, $item);
                    }
                }

                if (!empty($request->choice_attributes)) {
                    $product->attributes = json_encode($request->choice_attributes);
                    $product->is_varient = 1;

                    if($request->has('vnames')){
                        $i = 0;
                        foreach ($request->vnames as $key => $name){
                            $stockcheckCode = rand(10000,99999);
                            $productStockCodeCheck = Product::where('product_code', $stockcheckCode)->first();
                            if($productStockCodeCheck){
                                $stockcheckCode = rand( 1,100,999);
                            }
                            $stock = ProductStock::create([
                                'product_id'    => $product->id,
                                'varient'       => $name,
                                'stock_code'    => $stockcheckCode,
                                'sku'           => $request->vskus[$i],
                                'price'         => $request->vprices[$i],
                                'resell_price'  => $request->vresellprices[$i],  
                                'qty'           => $request->vqtys[$i],
                            ]);

                            $image = $request->vimages[$i];
                            if($image){
                                $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
                                Image::make($image)->resize(900,1200)->save('upload/products/variations/'.$name_gen);
                                $save_url = 'upload/products/variations/'.$name_gen;
                            }else{
                                $save_url = '';
                            }

                            $stock->image = $save_url;
                            // return $stock;
                            $stock->save();

                            $i++;
                        }
                    }
                }
                else {
                    $product->attributes = json_encode(array());
                    $product->is_varient = 0;
                }

                $attr_values = collect($attribute_values);
                $attr_values_sorted = $attr_values->sortByDesc('attribute_id');

                $sorted_array = array();
                foreach($attr_values_sorted as $attr){
                    array_push($sorted_array, $attr);
                }

                $product->attribute_values = json_encode($sorted_array, JSON_UNESCAPED_UNICODE);
                /* ========= End Product Attributes ========= */
            }


            /* =========== Start Product Tags =========== */
            $product->tags = implode(',', $request->tags);
            /* =========== End Product Tags =========== */

            /* =========== Multiple Image Update =========== */

            $images = $request->file('multi_img');

            if($images == Null){
                $product->multi_imgs->photo_name = $request->multi_img;
                $product->update();
            }else{
                foreach ($images as $img) {
                    $make_name = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
                    Image::make($img)->resize(900,1200)->save('upload/products/multi-image/'.$make_name);
                    $uploadPath = 'upload/products/multi-image/'.$make_name;

                    MultiImg::insert([
                        'product_id' => $product->id,
                        'photo_name' => $uploadPath,
                        'created_at' => Carbon::now(),
                    ]);

                }
            }

        $product->save();

        //Ledger Update
        $ledger = AccountLedger::where('product_id', $product->id)->first();
        if ($ledger) {
            $ledger->update([
                'particulars' => $product->name_en,
                'debit' => $product->purchase_price * $product->stock_qty,
                'balance' => get_account_balance() - $product->purchase_price * $product->stock_qty,
            ]);
        } else {
            $ledger = AccountLedger::create([
                'account_head_id' => 1,
                'particulars' => $product->name_en,
                'debit' => $product->purchase_price * $product->stock_qty,
                'product_id' => $product->id,
                'type' => 1,
                'balance' => get_account_balance() - $product->purchase_price * $product->stock_qty,
            ]);
        }

        $ledger->save();
        Session::flash('success','Product Updated Successfully');
        return redirect()->route('product.all');
     }
    } // end method
    /*=================== End ProductUpdate Methoed ===================*/

    /*=================== Start Multi Image Delete =================*/
    public function MultiImageDelete($id){
        $oldimg = MultiImg::findOrFail($id);
        try {
            if(file_exists($oldimg->photo_name)){
                unlink($oldimg->photo_name);
            }
        } catch (Exception $e) {

        }
        MultiImg::findOrFail($id)->delete();


        return response()->json(['success'=> 'Product Deleted Successfully']);

    } // end method
    /*=================== End Multi Image Delete =================*/

    /*=================== Start ProductDelete Method =================*/
    public function ProductDelete($id){

        if(!demo_mode()){
            $product = Product::findOrFail($id);

            try {
                if(file_exists($product->product_thumbnail)){
                    unlink($product->product_thumbnail);
                }
            } catch (Exception $e) {

            }

            $product->delete();

            $images = MultiImg::where('product_id',$id)->get();
            foreach ($images as $img) {
                try {
                    if(file_exists($img->photo_name)){
                        unlink($img->photo_name);
                    }
                } catch (Exception $e) {

                }
                MultiImg::where('product_id',$id)->delete();
            }

            $notification = array(
                'message' => 'Product Deleted Successfully',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'message' => 'Product can not be deleted on demo mode.',
                'alert-type' => 'error'
            );
        }

        return redirect()->back()->with($notification);
    } // end method
    /*=================== End ProductDelete Method =================*/

    /*=================== Start Active/Inactive Methoed ===================*/
    public function active($id){
        $product = Product::find($id);
        $product->status = 1;
        $product->save();

        $notification = array(
            'message' => 'Product Active Successfully.',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // end method

    public function inactive($id){
        $product = Product::find($id);
        $product->status = 0;
        $product->save();

        $notification = array(
            'message' => 'Product Inactive Successfully.',
            'alert-type' => 'error'
        );
        return redirect()->back()->with($notification);
    } // end method

    /*=================== Start Featured Methoed ===================*/
    public function featured($id){
        $product = Product::find($id);
        if($product->is_featured == 1){
            $product->is_featured = 0;
        }else{
            $product->is_featured = 1;
        }
        $product->save();
        $notification = array(
            'message' => 'Product Feature Status Changed Successfully.',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // end method

    /*=================== Start Category With SubCategory  Ajax ===================*/
    public function GetSubProductCategory($category_id){
        $subcat = SubCategory::where('category_id',$category_id)->orderBy('subcategory_name_en','ASC')->get();
        return json_encode($subcat);
    } // end method

    /*=================== Start SubCategory With Childe Ajax ===================*/
    public function GetSubSubCategory($subcategory_id){
        $childe = SubSubCategory::where('subcategory_id',$subcategory_id)->orderBy('subsubcategory_name_en','ASC')->get();
        return json_encode($childe);
    } // end method

    public function add_more_choice_option(Request $request) {
        $attributes = Attribute::whereIn('id', $request->attribute_ids)->get();
        // dd($attributes);
        return view('backend.product.attribute_select_value',compact('attributes'));
    }


    /* ============== Category Store Ajax ============ */
    public function categoryInsert(Request $request)
    {

        if($request->name_en == Null){
            return response()->json(['error'=> 'Category Field  Required']);
        }

        $category = new Category();

        $category->name_en = $request->name_en;

        /* ======== Category Name English ======= */
        $category->name_en = $request->name_en;
        if($request->name_bn == ''){
            $category->name_bn = $request->name_en;
        }else{
            $category->name_bn = $request->name_bn;
        }

         /* ======== Category Parent Id  ======= */
        if ($request->parent_id != "0") {
            $category->parent_id = $request->parent_id;

            $parent = Category::find($request->parent_id);

            // dd($parent);
            $category->type = $parent->type + 1 ;
        }

        /* ======== Category Slug   ======= */
        if ($request->slug != null) {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        }else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name_en)).'-'.Str::random(5);
        }

        if($request->hasfile('image')){
            $image = $request->file('image');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            Image::make($image)->resize(300,300)->save('upload/category/'.$name_gen);
            $save_url = 'upload/category/'.$name_gen;
        }else{
            $save_url = '';
        }

        $category->image = $save_url;
        $category->created_by = Auth::guard('admin')->user()->id;
        $category->save();

        $categories = Category::with('childrenCategories')->orderBy('name_en','asc')->get();

        return response()->json([
            'success'=> 'Category Inserted Successfully',
            'categories' => $categories,
        ]);

    }

    /* ============== Brand Store Ajax ============ */

    /* ============== Brand Store Ajax ============== */
    public function brandInsert(Request $request)
    {

        if($request->name_en == Null){
            return response()->json(['error'=> 'Brand Field  Required']);
        }

        $brand = new Brand();

        $brand->name_en = $request->name_en;

        /* ======== brand Name English ======= */
        $brand->name_en = $request->name_en;
        if($request->name_bn == ''){
            $brand->name_bn = $request->name_en;
        }else{
            $brand->name_bn = $request->name_bn;
        }

        /* ======== Category Slug   ======= */
        if ($request->slug != null) {
            $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        }else {
            $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name_en)).'-'.Str::random(5);
        }



        // dd($request->image);


        if($request->hasfile('brand_image')){
            $image = $request->file('brand_image');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            Image::make($image)->resize(300,300)->save('upload/brand/'.$name_gen);
            $save_url = 'upload/brand/'.$name_gen;
        }else{
            $save_url = '';
        }

        $brand->brand_image = $save_url;
        $brand->created_by = Auth::guard('admin')->user()->id;

        $brand->save();
        $brands = Brand::all();

        return response()->json([
            'success'=> 'Brand Inserted Successfully',
            'brands' => $brands,
        ]);
    }

    public function barcode_print(Request $request,$id)
    {
        $ids = $id;
        $variants = $request->get('variant');

        if($variants){
            $product = null;
            $productstock = ProductStock::findOrFail($variants);
            //dd($productstock);
            return view('backend.invoices.barcode_print', compact('productstock','product'));
        }
        if($ids){
            $productstock =null;
            $product = Product::findOrFail($ids);
            return view('backend.invoices.barcode_print', compact('product','productstock'));
        }

    }

    public function barcode_all_print()
    {
        $products = Product::where('status',1)->latest()->get();
        return view('backend.invoices.barcode_all_print', compact('products'));
    }

    public function barcode_custom_print()
    {
        $products = Product::where('status',1)->latest()->get();
        return view('backend.product.barcode_custom_print', compact('products'));
    }

    public function custom_print_ajax($id)
    {
        //dd($id);
        $products = DB::table('products')
            ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->where('status', 1)
            ->where('products.id', $id)
            ->get();
        //dd($products);
        return json_encode($products);
    }

    public function custom_print_qty(Request $request)
    {
        if($request->size_qty2){
            $qty = null;
            $productstocks =null;
            $product =null;
            $qty = $request->size_qty2;
            $product = Product::where('id', $request->product_id)->first();

        return view('backend.product.barcode_custom_print_all', compact('product','qty','productstocks'));
        }

        if($request->size_qty){
            $qty = null;
            $productstocks =null;
            $product =null;
            $decode = json_encode($request->size_qty);
            $productstocks = ProductStock::where('product_id', $request->product_id)->get();
            return view('backend.product.barcode_custom_print_all', compact('product','qty','productstocks','decode'));
        }
    }

}