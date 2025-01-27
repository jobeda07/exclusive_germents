<?php

namespace App\Http\Controllers\Backend;

use Auth;
use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Staff;
use App\Models\Vendor;
use App\Models\Address;
use App\Models\PosCart;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Shipping;
use App\Models\Attribute;
use App\Models\AccountHead;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\AccountLedger;
use App\Models\AdvancePayment;
use App\Models\AttributeValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
//use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::where('status', 1)->latest()->get();
        $categories = Category::where('status', 1)->latest()->get();
        $brands = Brand::where('status', 1)->latest()->get();
        $staffs = Staff::latest()->get();
        $customers = User::where('role', 3)->where('status', 1)->latest()->get();
        $setting = Setting::latest()->first();
        $shippings = Shipping::where('status', 1)->get();
        return view('backend.pos.index', compact('products', 'categories', 'brands', 'customers', 'staffs', 'setting', 'shippings'));
    }

    public function add_pos_product(Request $request)
    {

        if ($request->product_id) {
            $prod_check = Product::where('id', $request->product_id)->first();
            if ($prod_check->stock_qty == 0) {
                return response()->json(['error' => "Product stock out"]);
            }
            $stock_id = $request->stock_id;
            $prod_attr = ProductStock::where('id', $stock_id)->first();
            if ($stock_id = null) {
                if ($prod_check->stock_qty == 0) {
                    return response()->json(['error' => "Product stock out"]);
                }
            }
            if (isset($stock_id)) {
                if ($prod_check->id = $prod_attr->product_id) {
                    if ($prod_attr->qty == 0) {
                        return response()->json(['error' => "Product stock out"]);
                    }
                }
            }
            $admin_id = auth()->user()->id ?? null;

            $s_id = session()->get('session_id');
            if ($s_id == null) {
                session()->put('session_id', uniqid());
                $s_id = session()->get('session_id');
            }

            if ($admin_id != null) {
                $posCart = PosCart::where('admin_id', $admin_id)
                    ->where('product_id', $request->product_id)->where('stock_id', $request->stock_id)
                    ->first();
            } else {

                $posCart = PosCart::where('session_id', $s_id)
                    ->where('product_id', $request->product_id)->where('stock_id', $request->stock_id)
                    ->first();
            }
            if ($posCart) {
                return response()->json(['error' => $prod_check->name . " Allready Added to cart"]);
            } else {

                $posCart =  PosCart::create([
                    'admin_id'          => auth()->user()->id ?? null,
                    'session_id'          => $s_id,
                    'product_id'          => $request->product_id,
                    'stock_id'          => empty($request->stock_id) ? null : $request->stock_id,
                    'quantity'          => 1,
                ]);
            }

            $posCart = session()->get('posCart');

            $posCart[$request->product_id] = [
                "id" => $request->product_id,
            ];

            session()->put('posCart', $posCart);
            return response()->json(['success' => 'Added to Cart']);
        }
    }

    public function getPosCartData()
    {

        $date_now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $setting = Setting::latest()->first();
        $taxTotalPrice = 0;
        $grandTotalPrice = 0;
        $shipping_price = 0;
        $taxTotal = 0;
        $totalPrice = 0;
        $totalbuyingPrice = 0;
        $admin_id    = auth()->user()->id ?? null;
        $count = 0;

        if ($admin_id != null) {
            $pos_cart_data = PosCart::where('admin_id', $admin_id)->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $s_id = session()->get('session_id');
            $pos_cart_data = PosCart::where('session_id', $s_id)->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();

            foreach ($pos_cart_data as $key => $value) {
                if($value->stock_id){
                    $stock=ProductStock::find($value->stock_id);
                    if ($value->product->discount_type == 1) {
                        $price_after_discount = $stock->price - $value->product->discount_price;
                    } elseif ($value->product->discount_type == 2) {
                        $price_after_discount = $stock->price - ($stock->price * $value->product->discount_price) / 100;
                    }
                    $totalPrice += ($value->product->discount_price ? $price_after_discount : $stock->price) * $value->quantity;
                }else{
                    if ($value->product->discount_type == 1) {
                        $price_after_discount = $value->product->regular_price - $value->product->discount_price;
                    } elseif ($value->product->discount_type == 2) {
                        $price_after_discount = $value->product->regular_price - ($value->product->regular_price * $value->product->discount_price) / 100;
                    }
                    $totalPrice += ($value->product->discount_price ? $price_after_discount : $value->product->regular_price) * $value->quantity;
                }

                $totalbuyingPrice += $value->product->purchase_price * $value->quantity;
                $count += $value->quantity;
            }
            // Now calculate tax based on the retrieved $setting
            if ($setting) {
                $taxTotal = ($totalPrice * $setting->product_vat) / 100;
                $taxTotalPrice = $totalPrice + $taxTotal;
            }
            $shipping_price = 0;
            $discount_price = 0;
        }

        return response()->json([
            'cart_data' => view('admin.includes.cart_item', compact('pos_cart_data', 'setting'))->render(),
            'totalPrice' => $taxTotalPrice, 'count' => $count, 'taxAmount' => $taxTotal, 'shipping_price' => $shipping_price,
            'discount_price' => $discount_price, 'totalbuyingPrice' => $totalbuyingPrice,
        ]);
    }

    public function posdelete($id)
    {
        if ($id == 0) {
            $admin_id    = auth()->user()->id ?? null;
            if ($admin_id != null) {
                $posCart = PosCart::where('admin_id', $admin_id);
            } else {
                $s_id       = session()->get('session_id');
                if (!$s_id) {
                    abort(404);
                }
                $posCart = PosCart::where('session_id', $s_id);
            }
        } else {
            $posCart   = PosCart::findorFail($id);
            $product_id = $posCart->product_id;
            $wl         = session()->get('posCart');
            unset($wl[$product_id]);
            session()->put('posCart', $wl);
        }
        Artisan::call('cache:clear');
        if ($posCart) {
            $posCart->delete();
            return response()->json(['success' => 'Deleted From Item']);
        }
        return response()->json(['error' => 'This product isn\'t available in your POS']);
    }

    public function updatePosCart(Request $request)
    {
        $product_id = $request->input('product_id');
        $stock_id = $request->input('stock_id');
        $type = $request->input('type');
        $s_id       = session()->get('session_id');
        $prod_check = Product::where('id', $product_id)->first();
        $prod_attr = ProductStock::where('id', $stock_id)->where('product_id', $product_id)->first();
        $cart = PosCart::where('product_id', $product_id)->where('stock_id', $stock_id)->where('session_id', $s_id)->first();
        if ($cart) {
            if ($cart->product->discount_type == 1) {
                $price_after_discount = $cart->product->regular_price - $cart->product->discount_price;
            } elseif ($cart->product->discount_type == 2) {
                $price_after_discount = $cart->product->regular_price - ($cart->product->regular_price * $cart->product->discount_price) / 100;
            }
            if ($type == '+') {
                if (isset($stock_id)) {
                    if ($prod_attr->qty == $cart->quantity) {
                        return response()->json(['status' => 'danger', 'message' => 'Product stock limited']);
                    }
                    if ($prod_attr->qty < $cart->quantity) {
                        return response()->json(['status' => 'danger', 'message' => 'Product stock limited']);
                    }
                }
                if (!isset($stock_id)) {
                    if ($prod_check->stock_qty == $cart->quantity) {
                        return response()->json(['status', 'danger', 'message' => "Product stock limited"]);
                    }
                    if ($cart->quantity > $prod_check->stock_qty) {
                        return response()->json(['status', 'danger', 'message' => "Product stock limited"]);
                    }
                }

                $cart->quantity += 1;
            } else {
                if ($cart->quantity == 1) {
                    return response()->json(['status' => 'danger', 'message' => "Minimum 1 item required"]);
                }
                $cart->quantity -= 1;
            }
            $cart->save();

            return response()->json([
                'status' => 'success',
                'message' => "Quantity update successfully",
                'type' => $type,
                'price' => $cart->product->discount_price ? $price_after_discount : $cart->product->regular_price,
            ]);
        }
    }

    public function filter()
    {
        $products = Product::where('status', 1);
        if (isset($_GET['_term'])) {
            $products = $products->where('name_en', 'like', '%' . $_GET['search_term'] . '%');
        }
        if (isset($_GET['category_id'])) {
            $products = $products->where('category_id', $_GET['category_id']);
        }
        if (isset($_GET['brand_id'])) {
            $products = $products->where('brand_id', $_GET['brand_id']);
        }
        $products = $products->get();
        return json_encode($products);
    }
    public function pos_filter($name)
    {
        $categories = Category::where('status', 1)->latest()->get();
        $brands = Brand::where('status', 1)->latest()->get();
        $staffs = Staff::latest()->get();
        $customers = User::where('role', 3)->where('status', 1)->latest()->get();
        $setting = Setting::latest()->first();
        $shippings = Shipping::where('status', 1)->get();
        if ($name) {
            $category = Category::where('name_en',$name)->first();
             $products = $category->products()->get();
        }
        else{
            $products =  [];
            }
        return view('backend.pos.index', compact('products', 'categories', 'brands', 'customers', 'staffs', 'setting', 'shippings'));
    }


    public function searchProductshow(Request $request)
    {
        $request->validate(["searchshow" => "required"]);
        $item = $request->searchshow;
        $products = Product::where('name_en', 'LIKE', '%' . $item . '%')
            ->orwhere('regular_price', 'LIKE', '%' . $item . '%')
            ->where(
                'status',
                1
            )->latest()->get();
        return view('admin.includes.search', compact('products'));
    }

    public function create()
    {
        //
    }

    public function get_advanced_amount(Request $request)
    {

        $user_id = $request->input('user_id');
        $order = OrderPayment::where('user_id', $user_id)->where('advanced_type', 1)->where('order_id', null)->orderBy('id', 'desc')->first();

        if ($order) {
            if ($order->paid > 0  && $order->user_id != 0) {
                $transectionNum = $order->transaction_num;
            } else {
                $transectionNum = 0;
            }
            if ($order->paid > 0 && $order->user_id != 0) {
                $advancedamount = $order->paid;
            } else {
                $advancedamount = 0;
            }
            return response()->json(['success' => true, 'advanced_amount' => $advancedamount, 'transection_Num' => $transectionNum]);
        } else {
            $transectionNum = 0;
            $advancedamount = 0;
            return response()->json(['success' => true, 'advanced_amount' => $advancedamount, 'transection_Num' => $transectionNum]);
        }
    }
    public function get_user_address_pos(Request $request)
        {
            $user_id = $request->user_id;
            $address = Address::where('user_id', $user_id)->first();

            $division_id = null;
            $distric_id = null;
            $division = null;
            $district = null;

            if ($address) {
                $division_id = $address->division_id;
                $distric_id = $address->district_id;
                $division = get_district_by_division_id($division_id);
                $district = get_upazilla_by_district_id($distric_id);
            }
            $user = User::find($user_id);
            $orderTotal = Order::where('user_id', $user_id)->sum('grand_total');
            $setting = Setting::where('name', 'premium_membership')->first();
            $premium_member = $setting->value;

            $member = 'Normal'; // Default value if user or order information not found
             if($user->membership ==0){
                if ($user) {
                    if ($orderTotal >= $premium_member) {
                        $member = 'MemberShip';
                    }
                }
             }else{
                $member = 'MemberShip';
             }
            return response()->json([
                'success' => true,
                'address' => $address,
                'division' => $division,
                'district' => $district,
                'useradd' => $user,
                'member' => $member
            ]);
        }


    public function store(Request $request)
    {
        $s_id      = session()->get('session_id');
        $carts     = PosCart::where('session_id', $s_id)->where('status', 1)
            ->with('productStock')
            ->orderBy('id', 'desc')
            ->get();
        if ($carts->count() == 0) {
            $alert = ['danger', 'Please add the product to your cart first, then proceed to the sale.'];
            return back()->withAlert($alert);
        }
        $request->validate([
            'grand_total'           => 'required',
            'paid_amount'           => 'nullable',
        ]);
        if ($request->payment_method == NULL) {
            $request->payment_method = "cash";
        }
        if ($request->due_amount == 0.00) {
            $payment ='paid';
        } else {
            $payment ='unpaid';
        }

        $invoice_data = Order::orderBy('id', 'desc')->first();
        if($invoice_data){
            $lastId = $invoice_data->id;
            $id = str_pad($lastId + 1, 7, 0, STR_PAD_LEFT);
            $invoice_no = $id;
        }else{
            $invoice_no = "0000001";
        }

        if ($request->staff_id) {
            $staff = Staff::where('id', $request->staff_id)->first();
            $staff_commission = (($request->grand_total / 100) * $staff->user->commission);
        } else {
            $staff_commission = 0;
        }
        $gust_user = User::where('role', 4)->first();
        $gust_user = User::where('role', 4)->first();
        if ($request->user_id == 0) {
            $customer = $gust_user->id;
            $user_name = $gust_user->name;
            $user_email = $gust_user->email;
            $user_phone = $gust_user->phone;
            $user_address = $gust_user->address;
            $address_division = 0;
            $address_district = 0;
            $address_upazilla = 0;
        } else {
            $customer = $request->user_id;
            $find_user = User::findOrFail($request->user_id);
            $user_name = $find_user->name;
            $user_email = $find_user->email;
            $user_phone = $find_user->phone;
            $user_address = $find_user->address;
            $find_address = Address::where('user_id', $request->user_id)->first();
            $address_division = $find_address->division_id ?? '0';
            $address_district = $find_address->district_id ?? '0';
            $address_upazilla = $find_address->upazilla_id ?? '0';
        }

        if ($request->paid_amount == '') {
            $alert = ['danger', 'The Paid amount can not be null.'];
        }

        if ($request->user_id == 0) {
            if ($request->paid_amount > $request->grand_total) {
                $alert = ['danger', 'Walking Customer Not allow  Advanced amount.'];
                return back()->withAlert($alert);
            }
            if ($request->due_amount > 0) {
                $alert = ['danger', 'Walking Customer Not allow Due amount'];
                return back()->withAlert($alert);
            }
        }
        $now = now();

        $order = Order::create([
            'user_id'           => $customer,
            'staff_id'          => $request->staff_id,
            'staff_commission'  => $staff_commission,
            'grand_total'       => $request->grand_total,
            'sub_total'         => $request->sub_total,
            'totalbuyingPrice'  => $request->totalbuyingPrice,
            'discount'          => $request->discount,
            'paid_amount'       => $request->paid_amount,
            'due_amount'        => $request->due_amount,
            'payment_method'    => $request->payment_method,
            'payment_status'    => $payment,
            'invoice_no'        => $invoice_no,
            'delivery_status'   => 'pending',
            'name'              => $user_name,
            'phone'             => $user_phone,
            'email'             => $user_email,
            'division_id'       => $address_division,
            'district_id'       => $address_district,
            'upazilla_id'       => $address_upazilla,
            'address'           => $user_address,
            'order_by'              => 1,
            'shipping_charge'    => $request->shipping_charge,
            'shipping_type'    => $request->shipping_type,
            'shipping_name'    => $request->shipping_name,
            'others'    => $request->others,
            'transaction_no'    => $request->transaction_no,
            'type'              => 1,
            'sale_type'         => 1,
        ]);
        //return $order;
        if ($request->advanced_id) {
            $advancedOrder = AdvancePayment::where('id', $request->advanced_id)->first();
            if ($advancedOrder) {
                if ($advancedOrder->advance_amount > $request->paid_amount) {
                    $advancedOrder->advance_amount = $advancedOrder->advance_amount - $request->paid_amount;
                } else {
                    $advancedOrder->advance_amount = 0;
                }
                $advancedOrder->save();
            }
        }
        if (!$request->advanced_id && $request->transaction_no) {
            $advancedOrder = AdvancePayment::where('transaction_no', $request->transaction_no)->get();
            if ($advancedOrder) {
                foreach ($advancedOrder as $Order) {
                    $Order->advance_amount = 0;
                    $Order->save();
                }
            }
        }
        // order details add //
        foreach ($carts as $cart) {
            if($cart->product->is_varient == 1){
                $stockproduct = ProductStock::where('id', $cart->stock_id)->first();
                $getPrice=$stockproduct->price;
            }else{
                $getPrice=$cart->product->regular_price;
            }
            if ($cart->product->discount_type == 1) {
                if($cart->product->is_varient == 1){
                    $stockproduct = ProductStock::where('id', $cart->stock_id)->first();
                    $price_after_discount = $stockproduct->price - $cart->product->discount_price;
                }else{
                    $price_after_discount = $cart->product->regular_price - $cart->product->discount_price;
                }
            } elseif ($cart->product->discount_type == 2) {
                if($cart->product->is_varient == 1){
                    $stockproduct = ProductStock::where('id', $cart->stock_id)->first();
                    $price_after_discount = $stockproduct->price - ($stockproduct->price * $cart->product->discount_price) / 100;
                }else{
                    $price_after_discount = $cart->product->regular_price - ($cart->product->regular_price * $cart->product->discount_price) / 100;
                }
            }
            $Price = ($cart->product->discount_price ? $price_after_discount : $getPrice) * $cart->quantity;
            $product = Product::where('id', $cart->product_id)->first();
            if ($cart->product->vendor == 0) {
                $vendor_comission = 0.00;
                $vendor = 0;
            } else {
                $vendor = Vendor::where('user_id', $cart->product->vendor)->select('vendors.commission', 'user_id')->first();
                $vendor_comission = ($cart->price * $vendor->commission) / 100;
            }
            if ($cart->product->is_varient == 1) {
                //return $cart->product;
                $stockproductId = ProductStock::where('id', $cart->stock_id)->first();
                $stockproductvarient = $stockproductId->varient;
                $varientdivided = explode('-', $stockproductvarient);
                $variations = array();
                foreach ($varientdivided as $onevarient) {
                    $attribute_value = AttributeValue::where('value', $onevarient)->first();
                    if ($attribute_value) {
                        $attribute_id = $attribute_value->attribute_id;
                        $attribute = Attribute::find($attribute_id);
                        if ($attribute) {
                            $item = [
                                'attribute_name' => $attribute->name,
                                'attribute_value' => $attribute_value->value,
                            ];
                            $variations[] = $item;
                        }
                    }
                }
                OrderDetail::insert([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name_en,
                    'is_varient' => 1,
                    'vendor_id' => $vendor->user_id ?? 0,
                    'v_comission' => $vendor_comission,
                    'variation' => json_encode($variations, JSON_UNESCAPED_UNICODE),
                    'qty' => $cart->quantity,
                    'price' => $Price,
                    // 'tax' => $cart->tax,
                    'created_at' => Carbon::now(),
                ]);
                // stock calculation //
                $stock = ProductStock::where('varient', $cart->productStock->varient)->first();
                // dd($cart);
                if ($stock) {
                    // dd($stock);
                    $stock->pre_qty = $stock->qty;
                    $stock->qty = $stock->qty - $cart->quantity;
                    $stock->save();
                    //return $stock;
                }
            } else {
                OrderDetail::insert([
                    'order_id' => $order->id,
                    'product_id' => $cart->product->id,
                    'product_name' => $cart->product->name_en,
                    'is_varient' => 0,
                    'vendor_id' => $vendor->user_id ?? 0,
                    'v_comission' => $vendor_comission,
                    'qty' => $cart->quantity,
                    'price' => $Price,
                    //'tax' => $cart->tax,
                    'created_at' => Carbon::now(),
                ]);
            }
            //  return $orderdetails;
            $product->previous_stock = $product->stock_qty;
            $product->stock_qty = $product->stock_qty - $cart->quantity;
            $product->save();
            if (Product::where('id', $cart->product_id)->exists()) {
                $removeSession = PosCart::where('product_id', $cart->product_id)->where('session_id', $s_id)->first();
                // Check if $removeSession is not null before calling delete()
                if ($removeSession !== null) {
                    $removeSession->delete();
                } else {
                }
            }
        }
        //Ledger Entry
        if($order->due_amount == 0){
            $ledger = AccountLedger::create([
                'account_head_id' => 2,
                'particulars' => 'Invoice No: '.$invoice_no,
                'credit' => $order->grand_total,
                'order_id' => $order->id,
                'type' => 2,
            ]);
            $ledger->balance = get_account_balance() + $order->grand_total;
            $ledger->save();
        }

        $alert = ['success', 'Order Complete Successfully'];
        return redirect()->back()->withAlert($alert);
    }
    public function store_withPrint(Request $request)
    {
        $s_id      = session()->get('session_id');
        $carts     = PosCart::where('session_id', $s_id)->where('status', 1)
            ->with('productStock')
            ->orderBy('id', 'desc')
            ->get();
        if ($carts->count() == 0) {
            $alert = ['danger', 'Please add the product to your cart first, then proceed to the sale.'];
            return back()->withAlert($alert);
        }
        $request->validate([
            'grand_total'           => 'required',
            'paid_amount'           => 'nullable',
        ]);
        if ($request->payment_method == NULL) {
            $request->payment_method = "cash";
        }
        if ($request->due_amount == 0.00) {
            $payment ='paid';
        } else {
            $payment ='unpaid';
        }
        $invoice_data = Order::orderBy('id', 'desc')->first();
        if($invoice_data){
            $lastId = $invoice_data->id;
            $id = str_pad($lastId + 1, 7, 0, STR_PAD_LEFT);
            $invoice_no = $id;
        }else{
            $invoice_no = "0000001";
        }
        if ($request->staff_id) {
            $staff = Staff::where('id', $request->staff_id)->first();
            $staff_commission = (($request->grand_total / 100) * $staff->user->commission);
        } else {
            $staff_commission = 0;
        }
        $gust_user = User::where('role', 4)->first();
        if ($request->user_id == 0) {
            $customer = $gust_user->id;
            $user_name = $gust_user->name;
            $user_email = $gust_user->email;
            $user_phone = $gust_user->phone;
            $user_address = $gust_user->address;
            $address_division = 0;
            $address_district = 0;
            $address_upazilla = 0;
        } else {
            $customer = $request->user_id;
            $find_user = User::findOrFail($request->user_id);
            $user_name = $find_user->name;
            $user_email = $find_user->email;
            $user_phone = $find_user->phone;
            $user_address = $find_user->address;
            $find_address = Address::where('user_id', $request->user_id)->first();
            $address_division = $find_address->division_id ?? '0';
            $address_district = $find_address->district_id ?? '0';
            $address_upazilla = $find_address->upazilla_id ?? '0';
        }
        if ($request->paid_amount == '') {
            $alert = ['danger', 'The Paid amount can not be null.'];
        }
        if ($request->transaction_no && $request->user_id == 0) {
            $alert = ['danger', 'Select a Customer'];
            return back()->withAlert($alert);
        }
        if ($request->user_id == 0){
            if ($request->paid_amount > $request->grand_total) {
                $alert = ['danger', 'Walking Customer Not allow  Advanced amount.'];
                return back()->withAlert($alert);
            }
            if ($request->due_amount > 0) {
                $alert = ['danger', 'Walking Customer Not allow Due amount'];
                return back()->withAlert($alert);
            }
        }
        $now = now();
        // $todayOrder = Order::where('user_id', $customer)->whereDate('created_at', $now->toDateString())->latest()->first();
        // if ($todayOrder) {
        //     $alert = ['danger', 'You have already placed an order today'];
        //         return back()->withAlert($alert);
        // }
        $oldOrder = Order::where('user_id', $customer)->get();
        if ($oldOrder) {
            foreach ($oldOrder as $item) {
                if ($item->delivery_status == 'pending' || $item->delivery_status == 'holding' || $item->delivery_status == 'processing') {
                    $alert = ['danger', 'Please Confirm Your Previous Order Should be Shipped.'];
                    return back()->withAlert($alert);
                }
            }
        }
        $order = Order::create([
            'user_id'           => $customer,
            'staff_id'          => $request->staff_id,
            'staff_commission'  => $staff_commission,
            'grand_total'       => $request->grand_total,
            'sub_total'         => $request->sub_total,
            'totalbuyingPrice'  => $request->totalbuyingPrice,
            'discount'          => $request->discount,
            'paid_amount'       => $request->paid_amount,
            'due_amount'        => $request->due_amount,
            'payment_method'    => $request->payment_method,
            'payment_status'    => $payment,
            'invoice_no'        => $invoice_no,
            'delivery_status'   => 'pending',
            'name'              => $user_name,
            'phone'             => $user_phone,
            'email'             => $user_email,
            'division_id'       => $address_division,
            'district_id'       => $address_district,
            'upazilla_id'       => $address_upazilla,
            'address'           => $user_address,
            'type'              => 1,
            'order_by'          => 1,
            'sale_type'         => 1,
            'shipping_charge'   => $request->shipping_charge,
            'shipping_type'     => $request->shipping_type,
            'shipping_name'     => $request->shipping_name,
            'others'            => $request->others,
            'transaction_no'    => $request->transaction_no,
        ]);
        if ($request->advanced_id) {
            $advancedOrder = AdvancePayment::where('id', $request->advanced_id)->first();
            if ($advancedOrder) {
                if ($advancedOrder->advance_amount > $request->paid_amount) {
                    $advancedOrder->advance_amount = $advancedOrder->advance_amount - $request->paid_amount;
                } else {
                    $advancedOrder->advance_amount = 0;
                }
                $advancedOrder->save();
            }
        }
        if (!$request->advanced_id && $request->transaction_no) {
            $advancedOrder = AdvancePayment::where('transaction_no', $request->transaction_no)->get();
            if ($advancedOrder) {
                foreach ($advancedOrder as $Order) {
                    $Order->advance_amount = 0;
                    $Order->save();
                }
            }
        }
        // order details add
        foreach ($carts as $cart) {
            if($cart->product->is_varient == 1){
                $stockproduct = ProductStock::where('id', $cart->stock_id)->first();
                $getPrice=$stockproduct->price;
            }else{
                $getPrice=$cart->product->regular_price;
            }
            if ($cart->product->discount_type == 1) {
                if($cart->product->is_varient == 1){
                    $stockproduct = ProductStock::where('id', $cart->stock_id)->first();
                    $price_after_discount = $stockproduct->price - $cart->product->discount_price;
                }else{
                    $price_after_discount = $cart->product->regular_price - $cart->product->discount_price;
                }
            } elseif ($cart->product->discount_type == 2) {
                if($cart->product->is_varient == 1){
                    $stockproduct = ProductStock::where('id', $cart->stock_id)->first();
                    $price_after_discount = $stockproduct->price - ($stockproduct->price * $cart->product->discount_price) / 100;
                }else{
                    $price_after_discount = $cart->product->regular_price - ($cart->product->regular_price * $cart->product->discount_price) / 100;
                }

            }
            $Price = ($cart->product->discount_price ? $price_after_discount : $getPrice) * $cart->quantity;
            $product = Product::where('id', $cart->product_id)->first();
            if ($cart->product->vendor == 0) {
                $vendor_comission = 0.00;
                $vendor = 0;
            } else {
                $vendor = Vendor::where('user_id', $cart->product->vendor)->select('vendors.commission', 'user_id')->first();
                $vendor_comission = ($cart->price * $vendor->commission) / 100;
            }
            if ($cart->product->is_varient == 1) {
                //return $cart->product;
                $stockproductId = ProductStock::where('id', $cart->stock_id)->first();
                $stockproductvarient = $stockproductId->varient;
                $varientdivided = explode('-', $stockproductvarient);
                $variations = array();
                foreach ($varientdivided as $onevarient) {
                    $attribute_value = AttributeValue::where('value', $onevarient)->first();
                    if ($attribute_value) {
                        $attribute_id = $attribute_value->attribute_id;
                        $attribute = Attribute::find($attribute_id);
                        if ($attribute) {
                            $item = [
                                'attribute_name' => $attribute->name,
                                'attribute_value' => $attribute_value->value,
                            ];
                            $variations[] = $item;
                        }
                    }
                }
                OrderDetail::insert([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name_en,
                    'is_varient' => 1,
                    'vendor_id' => $vendor->user_id ?? 0,
                    'v_comission' => $vendor_comission,
                    'variation' => json_encode($variations, JSON_UNESCAPED_UNICODE),
                    'qty' => $cart->quantity,
                    'price' => $Price,
                    // 'tax' => $cart->tax,
                    'created_at' => Carbon::now(),
                ]);
                // stock calculation //
                $stock = ProductStock::where('varient', $cart->productStock->varient)->first();
                // dd($cart);
                if ($stock) {
                    // dd($stock);
                    $stock->pre_qty = $stock->qty;
                    $stock->qty = $stock->qty - $cart->quantity;
                    $stock->save();
                    //return $stock;
                }
            } else {
                OrderDetail::insert([
                    'order_id' => $order->id,
                    'product_id' => $cart->product->id,
                    'product_name' => $cart->product->name_en,
                    'is_varient' => 0,
                    'vendor_id' => $vendor->user_id ?? 0,
                    'v_comission' => $vendor_comission,
                    'qty' => $cart->quantity,
                    'price' => $Price,
                    //'tax' => $cart->tax,
                    'created_at' => Carbon::now(),
                ]);
            }
            //  return $orderdetails;
            $product->previous_stock = $product->stock_qty;
            $product->stock_qty = $product->stock_qty - $cart->quantity;
            $product->save();

            if (Product::where('id', $cart->product_id)->exists()) {
                $removeSession = PosCart::where('product_id', $cart->product_id)->where('session_id', $s_id)->first();
                $removeSession->delete();
            }
        }
        //Ledger Entry
        if($order->due_amount == 0){
            $ledger = AccountLedger::create([
                'account_head_id' => 2,
                'particulars' => 'Invoice No: '.$invoice_no,
                'credit' => $order->grand_total,
                'order_id' => $order->id,
                'type' => 2,
            ]);
            $ledger->balance = get_account_balance() + $order->grand_total;
            $ledger->save();
        }
        $alert = ['success', 'Order Complete Successfully'];
        return redirect()->route('print.invoice.download', compact('order'))->with('_blank', true)->withAlert($alert);
    }

    public function pos_orderCencel(Request $request)
    {
        $s_id       = session()->get('session_id');
        $pos_cart_data     = PosCart::where('session_id', $s_id)->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();
        foreach ($pos_cart_data as $key => $value) {

            if (Product::where('id', $value->product_id)->exists()) {
                $removeSession = PosCart::where('product_id', $value->product_id)->where('session_id', $s_id)->where('status', 1)->first();
                $removeSession->delete();
            }
        }

        $alert = ['success', 'Card Clear Successfully'];
        return redirect()->back()->withAlert($alert);
    }
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
    public function customerInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => ['nullable', 'unique:users'],
            'phone' => ['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11', 'unique:users'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'address' => 'required',
            'profile_image' => 'nullable',
            'division_id' => 'required',
            'district_id' => 'required',
            'upazilla_id' => 'required',
            'address' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $customer = new User();
        $customer->name     = $request->name;
        $customer->phone    = $request->phone;
        $customer->email    = $request->email;
        $customer->address  = $request->address;
        $customer->role     = 3;
        $customer->status   = 1;
        $customer->customer_type   = 1;
        $customer->password = Hash::make("12345678");
        $customer->save();
        $address = new Address();
        $address->user_id = $customer->id;
        $address->is_default = 0;
        $address->status = 1;
        $address->division_id = $request->division_id ?? '0';
        $address->district_id = $request->district_id ?? '0';
        $address->upazilla_id = $request->upazilla_id ?? '0';
        $address->address = $request->address;
        $address->save();
        return response()->json([
            'success' => 'Customer Inserted successfully',
            'data' => $customer
        ]);
    }
    public function customerupdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => ['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => 'required',
            'division_id' => 'required',
            'district_id' => 'required',
            'upazilla_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $customer = User::find($request->userid);
        $customer->name     = $request->name;
        $customer->phone    = $request->phone;
        $customer->email    = $request->email;
        $customer->address  = $request->address;
        $customer->role     = 3;
        $customer->status   = 1;
        $customer->customer_type   = 1;
        $customer->password = Hash::make("12345678");
        $customer->save();
        $address = Address::where('user_id', $customer->id)->first();
        $address->user_id = $customer->id;
        $address->is_default = 0;
        $address->status = 1;
        $address->division_id = $request->division_id;
        $address->district_id = $request->district_id;
        $address->upazilla_id = $request->upazilla_id;
        $address->address = $request->address;
        $address->save();
        return response()->json(['success' => 'Customer Update successfully','data' => $customer]);
    }

    public function advancedPayment_searchtransection(Request $request)
    {
        $request->validate(["search" => "required"]);

        $item = $request->search;
        $transactions = AdvancePayment::where('transaction_no', 'LIKE', '%' . $item . '%')->where('advance_amount', '!=', 0)
            ->latest()->get();
        //dd($transactions);
        return view('admin.includes.transectionSearch', compact('transactions'));
    }
    public function get_transection_amount($transaction_no)
    {
        $amount = AdvancePayment::where('transaction_no', $transaction_no)->where('advance_amount', '!=', 0)->latest()->get();
        $AllAdvanceprice = DB::table('advance_payments')->where('transaction_no', $transaction_no)
            ->select(DB::raw('sum(advance_amount) as advance_amount'))
            ->first();
        $data = [
            'amounts' => $amount,
            'total_amount' => $AllAdvanceprice,
        ];
        // dd($data);
        return json_encode($data);
    }
    public function getcustomerdata()
    {
        $customers = User::where('role', 3)->where('status', 1)->latest()->get();
        return json_encode($customers);
    }
    public function get_update_customerdata()
    {
        $customers = User::where('role', 3)->where('status', 1)->latest('updated_at')->get();
        return json_encode($customers);
    }

    public function pos_filter_ajax(Request $request)
    {
         $categoryId=$request->category_id;
         //dd($categoryId);
         $search_term_barcode=$request->search_term_barcode;
         $products = Product::where('status', 1)->latest()->get();
         if($categoryId){
            $products = Product::where('category_id', $categoryId)->where('status', 1)->latest()->get();
            //dd($products);
         }
         if($search_term_barcode){
             $product_stock = ProductStock::where('stock_code','like','%'.$search_term_barcode.'%')->first();
             if($product_stock){
                 $products = Product::where('id', $product_stock->product_id)
                    ->where('status', 1)
                    ->latest()
                    ->get();
             }else{
                 $products = Product::where('product_code', 'like', '%'.$search_term_barcode.'%')
                    ->where('status', 1)
                    ->latest()
                    ->get();

             }
         }

        return view('backend.pos.productshow',compact('products'));
    }

    public function pos_barcode_addtocart(Request $request)
    {
        $barcode_product=$request->barcode_product_add;
        $admin_id = auth()->user()->id ?? null;
        $s_id = session()->get('session_id');
        if ($s_id == null) {
            session()->put('session_id', uniqid());
            $s_id = session()->get('session_id');
        }
        if($barcode_product){
            $product_stock = ProductStock::where('stock_code','like','%'.$barcode_product.'%')->first();
            $product = Product::where('product_code', 'like', '%'.$barcode_product.'%')->first();
            if($product_stock){
                if ($product_stock->qty == 0) {
                    return response()->json(['error' => "Product stock out"]);
                }
                if ($admin_id != null) {
                    $posCart = PosCart::where('admin_id', $admin_id)
                        ->where('product_id', $product_stock->product_id)->where('stock_id', $product_stock->id)
                        ->first();
                } else {
                    $posCart = PosCart::where('session_id', $s_id)
                        ->where('product_id', $product_stock->product_id)->where('stock_id', $product_stock->id)
                        ->first();
                }
                if ($posCart) {
                    return response()->json(['error' => $product_stock->name . " Allready Added to cart"]);
                }else{
                    $posCart =  PosCart::create([
                        'admin_id'          => auth()->user()->id ?? null,
                        'session_id'          => $s_id,
                        'product_id'          => $product_stock->product_id,
                        'stock_id'          =>  $product_stock->id,
                        'quantity'          => 1,
                    ]);
                    return response()->json([ 'status' => 'success','message' => "Added to Cart"]);
                }

            }else if($product){
                if ($product->stock_qty == 0) {
                    return response()->json(['error' => "Product stock out"]);
                }
                if ($admin_id != null) {
                    $posCart = PosCart::where('admin_id', $admin_id)
                        ->where('product_id', $product->id)
                        ->first();
                } else {
                    $posCart = PosCart::where('session_id', $s_id)
                        ->where('product_id', $product->id)
                        ->first();
                }
                if ($posCart) {
                    return response()->json(['error' => $product->name . " Allready Added to cart"]);
                }else{
                    $posCart =  PosCart::create([
                        'admin_id'          => auth()->user()->id ?? null,
                        'session_id'          => $s_id,
                        'product_id'          => $product->id,
                        'stock_id'          =>  null ,
                        'quantity'          => 1,
                    ]);
                    return response()->json([ 'status' => 'success','message' => "Added to Cart"]);
                }

            }
            else{
                return response()->json(['error' => "This barcode not match"]);
            }

        }
    }
}