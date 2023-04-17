<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;
use Twilio\Rest\Wireless\V1\Sim\DataSessionInstance;

class Expenses {
    
    /**
     * Render unpaid parts page
     * 
     * @return \Pecee\Http\Response
     */
    public function unpaid() {
        
        $title = 'Unpaid Parts';
        $user  = Auth::user();
        
        $expenses = Database::table('expenses')->where('company', $user->company)->where('paid', "No")->orderBy("id", false)->get();
        foreach ($expenses as $key => $expense) {
            $expense->project = Database::table('projects')->where('id', $expense->project)->first();
            if (!empty($expense->supplier)) {
                $expense->supplier = Database::table('suppliers')->where('id', $expense->supplier)->first();
            }
        }
        
        return view("parts-unpaid", compact("user", "title", "expenses"));
        
    }
    
    /**
     * Render expected parts page
     * 
     * @return \Pecee\Http\Response
     */
    public function expected() {
        
        $title = 'Expected Parts';
        $user  = Auth::user();
        
        $expenses = Database::table('expenses')->where('company', $user->company)->where('status',"!=","Delivered")->orderBy("id", false)->get();
        foreach ($expenses as $key => $expense) {
            $expense->project = Database::table('projects')->where('id', $expense->project)->first();
            if (!empty($expense->supplier)) {
                $expense->supplier = Database::table('suppliers')->where('id', $expense->supplier)->first();
            }
        }
        
        return view("parts-expected", compact("user", "title", "expenses"));
        
    }
    
    /**
     * Add an Expense
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();

        if (input("source") == "Suppliers") {

            $data = array(
                "company" => $user->company,
                "project" => escape(input('project')),
                "expense" => escape(input('expense')),
                "amount" => escape(input('amount')),
                "type" => escape(input('type')),
                "status" => escape(input('status')),
                "quantity" => escape(input('quantity')),
                "units" => escape(input('quantity_unit')),
                "expense_date" => escape(input('expense_date'))
            );

            if (!empty(input("expected_delivery_date"))) {
                $data["expected_delivery_date"] = escape(input('expected_delivery_date'));
            }

            if (!empty(input("expected_delivery_time"))) {
                $data["expected_delivery_time"] = escape(input('expected_delivery_time'));
            }

            if (!empty(input("payment_due"))) {
                $data["payment_due"] = escape(input('payment_due'));
            }

            if (!empty(input("supplier"))) {
                if (input("supplier") == "create") {
                    Database::table('suppliers')->insert(array(
                        "company" => $user->company,
                        "name" => escape(input('suppliername')),
                        "phonenumber" => escape(input('phonenumber'))
                    ));
                    $supplierId = Database::table('suppliers')->insertId();
                    if (empty($supplierId)) {
                        return response()->json(responder("error", "Hmmm!", "Something went wrong while creating new supplier, please try again."));
                    }
                    $data["supplier"] = $supplierId;
                    $supID = $supplierId;
                }else{
                    $data["supplier"] = escape(input('supplier'));
                    $supID = escape(input('supplier'));
                }
            }

            if (!empty(input("paid"))) {
                $data["paid"] = "Yes";
            }else{
                $data["paid"] = "No";
            }
            
            Database::table('expenses')->insert($data);
            $expenseid = Database::table('expenses')->insertId();


            if($data["paid"] == "Yes"){
                //Auto Create Payment

                $expense = Database::table('expenses')->where('company', $user->company)->where('id', $expenseid)->first();

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
            }

        }else{

            if (input("consumed") < 0) {
                return response()->json(responder("error", "Hmmm!", "Units Consumed can't be less than 0."));
            }

            $inventory = Database::table('inventory')->where('company', $user->company)->where('id', input("inventory"))->first();

            $data = array(
                "company" => $user->company,
                "project" => escape(input('project')),
                "inventory" => escape(input('inventory')),
                "expense" => $inventory->id,
                "amount" => escape(input('totalamount') ),
                // "cost" => $inventory->unit_cost,
                "quantity" => escape(input('consumed')),
                "units" => $inventory->quantity_unit,
                "expense_date" => escape(input('expensedate'))
            );
            
            Database::table('expenses')->insert($data);
            $expenseid = Database::table('expenses')->insertId();

            Database::table('inventory')->where('company', $user->company)->where('id', input("inventory"))->update(array(
                "quantity" => round(($inventory->quantity - input('consumed')), 2)
            ));

            // consumption log

            $data = array(
                "company" => $user->company,
                "project" => escape(input('project')),
                "item" => escape(input('inventory')),
                "issued_by" => escape($user->fname." ".$user->lname),
                "consumed" => escape(input('consumed')),
                "consumed_value" => escape(input('totalamount') )
            );

            Database::table('inventorylog')->insert($data);

        }

        if (input("source") == "Suppliers" && input("type") == "Part" && $user->parent->parts_to_inventory == "Enabled") {
            $this->addToInventory($expenseid);
        }

        // Ledgerposting
        $cashAccount   = Database::table('clients')->where('fullname','Cash/Bank')->where('isDefault','1')->get();

        //Client Account
        if(input("source") != "Suppliers"){
            $supID = $user->company;
        }
        Ledgerposting::save_ledger_posting($expenseid, $supID, 'supplier', 0, escape(input('amount')),'Expense',escape(input('expense_date')));

        //Sales Account
        Ledgerposting::save_ledger_posting($expenseid, $cashAccount[0]->id, 'sales_account', escape(input('amount')), 0,'Expense',escape(input('expense_date')));
        // Ledgerposting

        // ### Auto Insert To Invoice \\

        if($expenseid > 0){
            $_expense = Database::table('expenses')->where('company', $user->company)->where('id', $expenseid)->first();
            $_invoice = Database::table('invoices')->where('company', $user->company)->where('project', $_expense->project)->first();

            if($_invoice != null){
                //Invoice
                $subtotal = $_invoice->subtotal + $_expense->amount;
                $total = $_invoice->total + $_expense->amount;
                Database::table('invoices')->where('id', $_invoice->id)->where('company', $user->company)->update(['total'=>$total,'subtotal'=>$subtotal]);
        
                $data = array(
                    "company" => $user->company,
                    "project" => $_expense->project,
                    "invoice" => $_invoice->id,
                    "item" => $_expense->expense,
                    "item_description" => '',
                    "workType" => '',
                    "quantity" => $_expense->quantity,
                    "cost" => $_expense->amount,
                    "tax" => 0,
                    "total" => $_expense->amount,
                    "ref_module" => 'expense',
                    "ref_id" => $_expense->id
                );

                //InvoiceItems
                Database::table('invoiceitems')->insert($data);
            }

            // ### Auto Insert To Invoice \\
        }
        return response()->json(responder("success", "Alright!", "Expense successfully added.", "redirect('" . url('Projects@details', array(
            'projectid' => input('project'),
            'Isqt' => 'false'
        )) . "?view=expenses')"));
        
    }
    
    /**
     * Add an Expense
     * 
     * @return Json
     */
    public function addbulk() {
        
        $user = Auth::user();

        if (empty($_POST["indexing"])) {
            return response()->json(responder("error", "Hmmm!", "No exepense or part added."));
        }

        foreach ($_POST["indexing"] as $key => $index) {

            $expenseid = NULL;

            if (input("source".$index) == "Suppliers") {

                $data = array(
                    "company" => $user->company,
                    "project" => escape(input('project')),
                    "expense" => escape(input('expense'.$index)),
                    "amount" => escape(input('amount'.$index)),
                    "type" => escape(input('type'.$index)),
                    "status" => escape(input('status'.$index)),
                    "quantity" => escape(input('quantity'.$index)),
                    "units" => escape(input('quantity_unit'.$index)),
                    "expense_date" => escape(input('expense_date'.$index))
                );

                if (!empty(input("expected_delivery_date".$index))) {
                    $data["expected_delivery_date"] = escape(input('expected_delivery_date'.$index));
                }

                if (!empty(input("expected_delivery_time".$index))) {
                    $data["expected_delivery_time"] = escape(input('expected_delivery_time'.$index));
                }

                if (!empty(input("payment_due".$index))) {
                    $data["payment_due"] = escape(input('payment_due'.$index));
                }

                if (!empty(input("supplier".$index))) {
                    if (input("supplier".$index) == "create") {
                        Database::table('suppliers')->insert(array(
                            "company" => $user->company,
                            "name" => escape(input('suppliername'.$index)),
                            "phonenumber" => escape(input('phonenumber'.$index))
                        ));
                        $supplierId = Database::table('suppliers')->insertId();
                        if (!empty($supplierId)) {
                            $data["supplier"] = $supplierId;
                        }
                    }else{
                        $data["supplier"] = escape(input('supplier'.$index));
                    }
                }

                if (!empty(input("paid".$index))) {
                    $data["paid"] = "Yes";
                }else{
                    $data["paid"] = "No";
                }

                Database::table('expenses')->insert($data);
                $expenseid = Database::table('expenses')->insertId();

            }else{

                if (input("consumed".$index) < 0) {
                    return response()->json(responder("error", "Hmmm!", "Units Consumed can't be less than 0."));
                }

                $inventory = Database::table('inventory')->where('company', $user->company)->where('id', input("inventory".$index))->first();

                $data = array(
                    "company" => $user->company,
                    "project" => escape(input('project')),
                    "inventory" => escape(input('inventory'.$index)),
                    "expense" => $inventory->name,
                    "amount" => escape(input('consumed'.$index) * $inventory->unit_cost),
                    "quantity" => escape(input('consumed'.$index)),
                    "units" => $inventory->quantity_unit,
                    "expense_date" => escape(input('expensedate'.$index))
                );
                
                Database::table('expenses')->insert($data);
                $expenseid = Database::table('expenses')->insertId();

                Database::table('inventory')->where('company', $user->company)->where('id', input("inventory".$index))->update(array(
                    "quantity" => round(($inventory->quantity - input('consumed'.$index)), 2)
                ));

                // consumption log

                $data = array(
                    "company" => $user->company,
                    "project" => escape(input('project')),
                    "item" => escape(input('inventory'.$index)),
                    "issued_by" => escape($user->fname." ".$user->lname),
                    "consumed" => escape(input('consumed'.$index)),
                    "consumed_value" => escape(input('consumed'.$index) * $inventory->unit_cost)
                );

                Database::table('inventorylog')->insert($data);

            }

            if (input("type".$index) == "Part" && $user->parent->parts_to_inventory == "Enabled") {
                $this->addToInventory($expenseid);
            }

        }
        
        return response()->json(responder("success", "Alright!", "Expense successfully added.", "redirect('" . url('Projects@details', array(
            'projectid' => input('project'),
            'Isqt' => 'false'
        )) . "?view=expenses')"));
        
    }
    
    /**
     * Add expense part to inventory 
     * 
     * @return Json
     */
    public function addToInventory($expenseid) {

        if (empty($expenseid)) {
            return;
        }

        $expense = Database::table('expenses')->where('id', $expenseid)->first();

        $data = array(
            "company" => $expense->company,
            "name" => $expense->expense,
            "quantity" => $expense->quantity,
            "quantity_unit" => $expense->units,
            "unit_cost" => round(($expense->amount / $expense->quantity), 2),
            "project" => $expense->project,
            "expense" => $expense->id,
            "project_specific" => "Yes",
            "received" => "No"
        );

        if (!empty($expense->supplier)) {
            $data["supplier"] = $expense->supplier;
        }

        Database::table('inventory')->insert($data);
        
        return;
        
    }
    
    /**
     * Add expense part to inventory 
     * 
     * @return Json
     */
    public function updateInventory($expenseid) {

        $expense = Database::table('expenses')->where('id', $expenseid)->first();

        $data = array(
            "name" => $expense->expense,
            "quantity" => $expense->quantity,
            "quantity_unit" => $expense->units,
            "unit_cost" => round(($expense->amount / $expense->quantity), 2)
        );

        if ($expense->supplier) {
            $data["supplier"] = $expense->supplier;
        }

        Database::table('inventory')->where("expense", $expense->id)->update($data);
        
        return;
        
    }
    
    
    /**
     * Import expenses from work requested
     * 
     * @return \Pecee\Http\Response
     */
    public function workrequested() {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();
        $suppliers = Database::table('suppliers')->where('company', $user->company)->orderBy("id", false)->get();

        if (!empty($project->work_requested)) {
            $project->work_requested = json_decode($project->work_requested);
        }else{
            $project->work_requested = array();
        }

        $inventory = Database::table('inventory')->where('company', $user->company)->where('project_specific', "No")->orderBy("id", false)->get();
        
        return view('modals/import-expense-workrequested', compact("project","user","suppliers","inventory"));
        
    }
    
    
    /**
     * Import expenses from jobcards
     * 
     * @return \Pecee\Http\Response
     */
    public function jobcards() {
        
        $items = array();
        $user   = Auth::user();
        $suppliers = Database::table('suppliers')->where('company', $user->company)->orderBy("id", false)->get();
        $jobcard = Database::table('jobcards')->where('company', $user->company)->where('id', input("jobcardid"))->first();

        if(!empty($jobcard->body_report)){
            $items = array_merge($items, json_decode($jobcard->body_report));
        }

        if(!empty($jobcard->mechanical_report)){
            $items = array_merge($items, json_decode($jobcard->mechanical_report));
        }

        if(!empty($jobcard->electrical_report)){
            $items = array_merge($items, json_decode($jobcard->electrical_report));
        }

        $inventory = Database::table('inventory')->where('company', $user->company)->where('project_specific', "No")->orderBy("id", false)->get();
        
        return view('modals/import-expense-jobcard', compact("items","user","suppliers","jobcard","inventory"));
        
    }
    
    
    /**
     * Expense update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $expense = Database::table('expenses')->where('company', $user->company)->where('id', input("expenseid"))->first();
        $suppliers = Database::table('suppliers')->where('company', $user->company)->orderBy("id", false)->get();
        $inventorys = Database::table('inventory')->where('company', $user->company)->get();
        
        return view('modals/update-expense', compact("expense","user","suppliers","inventorys"));
        
    }
    
    /**
     * Update Expense
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();
        $expense = Database::table('expenses')->where('id', input("expenseid"))->where('company', $user->company)->first();
        
        $data = array(
            "expense" => escape(input('expense')),
            "amount" => escape(input('amount')),
            "type" => escape(input('type')),
            "status" => escape(input('status')),
            "quantity" => escape(input('quantity')),
            "expense_date" => escape(input('expense_date'))
        );

        if (!empty(input("expected_delivery_date"))) {
            $data["expected_delivery_date"] = escape(input('expected_delivery_date'));
        }

        if (!empty(input("expected_delivery_time"))) {
            $data["expected_delivery_time"] = escape(input('expected_delivery_time'));
        }

        if (!empty(input("payment_due"))) {
            $data["payment_due"] = escape(input('payment_due'));
        }

        if (!empty(input("supplier"))) {
            if (input("supplier") == "create") {
                Database::table('suppliers')->insert(array(
                    "company" => $user->company,
                    "name" => escape(input('suppliername')),
                    "phonenumber" => escape(input('phonenumber'))
                ));
                $supplierId = Database::table('suppliers')->insertId();
                if (empty($supplierId)) {
                    return response()->json(responder("error", "Hmmm!", "Something went wrong while creating new supplier, please try again."));
                }
                $data["supplier"] = $supplierId;
            }else{
                $data["supplier"] = escape(input('supplier'));
            }
        }

        if (!empty(input("paid"))) {
            $data["paid"] = "Yes";
        }else{
            $data["paid"] = "No";
        }
        
        Database::table('expenses')->where('id', input('expenseid'))->where('company', $user->company)->update($data);

        if ($user->parent->parts_to_inventory == "Enabled") {
            $inventory = Database::table('inventory')->where('company', $user->company)->where('expense', $expense->id)->first();
            if (!empty($inventory)) {
                $this->updateInventory($expense->id);
            }elseif (input("type") == "Part" && $expense->type == "Service") {
                $this->addToInventory($expense->id);
            }
        }
        //Delete Ledger Posting
        Ledgerposting::delete_ledger_posting($expense->id,'Expense');

        // Ledgerposting
        $salesAccount   = Database::table('clients')->where('fullname','Sales Account')->where('isDefault','1')->get();

        //Client Account
        if(input("source") != "Suppliers"){
            $supID = $user->company;
        }
        Ledgerposting::save_ledger_posting($expense->id, $expense->supplier, 'supplier', 0, escape(input('amount')),'Expense',escape(input('expense_date')));

        //Sales Account
        Ledgerposting::save_ledger_posting($expense->id, $salesAccount[0]->id, 'sales_account', escape(input('amount')), 0,'Expense',escape(input('expense_date')));
        // Ledgerposting

        return response()->json(responder("success", "Alright!", "Expense successfully updated.", "reload()"));
        
    }
    
    
    /**
     * Delete Expense
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();

        $expense = Database::table('expenses')->where('company', $user->company)->where('id', input("expenseid"))->first();
        if (!empty($expense->inventory)) {
            $inventory = Database::table('inventory')->where('company', $user->company)->where('id', $expense->inventory)->first();
            if (!empty($inventory)) {
                Database::table('inventory')->where('company', $user->company)->where('id', $inventory->id)->update(array(
                    "quantity" => round(($inventory->quantity + $expense->quantity), 2)
                ));
            }

            $_expese_invoiceItem = Database::table('invoiceitems')->where('ref_id',$expense->id)->where('ref_module','expense')->first();
            $_invoice = Database::table('invoices')->where('id',$_expese_invoiceItem->invoice)->first();

            if($_expese_invoiceItem != null){
                if($_expese_invoiceItem->tax > 0){
                    $_tax_percantage = $_expese_invoiceItem->tax / 100;
                    $_tax = $_expese_invoiceItem->cost * $_tax_percantage;

                    $data = array(
                        'subtotal' => $_invoice->subtotal - $_expese_invoiceItem->cost, 
                        'tax' => $_invoice->tax - $_tax,
                        'total' => $_invoice->total - $_expese_invoiceItem->total,
                    );

                    
                }
                else{
                    $data = array(
                        'subtotal' => $_invoice->subtotal - $_expese_invoiceItem->cost, 
                        'total' => $_invoice->total - $_expese_invoiceItem->total,
                    );
                }
                Database::table('invoices')->where('id', $_invoice->id)->update($data);
                Database::table('invoiceitems')->where('id', $_expese_invoiceItem->id)->delete();
            }
        }

        Database::table('expenses')->where('id', input('expenseid'))->where('company', $user->company)->delete();

        //Delete Ledger Posting
        Ledgerposting::delete_ledger_posting($expense->id,'Expense');
        
        return response()->json(responder("success", "Alright!", "Expense successfully deleted.", "reload()"));
        
    }
    
}
