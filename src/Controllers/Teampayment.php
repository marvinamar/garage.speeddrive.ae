<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Teampayment {
    
    /**
     * Add Payment
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        
        $data = array(
            "company" => $user->company,
            "member" => escape(input('member')),
            "note" => escape(input('note')),
            "amount" => escape(input('amount')),
            "payment_date" => escape(input('payment_date')),
            "mode" => escape(input('mode'))
        );
        if (!empty(input("deduct"))) {
            $data["deduct_balance"] = "Yes";
        }else {
            $data["deduct_balance"] = "No";
        }
        Database::table('teampayments')->insert($data);
        $paymentid = Database::table('teampayments')->insertId();

        if (!empty(input("deduct"))) {
            $this->balance($paymentid, "minus");
        }
        
        return response()->json(responder("success", "Alright!", "Payment successfully added.", "reload()"));
        
    }
    
    
    /**
     * Payment update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $payment = Database::table('teampayments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        
        return view('modals/update-teampayment', compact("payment","user"));
        
    }
    
    /**
     * Update Payment
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date')),
            "mode" => escape(input('mode'))
        );
        
        Database::table('teampayments')->where('id', input('paymentid'))->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Payment successfully updated.", "reload()"));
        
    }
    
    
    /**
     * Delete Payment
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();

        $payment = Database::table('teampayments')->where('company', $user->company)->where('id', input("paymentid"))->first();

        if ($payment->deduct_balance == "Yes") {
            $this->balance(input('paymentid'), "plus");
        }

        Database::table('teampayments')->where('id', input('paymentid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Payment successfully deleted.", "reload()"));
        
    }
    
    /**
     * Balance calculation
     * 
     * @return Json
     */
    public function balance($paymentid, $action) {
        
        $user = Auth::user();
        $payment = Database::table('teampayments')->where('id', $paymentid)->where('company', $user->company)->first();
        $member = Database::table('users')->where('id', $payment->member)->where('company', $user->company)->first();

        if (empty($member) || empty($payment)) {
            return;
        }

        if ($action == "plus") {
            $member->balance = $member->balance + $payment->amount;
        }elseif ($action == "minus") {
            $member->balance = $member->balance - $payment->amount;
        }
        
        Database::table('users')->where('id', $member->id)->update(array(
            "balance" => $member->balance
        ));
        
    }
    
}
