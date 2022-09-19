<?php
namespace Simcify\Controllers;

$tcpdfPath = str_replace("Controllers", "TCPDF/", dirname(__FILE__));

require_once $tcpdfPath.'tcpdf.php';

use TCPDF;
use Simcify\Database;
use Simcify\Auth;

class Projectpayment {
    
    /**
     * Render project payments page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = 'Receipts';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $payments = Database::table('projectpayments')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($payments as $key => $payment) {
            $payment->project = Database::table('projects')->where('id', $payment->project)->first();
            $payment->client = Database::table('clients')->where('id', $payment->client)->first();
        }

        $clients = Database::table('clients')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($clients as $key => $client) {
            $client->invoices = Database::table('invoices')->where('company', $user->company)->where('client', $client->id)->where('status',"!=", "Paid")->where('total',">", "0")->orderBy("id", false)->get();
            if (empty($client->invoices)) {
                unset($clients[$key]);
                continue;
            }

            foreach ($client->invoices as $invoice) {
                $invoice->project = Database::table('projects')->where('id', $invoice->project)->first();
            }

        }
        
        return view("project-payments", compact("user", "title", "payments","clients"));
        
    }
    
    /**
     * Add a project payment
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        $update = array();

        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', input("invoice"))->first();

        $data = array(
            "company" => $user->company,
            "project" => $invoice->project,
            "client" => $invoice->client,
            "invoice" => $invoice->id,
            "method" => escape(input('method')),
            "amount" => escape(input('amount')),
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date'))
        );

        if (!empty($invoice->insurance)) {
            $data["insurance"] = $invoice->insurance;
            unset($data["client"]);
        }
        
        Database::table('projectpayments')->insert($data);
        $projectId = Database::table('projectpayments')->insertId();

        $balance = $invoice->total - $invoice->amount_paid; 
        if (abs((floatval(input('amount')) - $balance)/$balance) < 0.00001 || floatval(input('amount')) > $balance) {
            $update["status"] = "Paid";
        }else{
            $update["status"] = "Partial";
        }

        $update["amount_paid"] = $invoice->amount_paid + input('amount');
        Database::table('invoices')->where('company', $user->company)->where('id', input("invoice"))->update($update);

        // Ledgerposting
        $salesAccount   = Database::table('clients')->where('fullname','Sales Account')->where('isDefault','1')->get();

        //Client ID
        Ledgerposting::save_ledger_posting($projectId, $invoice->client, 'client', 0, $invoice->amount_paid + input('amount'),'Receipt',escape(input('payment_date')));

        //Sales Account
        Ledgerposting::save_ledger_posting($projectId, $salesAccount[0]->id, 'sales_account', $invoice->amount_paid + input('amount'), 0,'Receipt',escape(input('payment_date')));
        // Ledgerposting
        
        return response()->json(responder("success", "Alright!", "Receipt successfully added.", "reload('" . url('Projects@details', array(
            'projectid' => $invoice->project,
            'Isqt' => 'false'
        )) . "?view=payments')"));
        
    }

    
    
    
    /**
     * Payment update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $payment = Database::table('projectpayments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        
        return view('modals/update-payment', compact("payment","user"));
        
    }
    
    /**
     * Update payment
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "method" => escape(input('method')),
            "amount" => escape(input('amount')),
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date'))
        );

        $payment = Database::table('projectpayments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', $payment->invoice)->first();

        $update = array(
            "amount_paid" => ($invoice->amount_paid - $payment->amount) + floatval(input('amount'))
        );

        if (abs((floatval($update["amount_paid"]) - $invoice->total)/$invoice->total) < 0.00001 || $update["amount_paid"] > $invoice->total) {
            $update["status"] = "Paid";
        }else{
            $update["status"] = "Partial";
        }

        Database::table('invoices')->where('company', $user->company)->where('id', $payment->invoice)->update($update);
        Database::table('projectpayments')->where('id', input('paymentid'))->where('company', $user->company)->update($data);

        return response()->json(responder("success", "Alright!", "Payment successfully updated.", "reload()"));
        
    }
    
    
    /**
     * Delete payment
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();
        $payment = Database::table('projectpayments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', $payment->invoice)->first();

        $data = array(
            "amount_paid" => $invoice->amount_paid - $payment->amount
        );

        if ($invoice->total == $payment->amount || $invoice->total < $payment->amount) {
            $data["status"] = "Unpaid";
        }else{
            $data["status"] = "Partial";
        }

        Database::table('invoices')->where('company', $user->company)->where('id', $payment->invoice)->update($data);
        Database::table('projectpayments')->where('id', input('paymentid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Payment successfully deleted.", "reload()"));
        
    }

    /**
     * View Receipt
     *
     * @return \Pecee\Http\Response
     */
    public function view($receiptid) 
    {

        $user   = Auth::user();
        $receipt = Database::table('projectpayments')->where('company', $user->company)->where('id', $receiptid)->first();
        if (empty($receipt)) {
            return view('errors/404');
        }

        $title = "Receipt #".$receipt->id;

        if (!empty($receipt->client)) {
            $owner = Database::table('clients')->where('company', $user->company)->where('id', $receipt->client)->first();
        }else{
            $owner = Database::table('insurance')->where('company', $user->company)->where('id', $receipt->insurance)->first();
        }

        return view('view-receipt', compact("title", "user","receipt","owner"));
    }

    public function render($receiptid) {
        include("config/connect.php");
        $user   = Auth::user();
        $receipt = Database::table('projectpayments')->where('company', $user->company)->where('id', $receiptid)->first();
        $project = Database::table('projects')->where('company', $user->company)->where('id', $receipt->project)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', $receipt->invoice)->first();
        $invoiceitems = Database::table('invoiceitems')->where('company', $user->company)->where('invoice', $invoice->id)->get();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }
        
        $this->generate($user, $receipt, $project, $client, $invoice, $invoiceitems);

        die();

    }

    public function generate($user,$receipt, $project, $client, $invoice, $invoiceitems, $save = false) {

        $outputName = uniqid("asilify_").".pdf";
        $outputPath = config("app.storage")."/tmp/". $outputName;

        $pdf = new PDF(null, 'px');
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(TRUE, 50);
        $pdf->AddPage();
        $pdf->company = $user->parent;

        $xPos = 50;//$pdf->GetX();
        $yPos = 30;

        // Document Title
        $pdf->Image(env("APP_URL")."/assets/images/headerpattern.png", 0, $yPos + 1,  600);
        $pdf->SetFillColor(64, 68, 72);
        $pdf->Rect(220,140, 170, 40, 'F');
        $yPos +=120;
        $pdf->SetTextColor(255,255,255);//(64, 68, 72);
        $pdf->SetFont('','',20);
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(500, 100, "Receipt", null, "C");


        // client info
        $xPos = $pdf->GetX();
        $yPos = 190;//$pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Receipt To: ", null, "L");

        $yPos += 20;
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        if (!empty($receipt->insurance)) {
            $pdf->SetFont('','B',11);
            $pdf->MultiCell(170, 17, $receipt->insurance->name, null, "L");
            $pdf->SetFont('','',10);
            $pdf->MultiCell(170, 15, $receipt->insurance->phonenumber, null, "L");
            $pdf->MultiCell(170, 35, $receipt->insurance->address, null, "L");

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
        $yPos = 190;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(170, 25, "Receipt Details: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos += 20;
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Receipt #: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $receipt->id, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Receipt Date: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M j, Y", strtotime($receipt->created_at)), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Invoice #: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $receipt->invoice, null, "R");
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

        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Total: ", null, "L");
        $pdf->SetFillColor(9, 113, 254);
        $yPos += 15;
        $pdf->Rect($xPos, $yPos, 170, 40, 'F');

        $yPos += 8;
        $xPos += 5;
        $pdf->SetXY($xPos , $yPos);
        $pdf->SetFont('','',20);
        $pdf->SetTextColor(254, 254, 254);
        $pdf->MultiCell(170, 40, money($receipt->amount, $user->parent->currency), null, "L");

        $yPos = 190;
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
        $yPos = 355;
        $pdf->SetXY($xPos, $yPos);

        $items = array();
        foreach ($invoiceitems as $key => $invoiceitem) {
            $items[] = '<tr>
                <td style="width:5%;">'.($key + 1).'</td>
                <td style="width:37%;">'.$invoiceitem->item.'</td>
                <td style="width:12%;">'.$invoiceitem->quantity.'</td>
                <td style="width:18%;">'.money($invoiceitem->cost, $user->parent->currency).'</td>
                <td style="width:10%;">'.$invoiceitem->tax.'%</td>
                <td style="width:18%;text-align:right;"><b>'.money($invoiceitem->total, $user->parent->currency).'</b></td>
            </tr>';
        }


        $details = '
        <table cellpadding="5" cellspacing="5" style="width:100%;background-color: #404448;color:#ffffff;">
            <tr>
                <td style="width:5%;"><b>#</b></td>
                <td style="width:37%;"><b>Item</b></td>
                <td style="width:12%;"><b>Quantity</b></td>
                <td style="width:18%;"><b>Unit Cost</b></td>
                <td style="width:10%;"><b>Tax</b></td>
                <td style="width:18%;text-align:right;"><b>Total</b></td>
            </tr>
        </table>

        <table cellpadding="5" cellspacing="5" style="width:100%;color:#526484;">
        '.implode(" ", $items).'
        </table>
        <table style="width:100%;height:30px;border-color:#404448;"><tr><td></td></tr></table>

        ';

        $pdf->writeHTML($details, true, false, true, false, '');

        $yPos = $pdf->GetY();

        if ($yPos > 690) {
            $pdf->AddPage();
            $yPos = $pdf->GetY();
        }

        $xPos = $pdf->GetX() + 350;
        $yPos = $pdf->GetY();
        $yPos -= 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(60, 20, "Subtotal ", null, "R");
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(193, 20, money($receipt->amount, $user->parent->currency), null, "R");

        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(60, 20, "Tax ", null, "R");
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(193, 20, money(0), null, "R");

        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFillColor(9, 113, 254);
        $pdf->Rect($xPos - 5, $yPos, 230, 30, 'F');

        $yPos = $pdf->GetY() + 7;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',13);
        $pdf->SetTextColor(254, 254, 254);
        $pdf->MultiCell(60, 20, "TOTAL", null, "R");
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(193, 20, money($receipt->amount, $user->parent->currency), null, "R");
        $totalsY = $pdf->GetY();
        $totalsPage = $pdf->getPage();


        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Receipt #".$receipt->id.".pdf", 'I');
        }

        die();

    }
    
}
class PDF extends TCPDF {

    // Page footer
    public function Footer() {

        $this->SetY(-50);

        $yPos = $this->getY() - 10;

        $this->Image(env("APP_URL")."/assets/images/footer.png", 0, $yPos, 600);

    }
}
