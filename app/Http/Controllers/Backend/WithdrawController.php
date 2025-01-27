<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Vendor;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Auth;
use Session;

class WithdrawController extends Controller
{

    public function index()
    {
        $withdraw = Withdraw::latest()->get();
        return view('backend.withdraw.index',compact('withdraw'));
    }


    public function store(Request $request){
        $adminData = User::select("users.*")->where('users.id',$request->user_id)->first();
        $wallet = OrderDetail::where('vendor_id', Auth::guard('admin')->user()->id)->sum('price');
        $commissionValue = OrderDetail::where('vendor_id', Auth::guard('admin')->user()->id)->sum('v_comission');
        $vendorWalletValue = $wallet - $commissionValue;

        if($request->amount>$vendorWalletValue){
            $notification = array(
                'message' => 'Your Requested amount is incufficient',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);

        }else{
            $withdraw = new Withdraw();
            $withdraw->user_id = $request->user_id;
            $withdraw->method = $request->method;
            $withdraw->account_type = $request->account_type;
            $withdraw->user_type = $request->user_type;
            $withdraw->phone = $request->transition_number ?? 0;
            $withdraw->account_holder_name = $request->account_holder_name;
            $withdraw->account_no = $request->account_no;
            $withdraw->bank_name = $request->bank_name;
            $withdraw->bank_brunch = $request->bank_brunch;
            $withdraw->purpose = $request->purpose ?? 0;
            $withdraw->amount = $request->amount;
            $withdraw->status = 0;
            $withdraw->save();

            $notification = array(
                'message' => 'Request Submit Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
    }

    public function Bkash(){
        $bkash = Withdraw::select('withdraws.*','users.name')
                ->join('users','users.id','withdraws.user_id')
                ->where('method','Bkash')
                ->get();

        return view('backend.withdraw.bkash',compact('bkash'));
    }

    public function Nagad(){
        $nagad = Withdraw::select('withdraws.*','users.name')
                ->join('users','users.id','withdraws.user_id')
                ->where('method','Nagad')
                ->get();

        return view('backend.withdraw.nagad',compact('nagad'));
    }

    public function Bank(){
        $bank = Withdraw::select('withdraws.*','users.name')
                ->join('users','users.id','withdraws.user_id')
                ->where('method','Bank')
                ->get();

        return view('backend.withdraw.bank',compact('bank'));
    }

    public function Cash(){
        $cash = Withdraw::select('withdraws.*','users.name')
                ->join('users','users.id','withdraws.user_id')
                ->where('method','cash')
                ->get();
        return view('backend.withdraw.cash',compact('cash'));
    }

    public function withdraw_history(){
        $withdraw_list = Withdraw::select("withdraws.*")->where('withdraws.user_id',Auth::guard('admin')->user()->id)->get();
        return view('backend.withdraw.withdraw_list',compact('withdraw_list'));
    }

    public function withdrawStatus($id){
        $withdraw = Withdraw::findOrFail($id);
        $user = User::findOrFail($withdraw->user_id);
        $orderID = OrderDetail::where('vendor_id', $user->id)->sum('price');
        $wallet = OrderDetail::where('vendor_id', $user->id)->sum('v_comission');
        $vendorWalletValue = $orderID - $wallet;

        $user->income = $vendorWalletValue - $withdraw->amount;
        $user->save();

        $withdraw->status = 1;
        $withdraw->save();

        $notification = array(
            'message' => 'Cash withdraw successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }
}
