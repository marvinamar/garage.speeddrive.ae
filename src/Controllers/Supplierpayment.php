<?php
namespace Simcify\Controllers;

$tcpdfPath = str_replace("Controllers", "TCPDF/", dirname(__FILE__));

require_once $tcpdfPath.'tcpdf.php';

use TCPDF;
use Simcify\Database;
use Simcify\Auth;

class Supplierpayment{
    
    public function get() {
        
        $title = 'Payments';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $payments = Database::table('s_payments')->where('company', $user->company)->orderBy("id", false)->get();
        
        foreach ($payments as $key => $payment) {
            $payment->expense = Database::table('expenses')->where('id', $payment->expense)->first();
            $payment->supplier = Database::table('suppliers')->where('id', $payment->supplier)->first();
        }

        $pay_expenses = Database::table('expenses')->get();
        

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
        
        return view("project-suplier-payments", compact("user", "title", "payments","clients","pay_expenses"));
        
    }

    public function create() {
        
        $user = Auth::user();

        $expense = Database::table('expenses')->where('company', $user->company)->where('id', input("s_expense"))->first();

        $data = array(
            "company" => $user->company,
            "project" => $expense->project,
            "supplier" => $expense->supplier,
            "expense" => $expense->id,
            "method" => escape(input('method')),
            "amount" => escape(input('amount')),
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date'))
        );
        
        Database::table('s_payments')->insert($data);
        $s_paymentsId = Database::table('s_payments')->insertId();

        //Update Paid to Yes
        $data["paid"] = "Yes";
        Database::table('expenses')->where('company', $user->company)->where('id', $expense->id)->update($data);

        // Ledgerposting
        $purchaseAccount   = Database::table('suppliers')->where('name','Purchase Account')->where('isDefault','1')->get();

        //Client ID
        Ledgerposting::save_ledger_posting($s_paymentsId, $expense->supplier, 'supplier', $expense->amount, 0, 'Payment',escape(input('payment_date')));

        //Sales Account
        Ledgerposting::save_ledger_posting($s_paymentsId, $purchaseAccount[0]->id, 'purchase_account',  0, $expense->amount,'Payment',escape(input('payment_date')));
        // Ledgerposting
        
        return response()->json(responder("success", "Alright!", "Payment successfully added.", "reload('" . url('Projects@details', array(
            'projectid' => $expense->project,
            'Isqt' => 'false'
        )) . "?view=s_payments')"));
        
    }

    public function delete() {
        
        $user = Auth::user();
        $payment = Database::table('s_payments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        
        $data["paid"] = "No";
        Database::table('expenses')->where('company', $user->company)->where('id', $payment->expense)->update($data);
        Database::table('s_payments')->where('id', input('paymentid'))->where('company', $user->company)->delete();
        
        //Delete Ledger Posting
        Ledgerposting::delete_ledger_posting($payment->id,'Payment');

        return response()->json(responder("success", "Alright!", "Payment successfully deleted.", "reload()"));
        
    }

    public function updateview() {
        
        $user   = Auth::user();
        $payment = Database::table('s_payments')->where('company', $user->company)->where('id', input("paymentid"))->first();
        
        return view('modals/updated-s-payment', compact("payment","user"));
        
    }

    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "method" => escape(input('method')),
            "amount" => escape(input('amount')),
            "note" => escape(input('note')),
            "payment_date" => escape(input('payment_date'))
        );

        Database::table('s_payments')->where('id', input('paymentid'))->where('company', $user->company)->update($data);

        $payment = Database::table('s_payments')->where('company', $user->company)->where('id', input("paymentid"))->first();

        //Delete Ledger Posting
        Ledgerposting::delete_ledger_posting(input('paymentid'),'Expense');

        // Ledgerposting
        $purchaseAccount   = Database::table('suppliers')->where('name','Purchase Account')->where('isDefault','1')->get();

        //Client ID
        Ledgerposting::save_ledger_posting($payment->id, $payment->supplier, 'supplier', $payment->amount, 0, 'Payment',escape(input('payment_date')));

        //Sales Account
        Ledgerposting::save_ledger_posting($payment->id, $purchaseAccount[0]->id, 'purchase_account',  0, $payment->amount,'Payment',escape(input('payment_date')));
        // Ledgerposting

        return response()->json(responder("success", "Alright!", "Payment successfully updated.", "reload()"));
        
    }
    /**
     * View Receipt
     *
     * @return \Pecee\Http\Response
     */
    public function view($paymentid) 
    {

        $user   = Auth::user();
        $payment = Database::table('s_payments')->where('company', $user->company)->where('id', $paymentid)->first();
        if (empty($payment)) {
            return view('errors/404');
        }

        $title = "Payment #".$payment->id;

        if (!empty($payment->client)) {
            $owner = Database::table('suppliers')->where('company', $user->company)->where('id', $payment->supplier)->first();
        }else{
            $owner = Database::table('insurance')->where('company', $user->company)->where('id', $payment->insurance)->first();
        }

        return view('view-payment', compact("title", "user","payment","owner"));
    }

    public function render($paymentid) {
        include("config/connect.php");
        $user   = Auth::user();
        $payment = Database::table('s_payments')->where('company', $user->company)->where('id', $paymentid)->first();
        $project = Database::table('projects')->where('company', $user->company)->where('id', $payment->project)->first();
        $supplier = Database::table('suppliers')->where('company', $user->company)->where('id', $payment->supplier)->first();
        $expense = Database::table('expenses')->where('company', $user->company)->where('id', $payment->expense)->get();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }
        $this->generate($user, $payment, $project, $supplier, $expense);

        die();

    }

    public function generate($user,$payment, $project, $supplier, $expenses, $save = false) {

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
        $pdf->MultiCell(500, 100, "Payment", null, "C");


        // client info
        $xPos = $pdf->GetX();
        $yPos = 190;//$pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Payment To: ", null, "L");

        $yPos += 20;
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        if (!empty($payment->insurance)) {
            $pdf->SetFont('','B',11);
            $pdf->MultiCell(170, 17, $payment->insurance->name, null, "L");
            $pdf->SetFont('','',10);
            $pdf->MultiCell(170, 15, $payment->insurance->phonenumber, null, "L");
            $pdf->MultiCell(170, 35, $payment->insurance->address, null, "L");

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
        $pdf->MultiCell(170, 17, $supplier->name, null, "L");
        $pdf->SetFont('','',10);
        $pdf->MultiCell(170, 15, $supplier->phonenumber, null, "L");
        $pdf->MultiCell(170, 35, $supplier->address, null, "L");


        // Document details
        $xPos += 190;
        $yPos = 190;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(170, 25, "Payment Details: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos += 20;
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Payment No #: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $payment->id, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Payment Date: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M j, Y", strtotime($payment->created_at)), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Expense #: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $payment->expense, null, "R");
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
        $pdf->MultiCell(170, 40, money($payment->amount, $user->parent->currency), null, "L");

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
        foreach ($expenses as $key => $expense) {
            $items[] = '<tr>
                <td style="width:5%;">'.($key + 1).'</td>
                <td style="width:47%;">'.$expense->expense.'</td>
                <td style="width:12%;">'.$expense->quantity.'</td>
                <td style="width:18%;">'.money($expense->amount, $user->parent->currency).'</td>
                <td style="width:18%;text-align:right;"><b>'.money($expense->amount, $user->parent->currency).'</b></td>
            </tr>';
        }


        $details = '
        <table cellpadding="5" cellspacing="5" style="width:100%;background-color: #404448;color:#ffffff;">
            <tr>
                <td style="width:5%;"><b>#</b></td>
                <td style="width:47%;"><b>Item</b></td>
                <td style="width:12%;"><b>Quantity</b></td>
                <td style="width:18%;"><b>Unit Cost</b></td>
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
        $pdf->MultiCell(193, 20, money($payment->amount, $user->parent->currency), null, "R");

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
        $pdf->MultiCell(193, 20, money($payment->amount, $user->parent->currency), null, "R");
        $totalsY = $pdf->GetY();
        $totalsPage = $pdf->getPage();


        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Payment #".$payment->id.".pdf", 'I');
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

?>