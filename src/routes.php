<?php

use Simcify\Router;
use Simcify\Exceptions\Handler;
use Simcify\Middleware\Authenticate;
use Simcify\Middleware\RedirectIfAuthenticated;

/**
 * ,------,
 * | NOTE | CSRF Tokens are checked on all PUT, POST and GET requests. It
 * '------' should be passed in a hidden field named "csrf-token" or a header
 *          (in the case of AJAX without credentials) called "X-CSRF-TOKEN"
 *  */
// Router::csrfVerifier(new BaseCsrfVerifier());

Router::group(array(
    // 'prefix' => '/' . env('URL_PREFIX'),
    'exceptionHandler' => Simcify\Exceptions\Handler::class,
), function() {
    
    Router::group(array(
        'middleware' => Simcify\Middleware\AuthenticateIfActive::class
    ), function() {
        
        // Overview
        Router::get('/', 'Overview@get');
        
        // Insurance
        Router::get('/insurance', 'Insurance@get');
        Router::post('/insurance/create', 'Insurance@create');
        Router::post('/insurance/update', 'Insurance@update');
        Router::get('/insurance/{insuranceid}/details', 'Insurance@details', array(
            'as' => 'insuranceid'
        ));
        Router::post('/insurance/update/view', 'Insurance@updateview');
        Router::post('/insurance/delete', 'Insurance@delete');
        
        // Clients
        Router::get('/clients', 'Clients@get');
        Router::post('/clients/create', 'Clients@create');
        Router::post('/clients/update', 'Clients@update');
        Router::get('/clients/{clientid}/details', 'Clients@details', array(
            'as' => 'clientid'
        ));
        Router::post('/clients/update/view', 'Clients@updateview');
        Router::post('/clients/delete', 'Clients@delete');
        
        // Projects
        Router::get('/projects', 'Projects@get');
        Router::post('/projects/create', 'Projects@create');
        Router::post('/projects/update', 'Projects@update');
        Router::post('/projects/models', 'Projects@models');
        Router::post('/projects/booking', 'Projects@booking');
        Router::get('/projects/{projectid}/{Isqt}/details', 'Projects@details', array(
            'as' => 'projectid',
        ));
        Router::post('/projects/update/view', 'Projects@updateview');
        Router::post('/projects/checkout', 'Projects@checkout');
        Router::post('/projects/cancel', 'Projects@cancel');
        Router::post('/projects/delete', 'Projects@delete');
        Router::get('/projects/{projectid}/{quoteid}/approve', 'Projects@approve');
        
        // Suppliers
        Router::get('/suppliers', 'Suppliers@get');
        Router::post('/suppliers/create', 'Suppliers@create');
        Router::post('/suppliers/update', 'Suppliers@update');
        Router::get('/suppliers/{supplierid}/details', 'Suppliers@details', array(
            'as' => 'supplierid'
        ));
        Router::post('/suppliers/update/view', 'Suppliers@updateview');
        Router::post('/suppliers/delete', 'Suppliers@delete');
        
        // Inventory
        Router::get('/inventory', 'Inventory@get');
        Router::post('/inventory/confirmreceipt', 'Inventory@confirmreceipt');
        Router::post('/inventory/create', 'Inventory@create');
        Router::post('/inventory/addstock', 'Inventory@addstock');
        Router::post('/inventory/update', 'Inventory@update');
        Router::get('/inventory/{inventoryid}/details', 'Inventory@details', array(
            'as' => 'inventoryid'
        ));
        Router::post('/inventory/update/view', 'Inventory@updateview');
        Router::post('/inventory/issue', 'Inventory@issue');
        Router::post('/inventory/issue/view', 'Inventory@issueview');
        Router::post('/inventory/delete', 'Inventory@delete');
        Router::get('/issueables', 'Inventory@issueables');
        Router::get('/receiveables', 'Inventory@receiveables');

        // Inventory report
        Router::get('/inventory/report/{inventoryid}/render', 'Inventoryreport@render', array(
            'as' => 'inventoryid'
        ));
        Router::get('/inventory/report/{inventoryid}/view', 'Inventoryreport@view', array(
            'as' => 'inventoryid'
        ));
        
        // Booking parts
        Router::get('/settings/bookingparts', 'Bookingparts@get');
        Router::post('/settings/bookingparts/create', 'Bookingparts@create');
        Router::post('/settings/bookingparts/update', 'Bookingparts@update');
        Router::post('/settings/bookingparts/update/view', 'Bookingparts@updateview');
        Router::post('/settings/bookingparts/delete', 'Bookingparts@delete');
        Router::post('/settings/bookingparts/reorder', 'Bookingparts@reorder');
        
        // Team
        Router::get('/team', 'Team@get');
        Router::post('/team/create', 'Team@create');
        Router::post('/team/update', 'Team@update');
        Router::get('/team/{teamid}/details', 'Team@details', array(
            'as' => 'teamid'
        ));
        Router::post('/team/update/view', 'Team@updateview');
        Router::post('/team/delete', 'Team@delete');
        
        // Team Payment
        Router::post('/team/payment/create', 'Teampayment@create');
        Router::post('/team/payment/update', 'Teampayment@update');
        Router::post('/team/payment/update/view', 'Teampayment@updateview');
        Router::post('/team/payment/delete', 'Teampayment@delete');
        
        // Tasks
        Router::post('/tasks/create', 'Tasks@create');
        Router::post('/tasks/addbulk', 'Tasks@addbulk');
        Router::post('/tasks/update', 'Tasks@update');
        Router::post('/tasks/update/view', 'Tasks@updateview');
        Router::get('/tasks/{taskid}/download', 'Tasks@download', array(
            'as' => 'taskid'
        ));
        Router::post('/tasks/cancel', 'Tasks@cancel');
        Router::post('/tasks/delete', 'Tasks@delete');
        Router::get('/tasks/pending', 'Tasks@pending');
        Router::post('/tasks/import/workrequested', 'Tasks@workrequested');
        Router::post('/tasks/import/jobcards', 'Tasks@jobcards');
        
        // Job cards
        Router::post('/jobcards/create', 'Jobcards@create');
        Router::post('/jobcards/create/form', 'Jobcards@createform');
        Router::post('/jobcards/update', 'Jobcards@update');
        Router::post('/jobcards/update/view', 'Jobcards@updateview');
        Router::get('/jobcards/{jobcardid}/render', 'Jobcards@render', array(
            'as' => 'jobcardid'
        ));
        Router::get('/jobcards/{jobcardid}/view', 'Jobcards@view', array(
            'as' => 'jobcardid'
        ));
        Router::post('/jobcards/send', 'Jobcards@send');
        Router::post('/jobcards/delete', 'Jobcards@delete');
        
        // Expenses
        Router::post('/expenses/create', 'Expenses@create');
        Router::post('/expenses/addbulk', 'Expenses@addbulk');
        Router::post('/expenses/update', 'Expenses@update');
        Router::post('/expenses/update/view', 'Expenses@updateview');
        Router::post('/expenses/delete', 'Expenses@delete');
        Router::get('/parts/expected', 'Expenses@expected');
        Router::get('/parts/unpaid', 'Expenses@unpaid');
        Router::post('/expenses/import/workrequested', 'Expenses@workrequested');
        Router::post('/expenses/import/jobcards', 'Expenses@jobcards');
        
        // Quote
        Router::get('quotes', 'Quote@get');
        Router::post('/quotes/create', 'Quote@create');
        Router::post('/quotes/create/form', 'Quote@createform');
        Router::post('/quotes/update', 'Quote@update');
        Router::post('/quotes/update/view', 'Quote@updateview');
        Router::post('/quotes/delete', 'Quote@delete');
        Router::post('/quotes/send', 'Quote@send');
        Router::post('/quotes/sign', 'Quote@sign');
        Router::post('/quotes/convert', 'Quote@convert');
        Router::get('/quotes/{quoteid}/render', 'Quote@render', array(
            'as' => 'quoteid'
        ));
        Router::get('quotes/get_item_details/{itemid}','Quote@get_item_details',array(
            'as' => 'itemid'));
        Router::post('/quotes/create_atproject', 'Quote@create_at_project');
        Router::post('/quotes/update_atproject', 'Quote@update_at_project');
        Router::post('/quotes/update/view-v2', 'Quote@updateviewv2');
        Router::post('/quotes/delete_atproject', 'Quote@delete_at_project');
        
        
        // Invoice
        Router::get('invoices', 'Invoice@get');
        Router::post('/invoices/create', 'Invoice@create');
        Router::post('/invoices/create/form', 'Invoice@createform');
        Router::post('/invoices/update', 'Invoice@update');
        Router::post('/invoices/update/view', 'Invoice@updateview');
        Router::post('/invoices/delete', 'Invoice@delete');
        Router::post('/invoices/send', 'Invoice@send');
        Router::post('/invoices/sign', 'Invoice@sign');
        Router::post('/invoices/workrequested', 'Invoice@workrequested');
        Router::post('/invoices/jobcards', 'Invoice@jobcards');
        Router::post('/invoices/expenses', 'Invoice@expenses');
        Router::get('/invoices/{invoiceid}/render', 'Invoice@render', array(
            'as' => 'invoiceid'
        ));
        Router::get('/invoices/{invoiceid}/view', 'Invoice@view', array(
            'as' => 'invoiceid'
        ));

        // Booking
        Router::post('/booking/sign', 'Booking@sign');
        Router::post('/booking/exitsign', 'Booking@exitsign');
        Router::post('/booking/send', 'Booking@send');
        Router::post('/booking/checkout', 'Booking@checkout');
        Router::post('/booking/regenerate', 'Booking@regenerate');
        Router::get('/booking/{level}/fuellevel', 'Booking@fuellevel', array(
            'as' => 'level'
        ));
        Router::get('/booking/{projectid}/render', 'Booking@render', array(
            'as' => 'projectid'
        ));
        Router::get('/projects/{projectid}/booking', 'Booking@view', array(
            'as' => 'projectid'
        ));
        
        // Project Payments
        Router::get('/project/payments', 'Projectpayment@get');
        Router::post('/project/payments/create', 'Projectpayment@create');
        Router::post('/project/payments/update', 'Projectpayment@update');
        Router::post('/project/payments/update/view', 'Projectpayment@updateview');
        Router::post('/project/payments/delete', 'Projectpayment@delete');

        //s_payments
        Router::get('/project/supplierpayments', 'Supplierpayment@get');
        Router::post('/project/supplierpayments/create', 'Supplierpayment@create');
        Router::post('/project/supplierpayments/update', 'Supplierpayment@update');
        Router::post('/project/supplierpayments/delete', 'Supplierpayment@delete');
        Router::post('/project/supplierpayments/update/view', 'Supplierpayment@updateview');
        Router::get('/project/{id}/view', 'Supplierpayment@view', array(
            'as' => 'id'
        ));
        
        
        // Notes
        Router::post('/notes/create', 'Notes@create');
        Router::post('/notes/delete', 'Notes@delete');

        // Reports
        Router::get('/projects/{projectid}/report/render', 'Vehiclereport@render', array(
            'as' => 'projectid'
        ));
        Router::get('/projects/{projectid}/report/view', 'Vehiclereport@view', array(
            'as' => 'projectid'
        ));
        Router::get('/team/report', 'Teamreport@view');
        Router::get('/team/report/render', 'Teamreport@render');
        Router::get('/supplier/report', 'Supplierreport@view');
        Router::get('/supplier/report/render', 'Supplierreport@render');
        
        // Users
        Router::get('/users', 'Users@get');
        Router::post('/users/create', 'Users@create');
        Router::post('/users/update', 'Users@update');
        Router::post('/users/update/view', 'Users@updateview');
        Router::post('/users/delete', 'Users@delete');
        
        // Marketing
        Router::get('/marketing', 'Marketing@get');
        Router::get('/marketing/recipients', 'Marketing@recipients');
        Router::post('/marketing/create', 'Marketing@create');
        Router::post('/marketing/resend', 'Marketing@resend');
        Router::post('/marketing/delete', 'Marketing@delete');
        Router::post('/marketing/balance', 'Marketing@balance');
        Router::post('/marketing/delete/message', 'Marketing@deletemessage');
        
    });
    
    Router::group(array(
        'middleware' => Simcify\Middleware\Authenticate::class
    ), function() {
        
        // Billing
        Router::get('/billing', 'Billing@get');
        Router::get('/billing/payments', 'Billing@payments');
        Router::post('/billing/cancel', 'Billing@cancel');
        Router::get('/billing/{type}/transaction', 'Billing@verify', array(
            'as' => 'type'
        ));
        Router::post('/billing/payments/verify', 'Mpesa@verify');
        
        // Settings
        Router::get('settings', 'Settings@get');
        Router::post('/settings/update/profile', 'Settings@updateprofile');
        Router::post('/settings/update/company', 'Settings@updatecompany');
        Router::post('/settings/update/booking', 'Settings@updatebooking');
        Router::post('/settings/update/system', 'Settings@updatesystem');
        Router::post('/settings/update/password', 'Settings@updatepassword');
        
        // Auth
        Router::get('/signout', 'Auth@signout');
        
    });
    
    Router::group(array(
        'middleware' => Simcify\Middleware\RedirectIfAuthenticated::class
    ), function() {
        
        /**
         * No login Required for these pages
         **/
        Router::get('/signin', 'Auth@get');
        Router::get('/getstarted', 'Auth@getstarted');
        Router::post('/signin/authenticate', 'Auth@signin');
        Router::get('/forgot', 'Auth@forgot');
        Router::post('/forgot/validate', 'Auth@forgotvalidation');
        Router::post('/createaccount', 'Auth@createaccount');
        Router::post('/reset', 'Auth@reset');
        Router::get('/reset/{token}', 'Auth@resetpage', array(
            'as' => 'token'
        ));
        
    });
    
    Router::get('/authorize/{auth_token}', 'Auth@authorize', array(
        'as' => 'auth_token'
    ));
    Router::post('/payments/validate', 'Mpesa@validate');
    Router::post('/payments/confirm', 'Mpesa@confirm');
    Router::post('/payments/callback', 'Mpesa@callback');
    Router::post('/payments/stkpush', 'Mpesa@stkpush');
    Router::get('/payments/play', 'Mpesa@play');
    
    Router::get('/booking/{project_key}', 'Booking@print', array(
        'as' => 'project_key'
    ));
    
    Router::get('/404', function() {
        response()->httpCode(404);
        echo view();
    });
    
});