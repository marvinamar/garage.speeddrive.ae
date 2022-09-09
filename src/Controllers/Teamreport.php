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

class Teamreport {

    /**
     * View Job card
     * 
     * @return \Pecee\Http\Response
     */
    public function view() {

        if (!isset($_GET["staff"]) || !isset($_GET["project"]) || !isset($_GET["status"])) {
            return view('errors/404');
        }

        $user   = Auth::user();
        $staff = Database::table('users')->where('company', $user->company)->where('id', $_GET["staff"])->first();
        if (empty($staff)) {
            return view('errors/404');
        }

        $projects = Database::table('projects')->where('company', $user->company)->orderBy("id", false)->get();

        $title = "HR Report :".$staff->fname." ".$staff->fname;

        return view('view-teamreport', compact("title", "user","staff","projects"));
        
    }
    
    /**
     * Generate PDF
     * 
     * @return application/pdf
     */
    public function render() {
        
        $user   = Auth::user();
        $staff = Database::table('users')->where('company', $user->company)->where('id', $_GET["staff"])->first();
        if (empty($staff)) {
            return view('errors/404');
        }

        $counter = array(
            "completed" => 0,
            "inprogress" => 0,
            "cancelled" => 0
        );

        if ($_GET["project"] == "All" && $_GET["status"] == "All") {
            $tasks = Database::table('tasks')->where('company', $user->company)->where('member', $_GET["staff"])->orderBy("id", false)->get();
        }elseif ($_GET["project"] == "All" && $_GET["status"] != "All") {
            $tasks = Database::table('tasks')->where('company', $user->company)->where('member', $_GET["staff"])->where('status', $_GET["status"])->orderBy("id", false)->get();
        }elseif ($_GET["project"] != "All" && $_GET["status"] == "All") {
            $tasks = Database::table('tasks')->where('company', $user->company)->where('member', $_GET["staff"])->where('project', $_GET["project"])->orderBy("id", false)->get();
        }elseif ($_GET["project"] != "All" && $_GET["status"] != "All") {
            $tasks = Database::table('tasks')->where('company', $user->company)->where('member', $_GET["staff"])->where('project', $_GET["project"])->where('status', $_GET["status"])->orderBy("id", false)->get();
        }else{
            $tasks = array();
        }
        
        foreach ($tasks as $key => $task) {

            if($task->status == "Completed"){
                $counter["completed"]++;
            }elseif($task->status == "Cancelled"){
                $counter["cancelled"]++;
            }else{
                $counter["inprogress"]++;
            }

            $task->project = Database::table('projects')->where('company', $user->company)->where('id', $task->project)->first();
        }

        $this->generate($user, $tasks, $staff, ( object ) $counter);

        die();
        
    }


    /**
     * Generate PDF Work Sheet
     * 
     * @return Json
     */
    public function generate($user, $tasks, $staff, $counter, $save = false) {

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
        $pdf->SetFont('','',25);
        $pdf->SetXY($xPos, $yPos + 5);
        $pdf->MultiCell(200, 70, "HR REPORT", null, "L");


        // client info
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Report For: ", null, "L");

        $yPos += 20;
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $pdf->SetFont('','B',11);
        $pdf->MultiCell(170, 17, $staff->fname." ".$staff->lname, null, "L");
        $pdf->SetFont('','',10);
        $pdf->MultiCell(170, 15, $staff->phonenumber, null, "L");
        $pdf->MultiCell(170, 35, $staff->role, null, "L");


        // Document details
        $xPos += 190;
        $yPos = 106;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(170, 25, "Overview: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos += 20;
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Total Tasks : ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $counter->completed + $counter->inprogress + $counter->cancelled, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Completed: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $counter->completed, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "In Progress: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $counter->inprogress, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Cancelled: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $counter->cancelled, null, "R");


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
        $yPos = $pdf->GetY() - 25;

        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);
        $pdf->Line($xPos, $yPos + 3, $xPos + 539, $yPos + 3);


        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Tasks report: ", null, "L");
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(450, 25, "Tasks assigned to ".$staff->fname." ".$staff->lname." and their status. ", null, "L");

        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);


        $total = 0;
        $items = array();
        foreach ($tasks as $key => $task) {

            if($task->status == "Completed"){
                $pdf->SetTextColor(3, 206, 24);
                $statuscolor = "rgb(3, 206, 24)";
            }elseif($task->status == "Cancelled"){
                $statuscolor = "rgb(82, 100, 132)";
            }else{
                $statuscolor = "rgb(250, 172, 15)";
            }

            $total = $total + $task->cost;

            $items[] = '<tr>
                <td style="width:5%;">'.($key + 1).'</td>
                <td style="width:25%;">'.$task->title.'</td>
                <td style="width:20%;">'.carmake($task->project->make).' - '.$task->project->registration_number.'</td>
                <td style="width:15%;">'.money($task->cost, $user->parent->currency).'</td>
                <td style="width:20%;">'.date("d/m/y", strtotime($task->created_at)).' - '.date("d/m/y", strtotime($task->due_date)).'</td>
                <td style="width:15%;text-align:right;color:'.$statuscolor.';">'.$task->status.'</td>
            </tr>';
        }


        $content = '
        <table cellpadding="5" cellspacing="5" style="width:100%;background-color: #404448;color:#ffffff;">
            <tr>
                <td style="width:5%;"><b>#</b></td>
                <td style="width:25%;"><b>Task</b></td>
                <td style="width:20%;"><b>Vehicle</b></td>
                <td style="width:15%;"><b>Cost</b></td>
                <td style="width:20%;"><b>Allocated - Due</b></td>
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
            $pdf->Output("Team Report #".$staff->fname." ".$staff->lname.".pdf", 'I');
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