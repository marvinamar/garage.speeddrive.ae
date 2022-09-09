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

class Supplierreport {

    /**
     * View Job card
     * 
     * @return \Pecee\Http\Response
     */
    public function view() {

        if (!isset($_GET["supplier"]) || !isset($_GET["project"]) || !isset($_GET["status"])) {
            return view('errors/404');
        }

        $user   = Auth::user();
        $supplier = Database::table('suppliers')->where('company', $user->company)->where('id', $_GET["supplier"])->first();
        if (empty($supplier)) {
            return view('errors/404');
        }

        $projects = Database::table('projects')->where('company', $user->company)->orderBy("id", false)->get();

        $title = "Procurement Report: ".$supplier->name;

        return view('view-procurementreport', compact("title", "user","supplier","projects"));
        
    }
    
    /**
     * Generate PDF
     * 
     * @return application/pdf
     */
    public function render() {
        
        $user   = Auth::user();
        $supplier = Database::table('suppliers')->where('company', $user->company)->where('id', $_GET["supplier"])->first();
        if (empty($supplier)) {
            return view('errors/404');
        }

        $counter = array(
            "awaitingdelivery" => 0,
            "ordered" => 0,
            "delivered" => 0,
            "toorder" => 0,
            "owed" => 0
        );

        if ($_GET["project"] == "All" && $_GET["status"] == "All") {
            $expenses = Database::table('expenses')->where('company', $user->company)->where('supplier', $_GET["supplier"])->orderBy("id", false)->get();
        }elseif ($_GET["project"] == "All" && $_GET["status"] != "All") {
            $expenses = Database::table('expenses')->where('company', $user->company)->where('supplier', $_GET["supplier"])->where('status', $_GET["status"])->orderBy("id", false)->get();
        }elseif ($_GET["project"] != "All" && $_GET["status"] == "All") {
            $expenses = Database::table('expenses')->where('company', $user->company)->where('supplier', $_GET["supplier"])->where('project', $_GET["project"])->orderBy("id", false)->get();
        }elseif ($_GET["project"] != "All" && $_GET["status"] != "All") {
            $expenses = Database::table('expenses')->where('company', $user->company)->where('supplier', $_GET["supplier"])->where('project', $_GET["project"])->where('status', $_GET["status"])->orderBy("id", false)->get();
        }else{
            $expenses = array();
        }
        
        foreach ($expenses as $key => $expense) {

            if($expense->status == "Delivered"){
                $counter["delivered"]++;
            }elseif($expense->status == "Ordered"){
                $counter["ordered"]++;
            }elseif($expense->status == "Awaiting Delivery"){
                $counter["awaitingdelivery"]++;
            }elseif($expense->status == "To Order"){
                $counter["toorder"]++;
            }

            if($expense->status == "Delivered" && $expense->paid == "No"){
                $counter["owed"] = $counter["owed"] + $expense->amount;
            }

            $expense->project = Database::table('projects')->where('company', $user->company)->where('id', $expense->project)->first();
        }

        $this->generate($user, $expenses, $supplier, ( object ) $counter);

        die();
        
    }


    /**
     * Generate PDF Work Sheet
     * 
     * @return Json
     */
    public function generate($user, $expenses, $supplier, $counter, $save = false) {

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
        $pdf->SetFont('','',20);
        $pdf->SetXY($xPos, $yPos + 8);
        $pdf->MultiCell(200, 70, "PROCUREMENT", null, "L");


        // client info
        $xPos = $pdf->GetX();
        $yPos = 106;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Report For: ", null, "L");

        $yPos += 20;
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $pdf->SetFont('','B',11);
        $pdf->MultiCell(170, 17, $supplier->name, null, "L");
        $pdf->SetFont('','',10);
        $pdf->MultiCell(170, 15, $supplier->phonenumber, null, "L");
        $pdf->MultiCell(170, 35, $supplier->email, null, "L");


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
        $pdf->MultiCell(150, 70, "Delivered : ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $counter->delivered, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Ordered: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $counter->ordered, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Awaiting Delivery: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $counter->awaitingdelivery, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "To Order: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $counter->toorder, null, "R");


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
        $pdf->MultiCell(450, 25, "Procurement report: ", null, "L");
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(450, 25, "Parts sourcing report for ".$supplier->name." and their status. ", null, "L");

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

            $total = $total + $expense->amount;

            $items[] = '<tr>
                <td style="width:5%;">'.($key + 1).'</td>
                <td style="width:25%;">'.$expense->expense.'</td>
                <td style="width:25%;">'.carmake($expense->project->make).' - '.$expense->project->registration_number.'</td>
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
                <td style="width:25%;"><b>Vehicle</b></td>
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
        $pdf->MultiCell(60, 20, "OWED", null, "R");
        $pdf->SetXY(337, $yPos);
        $pdf->MultiCell(220, 20, money($counter->owed, $user->parent->currency), null, "R");

        
        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Procurement Report #".$supplier->name.".pdf", 'I');
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