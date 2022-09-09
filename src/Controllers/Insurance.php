<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Insurance {
    
    /**
     * Render insurance page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = 'Insurance Companies';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $insurance = Database::table('insurance')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($insurance as $key => $insuranceco) {
            $insuranceco->active_projects = Database::table('projects')->where('insurance', $insuranceco->id)->where('status', "In progress")->count("id", "total")[0]->total;
            $insuranceco->total_projects = Database::table('projects')->where('insurance', $insuranceco->id)->count("id", "total")[0]->total;

            $total = Database::table('invoices')->where('insurance', $insuranceco->id)->sum("total", "total")[0]->total;
            $paid = Database::table('invoices')->where('insurance', $insuranceco->id)->sum("amount_paid", "total")[0]->total;

            $insuranceco->balance = $total - $paid;
        }
        
        return view("insurance", compact("user", "title", "insurance"));
        
    }
    
    /**
     * Create insurance account 
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        
        $data = array(
            "company" => $user->company,
            "name" => escape(input('name')),
            "email" => escape(input('email')),
            "phonenumber" => escape(input('phonenumber')),
            "address" => escape(input('address'))
        );
        Database::table('insurance')->insert($data);
        $insuranceid = Database::table('insurance')->insertId();
        
        return response()->json(responder("success", "Alright!", "Insurance company successfully added.", "redirect('" . url('Insurance@details', array(
            'insuranceid' => $insuranceid
        )) . "')"));
        
    }
    
    /**
     * Render insurance's details page
     * 
     * @return \Pecee\Http\Response
     */
    public function details($insuranceid) {
        
        $projects = $staffmembers = $quotes = $invoices = $payments = $jobcards = array();
        $user   = Auth::user();
        $insurance = Database::table('insurance')->where('company', $user->company)->where('id', $insuranceid)->first();
        
        if (empty($insurance)) {
            return view('errors/404');
        }

        $total = Database::table('invoices')->where('insurance', $insurance->id)->sum("total", "total")[0]->total;
        $paid = Database::table('invoices')->where('insurance', $insurance->id)->sum("amount_paid", "total")[0]->total;
        $insurance->balance = $total - $paid;

        $insurance->total_invoices = Database::table('invoices')->where('insurance', $insurance->id)->count("id", "total")[0]->total;
        $insurance->total_quotes = Database::table('quotes')->where('insurance', $insurance->id)->count("id", "total")[0]->total;
        $insurance->total_paid = Database::table('projectpayments')->where('insurance', $insurance->id)->sum("amount", "total")[0]->total;
        
        $title = $insurance->name;
        $insurance->active_projects = Database::table('projects')->where('insurance', $insurance->id)->where('status', "In progress")->count("id", "total")[0]->total;
        $insurance->total_projects = Database::table('projects')->where('insurance', $insurance->id)->count("id", "total")[0]->total;

        $notes = Database::table('notes')->where('item', $insuranceid)->where('type', "Insurance")->where('company', $user->company)->orderBy("id", false)->get();

        if (isset($_GET["view"]) && $_GET["view"] == "projects") {
            $projects = Database::table('projects')->where('company', $user->company)->where('insurance', $insurance->id)->orderBy("id", false)->get();
            foreach ($projects as $key => $project) {
                $project->pending_tasks = Database::table('tasks')->where('project', $project->id)->where('status', "In progress")->count("id", "total")[0]->total;
                $project->total_tasks = Database::table('tasks')->where('project', $project->id)->count("id", "total")[0]->total;
                $project->expenses = Database::table('expenses')->where('project', $project->id)->sum("amount", "total")[0]->total;
                $project->taskcost = Database::table('tasks')->where('project', $project->id)->sum("cost", "total")[0]->total;

                $project->client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
                
                $project->invoiced = Database::table('invoices')->where('project', $project->id)->sum("total", "total")[0]->total;
                $project->cost = $project->taskcost + $project->expenses;
            }
            $staffmembers = Database::table('users')->where('company', $user->company)->where('role', "Staff")->orderBy("id", false)->get();
        }elseif (isset($_GET["view"]) && $_GET["view"] == "quotes") {

            $quotes = Database::table('quotes')->where('company', $user->company)->where('insurance', $insurance->id)->orderBy("id", false)->get();
            foreach ($quotes as $key => $quote) {
                $quote->items = Database::table('quoteitems')->where('quote', $quote->id)->count("id", "total")[0]->total;
                $quote->project = Database::table('projects')->where('id', $quote->project)->first();
            }

        }elseif (isset($_GET["view"]) && $_GET["view"] == "payments") {

            $payments = Database::table('projectpayments')->where('company', $user->company)->where('insurance', $insurance->id)->orderBy("id", false)->get();
            foreach ($payments as $key => $payment) {
                $payment->project = Database::table('projects')->where('id', $payment->project)->first();
            }

        }elseif (isset($_GET["view"]) && $_GET["view"] == "jobcards") {

            $jobcards = Database::table('jobcards')->where('company', $user->company)->where('insurance', $insurance->id)->orderBy("id", false)->get();
            foreach ($jobcards as $key => $jobcard) {
                $jobcard->project = Database::table('projects')->where('id', $jobcard->project)->first();
            }

        }

        if (isset($_GET["view"]) && $_GET["view"] == "invoices" || isset($_GET["view"]) && $_GET["view"] == "payments") {

            $invoices = Database::table('invoices')->where('company', $user->company)->where('insurance', $insurance->id)->orderBy("id", false)->get();
            foreach ($invoices as $key => $invoice) {
                $invoice->items = Database::table('invoiceitems')->where('invoice', $invoice->id)->count("id", "total")[0]->total;
                $invoice->project = Database::table('projects')->where('id', $invoice->project)->first();
                $invoice->balance = $invoice->total - $invoice->amount_paid;
            }

        }
        
        return view("insurance-details", compact("user", "title", "insurance","notes","projects","staffmembers","quotes","invoices","payments","jobcards"));
        
    }
    
    
    /**
     * Insurance update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $insurance = Database::table('insurance')->where('company', $user->company)->where('id', input("insuranceid"))->first();
        
        return view('modals/update-insurance', compact("insurance"));
        
    }
    
    /**
     * Update insurance company
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "name" => escape(input('name')),
            "email" => escape(input('email')),
            "phonenumber" => escape(input('phonenumber')),
            "address" => escape(input('address'))
        );
        
        Database::table('insurance')->where('id', input('insuranceid'))->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Insurance company successfully updated.", "reload()"));
        
    }
    
    /**
     * Delete insurance company
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();
        Database::table('insurance')->where('id', input('insuranceid'))->where('company', $user->company)->delete();
        Database::table('notes')->where('item', input('insuranceid'))->where('type', "Insurance")->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Insurance company account successfully deleted.", "redirect('" . url("Insurance@get") . "', true)"));
        
    }
    
}