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

class Quote {
    
    /**
     * Render quotes page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = 'Quotes';
        $user  = Auth::user();
        
        if ($user->role == "Staff" || $user->role == "Inventory Manager" || $user->role == "Booking Manager") {
            return view('errors/404');
        }
        
        $quotes = Database::table('quotes')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($quotes as $key => $quote) {
            $quote->items = Database::table('quoteitems')->where('quote', $quote->id)->count("id", "total")[0]->total;
            $quote->project = Database::table('projects')->where('id', $quote->project)->first();
            $quote->client = Database::table('clients')->where('id', $quote->project->client)->first();
        }


        $clients = Database::table('clients')->where('company', $user->company)->orderBy("id", false)->get();
        foreach ($clients as $key => $client) {
            $client->projects = Database::table('projects')->where('company', $user->company)->where('client', $client->id)->orderBy("id", false)->get();
            if (empty($client->projects)) {
                unset($clients[$key]);
            }
        }

        $inventorys = Database::table('inventory')->where('company', $user->company)->get();
        
        return view("quotes", compact("user", "title", "quotes","clients","inventorys"));
        
    }
    
    /**
     * Create a quote
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        $total = $tax = 0;

        $project = Database::table('projects')->where('company', $user->company)->where('id', input('project'))->first();

        $data = array(
            "company" => $user->company,
            "project" => escape(input('project')),
            "client" => $project->client,
            "notes" => escape(input('notes'))
        );

        if (!empty($project->insurance)) {
            $data["insurance"] = $project->insurance;
            unset($data["client"]);
        }

        Database::table('quotes')->insert($data);
        $quoteid = Database::table('quotes')->insertId();
        if (empty($quoteid)) {
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }

        foreach ($_POST["item"] as $key => $item) {
            $line = array(
                "company" => $user->company,
                "project" => escape(input('project')),
                "quote" => $quoteid,
                "item" => $_POST["item"][$key],
                "workType" => $_POST["workType"][$key],
                "quantity" => $_POST["quantity"][$key],
                "cost" => $_POST["cost"][$key],
                "tax" => Asilify::zero($_POST["tax"][$key])
            );



            $linetotal = $this->linetotal((object) $line);

            $line["total"] = $linetotal->total + $linetotal->tax;
            $linetax = $linetotal->tax;

            Database::table('quoteitems')->insert($line);

            $total = $total + $linetotal->total;
            $tax = $tax + $linetax;

        }

        $update = array(
            "subtotal" => round($total, 2),
            "tax_amount" => round($tax, 2),
            "total" => round($total + $tax, 2)
        );

        Database::table('quotes')->where('id', $quoteid)->where('company', $user->company)->update($update);
        
        return response()->json(responder("success", "Alright!", "Quote successfully created.", "redirect('" . url('Quote@get') . "')"));

        // return response()->json(responder("success", "Alright!", "Quote successfully created.", "redirect('" . url('Quote@view', array(
        //     'quoteid' => $quoteid
        // )) . "')"));
        
    }

    public function create_at_project(){
        $user = Auth::user();
        $total = $tax = 0;

        $project = Database::table('projects')->where('company', $user->company)->where('id', input('project'))->first();

        $data = array(
            "company" => $user->company,
            "project" => escape(input('project')),
            "client" => $project->client,
            "notes" => escape(input('notes'))
        );

        if (!empty($project->insurance)) {
            $data["insurance"] = $project->insurance;
            unset($data["client"]);
        }

        Database::table('quotes')->insert($data);
        $quoteid = Database::table('quotes')->insertId();
        if (empty($quoteid)) {
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }

        foreach ($_POST["item"] as $key => $item) {
            $line = array(
                "company" => $user->company,
                "project" => escape(input('project')),
                "quote" => $quoteid,
                "item" => $_POST["item"][$key],
                "workType" => $_POST["workType"][$key],
                "quantity" => $_POST["quantity"][$key],
                "cost" => $_POST["cost"][$key],
                "tax" => Asilify::zero($_POST["tax"][$key])
            );



            $linetotal = $this->linetotal((object) $line);

            $line["total"] = $linetotal->total + $linetotal->tax;
            $linetax = $linetotal->tax;

            Database::table('quoteitems')->insert($line);

            $total = $total + $linetotal->total;
            $tax = $tax + $linetax;

        }

        $update = array(
            "subtotal" => round($total, 2),
            "tax_amount" => round($tax, 2),
            "total" => round($total + $tax, 2)
        );

        Database::table('quotes')->where('id', $quoteid)->where('company', $user->company)->update($update);

        $quote = Database::table('quotes')->where('company', $user->company)->where('id', $quoteid)->first();

        return response()->json(responder("success", "Alright!", "Quote successfully deleted.", "redirect('" . url('Projects@details', array(
            'projectid' => $quote->project,
            'Isqt' => 'false'
        )) . "?view=quotes')"));
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
     * Create quote form view
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
        
        return view('modals/create-quote', compact("projects","user"));
        
    }
    
    
    /**
     * Quote update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input("quoteid"))->first();
        $quoteitems = Database::table('quoteitems')->where('company', $user->company)->where('quote', $quote->id)->get();
        
        return view('modals/update-quote', compact("quote","quoteitems","user"));
        
    }

    public function updateviewv2() {
        
        $user   = Auth::user();
        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input("quoteid"))->first();
        $quoteitems = Database::table('quoteitems')->where('company', $user->company)->where('quote', $quote->id)->get();
        $inventorys = Database::table('inventory')->where('company', $user->company)->get();
        
        return view('modals/update-quote-v2', compact("quote","quoteitems","user","inventorys"));
        
    }
    
    /**
     * Update Quote
     * 
     * @return Json
     */
    public function update() {

        $user = Auth::user();
        $total = $tax = 0;

        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input("quoteid"))->first();
        $quoteitems = Database::table('quoteitems')->where('company', $user->company)->where('quote', $quote->id)->get("id");

        foreach ($_POST["item"] as $key => $item) {
            $line = array(
                "item" => $_POST["item"][$key],
                "workType" => $_POST["workType"][$key],
                "quantity" => $_POST["quantity"][$key],
                "cost" => $_POST["cost"][$key],
                "tax" => Asilify::zero($_POST["tax"][$key])
            );

            $linetotal = $this->linetotal((object) $line);
            $line["total"] = $linetotal->total + $linetotal->tax;

            if (empty($_POST["itemid"][$key])) {
                $line["quote"] = $quote->id;
                $line["project"] = $quote->project;
                $line["company"] = $quote->company;
                Database::table('quoteitems')->insert($line);
            }else{
                Database::table('quoteitems')->where('id', $_POST["itemid"][$key])->where('quote', $quote->id)->update($line);
            }

            $linetax = $linetotal->tax;

            $total = $total + $linetotal->total;
            $tax = $tax + $linetax;

        }

        $data = array(
            "notes" => escape(input("notes")),
            "subtotal" => round($total, 2),
            "tax_amount" => round($tax, 2),
            "total" => round($total + $tax, 2)
        );

        Database::table('quotes')->where('id', input("quoteid"))->where('company', $user->company)->update($data);

        foreach ($quoteitems as $key => $quoteitem) {
            if (!in_array($quoteitem->id, $_POST["itemid"])) {
                Database::table('quoteitems')->where('id', $quoteitem->id)->where('company', $user->company)->delete();
            }
        }

        Asilify::unsign($quote->id, "quote");
        
        return response()->json(responder("success", "Alright!", "Quote successfully created.", "redirect('" . url('Quote@get') . "')"));
        
    }

    public function update_at_project() {

        $user = Auth::user();
        $total = $tax = 0;

        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input("quoteid"))->first();
        $quoteitems = Database::table('quoteitems')->where('company', $user->company)->where('quote', $quote->id)->get("id");

        foreach ($_POST["item"] as $key => $item) {
            $line = array(
                "item" => $_POST["item"][$key],
                "workType" => $_POST["workType"][$key],
                "quantity" => $_POST["quantity"][$key],
                "cost" => $_POST["cost"][$key],
                "tax" => Asilify::zero($_POST["tax"][$key])
            );

            $linetotal = $this->linetotal((object) $line);
            $line["total"] = $linetotal->total + $linetotal->tax;

            if (empty($_POST["itemid"][$key])) {
                $line["quote"] = $quote->id;
                $line["project"] = $quote->project;
                $line["company"] = $quote->company;
                Database::table('quoteitems')->insert($line);
            }else{
                Database::table('quoteitems')->where('id', $_POST["itemid"][$key])->where('quote', $quote->id)->update($line);
            }

            $linetax = $linetotal->tax;

            $total = $total + $linetotal->total;
            $tax = $tax + $linetax;

        }

        $data = array(
            "notes" => escape(input("notes")),
            "subtotal" => round($total, 2),
            "tax_amount" => round($tax, 2),
            "total" => round($total + $tax, 2)
        );

        Database::table('quotes')->where('id', input("quoteid"))->where('company', $user->company)->update($data);

        foreach ($quoteitems as $key => $quoteitem) {
            if (!in_array($quoteitem->id, $_POST["itemid"])) {
                Database::table('quoteitems')->where('id', $quoteitem->id)->where('company', $user->company)->delete();
            }
        }

        Asilify::unsign($quote->id, "quote");
        
        return response()->json(responder("success", "Alright!", "Quote successfully deleted.", "redirect('" . url('Projects@details', array(
            'projectid' => $quote->project,
            'Isqt' => 'false'
        )) . "?view=quotes')"));
        
    }
    
    /**
     * Convert Quote
     * 
     * @return Json
     */
    public function convert() {

        $user = Auth::user();
        $total = 0;

        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input("quote"))->first();

        $data = array(
            "company" => $quote->company,
            "project" => $quote->project,
            "subtotal" => $quote->subtotal,
            "tax" => $quote->tax,
            "tax_amount" => $quote->tax_amount,
            "total" => $quote->total,
            "invoice_date" => escape(input('invoice_date')),
            "due_date" => escape(input('due_date')),
            "notes" => escape(input('notes')),
            "quote" => escape(input('quote')),
            "payment_details" => escape(input('payment_details'))
        );

        if (!empty($quote->client)) {
            $data["client"] = $quote->client;
        }

        if (!empty($quote->insurance)) {
            $data["insurance"] = $quote->insurance;
        }

        Database::table('invoices')->insert($data);
        $invoiceid = Database::table('invoices')->insertId();

        $quoteitems = Database::table('quoteitems')->where('company', $user->company)->where('quote', $quote->id)->get();
        foreach ($quoteitems as $key => $quoteitem) {

            $line = array(
                "company" => $quoteitem->company,
                "project" => $quoteitem->project,
                "invoice" => $invoiceid,
                "item" => $quoteitem->item,
                "quantity" => $quoteitem->quantity,
                "cost" => $quoteitem->cost,
                "total" => $quoteitem->total,
                "tax" => $quoteitem->tax
            );

            Database::table('invoiceitems')->insert($line);

        }
        
        return response()->json(responder("success", "Alright!", "Quote successfully converted to invoice.", "redirect('" . url('invoice@view', array(
            'invoiceid' => $invoiceid
        )) . "')"));
        
    }
    
    
    /**
     * Delete Quote
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();

        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input('quoteid'))->first();
        Database::table('quotes')->where('id', input('quoteid'))->where('company', $user->company)->delete();

        return response()->json(responder("success", "Alright!", "Quote successfully created.", "redirect('" . url('Quote@get') . "')"));
        
    }

    public function delete_at_project() {
        
        $user = Auth::user();

        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input('quoteid'))->first();
        Database::table('quotes')->where('id', input('quoteid'))->where('company', $user->company)->delete();

        return response()->json(responder("success", "Alright!", "Quote successfully deleted.", "redirect('" . url('Projects@details', array(
            'projectid' => $quote->project,
            'Isqt' => 'false'
        )) . "?view=quotes')"));
        
    }

    /**
     * View Quote
     * 
     * @return \Pecee\Http\Response
     */
    public function view($quoteid) {

        $user   = Auth::user();
        $quote = Database::table('quotes')->where('company', $user->company)->where('id', $quoteid)->first();
        if (empty($quote)) {
            return view('errors/404');
        }

        $title = "Quote #".$quote->id;

        if (!empty($quote->client)) {
            $owner = Database::table('clients')->where('company', $user->company)->where('id', $quote->client)->first();
        }else{
            $owner = Database::table('insurance')->where('company', $user->company)->where('id', $quote->insurance)->first();
        }

        return view('view-quote', compact("title", "user","quote","owner"));
        
    }
    
    /**
     * Generate PDF
     * 
     * @return \Pecee\Http\Response
     */
    public function render($quoteid) {
        
        $user   = Auth::user();
        $quote = Database::table('quotes')->where('company', $user->company)->where('id', $quoteid)->first();
        $quoteitems = Database::table('quoteitems')->where('company', $user->company)->where('quote', $quoteid)->get();
        
        if (empty($quote) || empty($quoteitems)) {
            return view('errors/404');
        }
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', $quote->project)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        $this->generate($user, $quote, $quoteitems, $project, $client);

        die();
        
    }
    
    /**
     * Sign quote ( Acceptance )
     * 
     * @return \Pecee\Http\Response
     */
    public function sign() {
        
        $user   = Auth::user();
        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input("quoteid"))->first();

        $upload = File::upload(input("signature"), "quotesignatures", array(
            "source" => "base64",
            "extension" => "png"
        ));
        
        if ($upload['status'] == "success") {
            $signature = array(
                "signed" => "Yes",
                "signature" => $upload['info']['name'],
                "signed_by" => input("fullname")
            );
            Database::table('quotes')->where('id', $quote->id)->update($signature);
        }else{
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }

        return response()->json(responder("success", "Signed!", "Quote successfully signed.", "reload()"));
        
    }

    public function get_item_details($item_id)
    {
        $item = Database::table('inventory')->where('id',$item_id)->get();
        return json_encode($item);
    }
    
    /**
     * Send via email
     * 
     * @return Json
     */
    public function send() {
        
        $user   = Auth::user();
        $quote = Database::table('quotes')->where('company', $user->company)->where('id', input("itemid"))->first();
        $quoteitems = Database::table('quoteitems')->where('company', $user->company)->where('quote', input("itemid"))->get();
        
        if (empty($quote) || empty($quoteitems)) {
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', $quote->project)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();

        $document = $this->generate($user, $quote, $quoteitems, $project, $client, true);
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        $send = Mail::send(
            input("email"),
            input("subject"),
            array(
                "message" => nl2br(input("message"))
            ),
            "basic",
            null,
            array("Quote #".$quote->id.".pdf" => config("app.storage")."tmp/".$document)
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
     * Generate PDF Quote
     * 
     * @return Json
     */
    public function generate($user, $quote, $quoteitems, $project, $client, $save = false) {

        $outputName = uniqid("asilify_").".pdf";
        $outputPath = config("app.storage")."/tmp/". $outputName;

        $pdf = new PDF(null, 'px');
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(TRUE, 50);
        $pdf->AddPage();
        $pdf->company = $user->parent;


        $xPos = $pdf->GetX();
        $yPos = 30;


        // Document Title
        $pdf->Image(env("APP_URL")."/assets/images/headerpattern.png", 0, $yPos + 1, 320);

        $pdf->SetFillColor(64, 68, 72);
        $pdf->Rect(0, 30, 200, 55, 'F');

        $yPos +=6;
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('','',34);
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(200, 70, "QUOTE", null, "L");


        // client info
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Quote To: ", null, "L");

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
        $pdf->MultiCell(170, 25, "Quote Details: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos += 20;
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Quote #: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $quote->id, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Quote Date: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M j, Y", strtotime($quote->created_at)), null, "R");
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
        $pdf->MultiCell(170, 40, money($quote->total, $user->parent->currency), null, "L");


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
        $yPos = 290;
        $pdf->SetXY($xPos, $yPos);


        $items = array();
        foreach ($quoteitems as $key => $quoteitem) {
            $items[] = '<tr>
                <td style="width:5%;">'.($key + 1).'</td>
                <td style="width:37%;">'.$quoteitem->item.'</td>
                <td style="width:12%;">'.$quoteitem->quantity.'</td>
                <td style="width:18%;">'.money($quoteitem->cost, $user->parent->currency).'</td>
                <td style="width:10%;">'.$quoteitem->tax.'%</td>
                <td style="width:18%;text-align:right;"><b>'.money($quoteitem->total, $user->parent->currency).'</b></td>
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
        $pdf->MultiCell(193, 20, money($quote->subtotal, $user->parent->currency), null, "R");

        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(60, 20, "Tax ", null, "R");
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(193, 20, money($quote->tax_amount, $user->parent->currency), null, "R");

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
        $pdf->MultiCell(193, 20, money($quote->total, $user->parent->currency), null, "R");
        $totalsY = $pdf->GetY();
        $totalsPage = $pdf->getPage();


        // notes
        if(!empty($quote->notes)){

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY();

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
            $pdf->writeHTML(nl2br($quote->notes), true, false, true, false, '');

        }

        // Customer acceptance signature
        if($user->parent->quote_signing == "Enabled" && !empty($quote->signature)){
            $xPos = $pdf->GetX();

            if(!empty($quote->notes)){
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

            $pdf->Image(env("APP_URL")."/uploads/quotesignatures/".$quote->signature, $xPos, $yPos, 80);
            $pdf->SetXY($xPos, $yPos + 50);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->MultiCell(130, 20, $quote->signed_by, null, "L");
        }

        // Disclaimer
        if(!empty($user->parent->quote_disclaimer)){
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
            $pdf->writeHTML(nl2br($user->parent->quote_disclaimer), true, false, true, false, '');
        }

        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Quote #".$quote->id.".pdf", 'I');
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
