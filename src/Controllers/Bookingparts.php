<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;
use Simcify\Mail;

class Bookingparts {
    
    /**
     * Render team page
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        
        $title = 'Booking Parts';
        $user  = Auth::user();
        
        $partslist = Database::table('partslist')->where('company', $user->company)->orderBy("indexing", true)->get();
        
        return view("bookingparts", compact("user", "title", "partslist"));
        
    }
    
    /**
     * Add a part
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        
        $data = array(
            "company" => $user->company,
            "name" => escape(input('name'))
        );

        if (!empty(input("has_input"))) {
            $data["has_input"] = escape(input("has_input"));
            $data["input_name"] = escape(input("input_name"));
        }

        Database::table("partslist")->insert($data);
        
        return response()->json(responder("success", "Alright!", "Part successfully added.", "reload()"));
        
    }
    
    
    /**
     * Update part view
     * 
     * @return \Pecee\Http\Response
     */
    public function updateview() {
        
        $user   = Auth::user();
        $part = Database::table('partslist')->where('company', $user->company)->where('id', input("partid"))->first();
        
        return view('modals/update-bookingpart', compact("part"));
        
    }
    
    /**
     * Update part
     * 
     * @return Json
     */
    public function update() {
        
        $user = Auth::user();
        
        $data = array(
            "name" => escape(input('name'))
        );

        if (!empty(input("has_input"))) {
            $data["has_input"] = escape(input("has_input"));
            $data["input_name"] = escape(input("input_name"));
        }else{
            $data["has_input"] = "No";
            $data["input_name"] = "NULL";
        }
        
        Database::table('partslist')->where('id', input('partid'))->where('company', $user->company)->update($data);
        return response()->json(responder("success", "Alright!", "Part successfully updated.", "reload()"));
        
    }
    
    /**
     * Delete part
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();

        Database::table('partslist')->where('id', input('partid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Part successfully deleted.", "reload()"));
        
    }
    

    /**
     * Update booking parts order
     * 
     * @return Json
     */
    public function reorder() {

        foreach (input()->post as $field) {

            $key = explode("part_", $field->index);
            
            Database::table("partslist")->where("id", $key[1])->update(array(
                "indexing" => $field->value
            ));

        }

        die();

    }
    
}