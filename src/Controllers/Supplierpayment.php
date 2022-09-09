<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Supplierpayment{
    
    public function get() {
        
        $title = 'Payments';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $payments = Database::table('s_payments')->where('company', $user->company)->orderBy("id", false)->get();
        
        foreach ($payments as $key => $payment) {
            $payment->expense = Database::table('expenses')->where('id', $payment->expense)->first();
            $payment->supplier = Database::table('suppliers')->where('id', $payment->supplier)->first();
        }

        $pay_expenses = Database::table('expenses')->get();
        

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
        
        return view("project-suplier-payments", compact("user", "title", "payments","clients","pay_expenses"));
        
    }

    public function create() {
        
        $user = Auth::user();

        $expense = Database::table('expenses')->where('company', $user->company)->where('id', input("s_expense"))->first();

        $data = array(
            "company" => $user->company,
            "project" => $expense->project,
            "supplier" => $expense->supplier,
            "expense" => $expense->id,
            "method" => escape(input('method')),
            "amount" => escape(input('amount')),
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date'))
        );
        
        Database::table('s_payments')->insert($data);
        $s_paymentsId = Database::table('s_payments')->insertId();

        //Update Paid to Yes
        $data["paid"] = "Yes";
        Database::table('expenses')->where('company', $user->company)->where('id', $expense->id)->update($data);

        // Ledgerposting
        $purchaseAccount   = Database::table('suppliers')->where('name','Purchase Account')->where('isDefault','1')->get();

        //Client ID
        Ledgerposting::save_ledger_posting($s_paymentsId, $expense->supplier, 'supplier', $expense->amount, 0, 'Payment',escape(input('payment_date')));

        //Sales Account
        Ledgerposting::save_ledger_posting($s_paymentsId, $purchaseAccount[0]->id, 'purchase_account',  0, $expense->amount,'Payment',escape(input('payment_date')));
        // Ledgerposting
        
        return response()->json(responder("success", "Alright!", "Payment successfully added.", "reload('" . url('Projects@details', array(
            'projectid' => $expense->project,
            'Isqt' => 'false'
        )) . "?view=s_payments')"));
        
    }

    public function delete() {
        
        $user = Auth::user();
        $payment = Database::table('s_payments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        
        $data["paid"] = "No";
        Database::table('expenses')->where('company', $user->company)->where('id', $payment->expense)->update($data);
        Database::table('s_payments')->where('id', input('paymentid'))->where('company', $user->company)->delete();
        
        //Delete Ledger Posting
        Ledgerposting::delete_ledger_posting($payment->id,'Payment');

        return response()->json(responder("success", "Alright!", "Payment successfully deleted.", "reload()"));
        
    }

    public function updateview() {
        
        $user   = Auth::user();
        $payment = Database::table('s_payments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        
        return view('modals/updated-s-payment', compact("payment","user"));
        
    }

    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "method" => escape(input('method')),
            "amount" => escape(input('amount')),
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date'))
        );

        Database::table('s_payments')->where('id', input('paymentid'))->where('company', $user->company)->update($data);

        $payment = Database::table('s_payments')->where('company', $user->company)->where('id', input("paymentid"))->first();

        //Delete Ledger Posting
        Ledgerposting::delete_ledger_posting(input('paymentid'),'Expense');

        // Ledgerposting
        $purchaseAccount   = Database::table('suppliers')->where('name','Purchase Account')->where('isDefault','1')->get();

        //Client ID
        Ledgerposting::save_ledger_posting($payment->id, $payment->supplier, 'supplier', $payment->amount, 0, 'Payment',escape(input('payment_date')));

        //Sales Account
        Ledgerposting::save_ledger_posting($payment->id, $purchaseAccount[0]->id, 'purchase_account',  0, $payment->amount,'Payment',escape(input('payment_date')));
        // Ledgerposting

        return response()->json(responder("success", "Alright!", "Payment successfully updated.", "reload()"));
        
    }

}

?>