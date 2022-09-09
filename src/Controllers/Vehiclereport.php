<?php
namespace Simcify\Controllers;

$tcpdfPath = str_replace("Controllers", "TCPDF/", dirname(__FILE__));

require_once $tcpdfPath.'tcpdf.php';

use TCPDF;
use Simcify\Database;
use Simcify\Asilify;
use Simcify\Auth;
use Simcify\Mail;
use Simcify\File;

class Vehiclereport {
    
    /**
     * View Invoice
     * 
     * @return \Pecee\Http\Response
     */
    public function view($projectid) {

        $user   = Auth::user();
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', $projectid)->first();
        if (empty($project)) {
            return view('errors/404');
        }

        $title = "Vehicle report: ".carmake($project->make)." ".carmodel($project->model);

        return view('view-vehiclereport', compact("title", "user","project"));
        
    }
    
    /**
     * Generate PDF
     * 
     * @return application/pdf
     */
    public function render($projectid) {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', $projectid)->first();
        
        if (empty($project)) {
            return view('errors/404');
        }
        
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        $expenses = Database::table('expenses')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();
        $tasks = Database::table('tasks')->where('company', $user->company)->where('project', $project->id)->orderBy("id", false)->get();
        foreach ($tasks as $key => $task) {
            if (!empty($task->member)) {
                $task->member = Database::table('users')->where('company', $user->company)->where('id', $task->member)->first();
            }
        }


        $this->generate($user, $project, $client, $expenses, $tasks);

        die();
        
    }


    /**
     * Generate PDF Work Sheet
     * 
     * @return Json
     */
    public function generate($user, $project, $client, $expenses, $tasks, $save = false) {

        $outputName = uniqid("asilify_").".pdf";
        $outputPath = config("app.storage")."/tmp/". $outputName;
        
        $pdf = new PDF(null, 'px');
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(TRUE, 90);
        $pdf->AddPage();
        $pdf->company = $user->parent;


        $xPos = $pdf->GetX();
        $yPos = 30;


        // Document Title
        $pdf->Image(env("APP_URL")."/assets/images/headerpattern.png", 0, $yPos + 1, 320);

        $pdf->SetFillColor(64, 68, 72);
        $pdf->Rect(0, 30, 200, 55, 'F');

        $yPos +=7;
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('','',30);
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(200, 70, "REPORT", null, "L");


        // client info
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Created For: ", null, "L");

        $yPos += 20;
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        if (!empty($project->insurance)) {
            $pdf->SetFont('','B',11);
            $pdf->MultiCell(170, 17, $project->insurance->name, null, "L");
            $pdf->SetFont('','',10);
            $pdf->MultiCell(170, 15, $project->insurance->phonenumber, null, "L");
            $pdf->MultiCell(170, 35, $project->insurance->address, null, "L");

            $yPos += 60;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','B',11);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->MultiCell(170, 25, "Insured: ", null, "L");
            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);
            $yPos += 20;
            $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);
        }

        $pdf->SetFont('','B',11);
        $pdf->MultiCell(170, 17, $client->fullname, null, "L");
        $pdf->SetFont('','',10);
        $pdf->MultiCell(170, 15, $client->phonenumber, null, "L");
        $pdf->MultiCell(170, 35, $client->address, null, "L");


        // Document details
        $xPos += 190;
        $yPos = 106;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(170, 25, "Project Details: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos += 20;
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Project #: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, "AP".str_pad($project->id, 4, '0', STR_PAD_LEFT), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Start Date: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M j, Y", strtotime($project->start_date)), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "End Date: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M j, Y", strtotime($project->end_date)), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Vehicle: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, carmake($project->make)." ".carmodel($project->model), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Reg. No: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $project->registration_number, null, "R");


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


        $yPos = 130;
        $xPos = 415;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('',"",10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "P: ".$user->parent->phone, null, "L");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(150, 70, "E: ".$user->parent->email, null, "L");
        if (!empty($user->parent->kra_pin)) {
            $yPos += 15;
            $pdf->SetXY($xPos, $yPos);
            $pdf->MultiCell(150, 70, "KRA PIN: ".$user->parent->kra_pin, null, "L");
        }
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(180, 70, $user->parent->address, null, "L");


        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY() + 25;

        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);
        $pdf->Line($xPos, $yPos + 3, $xPos + 539, $yPos + 3);

        // Cost report

        $yPos = $pdf->GetY() + 40;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Cost report: ", null, "L");
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(450, 25, "The cost of parts and services spent on the vehicle. ", null, "L");

        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);


        $total = 0;
        $items = array();
        foreach ($expenses as $key => $expense) {

            if ($expense->status == "Delivered") {
                $statuscolor = "rgb(3, 206, 24)";
            }elseif ($expense->status == "Ordered") {
                $statuscolor = "rgb(9, 113, 254)";
            }elseif ($expense->status == "To Order") {
                $statuscolor = "rgb(239, 69, 141)";
            }else{
                $statuscolor = "rgb(250, 172, 15)";
            }

            if ($expense->paid == "Yes") {
                $paycolor = "rgb(3, 206, 24)";
            }else{
                $paycolor = "rgb(250, 172, 15)";
            }

            if (!empty($expense->supplier)) {
                $expense->supplier = Database::table('suppliers')->where('id', $expense->supplier)->first();
                $supplier = $expense->supplier->name;
            }else{
                $supplier = "";
            }

            $total = $total + $expense->amount;

            $items[] = '<tr>
                <td style="width:5%;">'.($key + 1).'</td>
                <td style="width:25%;">'.$expense->expense.'</td>
                <td style="width:25%;">'.$supplier.'</td>
                <td style="width:15%;">'.money($expense->amount, $user->parent->currency).'</td>
                <td style="width:18%;color:'.$statuscolor.';">'.$expense->status.'</td>
                <td style="width:12%;text-align:right;color:'.$paycolor.';">'.$expense->paid.'</td>
            </tr>';
        }


        $content = '
        <table cellpadding="5" cellspacing="5" style="width:100%;background-color: #404448;color:#ffffff;">
            <tr>
                <td style="width:5%;"><b>#</b></td>
                <td style="width:25%;"><b>Item</b></td>
                <td style="width:25%;"><b>Supplier</b></td>
                <td style="width:15%;"><b>Amount</b></td>
                <td style="width:18%;"><b>Status</b></td>
                <td style="width:12%;text-align:right;"><b>Payment</b></td>
            </tr>
        </table>

        <table cellpadding="5" cellspacing="5" style="width:100%;color:#526484;">
        '.implode(" ", $items).'
        </table>
        <table style="width:100%;height:30px;border-color:#404448;"><tr><td></td></tr></table>

        ';

        $pdf->writeHTML($content, true, false, true, false, '');

        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFillColor(9, 113, 254);
        $pdf->Rect(337, $yPos, 230, 30, 'F');

        $yPos = $pdf->GetY() + 7;
        $pdf->SetXY(337, $yPos);
        $pdf->SetFont('','B',13);
        $pdf->SetTextColor(254, 254, 254);
        $pdf->MultiCell(60, 20, "TOTAL", null, "R");
        $pdf->SetXY(337, $yPos);
        $pdf->MultiCell(220, 50, money($total, $user->parent->currency), null, "R");

        // Cost report

        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Tasks report: ", null, "L");
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(450, 25, "Tasks assigned to staff and their status. ", null, "L");

        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',8);


        $total = 0;
        $items = array();
        foreach ($tasks as $key => $task) {

            if (!empty($task->member)) {
                $assignedTo = $task->member->fname." ".$task->member->lname;
            }else{
                $assignedTo = "--|--";
            }

            if($task->status == "Completed"){
                $pdf->SetTextColor(3, 206, 24);
                $statuscolor = "rgb(3, 206, 24)";
            }elseif($task->status == "Cancelled"){
                $statuscolor = "rgb(82, 100, 132)";
            }else{
                $statuscolor = "rgb(250, 172, 15)";
            }

            if(!empty($task->due_time)){
                $task->due_date = date("M j, Y", strtotime($task->due_date))." • ".date("h:ia", strtotime($task->due_time));
            }else{
                $task->due_date = date("M j, Y", strtotime($task->due_date));
            }

            $total = $total + $task->cost;

            $items[] = '<tr>
                <td style="width:5%;">'.($key + 1).'</td>
                <td style="width:20%;">'.$assignedTo.'</td>
                <td style="width:25%;">'.$task->title.'</td>
                <td style="width:15%;">'.money($task->cost, $user->parent->currency).'</td>
                <td style="width:20%;">'.$task->due_date.'</td>
                <td style="width:15%;text-align:right;color:'.$statuscolor.';">'.$task->status.'</td>
            </tr>';
        }


        $content = '
        <table cellpadding="5" cellspacing="5" style="width:100%;background-color: #404448;color:#ffffff;">
            <tr>
                <td style="width:5%;"><b>#</b></td>
                <td style="width:20%;"><b>Staff</b></td>
                <td style="width:25%;"><b>Task</b></td>
                <td style="width:15%;"><b>Cost</b></td>
                <td style="width:20%;"><b>Due Date</b></td>
                <td style="width:15%;text-align:right;"><b>Status</b></td>
            </tr>
        </table>

        <table cellpadding="5" cellspacing="5" style="width:100%;color:#526484;">
        '.implode(" ", $items).'
        </table>
        <table style="width:100%;height:30px;border-color:#404448;"><tr><td></td></tr></table>

        ';

        $pdf->writeHTML($content, true, false, true, false, '');

        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFillColor(9, 113, 254);
        $pdf->Rect(337, $yPos, 230, 30, 'F');

        $yPos = $pdf->GetY() + 7;
        $pdf->SetXY(337, $yPos);
        $pdf->SetFont('','B',13);
        $pdf->SetTextColor(254, 254, 254);
        $pdf->MultiCell(60, 20, "TOTAL", null, "R");
        $pdf->SetXY(337, $yPos);
        $pdf->MultiCell(220, 20, money($total, $user->parent->currency), null, "R");

        
        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Vehicle Report #".$project->registration_number.".pdf", 'I');
        }

        die();

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