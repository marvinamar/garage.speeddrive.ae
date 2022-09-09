<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Clients {
    
    /**
     * Render clients page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = 'Clients';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $clients = Database::table('clients')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($clients as $key => $client) {
            $client->active_projects = Database::table('projects')->where('client', $client->id)->where('status', "In progress")->count("id", "total")[0]->total;
            $client->total_projects = Database::table('projects')->where('client', $client->id)->count("id", "total")[0]->total;

            $total = Database::table('invoices')->where('client', $client->id)->sum("total", "total")[0]->total;
            $paid = Database::table('invoices')->where('client', $client->id)->sum("amount_paid", "total")[0]->total;

            $client->balance = $total - $paid;
        }
        
        return view("clients", compact("user", "title", "clients"));
        
    }
    
    /**
     * Create client account 
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        
        $data = array(
            "company" => $user->company,
            "fullname" => escape(input('fullname')),
            "email" => escape(input('email')),
            "phonenumber" => escape(input('phonenumber')),
            "address" => escape(input('address'))
        );
        Database::table('clients')->insert($data);
        $clientid = Database::table('clients')->insertId();
        
        return response()->json(responder("success", "Alright!", "Client account successfully created.", "redirect('" . url('Clients@details', array(
            'clientid' => $clientid
        )) . "')"));
        
    }
    
    /**
     * Render client's details page
     * 
     * @return \Pecee\Http\Response
     */
    public function details($clientid) {
        
        $projects = $staffmembers = $quotes = $invoices = $payments = $jobcards = array();
        $user   = Auth::user();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $clientid)->first();
        
        if (empty($client)) {
            return view('errors/404');
        }

        $total = Database::table('invoices')->where('client', $client->id)->sum("total", "total")[0]->total;
        $paid = Database::table('invoices')->where('client', $client->id)->sum("amount_paid", "total")[0]->total;
        $client->balance = $total - $paid;

        $client->total_invoices = Database::table('invoices')->where('client', $client->id)->count("id", "total")[0]->total;
        $client->total_quotes = Database::table('quotes')->where('client', $client->id)->count("id", "total")[0]->total;
        $client->total_paid = Database::table('projectpayments')->where('client', $client->id)->sum("amount", "total")[0]->total;
        
        $title = $client->fullname;
        $client->active_projects = Database::table('projects')->where('client', $client->id)->where('status', "In progress")->count("id", "total")[0]->total;
        $client->total_projects = Database::table('projects')->where('client', $client->id)->count("id", "total")[0]->total;

        $notes = Database::table('notes')->where('item', $clientid)->where('type', "Client")->where('company', $user->company)->orderBy("id", false)->get();

        if (isset($_GET["view"]) && $_GET["view"] == "projects") {
            $projects = Database::table('projects')->where('company', $user->company)->where('client', $client->id)->orderBy("id", false)->get();
            foreach ($projects as $key => $project) {
                $project->pending_tasks = Database::table('tasks')->where('project', $project->id)->where('status', "In progress")->count("id", "total")[0]->total;
                $project->total_tasks = Database::table('tasks')->where('project', $project->id)->count("id", "total")[0]->total;
                $project->expenses = Database::table('expenses')->where('project', $project->id)->sum("amount", "total")[0]->total;
                $project->taskcost = Database::table('tasks')->where('project', $project->id)->sum("cost", "total")[0]->total;
                
                $project->invoiced = Database::table('invoices')->where('project', $project->id)->sum("total", "total")[0]->total;
                $project->cost = $project->taskcost + $project->expenses;
            }
            $staffmembers = Database::table('users')->where('company', $user->company)->where('role', "Staff")->orderBy("id", false)->get();
        }elseif (isset($_GET["view"]) && $_GET["view"] == "quotes") {

            $quotes = Database::table('quotes')->where('company', $user->company)->where('client', $client->id)->orderBy("id", false)->get();
            foreach ($quotes as $key => $quote) {
                $quote->items = Database::table('quoteitems')->where('quote', $quote->id)->count("id", "total")[0]->total;
                $quote->project = Database::table('projects')->where('id', $quote->project)->first();
            }

        }elseif (isset($_GET["view"]) && $_GET["view"] == "payments") {

            $payments = Database::table('projectpayments')->where('company', $user->company)->where('client', $client->id)->orderBy("id", false)->get();
            foreach ($payments as $key => $payment) {
                $payment->project = Database::table('projects')->where('id', $payment->project)->first();
            }

        }elseif (isset($_GET["view"]) && $_GET["view"] == "jobcards") {

            $jobcards = Database::table('jobcards')->where('company', $user->company)->where('client', $client->id)->orderBy("id", false)->get();
            foreach ($jobcards as $key => $jobcard) {
                $jobcard->project = Database::table('projects')->where('id', $jobcard->project)->first();
            }

        }

        if (isset($_GET["view"]) && $_GET["view"] == "invoices" || isset($_GET["view"]) && $_GET["view"] == "payments") {

            $invoices = Database::table('invoices')->where('company', $user->company)->where('client', $client->id)->orderBy("id", false)->get();
            foreach ($invoices as $key => $invoice) {
                $invoice->items = Database::table('invoiceitems')->where('invoice', $invoice->id)->count("id", "total")[0]->total;
                $invoice->project = Database::table('projects')->where('id', $invoice->project)->first();
                $invoice->balance = $invoice->total - $invoice->amount_paid;
            }

        }
        
        return view("client-details", compact("user", "title", "client","notes","projects","staffmembers","quotes","invoices","payments","jobcards"));
        
    }
    
    
    /**
     * Client update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $client = Database::table('clients')->where('company', $user->company)->where('id', input("clientid"))->first();
        
        return view('modals/update-client', compact("client"));
        
    }
    
    /**
     * Update Client account
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "fullname" => escape(input('fullname')),
            "email" => escape(input('email')),
            "phonenumber" => escape(input('phonenumber')),
            "address" => escape(input('address'))
        );
        
        Database::table('clients')->where('id', input('clientid'))->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Client account successfully updated.", "reload()"));
        
    }
    
    /**
     * Delete client account
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();
        Database::table('clients')->where('id', input('clientid'))->where('company', $user->company)->delete();
        Database::table('notes')->where('item', input('clientid'))->where('type', "Client")->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Client account successfully deleted.", "redirect('" . url("Clients@get") . "', true)"));
        
    }
    
}