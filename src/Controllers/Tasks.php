<?php
namespace Simcify\Controllers;

$tcpdfPath = str_replace("Controllers", "TCPDF/", dirname(__FILE__));

require_once $tcpdfPath.'tcpdf.php';

use TCPDF;
use Simcify\Database;
use Simcify\Asilify;
use Simcify\Auth;
use Simcify\Sms;

class Tasks {
    
    /**
     * Render pending tasks page
     * 
     * @return \Pecee\Http\Response
     */
    public function pending() {
        
        $title = 'Pending Tasks';
        $user  = Auth::user();
        
        $tasks = Database::table('tasks')->where('company', $user->company)->where('status', "In Progress")->orderBy("id", false)->get();
        foreach ($tasks as $key => $task) {
            if (!empty($task->member)) {
                $task->member = Database::table('users')->where('company', $user->company)->where('id', $task->member)->first();
            }
            $task->project = Database::table('projects')->where('id', $task->project)->first();

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
        
        return view("tasks-pending", compact("user", "title", "tasks"));
        
    }
    
    /**
     * Create a task  
     * 
     * @return Json
     */
    public function create() {
        
        $canComplete = true;
        $user = Auth::user();
        
        $data = array(
            "company" => $user->company,
            "member" => escape(input('member')),
            "project" => escape(input('project')),
            "status" => escape(input('status')),
            "title" => escape(input('title')),
            "description" => escape(input('description')),
            "cost" => escape(input('cost')),
            "due_date" => escape(input('due_date'))
        );

        if (!empty(input('due_time'))) {
            $data["due_time"] = escape(input('due_time'));
        }

        Database::table('tasks')->insert($data);
        $taskid = Database::table('tasks')->insertId();

        if (!empty($_POST["parts_required"])) {
            foreach ($_POST["parts_required"] as $key => $part) {

                if (input("status") == "Completed") {
                    $expense = Database::table('expenses')->where('company', $user->company)->where('id', $part)->first();
                    if (!empty($expense) && $expense->status != "Delivered") {
                        $canComplete = false;
                        Database::table('tasks')->where('id', $taskid)->where('company', $user->company)->update(array(
                            "status" => "In Progress"
                        ));
                    }
                }

                $part_required = array(
                    "part" => $part,
                    "task" => $taskid,
                    "company" => $user->company,
                    "project" => escape(input('project'))
                );

                Database::table('taskparts')->insert($part_required);
            }
        }

        $this->balance($taskid, "plus");

        if ($user->parent->sms_tasks == "Enabled" && $user->parent->sms_balance > 0) {
            $member = Database::table('users')->where('company', $user->company)->where('id', input("member"))->first();
            if (!empty($member->phonenumber)) {
                $message = "New Task #".$taskid."!\nTitle: ".input('title')."\nCost: ".money(input('cost'), $user->parent->currency)."\nDue Date: ".date("F j, Y", strtotime(input('due_date')))."\nDescription: ".nl2br(input('description'))."\n\n ".$user->parent->name;
                $response = Sms::africastalking($member->phonenumber, $message);

                Asilify::queue(array(
                            "phonenumber" => $member->phonenumber,
                            "name" => $member->fname." ".$member->lname
                        ), $message, $user, "NULL", $response);
            }
        }
        
        if ($canComplete) {
            return response()->json(responder("success", "Alright!", "Task successfully created.", "redirect('" . url('Projects@details', array(
                'projectid' => input('project')
            )) . "?view=tasks')"));
        }else{
            return response()->json(responder("success", "Alright!", "Task successfully created, but not marked as complete since one or more required parts have not been delivered yet.", "redirect('" . url('Projects@details', array(
                'projectid' => input('project'),
                'Isqt' => 'false'
            )) . "?view=tasks')"));
        }
        
        
    }
    
    /**
     * Create a task in bulk  
     * 
     * @return Json
     */
    public function addbulk() {
        
        $user = Auth::user();

        if (empty($_POST["indexing"])) {
            return response()->json(responder("error", "Hmmm!", "No tasks created."));
        }

        foreach ($_POST["indexing"] as $key => $index) {
        
            $canComplete = true;
            
            $data = array(
                "company" => $user->company,
                "member" => escape(input('member'.$index)),
                "project" => escape(input('project')),
                "status" => escape(input('status'.$index)),
                "title" => escape(input('title'.$index)),
                "description" => escape(input('description'.$index)),
                "cost" => escape(input('cost'.$index)),
                "due_date" => escape(input('due_date'.$index))
            );

            if (!empty(input('due_time'.$index))) {
                $data["due_time"] = escape(input('due_time'.$index));
            }
        
            Database::table('tasks')->insert($data);
            $taskid = Database::table('tasks')->insertId();

            if (!empty($_POST["parts_required".$index])) {
                foreach ($_POST["parts_required".$index] as $key => $part) {

                    if (input("status") == "Completed") {
                        $expense = Database::table('expenses')->where('company', $user->company)->where('id', $part)->first();
                        if (!empty($expense) && $expense->status != "Delivered") {
                            $canComplete = false;
                            Database::table('tasks')->where('id', $taskid)->where('company', $user->company)->update(array(
                                "status" => "In Progress"
                            ));
                        }
                    }

                    $part_required = array(
                        "part" => $part,
                        "task" => $taskid,
                        "company" => $user->company,
                        "project" => escape(input('project'))
                    );

                    Database::table('taskparts')->insert($part_required);
                }
            }

            $this->balance($taskid, "plus");

            if ($user->parent->sms_tasks == "Enabled" && $user->parent->sms_balance > 0) {
                $member = Database::table('users')->where('company', $user->company)->where('id', input("member"))->first();
                if (!empty($member->phonenumber)) {
                    $message = "New Task #".$taskid."!\nTitle: ".input('title'.$index)."\nCost: ".money(input('cost'.$index), $user->parent->currency)."\nDue Date: ".date("F j, Y", strtotime(input('due_date'.$index)))."\nDescription: ".nl2br(input('description'.$index))."\n\n ".$user->parent->name;
                    $response = Sms::africastalking($member->phonenumber, $message);

                    Asilify::queue(array(
                                "phonenumber" => $member->phonenumber,
                                "name" => $member->fname." ".$member->lname
                            ), $message, $user, "NULL", $response);
                }
            }

        }

        return response()->json(responder("success", "Alright!", "Task(s) successfully created.", "redirect('" . url('Projects@details', array(
                'projectid' => input('project'),
                'Isqt' => 'false'
            )) . "?view=tasks')"));
        
    }
    
    /**
     * Generate PDF
     * 
     * @return \Pecee\Http\Response
     */
    public function download($taskid) {
        
        $user   = Auth::user();
        $task = Database::table('tasks')->where('company', $user->company)->where('id', $taskid)->first();
        
        if (empty($task)) {
            return view('errors/404');
        }
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', $task->project)->first();
        if (!empty($task->member)) {
            $task->member = Database::table('users')->where('company', $user->company)->where('id', $task->member)->first();
        }

        $this->generate($user, $task, $project);

        die();
        
    }
    
    
    /**
     * Import tasks from work requested
     * 
     * @return \Pecee\Http\Response
     */
    public function workrequested() {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();
        $staffmembers = Database::table('users')->where('company', $user->company)->where('role', "Staff")->orderBy("id", false)->get();

        $expenses = Database::table('expenses')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();

        if (!empty($project->work_requested)) {
            $project->work_requested = json_decode($project->work_requested);
        }else{
            $project->work_requested = array();
        }
        
        return view('modals/import-tasks-workrequested', compact("project","user","staffmembers","expenses"));
        
    }
    
    
    /**
     * Import tasks from jobcards
     * 
     * @return \Pecee\Http\Response
     */
    public function jobcards() {
        
        $items = array();
        $user   = Auth::user();
        $staffmembers = Database::table('users')->where('company', $user->company)->where('role', "Staff")->orderBy("id", false)->get();
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
        
        return view('modals/import-tasks-jobcard', compact("items","user","staffmembers","jobcard"));
        
    }
    
    
    /**
     * Task update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $task = Database::table('tasks')->where('company', $user->company)->where('id', input("taskid"))->first();
        
        if (input("action") == "edit") {
            $expenses = Database::table('expenses')->where('company', $user->company)->where('project', $task->project)->orderBy("id", false)->get();
            foreach ($expenses as $key => $expense) {
                $required = Database::table('taskparts')->where('part', $expense->id)->where('task', $task->id)->first();
                if (!empty($required)) {
                    $expense->required = "selected";
                }else{
                    $expense->required = "";
                }
            }

            return view('modals/update-task', compact("task","user","expenses"));
        }else{
            $taskparts = Database::table('taskparts')->where('task', $task->id)->get();
            foreach ($taskparts as $key => $taskpart) {
                $taskpart->part = Database::table('expenses')->where('id', $taskpart->part)->first();
            }
            return view('modals/view-task', compact("task","user","taskparts"));
        }
        
        
    }
    
    /**
     * Update Task
     * 
     * @return Json
     */
    public function update() {
        
        $canComplete = true;
        $user = Auth::user();
        
        $data = array(
            "status" => escape(input('status')),
            "title" => escape(input('title')),
            "description" => escape(input('description')),
            "due_date" => escape(input('due_date'))
        );

        if (!empty(input('due_time'))) {
            $data["due_time"] = escape(input('due_time'));
        }

        $task = Database::table('tasks')->where('id', input("taskid"))->where('company', $user->company)->first();
        if ($task->status == "Cancelled" && input("status") != "Cancelled") {
            $this->balance($task->id, "plus");
        }elseif ($task->status != "Cancelled" && input("status") == "Cancelled") {
            $this->balance($task->id, "minus");
        }

        Database::table('tasks')->where('id', input('taskid'))->where('company', $user->company)->update($data);

        Database::table('taskparts')->where("task", $task->id)->delete();
        if (!empty($_POST["parts_required"])) {
            foreach ($_POST["parts_required"] as $key => $part) {

                if (input("status") == "Completed") {
                    $expense = Database::table('expenses')->where('company', $user->company)->where('id', $part)->first();
                    if (!empty($expense) && $expense->status != "Delivered") {
                        $canComplete = false;
                        Database::table('tasks')->where('id', $task->id)->where('company', $user->company)->update(array(
                            "status" => "In Progress"
                        ));
                    }
                }

                $part_required = array(
                    "part" => $part,
                    "task" => $task->id,
                    "company" => $user->company,
                    "project" => $task->project
                );

                Database::table('taskparts')->insert($part_required);
            }
        }

        if ($user->parent->sms_tasks == "Enabled" && $user->parent->sms_balance > 0) {
            $member = Database::table('users')->where('company', $user->company)->where('id', $task->member)->first();
            if (!empty($member->phonenumber) && input('description') != $task->description) {
                $message = "Task Updated #".$task->id."!\nTitle: ".input('title')."\nDue Date: ".date("F j, Y", strtotime(input('due_date')))."\nDescription: ".nl2br(input('description'))."\n\n ".$user->parent->name;
                $response = Sms::africastalking($member->phonenumber, $message);

                Asilify::queue(array(
                            "phonenumber" => $member->phonenumber,
                            "name" => $member->fname." ".$member->lname
                        ), $message, $user, "NULL", $response);
            }
        }
        
        if ($canComplete) {
            return response()->json(responder("success", "Alright!", "Task successfully updated.", "reload()"));
        }else{
            return response()->json(responder("success", "Alright!", "Task successfully updated but not marked as complete since one or more required parts have not been delivered yet", "reload()"));
        }
        
        
    }
    
    /**
     * Cancel Task
     * 
     * @return Json
     */
    public function cancel() {
        
        $user = Auth::user();
        
        $data = array(
            "status" => escape("Cancelled")
        );
        
        $this->balance(input('taskid'), "minus");

        Database::table('tasks')->where('id', input('taskid'))->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Task successfully cancelled.", "reload()"));
        
    }
    
    /**
     * Delete Task
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();

        $this->balance(input('taskid'), "minus");

        Database::table('tasks')->where('id', input('taskid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Task successfully deleted.", "reload()"));
        
    }
    
    /**
     * Balance calculation
     * 
     * @return Json
     */
    public function balance($taskid, $action) {

        if (empty($taskid)) {
            return;
        }
        
        $user = Auth::user();
        $task = Database::table('tasks')->where('id', $taskid)->where('company', $user->company)->first();
        $member = Database::table('users')->where('id', $task->member)->where('company', $user->company)->first();

        if (empty($member) || empty($task)) {
            return;
        }

        if ($action == "plus") {
            $member->balance = $member->balance + $task->cost;
        }elseif ($action == "minus") {
            $member->balance = $member->balance - $task->cost;
        }
        
        Database::table('users')->where('id', $member->id)->update(array(
            "balance" => $member->balance
        ));
        
    }




    /**
     * Generate PDF Work Sheet
     * 
     * @return Json
     */
    public function generate($user, $task, $project) {

        $pdf = new PDF(null, 'px');
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(TRUE, 150);
        $pdf->AddPage();
        $pdf->company = $user->parent;


        $xPos = $pdf->GetX();
        $yPos = 30;


        // Document Title
        $pdf->Image(env("APP_URL")."/assets/images/headerpattern.png", 0, $yPos + 1, 320);

        $pdf->SetFillColor(64, 68, 72);
        $pdf->Rect(0, 30, 200, 55, 'F');

        $yPos +=12;
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('','',24);
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(200, 70, "TASK SHEET", null, "L");

        // logo
        $yPos = 30;
        $xPos = 367;
        if (!empty($user->parent->logo)) {
            $pdf->Image(env("APP_URL")."/uploads/logos/".$user->parent->logo, $xPos + 40, $yPos, 160);
        }else{
            $pdf->SetFont('','B',24);
            $pdf->SetTextColor(54, 74, 99);
            $pdf->SetXY($xPos + 20, $yPos);
            $pdf->MultiCell(185,55, $user->parent->name, null, "R");
        }


        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY() + 20;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Task Details: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);

        $yPos = $pdf->GetY();
        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

        // details

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Task ID # ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(170, 70, ": ".$task->id, null, "L");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Created On ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".date("M j, Y", strtotime($task->created_at)), null, "L");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Vehicle", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".carmake($project->make)." ".carmodel($project->model), null, "L");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Reg. No", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$project->registration_number, null, "L");

        /////////////////////////////////////////////////////////////////////

        if (!empty($task->member)) {
            $assignedTo = $task->member->fname." ".$task->member->lname;
        }else{
            $assignedTo = "--|--";
        }


        /////////////////////////////////////////////////////////////////////



        $yPos += 25;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Task Title ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$task->title, null, "L");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Assigned To ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$assignedTo, null, "L");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Due Date ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".date("F j, Y", strtotime($task->due_date)), null, "L");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 20, "Status", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);

        if($task->status == "Completed"){
            $pdf->SetTextColor(3, 206, 24);
        }elseif($task->status == "Cancelled"){
            $pdf->SetTextColor(82, 100, 132);
        }else{
            $pdf->SetTextColor(250, 172, 15);
        }

        $pdf->MultiCell(170, 20, ": ".$task->status, null, "L");


        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY() + 20;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Task Description: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);

        $yPos = $pdf->GetY();
        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

        $yPos = $pdf->GetY() + 12;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->writeHTML(nl2br($task->description), true, false, true, false, '');


        /////////////////////////////////////////////////////////////////////

        $taskparts = Database::table('taskparts')->where('task', $task->id)->get();
        $task->undeliveredparts = 0;
        $task->taskparts = "Delivered";
        if (!empty($taskparts)) {

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(450, 20, "Required Parts: ", null, "L");
            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);

            $yPos = $pdf->GetY();
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->writeHTML("Parts required to complete this task.", true, false, true, false, '');

            $yPos = $pdf->GetY() + 12;
            $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);


            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);


            $quoteitems = array(1, 2, 3, 4);
            $items = array();

            foreach ($taskparts as $key => $taskpart) {
                $expense = Database::table('expenses')->where('company', $user->company)->where('id', $taskpart->part)->first();

                if ($expense->status == "Delivered") {
                    $color = "rgb(3, 206, 24)";
                }elseif ($expense->status == "Ordered") {
                    $color = "rgb(9, 113, 254)";
                }else{
                    $color = "rgb(250, 172, 15)";
                }

                $items[] = '<tr>
                    <td style="width:5%;">'.($key + 1).'</td>
                    <td style="width:20%;color:'.$color.';"><strong>'.$expense->status.'</strong></td>
                    <td style="width:75%;">'.$expense->expense.'</td>
                </tr>';

            }

            $required_parts = '
            <table cellpadding="5" cellspacing="5" style="width:100%;color:#526484;">
            '.implode(" ", $items).'
            </table>

            ';

            $pdf->writeHTML($required_parts, true, false, true, false, '');

        }
        /////////////////////////////////////////////////////////////////////

        $pdf->Output("Task Sheet #".$task->id.".pdf", 'D');

        die();

    }
    public function add_to_invoice(){
        $task_id = input('taskid');
    }
    
}


class PDF extends TCPDF {

    // Page footer
    public function Footer() {

        $this->SetY(-40);

        $yPos = $this->getY() - 10;

        $this->Image(env("APP_URL")."/assets/images/docpattern.png", 0, $yPos, 680);

    }
}