<?php
namespace Simcify\Controllers;
use Simcify\Database;
use Simcify\Auth;

class Overview{

    /**
     * Render overview page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {

        $title = 'Overview';
        $user = Auth::user();

        if ($user->role == "Booking Manager") {
            redirect(url("Projects@get"));
        }elseif ($user->role == "Inventory Manager") {
            redirect(url("Inventory@get"));
        }

        $widgets = $this->widgets($user);
        $projects = $this->projects($user);
        $tasks = $this->tasks($user);
        $income = $this->twelve($user);

        return view('overview', compact("user","title","widgets","projects","tasks","income"));

    }

    /**
     * Get company projects info
     * 
     * @return array
     */
    public function projects($user) {

        $projects = array();

        $projects["progress"] = Database::table('projects')->where('company', $user->company)->where('status', "In Progress")->count("id", "total")[0]->total;
        $projects["complete"] = Database::table('projects')->where('company', $user->company)->where('status', "Completed")->count("id", "total")[0]->total;
        $projects["bookedin"] = Database::table('projects')->where('company', $user->company)->where('status', "Booked In")->count("id", "total")[0]->total;
        $projects["cancelled"] = Database::table('projects')->where('company', $user->company)->where('status', "Cancelled")->count("id", "total")[0]->total;

        return $projects;

    }

    /**
     * Get company tasks info
     * 
     * @return array
     */
    public function tasks($user) {

        $tasks = array();

        $tasks["progress"] = Database::table('tasks')->where('company', $user->company)->where('status', "In Progress")->count("id", "total")[0]->total;
        $tasks["complete"] = Database::table('tasks')->where('company', $user->company)->where('status', "Completed")->count("id", "total")[0]->total;
        $tasks["cancelled"] = Database::table('tasks')->where('company', $user->company)->where('status', "Cancelled")->count("id", "total")[0]->total;

        return $tasks;

    }

    /**
     * Income last 12 months
     * 
     * @return array
     */
    public function twelve($user) {
        $now = new \DateTime( "11 months ago");
        $interval = new \DateInterval( 'P1M'); 
        $period = new \DatePeriod( $now, $interval, 11); 
        $twelve = array("amount" => array(), "label" => array());
        foreach( $period as $theMonth) {
            $month = $theMonth->format( 'm');
            $year = $theMonth->format( 'Y');
            $amount = Database::table('projectpayments')->where('MONTH(`payment_date`)', $month)->where('YEAR(`payment_date`)', $year)->where("company", $user->company)->sum("amount","total")[0]->total;
            $twelve['amount'][] = round($amount, 2);
            $twelve['label'][] = $theMonth->format( 'M');
        }

        return $twelve;
        
    }

    /**
     * Get company widgets data
     * 
     * @return array
     */
    public function widgets($user) {

        $widgets = array();

        // unpaid invoices
        $total = Database::table('invoices')->where('company', $user->company)->sum("total", "total")[0]->total;
        $paid = Database::table('invoices')->where('company', $user->company)->sum("amount_paid", "total")[0]->total;
        $widgets["unpaidinvoices"] = $total - $paid;

        // overdue invoices
        $total = Database::table('invoices')->where('company', $user->company)->where('due_date',"<", date("Y-m-d"))->sum("total", "total")[0]->total;
        $paid = Database::table('invoices')->where('company', $user->company)->where('due_date',"<", date("Y-m-d"))->sum("amount_paid", "total")[0]->total;
        $widgets["overdueinvoices"] = $total - $paid;

        // projects
        $widgets["activeprojects"] = Database::table('projects')->where('company', $user->company)->where('status', "In Progress")->count("id", "total")[0]->total;
        $widgets["completedprojects"] = Database::table('projects')->where('company', $user->company)->where('status', "Completed")->count("id", "total")[0]->total;

        // tasks
        $widgets["pendingtasks"] = Database::table('tasks')->where('company', $user->company)->where('status', "In Progress")->count("id", "total")[0]->total;
        $widgets["completedtasks"] = Database::table('tasks')->where('company', $user->company)->where('status', "Completed")->count("id", "total")[0]->total;

        // income
        $widgets["incomethismonth"] = Database::table('projectpayments')->where('company', $user->company)->where('MONTH(`payment_date`)', date("m"))->sum("amount", "total")[0]->total;
        $widgets["paymentsthismonth"] = Database::table('projectpayments')->where('company', $user->company)->where('MONTH(`payment_date`)', date("m"))->count("id", "total")[0]->total;

        // income this year
        $widgets["incomethisyear"] = Database::table('projectpayments')->where('company', $user->company)->where('YEAR(`payment_date`)', date("Y"))->sum("amount", "total")[0]->total;

        // profits
        // $expenses = Database::table('expenses')->where('company', $user->company)->where('YEAR(`expense_date`)', date("Y"))->sum("amount", "total")[0]->total;
        // $taskcost = Database::table('tasks')->where('company', $user->company)->where('YEAR(`created_at`)', date("Y"))->sum("cost", "total")[0]->total;
        // $invoiced = Database::table('invoices')->where('company', $user->company)->where('YEAR(`invoice_date`)', date("Y"))->sum("total", "total")[0]->total;
        // $widgets["profits"] = $invoiced - ($taskcost + $expenses);

        $amount = Database::table('expenses')->where('company', $user->company)->where('YEAR(`expense_date`)', date("Y"))->sum("amount", "total")[0]->total;
        $purchase_amount = Database::table('expenses')->where('company', $user->company)->where('YEAR(`expense_date`)', date("Y"))->sum("purchase_amount", "total")[0]->total;
        $widgets["profits"] = $amount- $purchase_amount;

        // $amount = Database::table('expenses')->where('company', $user->company)->where('MONTH(`expense_date`)', date("m"))->sum("amount", "total")[0]->total;
        // $purchase_amount = Database::table('expenses')->where('company', $user->company)->where('MONTH(`expense_date`)', date("m"))->sum("purchase_amount", "total")[0]->total;
        // $widgets["profitsthismonth_service"] = $amount- $purchase_amount;

        $widgets["profitsthismonth_service"] = Database::table('projectpayments')
        ->leftJoin('invoiceitems','invoiceitems.invoice','projectpayments.invoice')
        ->leftJoin('inventory','inventory.id','invoiceitems.item')
        ->where('inventory`.`shelf_number','Service Charge')
        ->where('projectpayments`.`company', $user->company)->where('MONTH(`projectpayments`.`payment_date`)', date("m"))->sum("invoiceitems.total", "total")[0]->total;

        $widgets["profit_service"] = Database::table('projectpayments')
        ->leftJoin('invoiceitems','invoiceitems.invoice','projectpayments.invoice')
        ->leftJoin('inventory','inventory.id','invoiceitems.item')
        ->where('inventory`.`shelf_number','Service Charge')
        ->where('projectpayments`.`company', $user->company)->where('YEAR(`projectpayments`.`payment_date`)', date("Y"))->sum("invoiceitems.total", "total")[0]->total;

        //  $purchase_amount = Database::table('projectpayments')
        // ->leftJoin('invoiceitems','invoiceitems.invoice','projectpayments.invoice')
        // ->leftJoin('inventory','inventory.id','invoiceitems.item')
        // ->where('inventory`.`shelf_number','!=','Service Charge')
        // ->where('projectpayments`.`company', $user->company)->where('MONTH(`projectpayments`.`payment_date`)', date("m"))->sum("invoiceitems.total", "total")[0]->total;;

        $purchase_amount = Database::table('invoiceitems')
        ->leftJoin('invoices','invoices.id','invoiceitems.invoice')
        ->leftJoin('inventory','inventory.id','invoiceitems.item')
        ->leftJoin('expenses','expenses.expense','invoiceitems.item')
        ->where('expenses`.`company', $user->company)
        ->where('inventory`.`shelf_number','!=','Service Charge')
        ->where('expenses`.`purchase_amount','>','0')
        ->where('MONTH(`expenses`.`expense_date`)', date("m"))
        ->sum("expenses.purchase_amount", "total")[0]->total;

        $amount = Database::table('invoiceitems')
        ->leftJoin('invoices','invoices.id','invoiceitems.invoice')
        ->leftJoin('inventory','inventory.id','invoiceitems.item')
        ->leftJoin('expenses','expenses.expense','invoiceitems.item')
        ->where('expenses`.`company', $user->company)
        ->where('inventory`.`shelf_number','!=','Service Charge')
        ->where('expenses`.`purchase_amount','>','0')
        ->where('MONTH(`expenses`.`expense_date`)', date("m"))
        ->sum("expenses.amount", "total")[0]->total;

        $widgets["profitsthismonth_partsandexpense"] = $amount - $purchase_amount;

        $purchase_amount = Database::table('invoiceitems')
        ->leftJoin('invoices','invoices.id','invoiceitems.invoice')
        ->leftJoin('inventory','inventory.id','invoiceitems.item')
        ->leftJoin('expenses','expenses.expense','invoiceitems.item')
        ->where('expenses`.`company', $user->company)
        ->where('inventory`.`shelf_number','!=','Service Charge')
        ->where('expenses`.`purchase_amount','>','0')
        ->where('YEAR(`expenses`.`expense_date`)', date("Y"))
        ->sum("expenses.purchase_amount", "total")[0]->total;

        $amount = Database::table('invoiceitems')
        ->leftJoin('invoices','invoices.id','invoiceitems.invoice')
        ->leftJoin('inventory','inventory.id','invoiceitems.item')
        ->leftJoin('expenses','expenses.expense','invoiceitems.item')
        ->where('expenses`.`company', $user->company)
        ->where('inventory`.`shelf_number','!=','Service Charge')
        ->where('expenses`.`purchase_amount','>','0')
        ->where('YEAR(`expenses`.`expense_date`)', date("Y"))
        ->sum("expenses.amount", "total")[0]->total;

        $widgets["profits_partsandexpense"] = $amount - $purchase_amount;

        // total clients
        $widgets["totalclients"] = Database::table('clients')->where('company', $user->company)->count("id", "total")[0]->total;
        $widgets["totalstaff"] = Database::table('users')->where('company', $user->company)->count("id", "total")[0]->total;

        return $widgets;

    }


}
