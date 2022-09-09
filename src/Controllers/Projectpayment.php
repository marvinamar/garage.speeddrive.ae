<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Projectpayment {
    
    /**
     * Render project payments page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = 'Receipts';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $payments = Database::table('projectpayments')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($payments as $key => $payment) {
            $payment->project = Database::table('projects')->where('id', $payment->project)->first();
            $payment->client = Database::table('clients')->where('id', $payment->client)->first();
        }

        $clients = Database::table('clients')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($clients as $key => $client) {
            $client->invoices = Database::table('invoices')->where('company', $user->company)->where('client', $client->id)->where('status',"!=", "Paid")->where('total',">", "0")->orderBy("id", false)->get();
            if (empty($client->invoices)) {
                unset($clients[$key]);
                continue;
            }

            foreach ($client->invoices as $invoice) {
                $invoice->project = Database::table('projects')->where('id', $invoice->project)->first();
            }

        }
        
        return view("project-payments", compact("user", "title", "payments","clients"));
        
    }
    
    /**
     * Add a project payment
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        $update = array();

        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', input("invoice"))->first();

        $data = array(
            "company" => $user->company,
            "project" => $invoice->project,
            "client" => $invoice->client,
            "invoice" => $invoice->id,
            "method" => escape(input('method')),
            "amount" => escape(input('amount')),
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date'))
        );

        if (!empty($invoice->insurance)) {
            $data["insurance"] = $invoice->insurance;
            unset($data["client"]);
        }
        
        Database::table('projectpayments')->insert($data);
        $projectId = Database::table('projectpayments')->insertId();

        $balance = $invoice->total - $invoice->amount_paid; 
        if (abs((floatval(input('amount')) - $balance)/$balance) < 0.00001 || floatval(input('amount')) > $balance) {
            $update["status"] = "Paid";
        }else{
            $update["status"] = "Partial";
        }

        $update["amount_paid"] = $invoice->amount_paid + input('amount');
        Database::table('invoices')->where('company', $user->company)->where('id', input("invoice"))->update($update);

        // Ledgerposting
        $salesAccount   = Database::table('clients')->where('fullname','Sales Account')->where('isDefault','1')->get();

        //Client ID
        Ledgerposting::save_ledger_posting($projectId, $invoice->client, 'client', 0, $invoice->amount_paid + input('amount'),'Receipt',escape(input('payment_date')));

        //Sales Account
        Ledgerposting::save_ledger_posting($projectId, $salesAccount[0]->id, 'sales_account', $invoice->amount_paid + input('amount'), 0,'Receipt',escape(input('payment_date')));
        // Ledgerposting
        
        return response()->json(responder("success", "Alright!", "Receipt successfully added.", "reload('" . url('Projects@details', array(
            'projectid' => $invoice->project,
            'Isqt' => 'false'
        )) . "?view=payments')"));
        
    }

    
    
    
    /**
     * Payment update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $payment = Database::table('projectpayments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        
        return view('modals/update-payment', compact("payment","user"));
        
    }
    
    /**
     * Update payment
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "method" => escape(input('method')),
            "amount" => escape(input('amount')),
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date'))
        );

        $payment = Database::table('projectpayments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', $payment->invoice)->first();

        $update = array(
            "amount_paid" => ($invoice->amount_paid - $payment->amount) + floatval(input('amount'))
        );

        if (abs((floatval($update["amount_paid"]) - $invoice->total)/$invoice->total) < 0.00001 || $update["amount_paid"] > $invoice->total) {
            $update["status"] = "Paid";
        }else{
            $update["status"] = "Partial";
        }

        Database::table('invoices')->where('company', $user->company)->where('id', $payment->invoice)->update($update);
        Database::table('projectpayments')->where('id', input('paymentid'))->where('company', $user->company)->update($data);

        return response()->json(responder("success", "Alright!", "Payment successfully updated.", "reload()"));
        
    }
    
    
    /**
     * Delete payment
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();
        $payment = Database::table('projectpayments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', $payment->invoice)->first();

        $data = array(
            "amount_paid" => $invoice->amount_paid - $payment->amount
        );

        if ($invoice->total == $payment->amount || $invoice->total < $payment->amount) {
            $data["status"] = "Unpaid";
        }else{
            $data["status"] = "Partial";
        }

        Database::table('invoices')->where('company', $user->company)->where('id', $payment->invoice)->update($data);
        Database::table('projectpayments')->where('id', input('paymentid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Payment successfully deleted.", "reload()"));
        
    }
    
}
