<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Asilify;
use Simcify\Auth;
use Simcify\File;

class Projects {

    /**
     * Render projects page
     *
     * @return \Pecee\Http\Response
     */
    public function get() {

        $title = 'Projects';
        $user  = Auth::user();

        $clients = Database::table('clients')->where('company', $user->company)->orderBy("id", false)->get();

        $projects = Database::table('projects')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($projects as $key => $project) {
            $project->client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
            $project->pending_tasks = Database::table('tasks')->where('project', $project->id)->where('status', "In progress")->count("id", "total")[0]->total;
            $project->total_tasks = Database::table('tasks')->where('project', $project->id)->count("id", "total")[0]->total;
            $project->expenses = Database::table('expenses')->where('project', $project->id)->sum("amount", "total")[0]->total;
            $project->taskcost = Database::table('tasks')->where('project', $project->id)->sum("cost", "total")[0]->total;

            $project->invoiced = Database::table('invoices')->where('project', $project->id)->sum("total", "total")[0]->total;
            $project->cost = $project->taskcost + $project->expenses;
        }

        $staffmembers = Database::table('users')->where('company', $user->company)->where('role', "Staff")->orderBy("id", false)->get();
        $insurance = Database::table('insurance')->where('company', $user->company)->orderBy("id", false)->get();
        $inventorys = Database::table('inventory')->where('company', $user->company)->get();

        return view("projects", compact("user", "title", "projects","clients","staffmembers","insurance","inventorys"));

    }

    /**
     * Create projects
     *
     * @return Json
     */
    public function create() {

        $user = Auth::user();

        $clientid = $this->client((object) $_POST, $user);

        $data = array(
            "company" => $user->company,
            "received_by" => escape($user->fname." ".$user->lname),
            "make" => escape(input('make')),
            "model" => escape(input('model')),
            "registration_number" => escape(input('registration_number')),
            "vin" => escape(input('vin')),
            "client" => escape($clientid),
            "project_key" => escape(uniqid("ap")),
            "status" => escape(input('status')),
            "milleage" => escape(input('milleage')),
            "milleage_unit" => escape(input('milleage_unit')),
            "engine_number" => escape(input('engine_number')),
            "start_date" => escape(input('start_date')),
            "end_date" => escape(input('end_date'))
        );

        if (!empty(input("covered")) && !empty(input("insurance"))) {
            $data["insurance"] = escape(input("insurance"));
        }

        if($user->parent->booking_form == "Detailed"){
            $data["towing_details"] = escape(input("towing_details"));
            $data["booking_in_notes"] = escape(input("booking_in_notes"));
            $data["other_defects"] = escape(input("other_defects"));
            $data["fuel_level"] = escape(input("fuel_level"));
            $data["color"] = escape(input("color"));
            $data["date_in"] = escape(input("date_in"));
            $data["time_in"] = escape(input("time_in"));
            $data["insurance_expiry"] = escape(input("insurance_expiry"));
            $data["insurance_company"] = escape(input("insurance_company"));
            $data["road_test_in"] = escape(input("road_test_in"));

            if (!empty($_POST["work_requested"])) {
                $data["work_requested"] = Asilify::array2json($_POST["work_requested"]);
            }

            if (!empty(input("delivered_by")) && !empty(input("delivered_by"))) {
                $data["delivered_by"] = "Client";
            }else{
                $data["delivered_by"] = "Other";
                $data["deliveredby_fullname"] = escape(input("deliveredby_fullname"));
                $data["deliveredby_phonenumber"] = escape(input("deliveredby_phonenumber"));
                $data["deliveredby_email"] = escape(input("deliveredby_email"));
                $data["deliveredby_idnumber"] = escape(input("deliveredby_idnumber"));
            }

            if($user->parent->car_diagram == "Enabled" && !empty(input("car_diagram"))){

                $upload = File::upload(input("car_diagram"), "cardiagrams", array(
                    "source" => "base64",
                    "extension" => "png"
                ));

                if ($upload['status'] == "success") {
                    $data["car_diagram"] = $upload['info']['name'];
                }

            }

        }

        Database::table('projects')->insert($data);
        $projectid = Database::table('projects')->insertId();
        if (empty($projectid)) {
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }

        if($user->parent->booking_form == "Detailed"){
            if (!empty($_POST["partslist"])) {
                foreach ($_POST["partslist"] as $key => $part) {
                    $partchecked = array(
                        "company" => $user->company,
                        "project" => $projectid,
                        "part" => $part
                    );

                    if (!empty(input("partfield".$part))) {
                        $partchecked["input_field"] = escape(input("partfield".$part));
                        $partchecked["has_input"] = "Yes";
                    }

                    Database::table('partschecked')->insert($partchecked);
                }

            }
        }

        return response()->json(responder("success", "Alright!", "Project successfully created.", "redirect('" . url('Projects@details', array(
            'projectid' => $projectid,
            'Isqt' => 'true'
        )) . "', true)"));

    }

    /**
     * Return Client ID
     *
     * @return int
     */
    public function client($payload, $user) {

        if ($payload->client == "create") {
            $data = array(
                "company" => $user->company,
                "fullname" => escape($payload->fullname),
                "email" => escape($payload->email),
                "phonenumber" => escape($payload->phonenumber),
                "address" => escape($payload->address)
            );
            Database::table('clients')->insert($data);
            $clientid = Database::table('clients')->insertId();
        }else{
            $clientid = $payload->client;
        }

        return $clientid;

    }

    public function approve($projectid, $quoteId){

        $update = array(
            "isApproved" => true,
        );
        Database::table('quotes')->where('id', $quoteId)->update($update);
        return redirect(url('Projects@details', array('projectid' => $projectid,'Isqt' => 'false')).'?view=invoices', true);
    }


    /**
     * Render project's details page
     *
     * @return \Pecee\Http\Response
     */
    public function details($projectid,$Isqt) {

        $tasks = $quotes = $invoices = $payments = array();
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', $projectid)->first();

        if (empty($project)) {
            return view('errors/404');
        }

        $title = carmake($project->make)." ".carmodel($project->model)." - ".$project->registration_number;
        $project->pending_tasks = Database::table('tasks')->where('project', $project->id)->where('status', "In progress")->count("id", "total")[0]->total;
        $project->total_tasks = Database::table('tasks')->where('project', $project->id)->count("id", "total")[0]->total;
        $project->expenses = Database::table('expenses')->where('project', $project->id)->sum("amount", "total")[0]->total;
        $project->taskcost = Database::table('tasks')->where('project', $project->id)->sum("cost", "total")[0]->total;

        $project->invoiced = Database::table('invoices')->where('project', $project->id)->sum("total", "total")[0]->total;
        $project->cost = $project->taskcost + $project->expenses;

        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        $notes = Database::table('notes')->where('item', $projectid)->where('type', "Project")->where('company', $user->company)->orderBy("id", false)->get();
        $staffmembers = Database::table('users')->where('company', $user->company)->where('role', "Staff")->orderBy("id", false)->get();
        $expenses = Database::table('expenses')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();
        foreach ($expenses as $key => $expense) {
            if (!empty($expense->supplier)) {
                $expense->supplier = Database::table('suppliers')->where('id', $expense->supplier)->first();
            }
        }

        $jobcards = Database::table('jobcards')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();

        $suppliers = Database::table('suppliers')->where('company', $user->company)->orderBy("id", false)->get();
        $inventory = Database::table('inventory')->where('company', $user->company)->where('project_specific', "No")->orderBy("id", false)->get();

        if (isset($_GET["view"]) && $_GET["view"] == "tasks") {

            $tasks = Database::table('tasks')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();
            foreach ($tasks as $key => $task) {
                if (!empty($task->member)) {
                    $task->member = Database::table('users')->where('company', $user->company)->where('id', $task->member)->first();
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

        }elseif (isset($_GET["view"]) && $_GET["view"] == "quotes") {

            $quotes = Database::table('quotes')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();
            foreach ($quotes as $key => $quote) {
                $quote->items = Database::table('quoteitems')->where('quote', $quote->id)->count("id", "total")[0]->total;
                $quote->inventory = Database::table('inventory')->where('id', $quote->items)->first();
            }

        }elseif (isset($_GET["view"]) && $_GET["view"] == "payments") {

            $payments = Database::table('projectpayments')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();

        }elseif (isset($_GET["view"]) && $_GET["view"] == "s_payments") {

            $s_payments = Database::table('s_payments')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();

        }

        if (isset($_GET["view"]) && $_GET["view"] == "invoices" || isset($_GET["view"]) && $_GET["view"] == "payments") {

            $invoices = Database::table('invoices')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();
            foreach ($invoices as $key => $invoice) {
                $invoice->items = Database::table('invoiceitems')->where('invoice', $invoice->id)->count("id", "total")[0]->total;
                $invoice->balance = $invoice->total - $invoice->amount_paid;
            }

        }

        if (isset($_GET["view"]) == "s_payments") {

            $pay_expenses = Database::table('expenses')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();
            // foreach ($pay_expenses as $key => $invoice) {
            //     $invoice->items = Database::table('invoiceitems')->where('invoice', $invoice->id)->count("id", "total")[0]->total;
            //     $invoice->balance = $invoice->total - $invoice->amount_paid;
            // }

        }else{
            $pay_expenses = null;
        }
        

        if(!empty($project->insurance)){
            $project->covered_by = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        if (!empty($project->work_requested)) {
            $project->work_requested = json_decode($project->work_requested);
        }else{
            $project->work_requested = array();
        }

        // if()$open_quote = true;
        // $Isqt = false;
        $inventorys = Database::table('inventory')->where('company', $user->company)->get();
        $date = date('Y-m-d');
        $time = date('h:i A');
        return view("project-details", compact("user", "title", "client","notes","project","staffmembers","tasks","expenses","quotes","invoices","payments","jobcards","suppliers","inventory","Isqt","s_payments","pay_expenses","inventorys","date","time"));

    }


    /**
     * project update view
     *
     * @return \Pecee\Http\Response
     */
    public function updateview() {

        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();
        $insurance = Database::table('insurance')->where('company', $user->company)->orderBy("id", false)->get();
        

        $makes = Database::table('makes')->where('status', "Enabled")->orderBy("id", false)->get();

        if (!empty($project->model)) {
            $models = Database::table('models')->where('makeid', $project->make)->orderBy("id", false)->get();
        }else{
            $models = array();
        }

        if($user->parent->booking_form == "Detailed"){
            $partslist = Database::table('partslist')->where('company', $user->company)->orderBy("indexing", true)->get();
            foreach ($partslist as $key => $part) {
                $checked = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "In")->first();
                if (!empty($checked)) {
                    $part->checked = "checked";
                    $part->input = $checked->input_field;
                }else{
                    $part->checked = "";
                    $part->input = "";
                }
            }

            if (!empty($project->work_requested)) {
                $project->work_requested = json_decode($project->work_requested, JSON_HEX_QUOT | JSON_HEX_APOS);
            }else{
                $project->work_requested = array();
            }

            return view('modals/update-project-detailed', compact("project","insurance","user","partslist","makes","models"));
        }else{
            return view('modals/update-project-basic', compact("project","insurance","user","makes","models"));
        }

    }


    /**
     * project update view
     *
     * @return \Pecee\Http\Response
     */
    public function checkout() {

        $allowCheckOut = true;
        $message = "";

        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();

        $partslist = Database::table('partslist')->where('company', $user->company)->orderBy("indexing", true)->get();
        foreach ($partslist as $key => $part) {
            $checked = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "In")->first();
            if (!empty($checked)) {
                $part->checked = "checked";
                $part->input = $checked->input_field;
            }else{
                $part->checked = "";
                $part->input = "";
            }
        }

        $receiveables = Database::table('inventory')->where('company', $user->company)->where('project', $project->id)->where('received', "No")->orderBy("id", false)->get();
        if (!empty($receiveables) && $user->parent->restricted_checkout == "Enabled") {
            $allowCheckOut = false;
            $message = "Some receiveables for this vehicle have not been cleared. Please clear them and try again.";
        }
        if ($user->parent->forced_checkout == "Enabled" && $project->status != "Completed") {
            $allowCheckOut = false;
            $message = "Please mark this project as completed before checking out.";
        }

        return view('modals/check-out', compact("project","user","partslist","allowCheckOut","message"));

    }


    /**
     * Project booking form
     *
     * @return \Pecee\Http\Response
     */
    public function booking() {

        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();
        $insurance = Database::table('insurance')->where('company', $user->company)->orderBy("id", false)->get();
        $clients = Database::table('clients')->where('company', $user->company)->orderBy("id", false)->get();

        $makes = Database::table('makes')->where('status', "Enabled")->orderBy("id", false)->get();

        if($user->parent->booking_form == "Detailed"){
            $partslist = Database::table('partslist')->where('company', $user->company)->orderBy("indexing", true)->get();
            return view('modals/project-booking-detailed', compact("project","insurance","clients","user","partslist","makes"));
        }else{
            return view('modals/project-booking-basic', compact("project","insurance","clients","user","makes"));
        }

    }


    /**
     * Models of a make
     *
     * @return Json
     */
    public function models() {

        $sortedmodels = array(array(
                "id" => "",
                "text" => "Select Model"
            ));

        if (empty(input("makeid"))) {
            return response()->json($sortedmodels);
        }

        $models = Database::table('models')->where('makeid', input("makeid"))->orderBy("id", false)->get();
        foreach ($models as $key => $model) {
            $sortedmodels[] = array(
                "id" => $model->id,
                "text" => $model->name
            );
        }

        return response()->json($sortedmodels);

    }

    /**
     * Update Project
     *
     * @return Json
     */
    public function update() {

        $user = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();

        $data = array(
            "make" => escape(input('make')),
            "model" => escape(input('model')),
            "registration_number" => escape(input('registration_number')),
            "vin" => escape(input('vin')),
            "status" => escape(input('status')),
            "milleage" => escape(input('milleage')),
            "milleage_unit" => escape(input('milleage_unit')),
            "engine_number" => escape(input('engine_number')),
            "start_date" => escape(input('start_date')),
            "end_date" => escape(input('end_date'))
        );

        if (!empty(input("covered"))) {
            $data["insurance"] = escape(input("insurance"));
        }else{
            $data["insurance"] = "NULL";
        }

        if($user->parent->booking_form == "Detailed"){
            $data["towing_details"] = escape(input("towing_details"));
            $data["fuel_level"] = escape(input("fuel_level"));
            $data["booking_in_notes"] = escape(input("booking_in_notes"));
            $data["other_defects"] = escape(input("other_defects"));
            $data["color"] = escape(input("color"));
            $data["date_in"] = escape(input("date_in"));
            $data["time_in"] = escape(input("time_in"));
            $data["insurance_expiry"] = escape(input("insurance_expiry"));
            $data["insurance_company"] = escape(input("insurance_company"));
            $data["road_test_in"] = escape(input("road_test_in"));

            if (!empty($_POST["work_requested"])) {
                $data["work_requested"] = Asilify::array2json($_POST["work_requested"]);
            }

            if (!empty(input("delivered_by")) && !empty(input("delivered_by"))) {
                $data["delivered_by"] = "Client";
            }else{
                $data["delivered_by"] = "Other";
                $data["deliveredby_fullname"] = escape(input("deliveredby_fullname"));
                $data["deliveredby_phonenumber"] = escape(input("deliveredby_phonenumber"));
                $data["deliveredby_email"] = escape(input("deliveredby_email"));
                $data["deliveredby_idnumber"] = escape(input("deliveredby_idnumber"));
            }

            Database::table('partschecked')->where('company', $user->company)->where('project', $project->id)->where('booking', "In")->delete();
            if (!empty($_POST["partslist"])) {
                foreach ($_POST["partslist"] as $key => $part) {
                    $partchecked = array(
                        "company" => $user->company,
                        "project" => $project->id,
                        "part" => $part
                    );

                    if (!empty(input("partfield".$part))) {
                        $partchecked["input_field"] = escape(input("partfield".$part));
                        $partchecked["has_input"] = "Yes";
                    }

                    Database::table('partschecked')->insert($partchecked);
                }

            }
        }

        if ($user->parent->forced_completion == "Enabled") {
            $tasks = Database::table('tasks')->where('company', $user->company)->where('project', $project->id)->where('status', "In Progress")->orderBy("id", false)->get();
            if (input("status") == "Completed" && !empty($tasks)) {
                return response()->json(responder("warning", "Hmmm!", "Could not mark project as complete. There are ".count($tasks)." task(s) in progress."));
            }
        }

        Database::table('projects')->where('id', $project->id)->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Project successfully updated.", "reload()"));

    }

    /**
     * Cancel Project
     *
     * @return Json
     */
    public function cancel() {

        $user = Auth::user();

        $data = array(
            "status" => escape("Cancelled")
        );

        Database::table('projects')->where('id', input('projectid'))->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Project successfully cancelled.", "reload()"));

    }

    /**
     * Delete Project
     *
     * @return Json
     */
    public function delete() {

        $user = Auth::user();
        Database::table('projects')->where('id', input('projectid'))->where('company', $user->company)->delete();
        Database::table('notes')->where('item', input('projectid'))->where('type', "Project")->where('company', $user->company)->delete();

        return response()->json(responder("success", "Alright!", "Project successfully deleted.", "redirect('" . url("Projects@get") . "', true)"));

    }





}