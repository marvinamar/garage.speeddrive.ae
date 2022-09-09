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
use Simcify\Sms;

class Booking {
    
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

        $title = "Vehicle Booking: ".carmake($project->make)." ".carmodel($project->model);

        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();

        return view('view-booking', compact("title", "user","project","client"));
        
    }
    
    /**
     * Render PDF
     * 
     * @return application/pdf
     */
    public function print($project_key) {
        
        $project = Database::table('projects')->where('project_key', $project_key)->first();
        
        if (empty($project)) {
            return view('errors/404');
        }

        if (empty($project->booking_form)) {

            $company = Database::table('companies')->where('id', $project->company)->first();
            $client = Database::table('clients')->where('id', $project->client)->first();
            if(!empty($project->insurance)){
                $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
            }

            $partslist = Database::table('partslist')->where('company', $project->company)->orderBy("indexing", true)->get();
            foreach ($partslist as $key => $part) {

                $checkedin = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "In")->first();
                if (!empty($checkedin)) {
                    $part->checkedin = "checked";
                    $part->checkedin_input = $checkedin->input_field;
                }else{
                    $part->checkedin_input = $part->checkedin = NULL;
                }

                $checkedout = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "Out")->first();
                if (!empty($checkedout)) {
                    $part->checkedout = "checked";
                    $part->checkedout_input = $checkedout->input_field;
                }else{
                    $part->checkedout_input = $part->checkedout = NULL;
                }

            }

            $this->generate($company, $project, $client, $partslist);

            die();

        }else{

            $file = config("app.storage")."booking/".$project->booking_form;

            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="Vehicle Booking: ' . carmake($project->make) . ' ' . carmodel($project->model) . '.pdf"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($file));
            header('Accept-Ranges: bytes');

            @readfile($file);

            die();

        }
        
        die();
        
    }
    
    /**
     * Vehicle check out processing
     * 
     * @return Json
     */
    public function checkout() {
        
        $user = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();
        
        $data = array(
            "booking_out_notes" => escape(input('booking_out_notes')),
            "exit_fuel_level" => escape(input('fuel_level')),
            "road_test_out" => escape(input('road_test_out')),
            "milleage_out" => escape(input('milleage_out')),
            "date_out" => escape(input('date_out')),
            "time_out" => escape(input('time_out')),
            "old_parts_collected" => escape(input('old_parts_collected')),
            "booking_form" => "NULL",
            "checkedout" => "Yes"
        );

        if (!empty($_POST["partslist"])) {
            foreach ($_POST["partslist"] as $key => $part) {
                $partchecked = array(
                    "company" => $user->company,
                    "project" => $project->id,
                    "part" => $part,
                    "booking" => "Out"
                );
                if (!empty(input("partfield".$part))) {
                    $partchecked["input_field"] = escape(input("partfield".$part));
                    $partchecked["has_input"] = "Yes";
                }
                Database::table('partschecked')->insert($partchecked);
            }
            
        }
        
        Database::table('projects')->where('id', $project->id)->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Checked Out!", "Vehicle successfully checked out, proceed to checkout signature.", "redirect('".url('Booking@view', array('projectid' => $project->id))."', true)"));
        
    }
    
    /**
     * Generate PDF
     * 
     * @return \Pecee\Http\Response
     */
    public function render($projectid) {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', $projectid)->first();
        
        if (empty($project)) {
            return view('errors/404');
        }
        
        $company = Database::table('companies')->where('id', $user->company)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        $partslist = Database::table('partslist')->where('company', $user->company)->orderBy("indexing", true)->get();
        foreach ($partslist as $key => $part) {

            $checkedin = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "In")->first();
            if (!empty($checkedin)) {
                $part->checkedin = "checked";
                $part->checkedin_input = $checkedin->input_field;
            }else{
                $part->checkedin_input = $part->checkedin = NULL;
            }

            $checkedout = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "Out")->first();
            if (!empty($checkedout)) {
                $part->checkedout = "checked";
                $part->checkedout_input = $checkedout->input_field;
            }else{
                $part->checkedout_input = $part->checkedout = NULL;
            }

        }

        $this->generate($company, $project, $client, $partslist);

        die();
        
    }
    
    /**
     * Regenerate PDF
     * 
     * @return Json
     */
    public function regenerate() {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("projectid"))->first();

        Database::table('projects')->where('company', $user->company)->where('id', $project->id)->update(array(
            "booking_form" => "NULL",
            "booking_signature" => "NULL",
            "signed_by" => "NULL"
        ));

        return response()->json(responder("success", "Regenerated!", "Booking sheet regenerated successfully.", "reload()"));
        
    }
    
    /**
     * Sign Booking on entry
     * 
     * @return \Pecee\Http\Response
     */
    public function sign() {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("project"))->first();
        
        $company = Database::table('companies')->where('id', $user->company)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        $partslist = Database::table('partslist')->where('company', $user->company)->orderBy("indexing", true)->get();
        foreach ($partslist as $key => $part) {

            $checkedin = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "In")->first();
            if (!empty($checkedin)) {
                $part->checkedin = "checked";
                $part->checkedin_input = $checkedin->input_field;
            }else{
                $part->checkedin_input = $part->checkedin = NULL;
            }

            $checkedout = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "Out")->first();
            if (!empty($checkedout)) {
                $part->checkedout = "checked";
                $part->checkedout_input = $checkedout->input_field;
            }else{
                $part->checkedout_input = $part->checkedout = NULL;
            }

        }

        $upload = File::upload(input("signature"), "bookingsignatures", array(
            "source" => "base64",
            "extension" => "png"
        ));
        
        if ($upload['status'] == "success") {
            $signature = array(
                "booking_signature" => $upload['info']['name'],
                "signed_by" => input("fullname")
            );
            Database::table('projects')->where('id', $project->id)->update($signature);
            $project->booking_signature = $signature["booking_signature"];
            $project->signed_by = $signature["signed_by"];
        }else{
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }

        $booking = $this->generate($company, $project, $client, $partslist, true);

        Database::table('projects')->where('company', $user->company)->where('id', $project->id)->update(array("booking_form" => escape($booking)));

        return response()->json(responder("success", "Signed!", "Booking form successfully signed.", "reload()"));
        
    }
    
    /**
     * Sign Booking on exit
     * 
     * @return \Pecee\Http\Response
     */
    public function exitsign() {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("project"))->first();
        
        $company = Database::table('companies')->where('id', $user->company)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }

        $partslist = Database::table('partslist')->where('company', $user->company)->orderBy("indexing", true)->get();
        foreach ($partslist as $key => $part) {

            $checkedin = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "In")->first();
            if (!empty($checkedin)) {
                $part->checkedin = "checked";
                $part->checkedin_input = $checkedin->input_field;
            }else{
                $part->checkedin_input = $part->checkedin = NULL;
            }

            $checkedout = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "Out")->first();
            if (!empty($checkedout)) {
                $part->checkedout = "checked";
                $part->checkedout_input = $checkedout->input_field;
            }else{
                $part->checkedout_input = $part->checkedout = NULL;
            }

        }

        $signature = ( object ) array(
            "booking_signature" => input("signature"),
            "signed_by" => input("fullname")
        );

        $booking = $this->generate($company, $project, $client, $partslist, true, $signature);

        Database::table('projects')->where('company', $user->company)->where('id', $project->id)->update(array(
            "booking_form" => escape($booking),
            "signed_out" => "Yes"
        ));

        return response()->json(responder("success", "Completed!", "Vehicle signed out successfully.", "reload()"));
        
    }
    
    /**
     * Send via email
     * 
     * @return Json
     */
    public function send() {
        
        $user   = Auth::user();
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("itemid"))->first();
        
        if (empty($project)) {
            return view('errors/404');
        }

        if (empty($project->booking_form)) {

            $company = Database::table('companies')->where('id', $user->company)->first();
            $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
            if(!empty($project->insurance)){
                $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
            }

            $partslist = Database::table('partslist')->where('company', $user->company)->orderBy("indexing", true)->get();
            foreach ($partslist as $key => $part) {

                $checkedin = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "In")->first();
                if (!empty($checkedin)) {
                    $part->checkedin = "checked";
                    $part->checkedin_input = $checkedin->input_field;
                }else{
                    $part->checkedin_input = $part->checkedin = NULL;
                }

                $checkedout = Database::table('partschecked')->where('company', $user->company)->where('part', $part->id)->where('project', $project->id)->where('booking', "Out")->first();
                if (!empty($checkedout)) {
                    $part->checkedout = "checked";
                    $part->checkedout_input = $checkedout->input_field;
                }else{
                    $part->checkedout_input = $part->checkedout = NULL;
                }

            }

            $document = $this->generate($company, $project, $client, $partslist, true);

            $booking_form = config("app.storage")."tmp/".$document;

        }else{
            $booking_form = config("app.storage")."booking/".$project->booking_form;
        }

        $send = Mail::send(
            input("email"),
            input("subject"),
            array(
                "message" => nl2br(input("message"))
            ),
            "basic",
            null,
            array("Vehicle Booking: ".carmake($project->make)." ".carmodel($project->model).".pdf" => $booking_form)
        );

        if (empty($project->booking_form)) {
            File::delete($document, "tmp");
        }

        if ($send) {
            return response()->json(responder("success", "Alright!", "Email successfully sent.", "reload()"));
        }else{
            return response()->json(responder("error", "Hmmm!", "Email could not be sent, please try again."));
        }
        
    }

    /**
     * Generate PDF Invoice
     * 
     * @return Json
     */
    public function generate($company, $project, $client, $partslist, $save = false, $signature = NULL) {

        $outputName = uniqid("asilify_".$company->id."_").".pdf";
        
        if (!empty($project->booking_signature) && !empty($project->signed_by)) {
            $outputPath = config("app.storage")."/booking/". $outputName;
        }else{
            $outputPath = config("app.storage")."/tmp/". $outputName;
        }

        if (!empty($project->work_requested)) {
            $project->work_requested = json_decode($project->work_requested);
        }else{
            $project->work_requested = array();
        }

        $pdf = new PDF(null, 'px');
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(TRUE, 50);
        $pdf->AddPage();

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
        $pdf->MultiCell(200, 70, "BOOKING", null, "L");


        // client info
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY();
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',11);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 25, "Project For: ", null, "L");

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
        $pdf->Line($xPos, $yPos, $xPos + 180, $yPos);

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Booking No.: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(180, 70, $project->id, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Start Date: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(180, 70, date("M j, Y", strtotime($project->start_date)), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Expected Completion: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(180, 70, date("M j, Y", strtotime($project->end_date)), null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Received By: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(180, 70, $project->received_by, null, "R");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Signature In: ", null, "L");

        if (!is_null($signature)) {
            $pdf->SetXY($xPos + 98, $yPos);
            $pdf->MultiCell(150, 70, "Signature Out: ", null, "L");
        }

        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        if (!empty($project->booking_signature) && !empty($project->signed_by)) {
            $pdf->Image(env("APP_URL")."/uploads/bookingsignatures/".$project->booking_signature, $xPos, $yPos + 15, 80);
            $pdf->SetXY($xPos, $yPos + 60);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(128, 148, 174);
            $pdf->MultiCell(130, 20, $project->signed_by, null, "L");
        }
        if (!is_null($signature)) {
            $booking_signature = explode( ',', $signature->booking_signature );
            $booking_signature = base64_decode($booking_signature[1]);
            $pdf->SetLineWidth( 1 );
            $pdf->Image("@".$booking_signature, $xPos + 98, $yPos + 15, 80);
            $pdf->SetXY($xPos + 98, $yPos + 60);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(128, 148, 174);
            $pdf->MultiCell(130, 20, $signature->signed_by, null, "L");
        }
        


        // logo
        $yPos = 30;
        $xPos = 367;
        if (!empty($company->logo)) {
            $pdf->Image(env("APP_URL")."/uploads/logos/".$company->logo, $xPos + 40, $yPos, 160);
        }else{
            $pdf->SetFont('','B',24);
            $pdf->SetTextColor(54, 74, 99);
            $pdf->SetXY($xPos + 20, $yPos);
            $pdf->MultiCell(185,55, $company->name, null, "R");
        }


        $yPos = 130;
        $xPos = 415;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('',"",10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "P: ".$company->phone, null, "L");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->MultiCell(150, 70, "E: ".$company->email, null, "L");
        if (!empty($company->kra_pin)) {
            $yPos += 15;
            $pdf->SetXY($xPos, $yPos);
            $pdf->MultiCell(150, 70, "KRA PIN: ".$company->kra_pin, null, "L");
        }
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(180, 70, $company->address, null, "L");


        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY() + 25;
        
        if($yPos < 275){
            $yPos = 275;
        }

        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);
        $pdf->Line($xPos, $yPos + 3, $xPos + 539, $yPos + 3);


        $xPos = $pdf->GetX();
        $yPos += 10;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Vehicle Details: ", null, "L");

        $pdf->SetXY($xPos + 289, $yPos);
        $pdf->MultiCell(450, 25, "Fuel Level: ", null, "L");

        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos = $pdf->GetY();
        $pdf->Line($xPos, $yPos, $xPos + 269, $yPos);
        $pdf->Line($xPos + 289, $yPos, $xPos + 539, $yPos);

        $fuelY = $yPos;

        // details

        $yPos += 5;
        $pdf->SetTextColor(82, 100, 132);
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Vehicle ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".carmake($project->make)." ".carmodel($project->model), null, "L");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Registration No. ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$project->registration_number, null, "L");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "VIN / Chasis ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(170, 70, ": ".$project->vin, null, "L");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Engine No. ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$project->engine_number, null, "L");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Color", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$project->color, null, "L");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Insurance Expiry", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".date("M j, Y", strtotime($project->insurance_expiry)), null, "L");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Insurance Company", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$project->insurance_company, null, "L");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Date & Time In ", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".date("M j, Y", strtotime($project->date_in))." ".date("h:ia", strtotime($project->time_in)), null, "L");
        if ($project->checkedout == "Yes") {
            $yPos += 20;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->MultiCell(150, 70, "Date & Time Out ", null, "L");
            $pdf->SetXY($xPos + 120, $yPos);
            $pdf->SetFont('','B',10);
            $pdf->MultiCell(170, 70, ": ".date("M j, Y", strtotime($project->date_out))." ".date("h:ia", strtotime($project->time_out)), null, "L");
        }
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Milleage In", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        if(!empty($project->milleage)){
            $pdf->MultiCell(170, 70, ": ".$project->milleage." ".$project->milleage_unit, null, "L");
        }else{
            $pdf->MultiCell(170, 70, ": ".$project->milleage, null, "L");
        }
        if ($project->checkedout == "Yes") {
            $yPos += 20;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->MultiCell(150, 70, "Milleage Out", null, "L");
            $pdf->SetXY($xPos + 120, $yPos);
            $pdf->SetFont('','B',10);
            if(!empty($project->milleage_out)){
                $pdf->MultiCell(170, 70, ": ".$project->milleage_out." ".$project->milleage_unit, null, "L");
            }else{
                $pdf->MultiCell(170, 70, ": ".$project->milleage_out, null, "L");
            }
        }
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Towing Details", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$project->towing_details, null, "L");
        $yPos += 20;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->MultiCell(150, 70, "Road Test In", null, "L");
        $pdf->SetXY($xPos + 120, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->MultiCell(170, 70, ": ".$project->road_test_in, null, "L");
        if ($project->checkedout == "Yes") {
            $yPos += 20;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->MultiCell(150, 70, "Road Test Out", null, "L");
            $pdf->SetXY($xPos + 120, $yPos);
            $pdf->SetFont('','B',10);
            $pdf->MultiCell(170, 70, ": ".$project->road_test_out, null, "L");
            $yPos += 20;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->MultiCell(150, 70, "Old Parts Collected", null, "L");
            $pdf->SetXY($xPos + 120, $yPos);
            $pdf->SetFont('','B',10);
            $pdf->MultiCell(170, 70, ": ".$project->old_parts_collected, null, "L");
        }

        $lastY = $pdf->GetY();


        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->ImageSVG("@".$this->fuellevel($project->fuel_level), $xPos + 120, $fuelY + 30, $w='', $h=150);
        $pdf->SetXY($xPos + 260, $fuelY + 200);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(130, 20, "Entry", null, "C");

        if ($project->checkedout == "Yes") {
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->ImageSVG("@".$this->fuellevel($project->exit_fuel_level), $xPos + 250, $fuelY + 30, $w='', $h=150);
            $pdf->SetXY($xPos + 390, $fuelY + 200 );
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(128, 148, 174);
            $pdf->MultiCell(130, 20, "Exit", null, "C");
        }

        $pdf->SetY($lastY - 70);

        if ($project->delivered_by == "Other") {
            // brought in by start

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 40;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(450, 25, "Brought In By: ", null, "L");

            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);
            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

            // details

            $deliveredbyY = $yPos += 5;
            $pdf->SetTextColor(82, 100, 132);
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->MultiCell(150, 20, "Name ", null, "L");
            $pdf->SetXY($xPos + 120, $yPos);
            $pdf->SetFont('','B',10);
            $pdf->MultiCell(170, 20, ": ".$project->deliveredby_fullname, null, "L");
            $yPos += 20;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->MultiCell(150, 20, "Phone Number ", null, "L");
            $pdf->SetXY($xPos + 120, $yPos);
            $pdf->SetFont('','B',10);
            $pdf->MultiCell(170, 20, ": ".$project->deliveredby_phonenumber, null, "L");

            $yPos = $deliveredbyY;
            $xPos = $xPos + 269;

            $pdf->SetTextColor(82, 100, 132);
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->MultiCell(150, 20, "Email Address ", null, "L");
            $pdf->SetXY($xPos + 120, $yPos);
            $pdf->SetFont('','B',10);
            $pdf->MultiCell(170, 20, ": ".$project->deliveredby_email, null, "L");
            $yPos += 20;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->MultiCell(150, 20, "ID Number", null, "L");
            $pdf->SetXY($xPos + 120, $yPos);
            $pdf->SetFont('','B',10);
            $pdf->MultiCell(170, 20, ": ".$project->deliveredby_idnumber, null, "L");
            // brought in by end


            $yPos = $pdf->GetY() + 20;
        }else{
            $yPos = $pdf->GetY() + 40;
        }

        // parts check in start

        $xPos = $pdf->GetX();
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Vehicle Condition & Parts Check IN: ", null, "L");

        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos = $pdf->GetY();
        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY() + 10;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('dejavusans','',10);
        $pdf->SetTextColor(82, 100, 132);

        if (!empty($partslist)) {

            $index = 1;
            foreach ($partslist as $key => $part) {
                $trueY = $pdf->GetY();
                if ($trueY > 770 && $index == 1) {
                    $pdf->AddPage();
                    $yPos = $pdf->GetY();
                }

                $pdf->SetFont('dejavusans','',10);

                if ($index == 1) {
                    $pdf->SetXY($xPos, $yPos);
                }elseif ($index == 2) {
                    $pdf->SetXY($xPos + 260, $yPos);
                    // $pdf->SetXY($xPos + 160, $yPos);
                }elseif ($index == 3) {
                    $pdf->SetXY($xPos + 350, $yPos);
                }

                if (!is_null($part->checkedin)) {
                    $pdf->SetTextColor(3, 206, 24);
                    $pdf->MultiCell(150, 18, "⬤", null, "L");
                }else{
                    $pdf->SetTextColor(229, 233, 243);
                    $pdf->MultiCell(150, 18, "◯", null, "L");
                }
                $pdf->SetTextColor(82, 100, 132);
                $pdf->SetFont('helvetica','',10);

                if ($index == 1) {
                    $pdf->SetXY($xPos + 18, $yPos);
                }elseif ($index == 2) {
                    $pdf->SetXY($xPos + 278, $yPos);
                    // $pdf->SetXY($xPos + 178, $yPos);
                }elseif ($index == 3) {
                    $pdf->SetXY($xPos + 368, $yPos);
                }

                // Use 150 for 3 grids
                if (!empty($part->checkedin_input)) {
                    $pdf->MultiCell(250, 18, $part->name." (".$part->checkedin_input.")", null, "L");
                }else{
                    $pdf->MultiCell(250, 18, $part->name, null, "L");  
                }

                if ($index == 2) {
                    $yPos += 18;
                    $index = 1;
                }else{
                    $index++;
                }
                
            }

        }

        // parts check in end

        // parts check out start

        if ($project->checkedout == "Yes") {

            $yPos = $pdf->GetY() + 20;

            $xPos = $pdf->GetX();
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(450, 25, "Vehicle Condition & Parts Check OUT: ", null, "L");

            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);
            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 10;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('dejavusans','',10);
            $pdf->SetTextColor(82, 100, 132);

            if (!empty($partslist)) {

                $index = 1;
                foreach ($partslist as $key => $part) {
                    $trueY = $pdf->GetY();
                    if ($trueY > 785 && $index == 1) {
                        $pdf->AddPage();
                        $yPos = $pdf->GetY();
                    }

                    $pdf->SetFont('dejavusans','',10);

                    if ($index == 1) {
                        $pdf->SetXY($xPos, $yPos);
                    }elseif ($index == 2) {
                        $pdf->SetXY($xPos + 260, $yPos);
                    }elseif ($index == 3) {
                        $pdf->SetXY($xPos + 350, $yPos);
                    }

                    if (!is_null($part->checkedout)) {
                        $pdf->SetTextColor(3, 206, 24);
                        $pdf->MultiCell(250, 18, "⬤", null, "L");
                    }else{
                        $pdf->SetTextColor(229, 233, 243);
                        $pdf->MultiCell(250, 18, "◯", null, "L");
                    }
                    $pdf->SetTextColor(82, 100, 132);
                    $pdf->SetFont('helvetica','',10);

                    if ($index == 1) {
                        $pdf->SetXY($xPos + 18, $yPos);
                    }elseif ($index == 2) {
                        $pdf->SetXY($xPos + 278, $yPos);
                    }elseif ($index == 3) {
                        $pdf->SetXY($xPos + 368, $yPos);
                    }

                    // Use 150 for 3 grids
                    if (!empty($part->checkedout_input)) {
                        $pdf->MultiCell(250, 18, $part->name." (".$part->checkedout_input.")", null, "L");
                    }else{
                        $pdf->MultiCell(250, 18, $part->name, null, "L");  
                    }

                    if ($index == 2) {
                        $yPos += 18;
                        $index = 1;
                    }else{
                        $index++;
                    }
                    
                }

            }
        }

        // parts check in end

        if (!empty($project->booking_in_notes)) {
            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(450, 25, "Booking IN Notes: ", null, "L");

            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);
            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 10;
            $pdf->SetXY($xPos, $yPos);

            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->writeHTML(nl2br($project->booking_in_notes), true, false, true, false, '');
        }

        if ($project->checkedout == "Yes" && !empty($project->booking_out_notes)) {

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(450, 25, "Booking OUT Notes: ", null, "L");

            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);
            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 10;
            $pdf->SetXY($xPos, $yPos);

            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->writeHTML(nl2br($project->booking_out_notes), true, false, true, false, '');

        }


        if (!empty($project->other_defects)) {
            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(450, 25, "Pre Accident / Other Defects: ", null, "L");

            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);
            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 10;
            $pdf->SetXY($xPos, $yPos);

            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->writeHTML(nl2br($project->other_defects), true, false, true, false, '');
        }

        if (!empty($project->work_requested)) {
            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(450, 25, "Work Requested / Owners Instructions: ", null, "L");

            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);
            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 10;
            $pdf->SetXY($xPos, $yPos);

            $yPos = $pdf->GetY() + 12;
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',10);
            $pdf->SetTextColor(82, 100, 132);

            $work_requested = array();
            if (!empty($project->work_requested)) {
                foreach ($project->work_requested as $key => $item) {
                    $work_requested[] = "<li>".$item."</li>";
                }
                $work_requested = "<ol>".implode(" ", $work_requested)."</ol>";
            }else{
                $work_requested = "";
            }
            $pdf->writeHTML($work_requested, true, false, true, false, '');
        }

        if($company->car_diagram == "Enabled" && !empty($project->car_diagram)){

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;

            if ($yPos > 490) {
                $pdf->AddPage();
                $yPos = $pdf->GetY();
            }

            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',12);
            $pdf->SetTextColor(9, 113, 254);
            $pdf->MultiCell(450, 25, "Dents & Scratch Markings: ", null, "L");
            $yPos = $pdf->GetY();
            $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

            $pdf->SetDrawColor(229, 233, 243);
            $pdf->SetLineWidth(2);

            $yPos = $pdf->GetY() + 20;
            $pdf->SetXY($xPos, $yPos);
            $pdf->Image(env("APP_URL")."/uploads/cardiagrams/".$project->car_diagram, $xPos, $yPos, 500, 250);

            $pdf->SetY($yPos + 250);

        }

        if (!empty($company->booking_disclaimer_in)) {

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',11);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->MultiCell(450, 25, "Disclaimer IN: ", null, "L");

            $yPos = $pdf->GetY();
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',9);
            $pdf->SetTextColor(128, 148, 174);
            $pdf->writeHTML(nl2br($company->booking_disclaimer_in), true, false, true, false, '');

        }

        if (!empty($company->booking_disclaimer_out) && $project->checkedout == "Yes") {

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',11);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->MultiCell(450, 25, "Disclaimer OUT: ", null, "L");

            $yPos = $pdf->GetY();
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',9);
            $pdf->SetTextColor(128, 148, 174);
            $pdf->writeHTML(nl2br($company->booking_disclaimer_out), true, false, true, false, '');

        }

        if (!empty($company->booking_terms)) {

            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY() + 30;
            $pdf->SetXY($xPos, $yPos);

            $pdf->SetFont('','B',11);
            $pdf->SetTextColor(82, 100, 132);
            $pdf->MultiCell(450, 25, "Terms & Conditions: ", null, "L");

            $yPos = $pdf->GetY();
            $pdf->SetXY($xPos, $yPos);
            $pdf->SetFont('','',9);
            $pdf->SetTextColor(128, 148, 174);
            $pdf->writeHTML(nl2br($company->booking_terms), true, false, true, false, '');

        }


        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Project Booking #".$project->id.".pdf", 'I');
        }

        die();

    }
    
    /**
     * Fuel Level SVG
     * 
     * @return image/svg
     */
    public function fuellevel($fuel_level = 82) {

        return '<svg xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:svg="http://www.w3.org/2000/svg" xmlns:ns1="http://sozi.baierouge.fr" xmlns:xlink="http://www.w3.org/1999/xlink" id="svg6359" sodipodi:docname="1245688392544500617khendraw_Fuelmeter.svg" viewBox="0 0 990 765" sodipodi:version="0.32" version="1.0" inkscape:output_extension="org.inkscape.output.svg.inkscape" inkscape:version="0.48.2 r9819" width="400" > <sodipodi:namedview id="base" inkscape:zoom="1.096732" borderopacity="1.0" inkscape:current-layer="layer1" inkscape:cx="495" inkscape:cy="382.5" inkscape:window-maximized="1" showgrid="false" inkscape:guide-bbox="true" units="mm" showguides="false" bordercolor="#666666" inkscape:window-x="1358" guidetolerance="10" objecttolerance="10" inkscape:window-y="-8" inkscape:window-width="1920" inkscape:pageopacity="0.0" inkscape:pageshadow="2" pagecolor="#ffffff" gridtolerance="10000" inkscape:document-units="mm" inkscape:window-height="1028" > <sodipodi:guide id="guide3992" position="1753.937,539.35157" orientation="1,0"/> <sodipodi:guide id="guide4002" position="181.33323,-87.179439" orientation="0,1"/> <sodipodi:guide id="guide4004" position="495,577.70908" orientation="1,0"/> <sodipodi:guide id="guide4006" position="195.28194,382.5" orientation="0,1"/> <sodipodi:guide id="guide4012" position="495,382.5" orientation="0.8660254,0.5"/> <sodipodi:guide id="guide4014" position="495,382.5" orientation="-0.8660254,0.5"/> <sodipodi:guide id="guide4020" position="495,382.5" orientation="-0.5,0.8660254"/> <sodipodi:guide id="guide4022" position="495,382.5" orientation="0.5,0.8660254"/> <sodipodi:guide id="guide4028" position="495,382.5" orientation="-0.34202014,0.93969262"/> <sodipodi:guide id="guide4030" position="495,382.5" orientation="-0.17364818,0.98480775"/> <sodipodi:guide id="guide4032" position="495,382.5" orientation="-0.64278761,0.76604444"/> <sodipodi:guide id="guide4034" position="495,382.5" orientation="-0.76604444,0.64278761"/> <sodipodi:guide id="guide4036" position="495,382.5" orientation="0.17364818,0.98480775"/> <sodipodi:guide id="guide4038" position="495,382.5" orientation="0.34202014,0.93969262"/> <sodipodi:guide id="guide4040" position="495,382.5" orientation="0.64278761,0.76604444"/> <sodipodi:guide id="guide4042" position="495,382.5" orientation="0.76604444,0.64278761"/> <sodipodi:guide id="guide4058" position="495,382.5" orientation="-0.93969262,0.34202014"/> <sodipodi:guide id="guide4060" position="495,382.5" orientation="0.93969262,0.34202014"/> </sodipodi:namedview > <g id="layer1" inkscape:label="Layer 1" inkscape:groupmode="layer" transform="translate(-223.9 518.24)" > <path id="path6946" style="fill:#000000" inkscape:connector-curvature="0" d="m740.87-180.08c-5.0746 0-9.2145 3.4343-9.2145 7.6219v80.2l44.935 0.62568 0.0568-46.869h8.4751c1.9473 0 3.5266 1.2894 3.5266 2.9009v35.322c0 4.7369 5.3825 8.8732 11.49 8.8732 5.7678 0 12.172-3.7834 12.172-8.8732-0.0885-1.1849-4.6641-22.24-4.6641-22.24 0-0.0488-0.96696-5.9155-0.96696-5.9155v-26.904c0-2.8812-1.1663-5.2581-3.5265-7.2237l-15.016-12.4c-1.0325 0-3.9247 2.389-3.9247 2.389 0 0.86685 7.4512 6.9962 7.4512 6.9962l-0.22752 10.466c0 3.6992 3.6346 6.7118 8.1338 6.7118h2.0477l-0.73943 19.965 1.0238 6.8824 4.5504 20.192c0.17692 3.284-3.4519 5.2329-6.3136 5.2329-2.6406 0-5.7449-1.8209-5.7449-4.1522l0.0568-35.379c0-4.3097-4.2495-7.7925-9.442-7.7925h-9.0439c0.63425-0.0366 0.62567-23.235 0.62567-28.155v-0.8532c0-4.1876-4.0978-7.6219-9.1576-7.6219h-26.563zm0 5.5173h26.563c1.4161 0 2.5027 0.94475 2.5027 2.1046v18.486c0 1.172-1.0866 2.0477-2.5027 2.0477h-26.563c-1.431 0-2.5027-0.87564-2.5027-2.0477v-18.486c0-1.1598 1.0717-2.1046 2.5027-2.1046zm55.401 14.846s2.2561 0.3898 2.7871 0.51192c1.0916 0.23194 1.7907 1.5907 1.8202 2.7871 0.0295 1.2088 0 4.8348 0 4.8348-3.3485-0.61047-4.6073-2.0374-4.6073-3.3559v-4.7779z"/> <path id="path3220" sodipodi:rx="201.71242" sodipodi:ry="201.71242" style="fill:none" sodipodi:type="arc" d="m-219.85 533.12a201.71 201.71 0 1 1 0.37722 403.42l-0.0778-201.71z" transform="matrix(1.8202 0 0 1.8202 1038.7 -1473.3)" sodipodi:cy="734.83527" sodipodi:cx="-219.54707" sodipodi:end="7.8535958" sodipodi:start="4.7109047"/> <path id="path3990" sodipodi:rx="201.71242" sodipodi:ry="201.71242" style="stroke:#000000;stroke-width:1.25;fill:#ffffff" sodipodi:type="arc" d="m-17.835 734.84a201.71 201.71 0 1 1 -403.42 0 201.71 201.71 0 1 1 403.42 0z" transform="matrix(.069018 0 0 .069018 654.26 -186.46)" sodipodi:cy="734.83527" sodipodi:cx="-219.54707"/> <rect id="rect3996" style="fill:#000000" rx="4.6768" ry="14.616" height="18.648" width="77.258" y="-145.07" x="919.92"/> <rect id="rect4000" style="fill:#ff0000" transform="rotate(-50.091)" inkscape:transform-center-x="-204.94147" inkscape:transform-center-y="-245.0226" rx="3.6308" ry="7.7842" height="9.932" width="59.979" y="398.18" x="803.61"/> <rect id="rect4016" style="fill:#000000" transform="rotate(-59.844)" inkscape:transform-center-x="-160.46972" inkscape:transform-center-y="-276.20013" rx="4.6768" ry="14.616" height="18.648" width="77.258" y="475.1" x="719.24"/> <rect id="rect4018" style="fill:#000000" transform="rotate(59.847)" inkscape:transform-center-x="-160.4543" inkscape:transform-center-y="276.20913" rx="4.6768" ry="14.616" height="18.648" width="77.258" y="-630.14" x="484.46"/> <rect id="rect4024" ry="14.616" style="fill:#000000" inkscape:transform-center-x="-276.56078" inkscape:transform-center-y="-159.84743" rx="4.6768" transform="rotate(-30.027)" height="18.648" width="77.258" y="192.97" x="902.07"/> <rect id="rect4026" style="fill:#000000" transform="rotate(30.093)" inkscape:transform-center-x="-276.37566" inkscape:transform-center-y="160.16718" rx="4.6768" ry="14.616" height="18.648" width="77.258" y="-447.23" x="765.71"/> <rect id="rect4044" ry="7.7842" style="fill:#ff0000" inkscape:transform-center-x="-245.03801" inkscape:transform-center-y="-204.92305" rx="3.6308" transform="matrix(.76710 -.64152 .64152 .76710 0 0)" height="9.932" width="59.979" y="300.91" x="866.79"/> <rect id="rect4046" style="fill:#ff0000" transform="rotate(-20.162)" inkscape:transform-center-x="-299.85823" inkscape:transform-center-y="-110.10056" rx="3.6308" ry="7.7842" height="9.932" width="59.979" y="87.897" x="936.18"/> <rect id="rect4048" ry="7.7842" style="fill:#ff0000" inkscape:transform-center-x="-314.59473" inkscape:transform-center-y="-55.382986" rx="3.6308" transform="rotate(-9.9844)" height="9.932" width="59.979" y="-27.842" x="942.41"/> <rect id="rect4050" style="fill:#ff0000" transform="rotate(20.02)" inkscape:transform-center-x="-300.1314" inkscape:transform-center-y="109.35372" rx="3.6308" ry="7.7842" height="9.932" width="59.979" y="-351.3" x="843.47"/> <rect id="rect4052" style="fill:#ff0000" transform="rotate(10.096)" inkscape:transform-center-x="-314.48678" inkscape:transform-center-y="55.99266" rx="3.6308" ry="7.7842" height="9.932" width="59.979" y="-250.63" x="894.87"/> <rect id="rect4054" ry="7.7842" style="fill:#ff0000" inkscape:transform-center-x="-244.39724" inkscape:transform-center-y="205.68684" rx="3.6308" transform="rotate(40.084)" height="9.932" width="59.979" y="-520.35" x="691.02"/> <rect id="rect4056" style="fill:#ff0000" transform="rotate(50.218)" inkscape:transform-center-x="-204.39212" inkscape:transform-center-y="245.48097" rx="3.6308" ry="7.7842" height="9.932" width="59.979" y="-582.97" x="594.07"/> <g id="text4161" style="fill:#000000" > <path id="path4315" style="baseline-shift:super" d="m746.75-430.11c0.11887-1.4957 0.21613-3.069 0.29179-4.7198l0.24154-5.0886c0.0848-1.7237 0.13995-3.1208 0.16535-4.1913-0.0595 0.19184-0.2048 0.36421-0.43607 0.51712-0.23128 0.15293-0.86998 0.502-1.9161 1.0472 0.0432-0.72785 0.0794-1.4172 0.10861-2.0681 0.0292-0.65085 0.0438-1.2016 0.0438-1.6523 0.37122-0.2118 0.80202-0.46644 1.2924-0.76393 0.49037-0.29745 0.78041-0.47658 0.87011-0.53739 0.0897-0.0608 0.2718-0.20005 0.54631-0.41783 0.49063 0.0168 1.1326 0.0251 1.9258 0.0251 0.78891 0.00002 1.5408-0.008 2.2557-0.0251l-0.14346 2.0126c-0.0351 0.41177-0.0735 0.9813-0.1151 1.7086l-0.50902 10.133-0.0373 1.4808c-0.008 0.31125-0.0114 0.62655-0.0113 0.9459l0.0138 1.5943c-0.7657-0.0168-1.4876-0.0251-2.1658-0.0251-0.78515 0-1.5922 0.008-2.4211 0.0251z"/> <path id="path4317" style="" d="m749.77-413.77 6.4518-11.84 6.7336-12.046 1.9253-3.6137c0.71741 0.0258 1.3043 0.0387 1.7607 0.0387 0.47217 0.00003 1.1401-0.0129 2.0039-0.0387l-2.9915 5.6749-10.386 18.697-1.6435 3.1274c-0.72906-0.0258-1.2611-0.0387-1.5961-0.0387-0.47301 0-1.2258 0.0129-2.2582 0.0387z"/> <path id="path4319" style="baseline-shift:sub" d="m767.38-405.6c0.11887-1.4957 0.21614-3.069 0.29179-4.7198l0.24154-5.0886c0.0848-1.7237 0.13995-3.1208 0.16535-4.1913-0.0594 0.19184-0.2048 0.36422-0.43607 0.51712-0.23128 0.15294-0.86998 0.50201-1.9161 1.0472 0.0432-0.72785 0.0794-1.4172 0.10861-2.0681 0.0292-0.65085 0.0438-1.2016 0.0438-1.6523 0.37122-0.21181 0.80202-0.46645 1.2924-0.76393 0.49037-0.29745 0.7804-0.47658 0.87011-0.53739 0.0897-0.0608 0.27179-0.20005 0.5463-0.41783 0.49064 0.0168 1.1326 0.0251 1.9258 0.0251 0.78891 0.00002 1.5408-0.008 2.2557-0.0251l-0.14347 2.0126c-0.0351 0.41177-0.0735 0.98131-0.11509 1.7086l-0.50902 10.133-0.0373 1.4808c-0.008 0.31125-0.0113 0.62655-0.0113 0.9459l0.0138 1.5943c-0.7657-0.0168-1.4876-0.0251-2.1658-0.0251-0.78515 0-1.5922 0.008-2.4211 0.0251z"/> </g > <g id="text4161-1" style="fill:#000000" > <path id="path4301" style="baseline-shift:super" d="m889.23-138.82c0.0965-1.2136 0.17536-2.49 0.23675-3.8294l0.19598-4.1287c0.0688-1.3986 0.11354-2.5321 0.13415-3.4007-0.0482 0.15565-0.16617 0.29551-0.35381 0.41957-0.18765 0.12409-0.70587 0.40731-1.5547 0.84967 0.0351-0.59055 0.0644-1.1499 0.0881-1.678 0.0237-0.52807 0.0355-0.97494 0.0355-1.3406 0.30119-0.17185 0.65073-0.37846 1.0486-0.61983 0.39786-0.24134 0.63319-0.38667 0.70597-0.43601 0.0728-0.0493 0.22053-0.16231 0.44325-0.33901 0.39809 0.0136 0.91894 0.0204 1.5626 0.0204 0.64009 0.00002 1.2502-0.007 1.8302-0.0204l-0.1164 1.6329c-0.0285 0.33409-0.0596 0.79619-0.0934 1.3863l-0.41299 8.2212-0.0303 1.2015c-0.006 0.25254-0.009 0.50836-0.009 0.76747l0.0112 1.2936c-0.62126-0.0136-1.207-0.0204-1.7572-0.0204-0.63704 0-1.2918 0.007-1.9644 0.0204z"/> <path id="path4303" style="" d="m891.68-125.56 5.2348-9.6065 5.4634-9.7734 1.5621-2.932c0.58208 0.0209 1.0583 0.0314 1.4286 0.0314 0.3831 0.00002 0.92505-0.0104 1.6259-0.0314l-2.4272 4.6044-8.4268 15.17-1.3335 2.5374c-0.59152-0.0209-1.0232-0.0314-1.295-0.0314-0.38379 0-0.99454 0.0105-1.8323 0.0314z"/> <path id="path4305" style="baseline-shift:sub" d="m901.73-118.93c0.0206-0.413 0.0309-0.69513 0.0309-0.84639l0.0651-1.5882c0.88168-0.79048 1.6866-1.5533 2.4149-2.2886 0.72822-0.73524 1.4546-1.5037 2.1791-2.3054 0.7245-0.80165 1.2808-1.4594 1.6688-1.9732 0.28935-0.37704 0.48467-0.6653 0.58595-0.8648 0.10127-0.19947 0.16759-0.36311 0.19894-0.49093 0.0313-0.12779 0.047-0.25307 0.047-0.37584-0.00001-0.32267-0.0983-0.58693-0.29495-0.79278-0.19664-0.20583-0.47713-0.37035-0.84145-0.49356-0.36434-0.12319-0.79225-0.18479-1.2837-0.1848-0.45947 0.00001-0.97287 0.043-1.5402 0.1289s-1.3793 0.24246-2.4359 0.46955l0.19795-1.287c0.0447-0.27663 0.11267-0.73917 0.20387-1.3876 1.7248-0.31346 3.322-0.4702 4.7916-0.47021 0.67473 0.00001 1.3364 0.0666 1.9851 0.19992 0.64864 0.13329 1.228 0.36095 1.7381 0.68296 0.5101 0.32204 0.88616 0.70095 1.1282 1.1367 0.242 0.43581 0.36301 0.88848 0.36302 1.358-0.00001 0.2657-0.0278 0.53774-0.0835 0.81613-0.0557 0.27841-0.14579 0.55528-0.27029 0.8306-0.16881 0.38846-0.39657 0.80025-0.68329 1.2354-0.28674 0.43515-0.74172 1.0223-1.3649 1.7615-0.62324 0.73919-1.2876 1.4799-1.993 2.2222-0.70544 0.74226-1.4861 1.4201-2.3419 2.0334h0.70565l2.3004-0.0105c0.80144 0 1.3654-0.008 1.6918-0.0234 0.32639-0.0156 0.83069-0.049 1.5129-0.10029-0.0158 0.36697-0.0391 0.72012-0.07 1.0595-0.0309 0.33935-0.0718 0.85559-0.12265 1.5488-1.7037-0.0136-3.3983-0.0204-5.0836-0.0204-2.0632 0-3.8632 0.007-5.3999 0.0204z"/> </g > <g id="text4161-1-9" style="fill:#000000" > <path id="path4287" style="baseline-shift:super" d="m749.09 130.54c1.3925 0.00002 2.5913 0.3037 3.5964 0.91104 1.005 0.60738 1.7788 1.5629 2.3214 2.8665 0.54251 1.3036 0.81377 2.8138 0.81378 4.5305-0.00001 1.3498-0.16388 2.6976-0.49159 4.0434-0.32774 1.3458-0.78651 2.4588-1.3763 3.339-0.58982 0.88024-1.4006 1.5661-2.4324 2.0576-1.0318 0.49145-2.1348 0.73718-3.309 0.73718-1.138 0-2.1532-0.21979-3.0456-0.65937s-1.6173-1.0552-2.1747-1.8468c-0.55738-0.79162-0.94442-1.6706-1.1611-2.6371-0.21668-0.96642-0.32502-1.9619-0.32502-2.9864 0-1.406 0.16291-2.7869 0.48875-4.1427 0.32584-1.3557 0.80473-2.4998 1.4367-3.4322 0.63195-0.93237 1.4526-1.6289 2.462-2.0896 1.0094-0.46064 2.075-0.69096 3.1968-0.69098zm-0.10213 3.1635c-0.27721 0.00001-0.5532 0.0565-0.82796 0.1694-0.27478 0.11295-0.54699 0.3041-0.81662 0.57346-0.26964 0.26938-0.49538 0.58981-0.6772 0.9613-0.18184 0.37151-0.34894 0.8273-0.50132 1.3674-0.15239 0.5401-0.27221 1.186-0.35947 1.9376-0.0873 0.75164-0.13091 1.5257-0.13091 2.3222 0 1.1385 0.11023 2.0507 0.3307 2.7364 0.22046 0.68572 0.53847 1.2077 0.95401 1.566 0.41553 0.35827 0.88834 0.53739 1.4184 0.53739 0.28908 0 0.57142-0.0577 0.84701-0.17305 0.27558-0.11536 0.56251-0.33975 0.86079-0.67315 0.29827-0.3334 0.57318-0.84039 0.82473-1.521 0.25152-0.68057 0.43605-1.4337 0.55359-2.2594 0.11752-0.82566 0.17628-1.6554 0.1763-2.4892-0.00002-1.1466-0.10836-2.0877-0.32503-2.8231-0.2167-0.73541-0.52646-1.2913-0.92928-1.6677-0.40285-0.37635-0.86877-0.56453-1.3978-0.56454z"/> <path id="path4289" style="" d="m749.83 165.07 6.4518-11.84 6.7336-12.046 1.9253-3.6137c0.71741 0.0258 1.3043 0.0387 1.7607 0.0387 0.47217 0.00003 1.1401-0.0129 2.0039-0.0387l-2.9915 5.6749-10.386 18.697-1.6435 3.1274c-0.72905-0.0258-1.2611-0.0387-1.5961-0.0387-0.47301 0-1.2258 0.0129-2.2583 0.0387z"/> <path id="path4291" style="baseline-shift:sub" d="m767.44 173.24c0.11887-1.4957 0.21614-3.069 0.2918-4.7198l0.24154-5.0886c0.0848-1.7237 0.13994-3.1208 0.16535-4.1913-0.0594 0.19184-0.20481 0.36422-0.43607 0.51712-0.23128 0.15294-0.86999 0.50201-1.9161 1.0472 0.0432-0.72785 0.0794-1.4172 0.10862-2.0681 0.0292-0.65085 0.0438-1.2016 0.0438-1.6523 0.37122-0.21181 0.80202-0.46645 1.2924-0.76393 0.49037-0.29745 0.7804-0.47658 0.87011-0.53739 0.0897-0.0608 0.27179-0.20005 0.5463-0.41783 0.49064 0.0168 1.1326 0.0251 1.9258 0.0251 0.78892 0.00002 1.5408-0.008 2.2557-0.0251l-0.14346 2.0126c-0.0351 0.41177-0.0735 0.98131-0.1151 1.7086l-0.50902 10.133-0.0373 1.4808c-0.008 0.31125-0.0114 0.62655-0.0113 0.9459l0.0138 1.5943c-0.7657-0.0168-1.4876-0.0251-2.1658-0.0251-0.78515 0-1.5922 0.008-2.4211 0.0251z"/> </g > <g id="text4161-1-9-2" style="fill:#000000" > <path id="path4294" style="baseline-shift:super" d="m849.51-10.726c0.0965-1.2136 0.17537-2.49 0.23675-3.8294l0.19598-4.1287c0.0688-1.3986 0.11355-2.5321 0.13416-3.4007-0.0482 0.15565-0.16617 0.29551-0.35381 0.41958-0.18765 0.12409-0.70587 0.40731-1.5547 0.84967 0.0351-0.59055 0.0644-1.1499 0.0881-1.678 0.0237-0.52807 0.0355-0.97494 0.0355-1.3406 0.3012-0.17185 0.65073-0.37846 1.0486-0.61983 0.39787-0.24134 0.63319-0.38668 0.70598-0.43602 0.0728-0.04931 0.22052-0.16231 0.44325-0.33901 0.39808 0.01361 0.91893 0.0204 1.5626 0.02039 0.6401 0.000015 1.2502-0.0068 1.8302-0.02039l-0.11641 1.6329c-0.0285 0.33409-0.0596 0.7962-0.0934 1.3863l-0.413 8.2212-0.0303 1.2015c-0.006 0.25254-0.009 0.50836-0.009 0.76747l0.0112 1.2936c-0.62126-0.01359-1.207-0.02039-1.7572-0.02039-0.63705 0-1.2918 0.0068-1.9644 0.02039z"/> <path id="path4296" style="" d="m851.96 2.5351 5.2348-9.6065 5.4634-9.7734 1.5621-2.932c0.58208 0.02093 1.0583 0.03139 1.4286 0.03136 0.3831 0.000023 0.92506-0.01043 1.6259-0.03136l-2.4272 4.6044-8.4268 15.17-1.3335 2.5374c-0.59153-0.020909-1.0232-0.031364-1.295-0.031364-0.38378 1e-7 -0.99453 0.010455-1.8323 0.031364z"/> <path id="path4298" style="baseline-shift:sub" d="m867.54 9.1657c0.13108-1.5415 0.22052-2.9583 0.26831-4.2503h-1.8486l-4.032 0.061818c0.0149-0.25384 0.0376-0.71354 0.0681-1.3791 0.0305-0.66553 0.0457-1.1447 0.0457-1.4376l0.88913-1.1226c0.11575-0.15432 0.39086-0.52698 0.82534-1.118l2.2116-3.0304c0.26042-0.37046 0.47755-0.66705 0.65139-0.88979 0.17383-0.22271 0.45519-0.66793 0.84408-1.3357 0.78697 0.012729 1.5156 0.019086 2.186 0.019072 0.62826 0.0000144 1.3074-0.00678 2.0374-0.020387-0.2087 3.0905-0.33606 5.6516-0.38209 7.6832 0.0728 0.00176 0.38778 0.00264 0.94503 0.00263 0.53662 0.0000068 0.97483-0.010296 1.3146-0.030909-0.0206 0.63046-0.0309 1.3195-0.0309 2.067v0.56163c-0.58707-0.027617-1.3626-0.041427-2.3267-0.041431-0.0132 0.19949-0.0247 0.42462-0.0345 0.6754-0.01 0.25078-0.0197 0.70609-0.0296 1.3659-0.01 0.65983-0.0141 1.1336-0.0128 1.4212v0.79838c-0.68396-0.013591-1.2668-0.020387-1.7487-0.020387-0.43755 1e-7 -1.0511 0.0068-1.8407 0.020387zm0.67079-6.7244c0.0136-0.26173 0.0204-0.42417 0.0204-0.48731l0.19597-4.787c-0.77821 1.4793-1.3543 2.4594-1.7283 2.9403l-1.8736 2.334z"/> </g > <g id="text4161-1-9-1" style="fill:#000000" > <path id="path4308" style="baseline-shift:super" d="m845.66-267.83c-0.0605-1.0237-0.13241-1.9584-0.21571-2.8042 1.6117 0.51603 3.0129 0.77404 4.2036 0.77404 0.57521 0 1.0908-0.0893 1.5468-0.26799 0.45596-0.17865 0.81349-0.43185 1.0726-0.75957 0.2591-0.32772 0.38866-0.71299 0.38867-1.1558-0.00001-0.36871-0.10425-0.6904-0.31271-0.96508-0.20848-0.27467-0.54925-0.48446-1.0223-0.62937-0.47307-0.14489-1.0926-0.21734-1.8585-0.21735-0.49192 0.00001-1.0954 0.0311-1.8105 0.0934 0.0697-0.9527 0.12933-1.8427 0.17888-2.67 0.56162 0.0688 1.0332 0.10326 1.4146 0.10325 0.59231 0.00001 1.142-0.0544 1.649-0.1631 0.50703-0.10872 0.89635-0.32508 1.168-0.64909 0.27159-0.32399 0.4074-0.67451 0.40741-1.0516-0.00001-0.15124-0.0226-0.29757-0.0677-0.43897-0.0452-0.14138-0.1083-0.27072-0.1894-0.38801-0.0811-0.11727-0.18305-0.22074-0.30581-0.31041-0.12276-0.0896-0.25911-0.16308-0.40905-0.22031-0.14995-0.0572-0.36883-0.10586-0.65665-0.14599-0.28784-0.0401-0.58936-0.0602-0.90459-0.0602-0.43668 0.00002-0.89494 0.029-1.3748 0.0868-0.47987 0.0579-1.1854 0.1699-2.1166 0.33606l0.20781-1.1463 0.26635-1.4554c0.89263-0.12406 1.623-0.20769 2.1913-0.25089 0.56819-0.0432 1.1598-0.0648 1.775-0.0648 0.73392 0.00002 1.4499 0.0602 2.1478 0.18052 0.69797 0.12037 1.3052 0.33563 1.8217 0.64581 0.51645 0.3102 0.89778 0.71136 1.144 1.2035 0.24617 0.49215 0.36926 1.0274 0.36927 1.6056-0.00001 0.37574-0.0543 0.74654-0.16277 1.1124-0.10852 0.36588-0.27052 0.70577-0.48599 1.0197-0.2155 0.31392-0.45773 0.57402-0.7267 0.78029-0.26898 0.20629-0.5293 0.37048-0.78094 0.49257-0.25167 0.12211-0.56098 0.17199-0.92794 0.14962 0.38055-0.008 0.71112 0.0373 0.99173 0.13679 0.28058 0.0995 0.55767 0.251 0.83125 0.45443 0.27357 0.20343 0.50572 0.43898 0.69645 0.70663 0.1907 0.26767 0.33428 0.57753 0.43075 0.92958 0.0964 0.35206 0.14467 0.71157 0.14468 1.0785-0.00001 0.67825-0.16102 1.3197-0.48304 1.9242-0.32203 0.6046-0.78885 1.106-1.4004 1.5044-0.61162 0.39831-1.299 0.67682-2.062 0.83553-0.76309 0.15871-1.5574 0.23807-2.383 0.23807-0.61074 0-1.2351-0.0382-1.873-0.11443-0.63791-0.0763-1.4771-0.23062-2.5174-0.46298z"/> <path id="path4310" style="" d="m852.04-254.24 5.2347-9.6065 5.4634-9.7734 1.5621-2.932c0.58208 0.0209 1.0583 0.0314 1.4286 0.0314 0.3831 0.00002 0.92506-0.0104 1.6259-0.0314l-2.4272 4.6044-8.4268 15.17-1.3335 2.5374c-0.59153-0.0209-1.0232-0.0314-1.295-0.0314-0.38378 0-0.99454 0.0104-1.8323 0.0314z"/> <path id="path4312" style="baseline-shift:sub" d="m867.63-247.61c0.13109-1.5415 0.22053-2.9583 0.26832-4.2503h-1.8486l-4.032 0.0618c0.0149-0.25384 0.0376-0.71353 0.0681-1.3791 0.0305-0.66553 0.0457-1.1447 0.0457-1.4376l0.88913-1.1226c0.11574-0.15432 0.39086-0.52698 0.82534-1.118l2.2116-3.0304c0.26042-0.37046 0.47756-0.66706 0.6514-0.88979 0.17383-0.22271 0.45519-0.66793 0.84408-1.3357 0.78697 0.0127 1.5156 0.0191 2.186 0.0191 0.62825 0.00002 1.3074-0.007 2.0374-0.0204-0.2087 3.0905-0.33607 5.6516-0.38209 7.6832 0.0728 0.002 0.38778 0.003 0.94503 0.003 0.53662 0.00001 0.97483-0.0103 1.3146-0.0309-0.0206 0.63047-0.0309 1.3194-0.0309 2.067v0.56162c-0.58706-0.0276-1.3626-0.0414-2.3267-0.0414-0.0132 0.19949-0.0247 0.42462-0.0345 0.6754-0.01 0.25078-0.0197 0.70609-0.0296 1.3659-0.01 0.65983-0.0141 1.1336-0.0128 1.4212v0.79837c-0.68396-0.0136-1.2668-0.0204-1.7487-0.0204-0.43756 0-1.0511 0.007-1.8407 0.0204zm0.6708-6.7244c0.0136-0.26174 0.0204-0.42417 0.0204-0.48731l0.19598-4.787c-0.77821 1.4793-1.3543 2.4594-1.7283 2.9403l-1.8736 2.334z"/> </g > <path id="path4275" class="fuel-level" style="fill:#ff6600" d="m626.21-131.11c-3.3393-7.4056 0.80597-13.284 3.2008-15.691 1.69-1.6242 4.0965-2.5823 4.0965-2.5823l321.39-123.29-310.34 148.94s-2.2396 0.81939-4.7499 1.0117c-3.4138 0.26152-10.536-0.8412-13.598-8.3893z" sodipodi:nodetypes="cccccsc" inkscape:transform-center-y="-61.75931" inkscape:connector-curvature="0" inkscape:transform-center-x="-153.00287" transform="rotate('.$fuel_level.', 635, -139)"/> <path id="path4282" sodipodi:rx="201.71242" sodipodi:ry="201.71242" style="stroke:#000000;stroke-width:1.25;fill:#ff6600" sodipodi:type="arc" d="m-17.835 734.84a201.71 201.71 0 1 1 -403.42 0 201.71 201.71 0 1 1 403.42 0z" transform="matrix(.0068034 0 0 .0068034 640.61 -140.74)" sodipodi:cy="734.83527" sodipodi:cx="-219.54707"/> </g > <metadata > <rdf:RDF > <cc:Work > <dc:format >image/svg+xml</dc:format > <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/> <cc:license rdf:resource="http://creativecommons.org/licenses/publicdomain/"/> <dc:publisher > <cc:Agent rdf:about="http://openclipart.org/" > <dc:title >Openclipart</dc:title > </cc:Agent > </dc:publisher > <dc:title >Fuel Gauge</dc:title > <dc:date >2012-10-29T23:53:16</dc:date > <dc:description >Just a fuel Gauge</dc:description > <dc:source >https://openclipart.org/detail/172934/fuel-gauge-by-etheraddict-172934</dc:source > <dc:creator > <cc:Agent > <dc:title >etheraddict</dc:title > </cc:Agent > </dc:creator > <dc:subject > <rdf:Bag > <rdf:li >Fuel</rdf:li > <rdf:li >fuel gauge</rdf:li > </rdf:Bag > </dc:subject > </cc:Work > <cc:License rdf:about="http://creativecommons.org/licenses/publicdomain/" > <cc:permits rdf:resource="http://creativecommons.org/ns#Reproduction"/> <cc:permits rdf:resource="http://creativecommons.org/ns#Distribution"/> <cc:permits rdf:resource="http://creativecommons.org/ns#DerivativeWorks"/> </cc:License > </rdf:RDF > </metadata ></svg>';

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
