<?php
namespace Simcify\Controllers;

use Simcify\File;
use Simcify\Auth;
use Simcify\Database;
use DotEnvWriter\DotEnvWriter;

class Settings {
    
    /**
     * Render settings page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title      = "Settings";
        $user       = Auth::user();
        $company    = Database::table("companies")->where("id", $user->company)->first();
        $timezones  = Database::table("timezones")->get();
        $currencies = Database::table("currencies")->get();
        $countries  = Database::table("countries")->get();
        
        return view('settings', compact("user", "title", "company", "timezones", "currencies", "countries"));
        
    }
    
    /**
     * Update profile infomation
     * 
     * @return Json
     */
    public function updateprofile() {
        
        $user = Auth::user();
        
        $account = Database::table("users")->where("email", input("email"))->first();
        if (!empty($account) && $account->id != $user->id) {
            return response()->json(responder("error", "Oops", input("email") . " already exists."));
        }
        
        $data = array(
            "fname" => escape(input("fname")),
            "lname" => escape(input("lname")),
            "email" => escape(input("email")),
            "phonenumber" => escape(input("phonenumber")),
            "address" => escape(input("address"))
        );
        
        Database::table("users")->where("id", $user->id)->update($data);
        return response()->json(responder("success", "Alright", "Profile successfully updated", "reload()"));
        
    }
    
    /**
     * Update company on settings page
     * 
     * @return Json
     */
    public function updatecompany() {
        
        $user = Auth::user();
        
        $data = array(
            "name" => escape(input("name")),
            "email" => escape(input("email")),
            "country" => escape(input("country")),
            "phone" => escape(input("phone")),
            "address" => escape(input("address")),
            "city" => escape(input("city")),
            "currency" => escape(input("currency")),
            "payment_details" => escape(input("payment_details")),
            "kra_pin" => escape(input("kra_pin")),
            "vat" => escape(input("vat")),
            "quote_disclaimer" => escape(input("quote_disclaimer")),
            "invoice_disclaimer" => escape(input("invoice_disclaimer")),
            "timezone" => escape(input("timezone"))
        );

        if (!empty(input("sms_tasks"))) {
            $data["sms_tasks"] = "Enabled";
        }else{
            $data["sms_tasks"] = "Disabled";
        }
        
        if (!empty(input("insurance"))) {
            $data["insurance"] = "Enabled";
        }else{
            $data["insurance"] = "Disabled";
        }

        if (!empty(input("parts_to_inventory"))) {
            $data["parts_to_inventory"] = "Enabled";
        }else{
            $data["parts_to_inventory"] = "Disabled";
        }

        if (!empty(input("forced_completion"))) {
            $data["forced_completion"] = "Enabled";
        }else{
            $data["forced_completion"] = "Disabled";
        }

        if (!empty(input("forced_checkout"))) {
            $data["forced_checkout"] = "Enabled";
        }else{
            $data["forced_checkout"] = "Disabled";
        }

        if (!empty(input("restricted_checkout"))) {
            $data["restricted_checkout"] = "Enabled";
        }else{
            $data["restricted_checkout"] = "Disabled";
        }

        if (!empty(input("invoice_signing"))) {
            $data["invoice_signing"] = "Enabled";
        }else{
            $data["invoice_signing"] = "Disabled";
        }

        if (!empty(input("quote_signing"))) {
            $data["quote_signing"] = "Enabled";
        }else{
            $data["quote_signing"] = "Disabled";
        }
        
        if (!empty(input("logo"))) {
            
            $upload = File::upload(input("logo"), "logos", array(
                "source" => "base64",
                "extension" => "png"
            ));
            
            if ($upload['status'] == "success") {
                if (!empty($company->logo)) {
                    File::delete($company->logo, "logos");
                }
                $data["logo"] = $upload['info']['name'];
            }
            
        }
        
        Database::table("companies")->where("id", $user->company)->update($data);
        return response()->json(responder("success", "Alright", "Company settings successfully updated", "reload()"));
        
    }
    
    /**
     * Update company booking form
     * 
     * @return Json
     */
    public function updatebooking() {
        
        $user = Auth::user();
        
        $data = array(
            "booking_form" => escape(input("booking_form")),
            "booking_disclaimer_in" => escape(input("booking_disclaimer_in")),
            "booking_disclaimer_out" => escape(input("booking_disclaimer_out")),
            "booking_terms" => escape(input("booking_terms"))
        );

        if (!empty(input("car_diagram"))) {
            $data["car_diagram"] = "Enabled";
        }else{
            $data["car_diagram"] = "Disabled";
        }

        Database::table("companies")->where("id", $user->company)->update($data);
        return response()->json(responder("success", "Alright", "Booking form settings successfully updated", "reload()"));
        
    }
    
    
    /**
     * Update System settings
     * 
     * @return Json
     */
    public function updatesystem() {

        $envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
        $env = new DotEnvWriter($envPath);
        $env->castBooleans();

        $options = array("APP_NAME", "AFRICASTALKING_USERNAME", "AFRICASTALKING_KEY","AFRICASTALKING_SENDERID","MAIL_USERNAME","MAIL_SENDER","SMTP_HOST","SMTP_PASSWORD","SMTP_PORT","MAIL_ENCRYPTION");

        foreach ($options as $option) {
            $env->set($option, input($option));
        }
        $env->save();

        return response()->json(responder("success", "Alright", "System settings successfully updated", "reload()"));
        
    }
    
    
    /**
     * Update password on settings page
     * 
     * @return Json
     */
    public function updatepassword() {
        
        $user = Auth::user();
        if (hash_compare($user->password, Auth::password(input("current")))) {
            Database::table(config('auth.table'))->where("id", $user->id)->update(array(
                "password" => Auth::password(input("password"))
            ));
            return response()->json(responder("success", "Alright", "Password successfully updated", "reload()"));
        } else {
            return response()->json(responder("error", "Oops", "You have entered an incorrect password."));
        }
        
    }
    
    
}