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

class Jobcards {
    
    /**
     * Create job card form view
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
        
        
        
        return view('modals/create-jobcard', compact("projects","user"));
        
    }
    
    
    /**
     * Create a task  
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', input("project"))->first();
        
        $data = array(
            "client" => $project->client,
            "company" => $user->company,
            "project" => escape(input('project')),
            "approved" => escape(input('approved'))
        );

        if (!empty($_POST["body_report"])) {
            $data["body_report"] = Asilify::array2json($_POST["body_report"]);
        }
        
        if (!empty($_POST["mechanical_report"])) {
            $data["mechanical_report"] = Asilify::array2json($_POST["mechanical_report"]);
        }
        
        if (!empty($_POST["electrical_report"])) {
            $data["electrical_report"] = Asilify::array2json($_POST["electrical_report"]);
        }

        if (!empty($project->insurance)) {
            $data["insurance"] = $project->insurance;
            unset($data["client"]);
        }

        if (!empty(input("jobcardid"))) {
            $data["assessment"] = escape(input('jobcardid'));
        }

        Database::table('jobcards')->insert($data);
        return response()->json(responder("success", "Alright!", "Job card successfully created.", "redirect('" . url('Projects@details', array(
            'projectid' => input('project'),
            'Isqt' => 'false'
        )) . "?view=jobcards')"));
        
    }
    
    
    /**
     * Job card update view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $jobcard = Database::table('jobcards')->where('company', $user->company)->where('id', input("jobcardid"))->first();

        $approved = Database::table('jobcards')->where('company', $user->company)->where('assessment', input("jobcardid"))->first();
        
        if (input("action") == "approved") {
            return view('modals/approved-jobcard', compact("jobcard","approved"));
        }else{
            return view('modals/update-jobcard', compact("jobcard"));
        }
        
        
    }
    
    /**
     * Update Job card
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "approved" => escape(input('approved'))
        );

        if (!empty($_POST["body_report"])) {
            $data["body_report"] = Asilify::array2json($_POST["body_report"]);
        }
        
        if (!empty($_POST["mechanical_report"])) {
            $data["mechanical_report"] = Asilify::array2json($_POST["mechanical_report"]);
        }
        
        if (!empty($_POST["electrical_report"])) {
            $data["electrical_report"] = Asilify::array2json($_POST["electrical_report"]);
        }
        
        Database::table('jobcards')->where('id', input('jobcardid'))->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Job card successfully updated.", "reload()"));
        
    }
    
    /**
     * Delete Job card
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();

        $jobcard = Database::table('jobcards')->where('company', $user->company)->where('id', input('jobcardid'))->first();
        Database::table('jobcards')->where('id', input('jobcardid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Job card successfully deleted.", "redirect('" . url('Projects@details', array(
            'projectid' => $jobcard->project,
            'Isqt' => 'false'
        )) . "?view=jobcards')"));
        
    }

    /**
     * View Job card
     * 
     * @return \Pecee\Http\Response
     */
    public function view($jobcardid) {

        $user   = Auth::user();
        $jobcard = Database::table('jobcards')->where('company', $user->company)->where('id', $jobcardid)->first();
        if (empty($jobcard)) {
            return view('errors/404');
        }

        $title = "Job card #".$jobcard->id;

        return view('view-jobcard', compact("title", "user","jobcard"));
        
    }
    
    /**
     * Send via email
     * 
     * @return Json
     */
    public function send() {
        
        $user   = Auth::user();
        $jobcard = Database::table('jobcards')->where('company', $user->company)->where('id', input("itemid"))->first();
        
        if (empty($jobcard)) {
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again."));
        }
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', $jobcard->project)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }


        $document = $this->generate($user, $jobcard, $project, $client, true);

        $send = Mail::send(
            input("email"),
            input("subject"),
            array(
                "message" => nl2br(input("message"))
            ),
            "basic",
            null,
            array("Job Card #".$jobcard->id.".pdf" => config("app.storage")."tmp/".$document)
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
     * Generate PDF
     * 
     * @return application/pdf
     */
    public function render($jobcardid) {
        
        $user   = Auth::user();
        $jobcard = Database::table('jobcards')->where('company', $user->company)->where('id', $jobcardid)->first();
        
        if (empty($jobcard)) {
            return view('errors/404');
        }
        
        $project = Database::table('projects')->where('company', $user->company)->where('id', $jobcard->project)->first();
        $client = Database::table('clients')->where('company', $user->company)->where('id', $project->client)->first();
        if(!empty($project->insurance)){
            $project->insurance = Database::table('insurance')->where('id', $project->insurance)->first();
        }


        $this->generate($user, $jobcard, $project, $client);

        die();
        
    }


    /**
     * Generate PDF Work Sheet
     * 
     * @return Json
     */
    public function generate($user, $jobcard, $project, $client, $save = false) {

        $outputName = uniqid("asilify_").".pdf";
        $outputPath = config("app.storage")."/tmp/". $outputName;
        
        $pdf = new PDF(null, 'px');
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(TRUE, 150);
        $pdf->AddPage();
        $pdf->company = $user->parent;

        if (!empty($project->work_requested)) {
            $project->work_requested = json_decode($project->work_requested);
        }else{
            $project->work_requested = array();
        }

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
        $pdf->MultiCell(200, 70, "JOB CARD", null, "L");


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
        $pdf->MultiCell(170, 25, "Job Card Details: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos += 20;
        $pdf->Line($xPos, $yPos, $xPos + 170, $yPos);

        $yPos += 5;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Job Card #: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, $jobcard->id, null, "R");
        $yPos += 15;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','B',10);
        $pdf->SetTextColor(82, 100, 132);
        $pdf->MultiCell(150, 70, "Created On: ", null, "L");
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(128, 148, 174);
        $pdf->MultiCell(170, 70, date("M j, Y", strtotime($jobcard->created_at)), null, "R");
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


        // Work Requested
        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY() + 25;

        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);
        $pdf->Line($xPos, $yPos + 3, $xPos + 539, $yPos + 3);


        $xPos = $pdf->GetX();
        $yPos = $pdf->GetY() + 60;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Work Requested / Owners Instructions: ", null, "L");

        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);
        $yPos = $pdf->GetY();
        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

        $xPos = $pdf->GetX();

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

        $yPos = $pdf->GetY() + 25;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Body Report: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);

        $yPos = $pdf->GetY();
        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

        $yPos = $pdf->GetY() + 12;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $body_report = array();
        if (!empty($jobcard->body_report)) {
            foreach (json_decode($jobcard->body_report) as $key => $item) {
                $body_report[] = "<li>".$item."</li>";
            }
            $body_report = "<ol>".implode(" ", $body_report)."</ol>";
        }else{
            $body_report = "";
        }
        $pdf->writeHTML(nl2br($body_report), true, false, true, false, '');


        // Mechanic's Report
        $yPos = $pdf->GetY() + 25;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Mechanical Report: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);

        $yPos = $pdf->GetY();
        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

        $yPos = $pdf->GetY() + 12;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $mechanical_report = array();
        if (!empty($jobcard->mechanical_report)) {
            foreach (json_decode($jobcard->mechanical_report) as $key => $item) {
                $mechanical_report[] = "<li>".$item."</li>";
            }
            $mechanical_report = "<ol>".implode(" ", $mechanical_report)."</ol>";
        }else{
            $mechanical_report = "";
        }
        $pdf->writeHTML(nl2br($mechanical_report), true, false, true, false, '');


        // Mechanic's Report
        $yPos = $pdf->GetY() + 25;
        $pdf->SetXY($xPos, $yPos);

        $pdf->SetFont('','B',12);
        $pdf->SetTextColor(9, 113, 254);
        $pdf->MultiCell(450, 25, "Electrical Report: ", null, "L");
        $pdf->SetDrawColor(229, 233, 243);
        $pdf->SetLineWidth(2);

        $yPos = $pdf->GetY();
        $pdf->Line($xPos, $yPos, $xPos + 539, $yPos);

        $yPos = $pdf->GetY() + 12;
        $pdf->SetXY($xPos, $yPos);
        $pdf->SetFont('','',10);
        $pdf->SetTextColor(82, 100, 132);
        $electrical_report = array();
        if (!empty($jobcard->electrical_report)) {
            foreach (json_decode($jobcard->electrical_report) as $key => $item) {
                $electrical_report[] = "<li>".$item."</li>";
            }
            $electrical_report = "<ol>".implode(" ", $electrical_report)."</ol>";
        }else{
            $electrical_report = "";
        }
        $pdf->writeHTML(nl2br($electrical_report), true, false, true, false, '');
        
        if ($save) {
            $pdf->Output($outputPath, 'F');
            return $outputName;
        }else{
            $pdf->Output("Job Card #".$jobcard->id.".pdf", 'I');
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