<?php
namespace Simcify\Controllers;

$tcpdfPath = str_replace("Controllers", "TCPDF/", dirname(__FILE__));

require_once $tcpdfPath.'tcpdf.php';
include("config/connect.php");

use Datamatrix;
use TCPDF;
use Simcify\Database;
use Simcify\Asilify;
use Simcify\Auth;
use Simcify\Mail;
use Simcify\File;
use Simcify\Controllers\Ledgerposting;
use PDO;

class Invoice {
    
    /**
     * Render invoice page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = 'Invoices';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $invoices = Database::table('invoices')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($invoices as $key => $invoice) {
            $invoice->items = Database::table('invoiceitems')->where('invoice', $invoice->id)->count("id", "total")[0]->total;
            $invoice->project = Database::table('projects')->where('id', $invoice->project)->first();
            $invoice->client = Database::table('clients')->where('id', $invoice->project->client)->first();
            $invoice->balance = $invoice->total - $invoice->amount_paid;
        }


        $clients = Database::table('clients')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($clients as $key => $client) {
            $client->projects = Database::table('projects')->where('company', $user->company)->where('client', $client->id)->orderBy("id", false)->get();
            if (empty($client->projects)) {
                unset($clients[$key]);
            }
        }
        
        return view("invoices", compact("user", "title", "invoices","clients"));
        
    }
    
    /**
     * Create an invoice
     * 
     * @return Json
     */
    public function create() {
        $user = Auth::user();
        $total = $tax = 0;

        if (empty($_POST["item"])) {
            return response()->json(responder("warning", "Hmmm!", "Add atleast one item."));
        }

        $project = Database::table('projects')->where('company', $user->company)->where('id', input('project'))->first();

        $data = array(
            "company" => $user->company,
            "project" => escape(input('project')),
            "client" => $project->client,
            "invoice_date" => escape(input('invoice_date')),
            "due_date" => escape(input('due_date')),
            "notes" => escape(input('notes')),
            "payment_details" => escape(input('payment_details'))
        );

        if (!empty($project->insurance)) {
            $data["insurance"] = $project->insurance;
            unset($data["client"]);
        }

        Database::table('invoices')->insert($data);
        $invoiceid = Database::table('invoices')->insertId();
        if (empty($invoiceid)) {
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }

        foreach ($_POST["item"] as $key => $item) {
            $line = array(
                "company" => $user->company,
                "project" => escape(input('project')),
                "invoice" => $invoiceid,
                "item" => $_POST["item"][$key],
                "quantity" => $_POST["quantity"][$key],
                "cost" => $_POST["cost"][$key],
                "tax" => Asilify::zero($_POST["tax"][$key])
            );

            $linetotal = $this->linetotal((object) $line);

            $line["total"] = $linetotal->total + $linetotal->tax;
            $linetax = $linetotal->tax;

            Database::table('invoiceitems')->insert($line);

            $total = $total + $linetotal->total;
            $tax = $tax + $linetax;

        }

        $update = array(
            "subtotal" => round($total, 2),
            "tax_amount" => round($tax, 2),
            "total" => round($total + $tax, 2)
        );

        if (empty($update["total"])) {
            $update["status"] = "Paid";
        }

        Database::table('invoices')->where('id', $invoiceid)->where('company', $user->company)->update($update);

        // Ledgerposting
        $vatAccount     = Database::table('clients')->where('fullname','Input VAT')->where('isDefault','1')->get();
        $salesAccount   = Database::table('clients')->where('fullname','Sales Account')->where('isDefault','1')->get();

        //Client Account
        Ledgerposting::save_ledger_posting($invoiceid, $project->client, 'cleint', 0, round($total, 2),'Invoice',escape(input('invoice_date')));

        //Input VAT account
        if($tax > 0)
        {
            Ledgerposting::save_ledger_posting($invoiceid, $vatAccount[0]->id, 'input_vat', 0, round($tax, 2),'Invoice',escape(input('invoice_date')));
        }

        //Sales Account
        Ledgerposting::save_ledger_posting($invoiceid, $salesAccount[0]->id, 'sales_account', round($total + $tax, 2), 0,'Invoice',escape(input('invoice_date')));
        // Ledgerposting
        
        return response()->json(responder("success", "Alright!", "Invoice successfully created.", "redirect('" . url('Invoice@view', array(
            'invoiceid' => $invoiceid
        )) . "')"));
        
    }
    
    
    /**
     * Calculate line total
     * 
     * @param array
     * @return int
     */
    private function linetotal($line) {

        $total = $line->quantity * $line->cost;

        if (!empty($line->tax)) {
            $tax = ($line->tax / 100) * $total;
        }else{
            $tax = 0;
        }

        return ( object ) array(
            "total" => round($total, 2),
            "tax" => round($tax, 2),
        );

    }
    
    
    /**
     * Create invoice form view
     * 
     * @return \Pecee\Http\Response
     */
    public function createform() {
        
        $user   = Auth::user();
        if (!empty(input("clientid"))) {
            $client = Database::table('clients')->where('company', $user->company)->where('id', input("clientid"))->first();
            $projects = Database::table('projects')->where('company', $user->company)->where('client', $client->id)->orderBy("id", false)->get();
        }else{
            $insurance = Database::table('insurance')->where('company', $user->company)->where('id', input("insuranceid"))->first();
            $projects = Database::table('projects')->where('company', $user->company)->where('insurance', $insurance->id)->orderBy("id", false)->get();
        }
        
        return view('modals/create-invoice', compact("projects","user"));
        
    }
    
    
    /**
     * Invoice update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', input("invoiceid"))->first();
        $invoiceitems = Database::table('invoiceitems')->where('company', $user->company)->where('invoice', $invoice->id)->get();
        
         $project = Database::table('projects')->where('company', $user->company)->where('id', $invoice->project)->first();
        
        return view('modals/update-invoice', compact("invoice","invoiceitems","user","project"));
        
    }
    
    /**
     * Update Invoice
     * 
     * @return Json
     */
    public function update() {

        $user = Auth::user();
        $total = $tax = 0;

        if (empty($_POST["item"])) {
            return response()->json(responder("warning", "Hmmm!", "Add atleast one item."));
        }

        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', input("invoiceid"))->first();
        $invoiceitems = Database::table('invoiceitems')->where('company', $user->company)->where('invoice', $invoice->id)->get("id");

        foreach ($_POST["item"] as $key => $item) {
            $line = array(
                "item" => $_POST["item"][$key],
                "quantity" => $_POST["quantity"][$key],
                "cost" => $_POST["cost"][$key],
                "tax" => Asilify::zero($_POST["tax"][$key])
            );

            $linetotal = $this->linetotal((object) $line);

            $line["total"] = $linetotal->total + $linetotal->tax;

            if (empty($_POST["itemid"][$key])) {
                $line["invoice"] = $invoice->id;
                $line["project"] = $invoice->project;
                $line["company"] = $invoice->company;
                Database::table('invoiceitems')->insert($line);
            }else{
                Database::table('invoiceitems')->where('id', $_POST["itemid"][$key])->where('invoice', $invoice->id)->update($line);
            }

            $linetax = $linetotal->tax;

            $total = $total + $linetotal->total;
            $tax = $tax + $linetax;

        }

        $data = array(
            "invoice_date" => escape(input('invoice_date')),
            "due_date" => escape(input('due_date')),
            "notes" => escape(input('notes')),
            "payment_details" => escape(input('payment_details')),
            "subtotal" => round($total, 2),
            "tax_amount" => round($tax, 2),
            "total" => round($total + $tax, 2)
        );

        Database::table('invoices')->where('id', input("invoiceid"))->where('company', $user->company)->update($data);

        foreach ($invoiceitems as $key => $invoiceitem) {
            if (!in_array($invoiceitem->id, $_POST["itemid"])) {
                Database::table('invoiceitems')->where('id', $invoiceitem->id)->where('company', $user->company)->delete();
            }
        }

        $this->invoicestatus($user, $invoice->id);
        Asilify::unsign($invoice->id, "invoice");

        // Ledgerposting

        //Delete Ledger Posting
        Ledgerposting::delete_ledger_posting($invoice->id,'Invoice');

        $vatAccount     = Database::table('clients')->where('fullname','Input VAT')->where('isDefault','1')->get();
        $salesAccount   = Database::table('clients')->where('fullname','Sales Account')->where('isDefault','1')->get();

        //Client Account
        Ledgerposting::save_ledger_posting($invoice->id, $invoice->client, 'cleint', 0, round($total, 2),'Invoice',escape(input('invoice_date')));

        //Input VAT account
        if($tax > 0)
        {
            Ledgerposting::save_ledger_posting($invoice->id, $vatAccount[0]->id, 'input_vat', 0, round($tax, 2),'Invoice',escape(input('invoice_date')));
        }

        //Sales Account
        Ledgerposting::save_ledger_posting($invoice->id, $salesAccount[0]->id, 'sales_account', round($total + $tax, 2), 0,'Invoice',escape(input('invoice_date')));
        // Ledgerposting
        
        return response()->json(responder("success", "Alright!", "Invoice successfully updated.", "redirect('" . url('invoice@view', array(
            'invoiceid' => $invoice->id
        )) . "')"));
        
    }
    
    
    /**
     * Update invoice status
     * 
     * @return Json
     */
    private function invoicestatus($user, $invoiceid) {

        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', $invoiceid)->first();

        if($invoice->amount_paid == 0.00 && $invoice->total > 0){
            $data["status"] = "Unpaid";
        }elseif($invoice->amount_paid == 0.00 && $invoice->total == 0.00){
            $data["status"] = "Paid";
        }elseif (abs(($invoice->total - $invoice->amount_paid)/$invoice->amount_paid) < 0.00001 || $invoice->amount_paid > $invoice->total) {
            $data["status"] = "Paid";
        }else{
            $data["status"] = "Partial";
        }

        Database::table('invoices')->where('id', $invoiceid)->where('company', $user->company)->update($data);

        return;

    }
    
    
    /**
     * Delete Invoice
     * 
     * @return Json
     */
    public function delete() {
        var_dump('Reahced');
        $user = Auth::user();

        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', input('invoiceid'))->first();
        Database::table('invoices')->where('id', input('invoiceid'))->where('company', $user->company)->delete();

        //Delete Ledger Posting
        Ledgerposting::delete_ledger_posting($invoice->id,'Invoice');

        return response()->json(responder("success", "Alright!", "Invoice successfully deleted.", "redirect('" . url('Projects@details', array(
            'projectid' => $invoice->project,
            'Isqt' => 'false'
        )) . "?view=invoices')"));
        
    }
    
    
    /**
     * Import invoice items from work requested
     * 
     * @return \Pecee\Http\Response
     */
    public function workrequested() {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();

        if (!empty($project->work_requested)) {
            $project->work_requested = json_decode($project->work_requested);
        }else{
            $project->work_requested = array();
        }
        
        return view('modals/import-invoice-workrequested', compact("project","user"));
        
    }
    
    
    /**
     * Import invoice items from jobcards
     * 
     * @return \Pecee\Http\Response
     */
    public function jobcards() {
        
        $items = array();
        $user   = Auth::user();
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
        
        return view('modals/import-invoice-jobcard', compact("items","user"));
        
    }
    
    protected $conn;
    /**
     * Import invoice items from expenses
     * 
     * @return \Pecee\Http\Response
     */
    public function expenses() {
        
        $user   = Auth::user();

        $expenses = Database::table('expenses')
                    ->where('company', $user->company)
                    ->where('project', input("projectid"))
                    ->orderBy("id", false)->get();  
                    
        $company = Database::table('companies')
                    ->where('id',1)->get();

        $tax = $company[0]->vat / 100;

        $instance = uniqid("instance-");
        
        return view('modals/import-invoice-expenses', compact("expenses","user","instance","company","tax"));
        
    }

    public function callconn(PDO $pdo) {
         $sql = 'SELECT * FROM expenses';

        $statement = $pdo->query($sql);

    }

    /**
     * View Invoice
     * 
     * @return \Pecee\Http\Response
     */
    public function view($invoiceid) {

        $user   = Auth::user();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', $invoiceid)->first();
        if (empty($invoice)) {
            return view('errors/404');
        }

        $title = "Invoice #".$invoice->id;

        if (!empty($invoice->client)) {
            $owner = Database::table('clients')->where('company', $user->company)->where('id', $invoice->client)->first();
        }else{
            $owner = Database::table('insurance')->where('company', $user->company)->where('id', $invoice->insurance)->first();
        }

        return view('view-invoice', compact("title", "user","invoice","owner"));
        
    }
    
    /**
     * Generate PDF
     * 
     * @return \Pecee\Http\Response
     */
    public function render($invoiceid) {
        
        $user   = Auth::user();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', $invoiceid)->first();
        $invoiceitems = Database::table('invoiceitems')->where('company', $user->company)->where('invoice', $invoiceid)->get();
        
        if (empty($invoice) || empty($invoiceitems)) {
            return view('errors/404');
        }
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', $invoice->project)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        $this->generate($user, $invoice, $invoiceitems, $project, $client);

        die();
        
    }
    
    /**
     * Sign invoice ( Acceptance )
     * 
     * @return \Pecee\Http\Response
     */
    public function sign() {
        
        $user   = Auth::user();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', input("invoiceid"))->first();

        $upload = File::upload(input("signature"), "invoicesignatures", array(
            "source" => "base64",
            "extension" => "png"
        ));
        
        if ($upload['status'] == "success") {
            $signature = array(
                "signed" => "Yes",
                "signature" => $upload['info']['name'],
                "signed_by" => input("fullname")
            );
            Database::table('invoices')->where('id', $invoice->id)->update($signature);
        }else{
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }

        return response()->json(responder("success", "Signed!", "Invoice successfully signed.", "reload()"));
        
    }
    
    /**
     * Send via email
     * 
     * @return Json
     */
    public function send() {
        
        $user   = Auth::user();
        $invoice = Database::table('invoices')->where('company', $user->company)->where('id', input("itemid"))->first();
        $invoiceitems = Database::table('invoiceitems')->where('company', $user->company)->where('invoice', input("itemid"))->get();
        
        if (empty($invoice) || empty($invoiceitems)) {
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', $invoice->project)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        $document = $this->generate($user, $invoice, $invoiceitems, $project, $client, true);

        $send = Mail::send(
            input("email"),
            input("subject"),
            array(
                "message" => nl2br(input("message"))
            ),
            "basic",
            null,
            array("Invoice #".$invoice->id.".pdf" => config("app.storage")."tmp/".$document)
        );

        File::delete($document, "tmp");

        if ($send) {
            return response()->json(responder("success", "Alright!", "Email successfully sent.", "reload()"));
        }else{
            return response()->json(responder("error", "Hmmm!", "Email could not be sent, please try again."));
        }

        die();
        
    }

    /**
     * Generate PDF Invoice
     * 
     * @return Json
     */
    public function generate($user, $invoice, $invoiceitems, $project, $client, $save = false) {



        $outputName = uniqid("asilify_").".pdf";
        $outputPath = config("app.storage")."/tmp/". $outputName;

        $pdf = new PDF(null, 'px');
        // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'px', PDF_PAGE_FORMAT, true, 'ISO-8859-1', false);
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(TRUE, 50);
        $pdf->AddPage();
        $pdf->company = $user->parent;

        $xPos = $pdf->GetX();
        $yPos = 30;


        // Document Title
        $pdf->Image("http://localhost:8080/assets/images/headerpattern.png", 0, $yPos + 1, 600);

        $pdf->SetFillColor(64, 68, 72);
        // $pdf->Rect(0, 30, 20, 55, 'F');

        $yPos +=10;
        $pdf->SetTextColor(64, 68, 72);
        $pdf->SetFont('','',12);
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(500, 100, "INVOICE", null, "C");
        




        // client info
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, 170);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Invoice To: ", null, "L");

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

        $xPos += 190;
        $yPos = 170;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(170, 25, "Invoice Details: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos += 20;
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Invoice #: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $invoice->id, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Invoice Date: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M j, Y", strtotime($invoice->invoice_date)), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Invoice Due: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M j, Y", strtotime($invoice->due_date)), null, "R");
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

        if (!empty($invoice->amount_paid) && $invoice->amount_paid > 0) {
            $yPos += 15;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','B',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->MultiCell(150, 70, "Amount Paid: ", null, "L");
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(128, 148, 174);
            $pdf->MultiCell(170, 70, money($invoice->amount_paid, $user->parent->currency), null, "R");
        }

        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Amount Due: ", null, "L");
        $pdf->SetFillColor(9, 113, 254);
        $yPos += 15;
        $pdf->Rect($xPos, $yPos, 170, 40, 'F');

        $yPos += 8;
        $xPos += 5;
        $pdf->SetXY($xPos , $yPos);
        $pdf->SetFont('','',20);
        $pdf->SetTextColor(254, 254, 254);
        $pdf->MultiCell(170, 40, money(($invoice->total - $invoice->amount_paid), $user->parent->currency), null, "L");


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


        $yPos = 170;
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
        if (!empty($invoice->amount_paid) && $invoice->amount_paid > 0) {
            $yPos = 345; // 300
        }else{
            $yPos = 335;//290
        }
        $pdf->SetXY($xPos, $yPos);
        
        $items = array();
        foreach ($invoiceitems as $key => $invoiceitem) {
            $items[] = '<tr style="border-bottom:1px;border-color:#ddd;">
                <td style="width:5%;">'.($key + 1).'</td>
                <td style="width:37%;">'.$invoiceitem->item.'</td>
                <td style="width:12%;">'.$invoiceitem->quantity.'</td>
                <td style="width:18%;">'.money($invoiceitem->cost, $user->parent->currency).'</td>
                <td style="width:10%;">'.$invoiceitem->tax.'%</td>
                <td style="width:18%;text-align:right;padding-right:0;"><b>'.money($invoiceitem->total, $user->parent->currency).'</b></td>
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
        $pdf->MultiCell(193, 20, money($invoice->subtotal, $user->parent->currency), null, "R");

        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(60, 20, "Tax ", null, "R");
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(193, 20, money($invoice->tax_amount, $user->parent->currency), null, "R");

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
        $pdf->MultiCell(193, 20, money($invoice->total, $user->parent->currency), null, "R");
        $totalsY = $pdf->GetY();
        $totalsPage = $pdf->getPage();


        // Payment details and notes
        if(!empty($invoice->payment_details)){
            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY();
            if ($yPos > 87 || $yPos < 790) {
                $yPos = $pdf->GetY() - 67;
            }

            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(250, 25, "Payment Details: ", null, "L");
            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);

            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 250, $yPos);

            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->writeHTML(nl2br($invoice->payment_details), true, false, true, false, '');
        }

        // notes
        if(!empty($invoice->notes)){

            $xPos = $pdf->GetX();
            if(!empty($invoice->payment_details)){
                $yPos = $pdf->GetY() + 30;
            }else{
                if ($yPos > 87 || $yPos < 790) {
                    $yPos = $pdf->GetY() - 67;
                }
            }

            // if (($yPos + 50) > 790) {
            //     $pdf->AddPage();
            //     $yPos = $pdf->GetY();
            // }

            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(250, 25, "Notes: ", null, "L");
            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);

            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 250, $yPos);

            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->writeHTML(nl2br($invoice->notes), true, false, true, false, '');

        }

        // Customer acceptance signature
        if($user->parent->invoice_signing == "Enabled" && !empty($invoice->signature)){
            $xPos = $pdf->GetX();

            if(!empty($invoice->payment_details) || !empty($invoice->notes)){
                $yPos = $pdf->GetY() + 30;
            }else{
                if ($yPos > 87 || $yPos < 790) {
                    $yPos = $pdf->GetY() - 67;
                }
            }

            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(250, 25, "Customer Signature: ", null, "L");
            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);

            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 250, $yPos);

            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);

            $pdf->Image(env("APP_URL")."/uploads/invoicesignatures/".$invoice->signature, $xPos, $yPos, 80);
            $pdf->SetXY($xPos, $yPos + 50);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->MultiCell(130, 20, $invoice->signed_by, null, "L");
        }

        // Disclaimer
        if(!empty($user->parent->invoice_disclaimer)){
            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 15;

            if ($yPos < $totalsY && $pdf->getPage() == $totalsPage) {
                $yPos = $totalsY + 15;
            }

            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(250, 25, "Disclaimer: ", null, "L");
            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);

            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 540, $yPos);

            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->writeHTML(nl2br($user->parent->invoice_disclaimer), true, false, true, false, '');
        }

        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Invoice #".$invoice->id.".pdf", 'I');
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
