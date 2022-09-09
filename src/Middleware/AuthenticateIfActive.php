<?php
namespace Simcify\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Simcify\Database;
use Simcify\Auth;

class AuthenticateIfActive implements IMiddleware {

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http]Request
     */
    public function handle(Request $request) {

        Auth::remember();
        
        if (Auth::check()) {
            $request->user = Auth::user();
            $company = Database::table("companies")->where("id", $request->user->company)->first();

            // Set the locale to the user's preference
            config('app.locale.default', $request->user->{config('auth.locale')});
            date_default_timezone_set( $company->timezone );

            Database::table("companies")->where("id", $request->user->company)->update(array("last_activity" => "NOW()"));

            if ($company->subscription_status != "Active") {
            	$request->setRewriteUrl(url('Billing@get'));
            }

        } else {
            $request->setRewriteUrl(url('Auth@get'));
        }
        return $request;

    }
}
