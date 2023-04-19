<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;
use Simcify\Mail;

class Team {
    
    /**
     * Render team page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = 'Team';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $projects = Database::table('projects')->where('company', $user->company)->orderBy("id", false)->get();
        $members = Database::table('users')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($members as $key => $member) {
            $total_expense = Database::table('s_payments')->where('company', $user->company)->where('isEmployeeExpense',1)->where('employee_id', $member->id)->sum('amount','total');
            $member->balance = $member->balance - $total_expense[0]->total;
            $member->pending_tasks = Database::table('tasks')->where('member', $member->id)->where('status', "In progress")->count("id", "total")[0]->total;
            $member->total_tasks = Database::table('tasks')->where('member', $member->id)->count("id", "total")[0]->total;
        }
        
        return view("team", compact("user", "title", "members","projects"));
        
    }
    
    /**
     * Create team member account 
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        $password = rand(111111, 999999);

        if (!empty(input("email"))) {
            $account = Database::table('users')->where('email', input("email"))->first();
            if (!empty($account)) {
                return response()->json(responder("warning", "Hmmm!", "This email address already exists."));
            }
        }

        if (input("role") != "Staff" && empty(input("email"))) {
            return response()->json(responder("warning", "Hmmm!", "An email is required for ".input("role")." role for login purposes."));
        }
        
        $data = array(
            "company" => $user->company,
            "fname" => escape(input('fname')),
            "lname" => escape(input('lname')),
            "phonenumber" => escape(input('phonenumber')),
            "type" => escape(input('type')),
            "role" => escape(input('role')),
            "status" => escape(input('status')),
            "address" => escape(input('address')),
            "password" => Auth::password($password)
        );
        
        if(!empty(input('email'))){
            $data["email"] = escape(input('email'));
        }
        
        
        Database::table('users')->insert($data);
        $teamid = Database::table('users')->insertId();

        if (!empty($teamid) && input("role") != "Staff") {
            Mail::send(
                input('email'),
                "Welcome to ".env("APP_NAME")."!",
                array(
                    "title" => "Welcome to ".env("APP_NAME")."!",
                    "subtitle" => "A new account has been created for you at ".env("APP_NAME").".",
                    "buttonText" => "Login Now",
                    "buttonLink" => env("APP_URL"),
                    "message" => "These are your login Credentials:<br><br><strong>Email:</strong>".input('email')."<br><strong>Password:</strong>".$password."<br><br>Cheers!<br>".env("APP_NAME")." Team."
                ),
                "withbutton"
            );
        }
        
        return response()->json(responder("success", "Alright!", "Team Member account successfully created.", "redirect('" . url('Team@details', array(
            'teamid' => $teamid
        )) . "')"));
        
    }
    
    /**
     * Render member's details page
     * 
     * @return \Pecee\Http\Response
     */
    public function details($teamid) {
        
        $tasks = $payments = array();
        $user   = Auth::user();
        $member = Database::table('users')->where('company', $user->company)->where('id', $teamid)->first();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }

        if (empty($member)) {
            return view('errors/404');
        }
        
        $title = $member->fname." ".$member->lname;
        $member->pending_tasks = Database::table('tasks')->where('member', $member->id)->where('status', "In progress")->count("id", "total")[0]->total;
        $member->total_tasks = Database::table('tasks')->where('member', $member->id)->count("id", "total")[0]->total;

        $notes = Database::table('notes')->where('item', $teamid)->where('type', "Team")->where('company', $user->company)->orderBy("id", false)->get();

        if (isset($_GET["view"]) && $_GET["view"] == "tasks") {

            $tasks = Database::table('tasks')->where('company', $user->company)->where('member', $member->id)->orderBy("id", false)->get();
            foreach ($tasks as $key => $task) {
                if (!empty($task->member)) {
                    $task->project = Database::table('projects')->where('company', $user->company)->where('id', $task->project)->first();
                }
                
                $taskparts = Database::table('taskparts')->where('task', $task->id)->get();
                $task->undeliveredparts = 0;
                $task->taskparts = "Delivered";
                if (!empty($taskparts)) {
                    foreach ($taskparts as $key => $taskpart) {
                        $expense = Database::table('expenses')->where('company', $user->company)->where('id', $taskpart->part)->first();
                        if (!empty($expense) && $expense->status != "Delivered") {
                            $task->undeliveredparts++;
                            $task->taskparts = "Pending";
                        }
                    }
                }else{
                    $task->taskparts = NULL;
                }
            }

        }elseif (isset($_GET["view"]) && $_GET["view"] == "payments") {

            $payments = Database::table('teampayments')->where('company', $user->company)->where('member', $member->id)->orderBy("id", false)->get();

        }

        $projects = Database::table('projects')->where('company', $user->company)->orderBy("id", false)->get();

        $expenses = Database::table('s_payments')->where('company', $user->company)->where('isEmployeeExpense',1)->where('employee_id', $teamid)->orderby('payment_date','desc')->get();
        $incomes = Database::table('tasks')->where('company', $user->company)->where('member', $teamid)->get();
        
        return view("team-details", compact("user", "title", "member","notes","tasks","projects","payments",'expenses','incomes'));
        
    }
    
    
    /**
     * Team member update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $member = Database::table('users')->where('company', $user->company)->where('id', input("teamid"))->first();
        
        return view('modals/update-member', compact("member", "user"));
        
    }
    
    /**
     * Update team member account
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();

        if (input("role") != "Staff" && empty(input("email"))) {
            return response()->json(responder("warning", "Hmmm!", "An email is required for ".input("role")." role for login purposes."));
        }
        
        $data = array(
            "fname" => escape(input('fname')),
            "lname" => escape(input('lname')),
            "phonenumber" => escape(input('phonenumber')),
            "type" => escape(input('type')),
            "role" => escape(input('role')),
            "status" => escape(input('status')),
            "address" => escape(input('address')),
            "balance" => escape(input('balance'))
        );
        
        if(!empty(input('email'))){
            $data["email"] = escape(input('email'));
        }else{
            $data["email"] = "NULL";
        }
        
        Database::table('users')->where('id', input('teamid'))->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Team member account successfully updated.", "reload()"));
        
    }
    
    /**
     * Delete team member account
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();
        if ($user->id == input("teamid")) {
            return response()->json(responder("error", "Hmmm!", "You can not delete your own account."));
        }

        $account = Database::table('users')->where('id', input("teamid"))->where('company', $user->company)->first();
        if ($account->role == "Owner") {
            $owners = Database::table('users')->where('role', "Owner")->where('company', $user->company)->count("id", "total")[0]->total;
            if ($owners == 1) {
                return response()->json(responder("error", "Hmmm!", "The system needs atleast one owner account."));
            }
        }

        Database::table('users')->where('id', input('teamid'))->where('company', $user->company)->delete();
        Database::table('notes')->where('item', input('teamid'))->where('type', "Team")->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Team member account successfully deleted.", "redirect('" . url("Team@get") . "', true)"));
        
    }
    
}