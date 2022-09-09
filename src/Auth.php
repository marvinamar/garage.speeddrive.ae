<?php
namespace Simcify;

use Simcify\Str;

class Auth {
    
    /**
     * Authenicate a user
     * 
     * @param   \Std    $user
     * @param   boolean $remember
     * @return  void
     */
    public static function authenticate($user, $remember = false) {
        session(config('auth.session'), $user->id);
        
        if(!empty($user->remember_token)){
            $remember_token = $user->remember_token;
        }else{
            $remember_token = Str::random();
        }
        
        Database::table("users")->where("id", $user->id)->update(array(
            "remember_token" => $remember_token
        ));
        setcookie("remember_token", $remember_token, time() + 2592000, '/');
        
    }

    
    /**
     * Check if the user is authenticated
     * 
     * @return  void
     */
    public static function check() {
        return session()->has(config('auth.session'));
    }
    
    /**
     * Log out the authenticated user
     * 
     * @return  void
     */
    public static function deauthenticate() {
        if(isset($_COOKIE['remember_token'])) {
            cookie('remember_token', '', -7);
        }
        session()->flush();
    }

    /**
     * Create a valid password
     * 
     * @param   string  $string
     * @return  string
     */
    public static function password($str) {
        return hash_hmac('sha256', $str, config('auth.secret'));
    }

    /**
     * Remember a user
     * 
     * @return  void
     */
    public static function remember() {
        if ( !static::check() && !is_null(cookie('remember_token')) ) {
            $remember_token = cookie('remember_token');
            $user = Database::table(config('auth.table'))->where("remember_token", $remember_token)->first();
            if ( is_object($user) ) {
                static::authenticate($user);
            } else {
                // static::deauthenticate();
            }
        }
    }

    
    /**
     * Get the authenticated user
     * 
     * @return \Std
     */
    public static function user() {
        $user = Database::table(config('auth.table'))->find(session(config('auth.session')) + 0);
        if (!empty($user)) {
            $user->parent = Database::table("companies")->where("id", $user->company)->first();
            $user->unpaidparts = Database::table('expenses')->where('company', $user->company)->where('paid', "No")->count("id", "total")[0]->total;
            $user->expectedparts = Database::table('expenses')->where('company', $user->company)->where("status","!=" ,"Delivered")->count("id", "total")[0]->total;
            $user->pendingtasks = Database::table('tasks')->where('company', $user->company)->where("status" ,"In Progress")->count("id", "total")[0]->total;
            $user->receiveables = Database::table('inventory')->where('company', $user->company)->where("received" ,"No")->count("id", "total")[0]->total;
            $user->issueables = Database::table('inventory')->where('company', $user->company)->where("project_specific" ,"Yes")->where("received" ,"Yes")->where("quantity",">" ,"0")->count("id", "total")[0]->total;
        }

        return $user;
    }
    
    /**
     * Login a user
     * 
     * @param string $username
     * @param password $password
     * @param string $options
     * @return mixed
     */
    public static function login($username, $password, $options = array()) {
        $givenPassword = self::password($password);
        $user = Database::table(config('auth.table'))->where("email",$username)->where("role","Owner")->orWhere("email",$username)->where("role","Manager")->orWhere("email",$username)->where("role","Inventory Manager")->orWhere("email",$username)->where("role","Booking Manager")->first();

        if (!empty($user)) {
            if (isset($options["status"])) {
                $statusColumnName = config('auth.statusColumn');
                if ($options["status"] != $user->$statusColumnName) {
                    return array(
                        "status" => "error",
                        "title" => "Account inactive",
                        "message" => "Your account is not active."
                    );
                }
            }

            $passwordColumn = config('auth.passwordColumn');
            if(hash_compare($user->$passwordColumn, self::password($password))){
                if (isset($options["rememberme"]) && $options["rememberme"]) {
                    self::authenticate($user, true);
                }else{
                    self::authenticate($user);
                }

                if ($user->role == "Booking Manager") {
                    $options['redirect'] = url("Projects@get");
                }elseif ($user->role == "Inventory Manager") {
                    $options['redirect'] = url("Inventory@get");
                }

                if (isset($options['redirect'])) {
                    $response = array(
                        "status" => "success",
                        "notify" => false,
                        "callback" => "redirect('".$options['redirect']."', true);"
                    );
                }else{
                    $response = array(
                        "status" => "success",
                        "title" => "Login Successful",
                        "message" => "You have been logged in successfully"
                    );
                }
                
            }else{
                $response = array(
                    "status" => "error",
                    "title" => "Incorrect Credentials",
                    "message" => "Incorrect username or password"
                );
            }
        }else{
            $response = array(
                "status" => "error",
                "title" => "User not found",
                "message" => "Incorrect username or password"
            );
        }

        return $response;
    }
   
       /**
     * Create new user
     * 
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public static function createUser($data, $options = array()) {
        if (isset($options['uniqueEmail'])) {
            $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'),$options["uniqueEmail"])->first();
            if (!empty($user)) {
                return array(
                    "status" => "error",
                    "title" => "Email Already exists.",
                    "message" => "Email Already exists."
                );
            }
        }

        $insert = Database::table(config('auth.table'))->insert($data);
        $newUserId = Database::table(config('auth.table'))->insertId();

        return true;
    }

    /**
     * Sign up new user
     * 
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public static function signup($data, $options = array()) {
        if (isset($options['uniqueEmail'])) {
            $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'),$options["uniqueEmail"])->first();
            if (!empty($user)) {
                return array(
                    "status" => "error",
                    "title" => "Email Already exists.",
                    "message" => "Email Already exists."
                );
            }
        }

        $insert = Database::table(config('auth.table'))->insert($data);
        $newUserId = Database::table(config('auth.table'))->insertId();

        if (isset($options["authenticate"]) AND $options["authenticate"]) {
            $user = Database::table(config('auth.table'))->where("id",$newUserId)->first();
            self::authenticate($user);
        }

        if (isset($options['redirect'])) {
            $response = array(
                "status" => "success",
                "notify" => false,
                "callback" => "redirect('".$options['redirect']."', true);"
            );
        }else{
            $response = array(
                "status" => "success",
                "title" => "Sign up Successful",
                "message" => "Your account was created successfully"
            );
        }

        return $response;
    }

    /**
     * forgot password
     * 
     * @param string $email
     * @param string $resetlink
     * @return mixed
     */
    public static function forgot($email, $resetlink) {
        $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'),$email)->first();
        if (!empty($user)) {

            $token = Str::random(32);
            $data = array(config('auth.passwordTokenColumn') => $token);
            $update = Database::table(config('auth.table'))->where(config('auth.emailColumn') ,$email)->update($data);
            $resetLink = str_replace("[token]", $token, $resetlink);

            $send = Mail::send(
                $email,
                env("APP_NAME")." Password Reset.",
                array(
                    "title" => "Password Reset",
                    "subtitle" => "Click the the button below to reset your password.",
                    "buttonText" => "Reset Password",
                    "buttonLink" => $resetLink,
                    "message" => "Someone hopefully you has requested a password reset on your account. If it is you go ahead and reset your password, if not please ignore this email."
                ),
                "withbutton"
            );

            if ($send) {
                    $response = array(
                        "status" => "success",
                        "title" => "Email sent!",
                        "message" => "Email with reset instructions successfully sent!",
                        "callback" => "redirect('".url("Auth@get")."')"
                    );
            }else{
                    $response = array(
                        "status" => "error",
                        "title" => "Failed to reset",
                        "message" => $send->ErrorInfo
                    );
            }
        }else{
            $response = array(
                "status" => "error",
                "title" => "Account not found",
                "message" => "Account with this email was not found"
            );
        }

        return $response;

    }
    
    /**
     * reset password
     * 
     * @param string $token
     * @param string $password
     * @return mixed
     */
    public static function reset($token, $password) {
        $user = Database::table(config('auth.table'))->where(config('auth.passwordTokenColumn'),$token)->first();
        if (!empty($user)) {
            $data = array(config('auth.passwordTokenColumn') => "" , config('auth.passwordColumn') => self::password($password));
            $update = Database::table(config('auth.table'))->where("id",$user->id)->update($data);

            if ($update) {
                    $response = array(
                        "status" => "success",
                        "title" => "Password reset!",
                        "message" => "Password successfully reset!",
                        "callback" => "redirect('".url("Auth@get")."', true)"
                    );
            }else{
                    $response = array(
                        "status" => "error",
                        "title" => "Failed to reset",
                        "message" => "Failed to reset password, please try again"
                    );
            }
        }else{
            $response = array(
                "status" => "error",
                "title" => "Token Mismatch",
                "message" => "Token not found or expired."
            );
        }

        return $response;

    }
}