<?php
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Notes {
    
    /**
     * Add a note
     * 
     * @return Json
     */
    public function create() {
        
        $user = Auth::user();
        
        $data = array(
            "company" => $user->company,
            "type" => escape(input('type')),
            "item" => escape(input('item')),
            "note" => escape(input('note'))
        );
        Database::table('notes')->insert($data);
        
        return response()->json(responder("success", "Alright!", "Note successfully added.", "reload()"));
        
    }
    
    
    /**
     * Delete a note
     * 
     * @return Json
     */
    public function delete() {
        
        $user = Auth::user();
        Database::table('notes')->where('id', input('noteid'))->where('company', $user->company)->delete();
        
        return response()->json(responder("success", "Alright!", "Note successfully deleted.", "reload()"));
        
    }
    
}