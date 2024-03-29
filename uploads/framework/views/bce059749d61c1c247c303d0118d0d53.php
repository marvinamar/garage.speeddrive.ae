<?php global $s_v_data, $user, $title, $widgets, $projects, $tasks, $income; ?>
<?= view( 'includes/head', $s_v_data ); ?>

<body class="nk-body bg-lighter npc-default has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <?= view( 'includes/sidebar', $s_v_data ); ?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <?= view( 'includes/header', $s_v_data ); ?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Overview</h3>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li>
                                                            <a href="<?=  url('Clients@get') ; ?>" class="btn btn-white btn-dim btn-primary"><em class="d-none d-sm-inline icon ni ni-users-fill"></em><span>View Clients</span> </a>
                                                        </li>
                                                        <li class="nk-block-tools-opt">
                                                            <a href="<?=  url('Projects@get') ; ?>" class="btn btn-primary"><em class="icon ni ni-note-add-fill"></em><span>Projects</span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                
                                <?php if (false && (strtotime($user->parent->subscription_end) - time()) < 345600) { ?>
                                <div class="mb-3">
                                    <a href="<?=  url('Billing@get') ; ?>">
                                        <div class="alert alert-warning alert-icon">
                                            <em class="icon ni ni-alert-circle"></em> 
                                            <strong>Heads up!</strong> Your subscriptions expires in <?=  timeLeft(strtotime($user->parent->subscription_end)) ; ?>. Renew to avoid service interuption.
                                        </div>
                                    </a>
                                </div>
                                <?php } ?>
                                
                                <div class="nk-block">
                                    <div class="row g-gs">
                                        <div class="col-md-6">
                                            <div class="row g-gs">

                                                <!-- stats row one -->
                                                <div class="col-md-6">
                                                    <div class="card border-top border-success">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Unpaid Invoices <sup class="text-muted"><?=  currency($user->parent->currency) ; ?></sup></h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount"><?=  number_format($widgets["unpaidinvoices"], 2) ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-danger"><em class="icon ni ni-cc"></em> <?=  money($widgets["overdueinvoices"], $user->parent->currency) ; ?></span><span> Overdue</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->
                                                <div class="col-md-6">
                                                    <div class="card border-top border-success">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Active Projects</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  $widgets["activeprojects"] ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-cards"></em> <?=  $widgets["completedprojects"] ; ?></span><span> Completed</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->
                                                <div class="col-md-6">
                                                    <div class="card border-top border-success">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Pending Tasks</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  $widgets["pendingtasks"] ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change up text-success"><em class="icon ni ni-todo"></em><?=  $widgets["completedtasks"] ; ?></span><span> Completed</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->
                                                <div class="col-md-6">
                                                    <div class="card border-top border-success">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Income <?=  date("M") ; ?> <sup class="text-muted"><?=  currency($user->parent->currency) ; ?></sup></h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold">
                                                                            <?=  number_format($widgets["incomethismonth"], 2) ; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-align-left"></em><?=  $widgets["paymentsthismonth"] ; ?></span><span> payments</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->

                                                <div class="col-md-6">
                                                    <div class="card border-top border-success">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Profits <?=  date("M") ; ?> <sup class="text-muted"><?=  currency($user->parent->currency) ; ?></sup></h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  number_format($widgets["profitsthismonth_partsandexpense"], 2) ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-check-circle-cut"></em></span><span> Parts and Expenses</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->

                                                <div class="col-md-6">
                                                    <div class="card border-top border-success">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Profits <?=  date("M") ; ?> <sup class="text-muted"><?=  currency($user->parent->currency) ; ?></sup></h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  number_format($widgets["profitsthismonth_service"], 2) ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-check-circle-cut"></em></span><span>Services</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->
                                            </div>
                                        </div>

                                        <!-- Project stats -->
                                        <div class="col-md-6">
                                            <div class="card card-full overflow-hidden border-top border-success">
                                                <div class="nk-ecwg nk-ecwg7 h-100">
                                                    <div class="card-inner flex-grow-1">
                                                        <div class="card-title-group mb-4">
                                                            <div class="card-title">
                                                                <h6 class="title">Project Statistics</h6>
                                                            </div>
                                                        </div>
                                                        <div class="nk-ecwg7-ck">
                                                            <canvas class="statistics" id="projectStatistics"></canvas>
                                                        </div>
                                                        <ul class="nk-ecwg7-legends">
                                                            <li>
                                                                <div class="title">
                                                                    <span class="dot dot-lg sq" data-bg="#0fac81"></span>
                                                                    <span>Completed</span>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="title">
                                                                    <span class="dot dot-lg sq" data-bg="#FFBB00"></span>
                                                                    <span>In Progress</span>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="title">
                                                                    <span class="dot dot-lg sq" data-bg="#e85347"></span>
                                                                    <span>Booked In</span>
                                                                </div>
                                                            </li>
                                                            <li style="display: none;">
                                                                <div class="title">
                                                                    <span class="dot dot-lg sq" data-bg="#f1f3f5"></span>
                                                                    <span>Cancelled</span>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div><!-- .card-inner -->
                                                </div>
                                            </div><!-- .card -->
                                        </div><!-- .col -->


                                    </div>
                                    <div class="row g-gs pt-3">

                                        <!-- Tasks stats -->
                                        <div class="col-md-6">
                                            <div class="card card-full overflow-hidden border-top border-primary">
                                                <div class="nk-ecwg nk-ecwg7 h-100">
                                                    <div class="card-inner flex-grow-1">
                                                        <div class="card-title-group mb-4">
                                                            <div class="card-title">
                                                                <h6 class="title">Task Statistics</h6>
                                                            </div>
                                                        </div>
                                                        <div class="nk-ecwg7-ck">
                                                            <canvas class="statistics" id="taskStatistics"></canvas>
                                                        </div>
                                                        <ul class="nk-ecwg7-legends">
                                                            <li>
                                                                <div class="title">
                                                                    <span class="dot dot-lg sq" data-bg="#0fac81"></span>
                                                                    <span>Completed</span>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="title">
                                                                    <span class="dot dot-lg sq" data-bg="#FFBB00"></span>
                                                                    <span>In Progress</span>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="title">
                                                                    <span class="dot dot-lg sq" data-bg="#f1f3f5"></span>
                                                                    <span>Cancelled</span>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div><!-- .card-inner -->
                                                </div>
                                            </div><!-- .card -->
                                        </div><!-- .col -->
                                        <div class="col-md-6">
                                            <div class="row g-gs">

                                                <!-- stats row one -->
                                                <div class="col-md-12">
                                                    <div class="card border-top border-primary">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Income <?=  date("Y") ; ?> <sup class="text-muted"><?=  currency($user->parent->currency) ; ?></sup></h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  number_format($widgets["incomethisyear"], 2) ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-check-circle-cut"></em></span><span> Payments this year</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->
                                                <div class="col-md-6">
                                                    <div class="card border-top border-primary">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Profits <?=  date("Y") ; ?> <sup class="text-muted"><?=  currency($user->parent->currency) ; ?></sup></h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  number_format($widgets["profits_partsandexpense"], 2) ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-check-circle-cut"></em></span><span>Parts and Expenses</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->

                                                <div class="col-md-6">
                                                    <div class="card border-top border-primary">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Profits <?=  date("Y") ; ?> <sup class="text-muted"><?=  currency($user->parent->currency) ; ?></sup></h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  number_format($widgets["profit_service"], 2) ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-check-circle-cut"></em></span><span>Services</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->

                                                <div class="col-md-6">
                                                    <div class="card border-top border-primary">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Total Clients</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  number_format($widgets["totalclients"]) ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-check-circle-cut"></em></span><span> Active clients</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->
                                                <div class="col-md-6">
                                                    <div class="card border-top border-primary">
                                                        <div class="nk-ecwg nk-ecwg6">
                                                            <div class="card-inner">
                                                                <div class="card-title-group">
                                                                    <div class="card-title">
                                                                        <h6 class="title">Total Staff</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="data">
                                                                    <div class="data-group">
                                                                        <div class="amount fw-bold"><?=  number_format($widgets["totalstaff"]) ; ?></div>
                                                                    </div>
                                                                    <div class="info"><span class="change text-success"><em class="icon ni ni-check-circle-cut"></em></span><span> Active & Inactive Staff</span></div>
                                                                </div>
                                                            </div><!-- .card-inner -->
                                                        </div><!-- .nk-ecwg -->
                                                    </div><!-- .card -->
                                                </div><!-- .col -->
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row g-gs pt-3">

                                        <div class="col-xxl-6">
                                            <div class="card card-full">
                                                <div class="nk-ecwg nk-ecwg8 h-100">
                                                    <div class="card-inner">
                                                        <div class="card-title-group mb-3">
                                                            <div class="card-title">
                                                                <h6 class="title">Payments last 12 months</h6>
                                                            </div>
                                                        </div>
                                                        <ul class="nk-ecwg8-legends">
                                                            <li>
                                                                <div class="title">
                                                                    <span class="dot dot-lg sq" data-bg="#0fac81"></span>
                                                                    <span>Payments</span>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                        <div class="">
                                                            <canvas class="line-chart" id="userGrowth" height="300"></canvas>
                                                        </div>
                                                    </div><!-- .card-inner -->
                                                </div>
                                            </div><!-- .card -->
                                        </div><!-- .col -->

                                    </div><!-- .row -->
                                </div><!-- .nk-block -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->
                <?= view( 'includes/footer', $s_v_data ); ?>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->

    <!-- Js Data -->
    <script type="text/javascript">
        var currency = "<?=  $user->parent->currency ; ?>";

        var projectData = [<?=  $projects["complete"] ; ?>, <?=  $projects["progress"] ; ?>, <?=  $projects["bookedin"] ; ?>, <?=  $projects["cancelled"] ; ?>];
        var tasksData = [<?=  $tasks["complete"] ; ?>, <?=  $tasks["progress"] ; ?>, <?=  $tasks["cancelled"] ; ?>];

        var amount = ["<?=  implode('", "', $income['amount']) ; ?>"];
        var labels = ["<?=  implode('", "', $income['label']) ; ?>"];
    </script>


    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
    <script src="<?=  asset('assets/js/charts/statistics.js') ; ?>"></script>
</body>

</html>
<?php return;
