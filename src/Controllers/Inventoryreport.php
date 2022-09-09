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

class Inventoryreport {

    /**
     * View inventory report
     * 
     * @return \Pecee\Http\Response
     */
    public function view($inventoryid) {

        $user   = Auth::user();
        $inventory = Database::table('inventory')->where('company', $user->company)->where('id', $inventoryid)->first();
        if (empty($inventory)) {
            return view('errors/404');
        }

        $title = "Inventory Report: ".$inventory->name;

        return view('view-inventoryreport', compact("title", "user","inventory"));
        
    }
    
    /**
     * Generate PDF
     * 
     * @return application/pdf
     */
    public function render($inventoryid) {
        
        $user   = Auth::user();
        $inventory = Database::table('inventory')->where('company', $user->company)->where('id', $inventoryid)->first();
        if (empty($inventory)) {
            return view('errors/404');
        }

        $supplier = Database::table('suppliers')->where('company', $user->company)->where('id', $inventory->supplier)->first();
        $inventorylogs = Database::table('inventorylog')->where('company', $user->company)->where('item', $inventory->id)->orderBy("id", false)->get();

        $this->generate($user, $inventory, $supplier, $inventorylogs);

        die();
        
    }


    /**
     * Generate PDF Work Sheet
     * 
     * @return Json
     */
    public function generate($user, $inventory, $supplier, $inventorylogs, $save = false) {


        $counter = ( object ) array(
            "awaitingdelivery" => 0,
            "ordered" => 0,
            "delivered" => 0,
            "toorder" => 0,
            "owed" => 0
        );

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
        $pdf->MultiCell(200, 70, "INVENTORY", null, "L");


        // client info
        $xPos = $pdf->GetX();
        $yPos = 106;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Inventory Item: ", null, "L");

        $yPos += 20;
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $pdf->SetFont('','B',11);
        $pdf->MultiCell(170, 17, $inventory->name, null, "L");
        $pdf->SetFont('','',10);
        $pdf->MultiCell(170, 15, "In Stock: ".$inventory->quantity." ".$inventory->quantity_unit, null, "L");
        $pdf->MultiCell(170, 35, "Stock Value: ".money(($inventory->quantity * $inventory->unit_cost), $user->parent->currency), null, "L");


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
        $pdf->MultiCell(150, 70, "Added On : ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M d, Y", strtotime($inventory->created_at)), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Report Date: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M d, Y"), null, "R");


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
        $pdf->MultiCell(450, 25, "Consumption report: ", null, "L");
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(450, 25, "Consumption report for ".$inventory->name.".", null, "L");

        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);


        $total = 0;
        $items = array();
        if (!empty($inventorylogs)) {
            foreach ($inventorylogs as $key => $inventorylog) {

                $project = Database::table('projects')->where('id', $inventorylog->project)->first();

                $items[] = '<tr>
                    <td style="width:5%;">'.($key + 1).'</td>
                    <td style="width:21%;">'.carmake($project->make).' - '.$project->registration_number.'</td>
                    <td style="width:15%;">'.$inventorylog->consumed.' '.$inventory->quantity_unit.'</td>
                    <td style="width:15%;">'.money($inventorylog->consumed_value, $user->parent->currency).'</td>
                    <td style="width:17%;">'.$inventorylog->issued_by.'</td>
                    <td style="width:17%;">'.$inventorylog->issued_to.'</td>
                    <td style="width:10%;">'.date("d/m/y", strtotime($inventorylog->created_at)).'</td>
                </tr>';
            }
        }


        $content = '
        <table cellpadding="5" cellspacing="5" style="width:100%;background-color: #404448;color:#ffffff;">
            <tr>
                <td style="width:5%;"><b>#</b></td>
                <td style="width:21%;"><b>Vehicle</b></td>
                <td style="width:15%;"><b>Units</b></td>
                <td style="width:15%;"><b>Value</b></td>
                <td style="width:17%;"><b>Issued By</b></td>
                <td style="width:17%;"><b>Issued To</b></td>
                <td style="width:10%;"><b>Date</b></td>
            </tr>
        </table>

        <table cellpadding="5" cellspacing="5" style="width:100%;color:#526484;">
        '.implode(" ", $items).'
        </table>
        <table style="width:100%;height:30px;border-color:#404448;"><tr><td></td></tr></table>

        ';

        $pdf->writeHTML($content, true, false, true, false, '');

        
        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Inventory Report #".$inventory->name.".pdf", 'I');
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