<?php
namespace Simcify\Controllers;

use Simcify\Auth as Authenticate;
use Pecee\Http\Request;
use Simcify\Database;
use Simcify\Asilify;
use Simcify\Mail;
use Simcify\Str;

class Auth {
    
    /**
     * Render login page view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        return view('auth-login');
    }
    
    /**
     * Render getstarted page view
     * 
     * @return \Pecee\Http\Response
     */
    public function getstarted() {
        return view('errors/404');
        return view('auth-getstarted');
    }
    
    /**
     * User signin
     * 
     * @return Json
     */
    public function signin() {
        $signin = Authenticate::login(input('email'), input('password'), array(
            "rememberme" => true,
            "redirect" => url(''),
            "status" => "Active"
        ));
        
        return response()->json($signin);
    }
    
    /**
     * Super admin auto login
     * 
     * @return Json
     */
    public function authorize($auth_token) {

        $user = Database::table('users')->where("auth_token",$auth_token)->first();

        if (empty($user)) {
            return response()->json(responder("error", "Access Denied!", "Auth token not found."));
        }

        Database::table('users')->where("id", $owner->id)->update(array(
            "auth_token" => "NULL"
        ));

        Authenticate::authenticate($user);

        redirect(url(''));

        die();
        
    }
    
    /**
     * Render forgot password page view
     * 
     * @return \Pecee\Http\Response
     */
    public function forgot() {
        return view('auth-forgot');
    }
    
    /**
     * Forgot password - send reset password email
     * 
     * @return Json
     */
    public function forgotvalidation() {
        $forgot = Authenticate::forgot(input('email'), env('APP_URL') . "/reset/[token]");
        return response()->json($forgot);
    }
    
    /**
     * Create a user account
     * 
     * @return Json
     */
    public function createaccount() {

        $account = Database::table('users')->where('email', input("email"))->first();
        if (!empty($account)) {
            return response()->json(responder("error", "Hmmm!", "An account already exists with this email ".input("email")));
        }

        $company = array(
            "name" => escape(input('company')),
            "email" => escape(input('email')),
            "phone" => escape(input('phonenumber')),
            "subscription_start" => date("Y-m-d"),
            "subscription_end" => date('Y-m-d', strtotime(date("Y-m-d"). ' + 14 days'))
        );

        Database::table('companies')->insert($company);
        $companyid = Database::table('companies')->insertId();

        if (empty($companyid)) {
            return response()->json(responder("error", "Hmmm!", "Something went wrong, please try again and contact support if issue persists."));
        }

        $create = array(
            "company" => $companyid,
            "fname" => escape(input('fname')),
            "lname" => escape(input('lname')),
            "email" => escape(input('email')),
            "phonenumber" => escape(input('phonenumber')),
            "password" => Authenticate::password(input('password')),
            "role" => "Owner"
        );

        Database::table('users')->insert($create);
        $userid = Database::table('users')->insertId();

        $user = Database::table("users")->where("id",$userid)->first();
        Authenticate::authenticate($user);

        Mail::send(
            input('email'),
            "You're in Asilify :)",
            array(
                "message" => "Thank you for signing up, ".input('fname')."<br><br>We built https://asilify.com to help Auto Garages all over the world automate their daily operations while increasing perfomance and accountability. We're here to ensure your success and increased perfomance. https://asilify.com is for you.<br><br>All the best,<br><br>Daniel K.<br>Founder Asilify"
            )
        );

        Asilify::syncbookingparts($companyid);

        return response()->json(responder("success", "Account Created!", "Your account was successfully created.","redirect('".url("Settings@get")."', true)", false));
        
    }
    
    
    /**
     * signout User
     * 
     * @return \Pecee\Http\Response
     */
    public function signout() {
        Authenticate::deauthenticate();
        redirect(url('Auth@get'));
    }
    
    
    /**
     * Get reset password page
     * 
     * @return \Pecee\Http\Response
     */
    public function resetpage($token) {
        return view('auth-reset', compact("token"));
    }
    
    /**
     * Reset password
     * 
     * @return Json
     */
    public function reset() {
        $reset = Authenticate::reset(input('token'), input('password'));
        return response()->json($reset);
    }
    
    
}