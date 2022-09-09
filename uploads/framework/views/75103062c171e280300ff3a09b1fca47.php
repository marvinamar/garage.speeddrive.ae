<?php global $s_v_data; ?>
<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Dotted Craft Limited">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?=  env('APP_NAME') ; ?> | Auto Garage Management System">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="<?=  asset('assets/images/favicon.png') ; ?>">
    <!-- Page Title  -->
    <title>Forgot Password | Auto Garage Management System</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="<?=  asset('assets/css/app.css') ; ?>">
    <link rel="stylesheet" href="<?=  asset('assets/css/theme.css') ; ?>">
    <link rel="stylesheet" href="<?=  asset('assets/css/simcify.min.css') ; ?>">
    <link rel="stylesheet" href="<?=  asset('assets/css/asilify.css') ; ?>">

</head>

<body class="nk-body bg-white npc-general pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="<?=  url() ; ?>" class="logo-link">
                                <img class="logo-dark" src="<?=  asset('assets/images/logo-dark.png') ; ?>" alt="logo-dark">
                            </a>
                        </div>
                        <div class="card">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Reset password</h4>
                                        <div class="nk-block-des">
                                            <p>If you forgot your password, well, then we’ll email you instructions to reset your password.</p>
                                        </div>
                                    </div>
                                </div>
                                <form class="simcy-form" action="<?=  url('Auth@forgotvalidation') ; ?>" data-parsley-validate="" method="POST" loader="true">
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="default-01">Email Address</label>
                                        </div>
                                        <input type="email" class="form-control form-control-lg" name="email" placeholder="Enter your email address">
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-lg btn-primary btn-block">Send Reset Link</button>
                                    </div>
                                </form>

                                <div class="form-note-s2 text-center pt-4">
                                    <a href="<?=  url('Auth@get') ; ?>"><strong>Return to login</strong></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-footer nk-auth-footer-full">
                        <div class="container wide-lg">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <div class="nk-block-content text-center">
                                        <p class="text-soft">&copy; <?=  date("Y") ; ?> <?=  env("APP_NAME") ; ?> • All Rights Reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="<?=  asset('assets/js/bundle.js') ; ?>"></script>
    <script src="<?=  asset('assets/js/scripts.js') ; ?>"></script>
    <script src="<?=  asset('assets/js/simcify.min.js') ; ?>"></script>

</html>
<?php return;
