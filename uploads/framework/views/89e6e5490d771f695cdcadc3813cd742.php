<?php global $s_v_data, $user, $title, $client, $notes, $project, $staffmembers, $tasks, $expenses, $quotes, $invoices, $payments, $jobcards, $suppliers, $inventory, $Isqt, $pay_expenses; ?>
<?= view( 'includes/head', $s_v_data ); ?>
<link rel="stylesheet" href="<?=  asset('assets/libs/summernote/summernote-lite.min.css') ; ?>" />

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
                                    <div class="nk-block-between g-3">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Projects / <strong class="text-primary small"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?></strong></h3>
                                            <div class="nk-block-des text-soft">
                                                <ul class="list-inline">
                                                    <li>Project ID: <span class="text-base">AP<?=  str_pad($project->id, 4, '0', STR_PAD_LEFT) ; ?></span></li>
                                                    <li>Created On: <span class="text-base"><?=  date("F j, Y h:ia", strtotime(timezoned($project->created_at, $user->parent->timezone))) ; ?></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="nk-block-head-content">
                                            <ul class="nk-block-tools g-3">
                                                <li>                                                   
                                                 <a href="<?=  url('Projects@get') ; ?>" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                                                    <a href="<?=  url('Projects@get') ; ?>" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none"><em class="icon ni ni-arrow-left"></em></a>
                                                </li>
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" data-toggle="dropdown"><em class="icon ni ni-more-h"></em> <span>More</span></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <ul class="link-list-opt no-bdr">
                                                                <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                                <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Projects@updateview') ; ?>" holder=".update-project-holder" modal="#update-project" href=""><em class="icon ni ni-pen"></em><span>Edit Details</span></a></li>
                                                                <li><a href="" data-toggle="modal" data-target="#createjobcard"><em class="icon ni ni-property-add"></em><span>Create Job Card</span></a></li>
                                                                <li><a href="" data-toggle="modal" data-target="#createtask"><em class="icon ni ni-todo"></em><span>Create Task</span></a></li>
                                                                <li class="divider"></li>
                                                                <li><a href="" data-toggle="modal" data-target="#addexpense"><em class="icon ni ni-cart"></em><span>Add Expense</span></a></li>
                                                                <li><a href="" data-toggle="modal" data-target="#createquote"><em class="icon ni ni-cards"></em><span>Create Quote</span></a></li>
                                                                <li><a href="" data-toggle="modal" data-target="#createinvoice"><em class="icon ni ni-cc"></em><span>Create Invoice</span></a></li>
                                                                <?php } ?>
                                                                <?php if ($project->checkedout == "No" && !empty($project->booking_signature) && !empty($project->signed_by)) { ?>
                                                                <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Projects@checkout') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-calendar"></em><span>Check Out</span></a></li>
                                                                <?php } ?>
                                                                <?php if ($project->status == "In Progress" && $user->role == "Owner" || $project->status == "In Progress" && $user->role == "Manager") { ?>
                                                                <li><a href="" class="send-to-server-click"  data="projectid:<?=  $project->id ; ?>" url="<?=  url('Projects@cancel') ; ?>" warning-title="Are you sure?" warning-message="This project will be marked as cancelled." warning-button="Yes, Cancel!"><em class="icon ni ni-na"></em><span>Cancel Project</span></a></li>
                                                                <?php } ?>
                                                                <?php if ($user->role == "Owner") { ?>
                                                                <li><a href="" class="send-to-server-click"  data="projectid:<?=  $project->id ; ?>" url="<?=  url('Projects@delete') ; ?>" warning-title="Are you sure?" warning-message="This project will be deleted permanently." warning-button="Yes, delete!"><em class="icon ni ni-trash"></em><span>Delete Project</span></a></li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card">
                                        <div class="card-aside-wrap">
                                            <div class="card-content">
                                                <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (!isset($_GET['view'])) { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>?details"><em class="icon ni ni-file-text"></em><span>Details</span></a>
                                                    </li>
                                                    <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'quotes') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>?view=quotes"><em class="icon ni ni-cards"></em><span>Quotes</span></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'jobcards') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>?view=jobcards"><em class="icon ni ni-property-add"></em><span>Job Cards</span></a>
                                                    </li>
                                                    
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'tasks') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>?view=tasks"><em class="icon ni ni-todo"></em><span>Tasks</span></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'expenses') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>?view=expenses"><em class="icon ni ni-cart"></em><span>Parts & Expenses</span></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'invoices') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>?view=invoices"><em class="icon ni ni-cc"></em><span>Invoices</span></a>
                                                    </li>

                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'payments') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>?view=payments"><em class="icon ni ni-align-left"></em><span>Receipts</span></a>
                                                    </li>

                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 's_payments') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>?view=s_payments"><em class="icon ni ni-align-left"></em><span>Payments</span></a>
                                                    </li>

                                                    <?php } ?>
                                                </ul><!-- .nav-tabs -->
                                                <?php if (!isset($_GET["view"])) { ?>

                                                <div class="card-inner">
                                                    <div class="nk-block">
                                                        <div class="nk-block-head">
                                                            <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                            <a href="<?=  url('Vehiclereport@view', array('projectid' => $project->id)) ; ?>" class="btn btn-primary pull-right" ><em class="icon ni ni-reports"></em><span>Vehicle Report</span></a>
                                                            <?php } ?>
                                                            <a href="<?=  url('Booking@view', array('projectid' => $project->id)) ; ?>" class="btn btn-dim btn-outline-primary pull-right mr-1"><em class="icon ni ni-todo"></em><span>Vehicle Booking</span></a>
                                                            <h5 class="title">Project Information</h5>
                                                            <p>Basic project info, that gives project overview.</p>
                                                        </div><!-- .nk-block-head -->
                                                        <div class="profile-ud-list">
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Project</span>
                                                                    <span class="profile-ud-value"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Registration</span>
                                                                    <span class="profile-ud-value"><?=  $project->registration_number ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">VIN</span>
                                                                    <span class="profile-ud-value"><?=  $project->vin ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Color</span>
                                                                    <span class="profile-ud-value"><?=  $project->color ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Insurance Expiry</span>
                                                                    <span class="profile-ud-value"><?=  date("F j, Y", strtotime($project->insurance_expiry)) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Insurance Company</span>
                                                                    <span class="profile-ud-value"><?=  $project->insurance_company ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Booking Date & Time</span>
                                                                    <span class="profile-ud-value"><?=  date("F j, Y", strtotime($project->date_in)) ; ?> <?=  date("h:ia", strtotime($project->time_in)) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <?php if ($project->checkedout == "Yes") { ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Check Out Date & Time</span>
                                                                    <span class="profile-ud-value"><?=  date("F j, Y", strtotime($project->date_out)) ; ?> <?=  date("h:ia", strtotime($project->time_out)) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Client Name</span>
                                                                    <span class="profile-ud-value"><?=  $client->fullname ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Start Date</span>
                                                                    <span class="profile-ud-value"><?=  date("F j, Y", strtotime($project->start_date)) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Completion</span>
                                                                    <span class="profile-ud-value"><?=  date("F j, Y", strtotime($project->end_date)) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Mileage In</span>
                                                                    <?php if (!empty($project->milleage)) { ?>
                                                                    <span class="profile-ud-value"><?=  $project->milleage ; ?> <?=  $project->milleage_unit ; ?></span>
                                                                    <?php } else { ?>
                                                                    <span class="profile-ud-value"><?=  $project->milleage ; ?></span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <?php if ($project->checkedout == "Yes") { ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Mileage Out</span>
                                                                    <?php if (!empty($project->milleage_out)) { ?>
                                                                    <span class="profile-ud-value"><?=  $project->milleage_out ; ?> <?=  $project->milleage_unit ; ?></span>
                                                                    <?php } else { ?>
                                                                    <span class="profile-ud-value"><?=  $project->milleage_out ; ?></span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Road Test In</span>
                                                                    <span class="profile-ud-value"><?=  $project->road_test_in ; ?></span>
                                                                </div>
                                                            </div>
                                                            <?php if ($project->checkedout == "Yes") { ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Road Test Out</span>
                                                                    <span class="profile-ud-value"><?=  $project->road_test_out ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Old Parts Collected</span>
                                                                    <span class="profile-ud-value"><?=  $project->old_parts_collected ; ?></span>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                            <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Invoiced</span>
                                                                    <span class="profile-ud-value"><?=  money(($project->invoiced), $user->parent->currency) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Total Cost  <span class="ml-1" data-toggle="tooltip" title="Expenses + Paid Tasks"> <em class="icon ni ni-info-fill"></em></span></span>
                                                                    <span class="profile-ud-value"><?=  money(($project->cost), $user->parent->currency) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                            <?php if ($user->parent->insurance == "Enabled") { ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Insurance Covered Repairs</span>
                                                                    <span class="profile-ud-value">
                                                                        <?php if (!empty($project->insurance)) { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Covered</span>
                                                                        <?php } else { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Not Covered</span>
                                                                        <?php } ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Insurance covering repairs</span>
                                                                    <?php if (!empty($project->insurance)) { ?>
                                                                    <span class="profile-ud-value"><?=  $project->covered_by->name ; ?></span>
                                                                    <?php } else { ?>
                                                                    <span class="profile-ud-value">N/A</span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                        </div><!-- .profile-ud-list -->
                                                    </div><!-- .nk-block -->
                                                    <div class="nk-block">
                                                        <div class="nk-block-head nk-block-head-line">
                                                            <h6 class="title overline-title text-base">Additional Information</h6>
                                                        </div><!-- .nk-block-head -->
                                                        <div class="profile-ud-list">
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Tasks</span>
                                                                    <span class="profile-ud-value"><?=  $project->pending_tasks ; ?> / <?=  $project->total_tasks ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Client Phone</span>
                                                                    <span class="profile-ud-value"><?=  $client->phonenumber ; ?></span>
                                                                </div>
                                                            </div>
                                                            <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Profit</span>
                                                                    <span class="profile-ud-value"><?=  money(($project->invoiced - $project->cost), $user->parent->currency) ; ?></span>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Status</span>
                                                                    <span class="profile-ud-value">
                                                                        <?php if ($project->status == "Completed") { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Completed</span>
                                                                        <?php } else if ($project->status == "Cancelled") { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-secondary d-mb-inline-flex">Cancelled</span>
                                                                        <?php } else if ($project->status == "Booked In") { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Booked In</span>
                                                                        <?php } else { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">In Progress</span>
                                                                        <?php } ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Brought In By</span>
                                                                    <?php if ($project->delivered_by == "Client") { ?>
                                                                    <span class="profile-ud-value">Client</span>
                                                                    <?php } else { ?>
                                                                    <span class="profile-ud-value"><?=  $project->deliveredby_fullname ; ?> • <?=  $project->deliveredby_phonenumber ; ?></span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div><!-- .profile-ud-list -->
                                                    </div><!-- .nk-block -->
                                                    <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                    <div class="nk-divider divider md"></div>
                                                    <div class="nk-block">
                                                        <div class="nk-block-head nk-block-head-sm nk-block-between">
                                                            <h5 class="title">Project Notes
                                                                <?php if (!empty($notes)) { ?> 
                                                                ( <?=  count($notes) ; ?> ) 
                                                                <?php } ?>
                                                            </h5>
                                                            <a href="" class="link link-sm" data-toggle="modal" data-target="#createnote">+ Add Note</a>
                                                        </div><!-- .nk-block-head -->
                                                        <div class="bq-note">
                                                            <?php if (!empty($notes)) { ?> 
                                                            <?php foreach ($notes as $note) { ?>
                                                            <div class="bq-note-item">
                                                                <div class="bq-note-text">
                                                                    <p><?=  $note->note ; ?></p>
                                                                </div>
                                                                <div class="bq-note-meta">
                                                                    <span class="bq-note-added">Added on <span class="date"><?=  date("F j, Y", strtotime(timezoned($note->created_at, $user->parent->timezone))) ; ?></span> at <span class="time"><?=  date("h:ia", strtotime(timezoned($note->created_at, $user->parent->timezone))) ; ?></span></span>
                                                                    <?php if ($user->role == "Owner") { ?>
                                                                    <span class="bq-note-sep sep">•</span>
                                                                    <a href="#" class="link link-sm link-danger send-to-server-click"  data="noteid:<?=  $note->id ; ?>" url="<?=  url('Notes@delete') ; ?>" warning-title="Are you sure?" warning-message="This note will be deleted permanently." warning-button="Yes, delete!">Delete Note</a>
                                                                    <?php } ?>
                                                                </div>
                                                            </div><!-- .bq-note-item -->
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <div class="empty text-center text-muted">
                                                                <em class="icon ni ni-info"></em>
                                                                <p>No notes added yet!</p>
                                                            </div>
                                                            <?php } ?>
                                                        </div><!-- .bq-note -->
                                                    </div><!-- .nk-block -->
                                                    <?php } ?>
                                                </div><!-- .card-inner -->
                                                <?php } ?>
                                                <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>

                                                <?php if (isset($_GET["view"]) && $_GET["view"] == "quotes") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <a href="" class="btn btn-primary pull-right" data-toggle="modal" data-target="#createquote"><em class="icon ni ni-plus"></em><span>Create Quote</span></a>
                                                            <!-- <div class="drodown pull-right mr-1">
                                                                <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" data-toggle="dropdown"><em class="icon ni ni-swap"></em> <span>Import From</span></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Invoice@workrequested') ; ?>" holder=".item-lines.quote-items" modal="#createquote" href=""><em class="icon ni ni-clipboad-check"></em><span>Work Requested</span></a></li>
                                                                        <li><a class="select-from-jobcard" data-url="<?=  url('Invoice@jobcards') ; ?>" holder=".item-lines.quote-items" modal="#createquote" href=""><em class="icon ni ni-todo"></em><span>Approved Jobcard</span></a></li>
                                                                        <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Invoice@expenses') ; ?>" holder=".item-lines.quote-items" modal="#createquote" href=""><em class="icon ni ni-cart"></em><span>Parts & Expenses</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div> -->
                                                            <h5 class="title">Project Quotes</h5>
                                                            <p>A list of quotes for <?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?> project.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->

                                                    <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                        <thead>
                                                            <tr class="nk-tb-item nk-tb-head">
                                                                <th class="nk-tb-col text-center">#</th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Project</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Items</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Date</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Total</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                                <th class="nk-tb-col nk-tb-col-tools text-right">
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($quotes)) { ?>
                                                            <?php foreach ($quotes as $index => $quote) { ?>
                                                            <tr class="nk-tb-item">
                                                                <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <div class="user-card">
                                                                        <div class="user-info">
                                                                            <span class="tb-lead"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?></span>
                                                                            <span><?=  $project->registration_number ; ?></span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span class="tb-amount"><?=  $quote->items ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span><?=  date("F j, Y", strtotime($quote->created_at)) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span class="tb-amount"><?=  money($quote->total, $user->parent->currency) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <?php if ($quote->isApproved == true) { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Approved</span>
                                                                    <?php } else { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Not Approved</span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="nk-tb-col nk-tb-col-tools">
                                                                    <ul class="nk-tb-actions gx-1">
                                                                        <li>
                                                                            <div class="drodown">
                                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <ul class="link-list-opt no-bdr">
                                                                                    <li><a href="<?=  url('Quote@view', array('quoteid' => $quote->id)) ; ?>"><em class="icon ni ni-eye"></em><span>View Quote</span></a></li>
                                                                                    <li><a href="<?=  url('Projects@approve', array('projectid' => $project->id,'quoteid' => $quote->id)) ; ?>?view=quotes"><em class="icon ni ni-check"></em><span>Approve Quote</span></a></li>
                                                                                    <li><a href="<?=  url('Quote@render', array('quoteid' => $quote->id)) ; ?>" download="Quote #<?=  $quote->id ; ?>.pdf"><em class="icon ni ni-download-cloud"></em><span>Download</span></a></li>
                                                                                    <li><a href="" class="send-via-email" data-url="<?=  url('Quote@send') ; ?>" data-id="<?=  $quote->id ; ?>" data-subject="Quote #<?=  $quote->id ; ?>"><em class="icon ni ni-mail"></em><span>Send Via Email</span></a></li>
                                                                                    <li><a class="fetch-display-click" data="quoteid:<?=  $quote->id ; ?>" url="<?=  url('Quote@updateview') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-pen"></em><span>Edit Quote</span></a></li>
                                                                                    <li><a class="convert-quote" data-id="<?=  $quote->id ; ?>" href=""><em class="icon ni ni-cc"></em><span>Convert to Invoice</span></a></li>
                                                                                    <?php if ($user->role == "Owner") { ?>
                                                                                    <li class="divider"></li>
                                                                                    <li><a class="send-to-server-click"  data="quoteid:<?=  $quote->id ; ?>" url="<?=  url('Quote@delete_at_project') ; ?>" warning-title="Are you sure?" warning-message="This quote will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Quote</span></a></li>
                                                                                    <?php } ?>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr><!-- .nk-tb-item  -->
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <tr>
                                                                <td class="text-center" colspan="6">It's empty here!</td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div><!-- .card-inner -->

                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "jobcards") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <a href="" class="btn btn-primary pull-right"  data-toggle="modal" data-target="#createjobcard"><em class="icon ni ni-plus"></em><span>Create Job Card</span></a>
                                                            <h5 class="title">Project Job Cards</h5>
                                                            <p>A list of job cards for <?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?> project.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->
                                                    
                                                    
                                                        <div class="bq-note">
                                                            <?php if (!empty($jobcards)) { ?> 
                                                            <?php foreach ($jobcards as $jobcard) { ?>
                                                            <div class="bq-note-item">
                                                                <div class="bq-note-text">

                                                                    <?php if ($jobcard->approved == "Yes") { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-success float-right">Approved</span>
                                                                    <?php } else { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-warning float-right">Assessment</span>
                                                                    <?php } ?>
                                                                    <h6 class="title">Body Report</h6>
                                                                    <ol class="styled-list">
                                                                        <?php if (!empty($jobcard->body_report)) { ?>
                                                                        <?php foreach (json_decode($jobcard->body_report) as $index => $report) { ?>
                                                                            <li><?=  $report ; ?></li>
                                                                        <?php } ?>
                                                                        <?php } ?>
                                                                    </ol>
                                                                    <div class="nk-divider divider md"></div>
                                                                    <h6 class="title">Mechanical Report</h6>
                                                                    <ol class="styled-list">
                                                                        <?php if (!empty($jobcard->mechanical_report)) { ?>
                                                                        <?php foreach (json_decode($jobcard->mechanical_report) as $index => $report) { ?>
                                                                            <li><?=  $report ; ?></li>
                                                                        <?php } ?>
                                                                        <?php } ?>
                                                                    </ol>
                                                                    <div class="nk-divider divider md"></div>
                                                                    <h6 class="title">Electrical Report</h6>
                                                                    <ol class="styled-list">
                                                                        <?php if (!empty($jobcard->electrical_report)) { ?>
                                                                        <?php foreach (json_decode($jobcard->electrical_report) as $index => $report) { ?>
                                                                            <li><?=  $report ; ?></li>
                                                                        <?php } ?>
                                                                        <?php } ?>
                                                                    </ol>
                                                                </div>
                                                                <div class="bq-note-meta">
                                                                    <span class="bq-note-added">Job Card <span class="fw-bold">#<?=  $jobcard->id ; ?></span> Created on <span class="date"><?=  date("F j, Y", strtotime(timezoned($jobcard->created_at, $user->parent->timezone))) ; ?></span> at <span class="time"><?=  date("h:ia", strtotime(timezoned($jobcard->created_at, $user->parent->timezone))) ; ?></span></span>
                                                                    <span class="bq-note-sep sep">•</span>
                                                                    <a href="" class="link link-sm link-primary fetch-display-click" data="jobcardid:<?=  $jobcard->id ; ?>|action:edit" url="<?=  url('Jobcards@updateview') ; ?>" holder=".update-holder-lg" modal="#update-lg">Edit Job Card</a>
                                                                    <?php if ($jobcard->approved == "No") { ?>
                                                                    <span class="bq-note-sep sep">•</span>
                                                                    <a href="" class="link link-sm link-primary fetch-display-click" data="jobcardid:<?=  $jobcard->id ; ?>|action:approved" url="<?=  url('Jobcards@updateview') ; ?>" holder=".update-holder-lg" modal="#update-lg">Create Approved Version</a>         
                                                                    <?php } ?>                                                           
                                                                    <span class="bq-note-sep sep">•</span>
                                                                    <a href="<?=  url('Jobcards@view', array('jobcardid' => $jobcard->id)) ; ?>" class="link link-sm link-primary">View Job Card</a>
                                                                    <span class="bq-note-sep sep">•</span>
                                                                    <a href="<?=  url('Jobcards@render', array('jobcardid' => $jobcard->id)) ; ?>" class="link link-sm link-primary" download="Job card #<?=  $jobcard->id ; ?>">Download Job Card</a>
                                                                    <?php if ($user->role == "Owner") { ?>
                                                                    <span class="bq-note-sep sep">•</span>
                                                                    <a href="" class="link link-sm link-danger send-to-server-click" data="jobcardid:<?=  $jobcard->id ; ?>" url="<?=  url('Jobcards@delete') ; ?>" warning-title="Are you sure?" warning-message="This job card will be deleted permanently." warning-button="Yes, delete!">Delete</a>
                                                                    <?php } ?>
                                                                </div>
                                                            </div><!-- .bq-note-item -->
                                                            <div class="nk-divider divider md"></div>
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <div class="empty text-center text-muted">
                                                                <em class="icon ni ni-info"></em>
                                                                <p>No job card created yet!</p>
                                                            </div>
                                                            <?php } ?>
                                                        </div><!-- .bq-note -->

                                                </div><!-- .card-inner -->
                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "tasks") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <a href="" class="btn btn-primary pull-right"  data-toggle="modal" data-target="#createtask"><em class="icon ni ni-plus"></em><span>Create Task</span></a>
                                                            <div class="drodown pull-right mr-1">
                                                                <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" data-toggle="dropdown"><em class="icon ni ni-swap"></em> <span>Import From</span></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Tasks@workrequested') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-clipboad-check"></em><span>Work Requested</span></a></li>
                                                                        <li><a class="select-from-jobcard" data-url="<?=  url('Tasks@jobcards') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-todo"></em><span>Approved Jobcard</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <h5 class="title">Project Tasks</h5>
                                                            <p>A list of tasks for <?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?> project totalling <span class="fw-bold"><?=  money($project->taskcost, $user->parent->currency) ; ?></span>.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->

                                                    <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                        <thead>
                                                            <tr class="nk-tb-item nk-tb-head">
                                                                <th class="nk-tb-col text-center">#</th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Project / Assigned To</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Title</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Due Date</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Cost</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                                <th class="nk-tb-col nk-tb-col-tools text-right">
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($tasks)) { ?>
                                                            <?php foreach ($tasks as $index => $task) { ?>
                                                            <tr class="nk-tb-item">
                                                                <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <div class="user-card">
                                                                        <div class="user-info">
                                                                            <span class="tb-lead"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <?php if (!empty($task->member)) { ?>
                                                                    <span><?=  $task->member->fname ; ?> <?=  $task->member->lname ; ?></span>
                                                                    <?php } else { ?>
                                                                    <span>--|--</span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span class="tb-amount">
                                                                        <?php if (is_null($task->taskparts)) { ?>
                                                                        <span data-toggle="tooltip" title="No parts required" class="text-light">
                                                                        <?php } else if ($task->taskparts == "Pending") { ?>
                                                                        <span data-toggle="tooltip" title="<?=  $task->undeliveredparts ; ?>  required parts have not been delivered" class="text-warning">
                                                                        <?php } else { ?>
                                                                        <span data-toggle="tooltip" title="All required parts have been delivered" class="text-success">
                                                                        <?php } ?>
                                                                            <em class="icon ni ni-info-fill"></em>
                                                                        </span> <?=  $task->title ; ?> 
                                                                    </span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span>
                                                                        <?=  date("M j, Y", strtotime($task->due_date)) ; ?>
                                                                        <?php if (!empty($task->due_time)) { ?>
                                                                        • <?=  date("h:ia", strtotime($task->due_time)) ; ?>
                                                                        <?php } ?>
                                                                    </span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span class="tb-amount"><?=  money($task->cost, $user->parent->currency) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <?php if ($task->status == "Completed") { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Completed</span>
                                                                    <?php } else if ($task->status == "Cancelled") { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-secondary d-mb-inline-flex">Cancelled</span>
                                                                    <?php } else { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">In Progress</span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="nk-tb-col nk-tb-col-tools">
                                                                    <ul class="nk-tb-actions gx-1">
                                                                        <li>
                                                                            <div class="drodown">
                                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <ul class="link-list-opt no-bdr">
                                                                                    <li><a class="fetch-display-click" data="taskid:<?=  $task->id ; ?>|action:view" url="<?=  url('Tasks@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                                    <li><a class="fetch-display-click" data="taskid:<?=  $task->id ; ?>|action:edit" url="<?=  url('Tasks@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Details</span></a></li>
                                                                                    <li><a href="<?=  url('Tasks@download', array('taskid' => $task->id)) ; ?>"><em class="icon ni ni-download-cloud"></em><span>Download</span></a></li>
                                                                                    <?php if ($task->status == "In Progress") { ?>
                                                                                    <li><a href="" class="send-to-server-click"  data="taskid:<?=  $task->id ; ?>" url="<?=  url('Tasks@cancel') ; ?>" warning-title="Are you sure?" warning-message="This task will be marked as cancelled." warning-button="Yes, Cancel!"><em class="icon ni ni-na"></em><span>Cancel Task</span></a></li>
                                                                                    <?php } ?>
                                                                                    <?php if ($user->role == "Owner") { ?>
                                                                                    <li><a class="send-to-server-click"  data="taskid:<?=  $task->id ; ?>" url="<?=  url('Tasks@delete') ; ?>" warning-title="Are you sure?" warning-message="This task will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Task</span></a></li>
                                                                                    <?php } ?>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr><!-- .nk-tb-item  -->
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <tr>
                                                                <td class="text-center" colspan="8">It's empty here!</td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div><!-- .card-inner -->
                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "expenses") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <a href="" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addexpense"><em class="icon ni ni-plus"></em><span>Add Expense</span></a>
                                                            <div class="drodown pull-right mr-1">
                                                                <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" data-toggle="dropdown"><em class="icon ni ni-swap"></em> <span>Import From</span></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Expenses@workrequested') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-clipboad-check"></em><span>Work Requested</span></a></li>
                                                                        <li><a class="select-from-jobcard" data-url="<?=  url('Expenses@jobcards') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-todo"></em><span>Approved Jobcard</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <h5 class="title">Project Expenses</h5>
                                                            <p>A list of expenses for <?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?> project totalling <span class="fw-bold"><?=  money($project->expenses, $user->parent->currency) ; ?></span>.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->

                                                    <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                        <thead>
                                                            <tr class="nk-tb-item nk-tb-head">
                                                                <th class="nk-tb-col text-center">#</th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Supplier</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Expense / Qty</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Date</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Amount</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Payment</span></th>
                                                                <th class="nk-tb-col nk-tb-col-tools text-right">
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($expenses)) { ?>
                                                            <?php foreach ($expenses as $index => $expense) { ?>
                                                            <tr class="nk-tb-item">
                                                                <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <div class="user-card">
                                                                        <div class="user-info">
                                                                            <?php if (!empty($expense->supplier)) { ?>
                                                                            <span class="tb-lead"><?=  $expense->supplier->name ; ?></span>
                                                                            <?php } else if (!empty($expense->inventory)) { ?>
                                                                            <span class="tb-lead">From Inventory</span>
                                                                            <?php } else { ?>
                                                                            <span class="tb-lead">--|--</span>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span class="tb-amount"><?=  $expense->expense ; ?></span>
                                                                    <span><?=  $expense->quantity ; ?><?=  $expense->units ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span><?=  date("F j, Y", strtotime($expense->expense_date)) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span class="tb-amount"><?=  money($expense->amount, $user->parent->currency) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <?php if ($expense->status == "Delivered") { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Delivered</span>
                                                                    <?php } else if ($expense->status == "Ordered") { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-secondary d-mb-inline-flex">Ordered</span>
                                                                    <?php } else if ($expense->status == "To Order") { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">To Order</span>
                                                                    <?php } else { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">Awaiting Delivery</span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <?php if ($expense->paid == "Yes") { ?>
                                                                    <span class="text-success fw-bold">Paid</span>
                                                                    <?php } else { ?>
                                                                    <span class="text-danger fw-bold">Unpaid</span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="nk-tb-col nk-tb-col-tools">
                                                                    <ul class="nk-tb-actions gx-1">
                                                                        <li>
                                                                            <div class="drodown">
                                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <ul class="link-list-opt no-bdr">
                                                                                    <?php if (empty($expense->inventory)) { ?>
                                                                                    <li><a class="fetch-display-click" data="expenseid:<?=  $expense->id ; ?>" url="<?=  url('Expenses@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Expense</span></a></li>
                                                                                    <?php } ?>
                                                                                    <?php if ($user->role == "Owner") { ?>
                                                                                    <li><a class="send-to-server-click"  data="expenseid:<?=  $expense->id ; ?>" url="<?=  url('Expenses@delete') ; ?>" warning-title="Are you sure?" warning-message="This expense will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Expense</span></a></li>
                                                                                    <?php } ?>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr><!-- .nk-tb-item  -->
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <tr>
                                                                <td class="text-center" colspan="8">It's empty here!</td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div><!-- .card-inner -->

                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "invoices") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <a href="" class="btn btn-primary pull-right" data-toggle="modal" data-target="#createinvoice"><em class="icon ni ni-plus"></em><span>Create Invoice</span></a>
                                                            <div class="drodown pull-right mr-1">
                                                                <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" data-toggle="dropdown"><em class="icon ni ni-swap"></em> <span>Import From</span></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Invoice@workrequested') ; ?>" holder=".item-lines.invoice-items" modal="#createinvoice" href=""><em class="icon ni ni-clipboad-check"></em><span>Work Requested</span></a></li>
                                                                        <li><a class="select-from-jobcard" data-url="<?=  url('Invoice@jobcards') ; ?>" holder=".item-lines.invoice-items" modal="#createinvoice" href=""><em class="icon ni ni-todo"></em><span>Approved Jobcard</span></a></li>
                                                                        <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Invoice@expenses') ; ?>" holder=".item-lines.invoice-items" modal="#createinvoice" href=""><em class="icon ni ni-cart"></em><span>Parts & Expenses</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <h5 class="title">Project Invoices</h5>
                                                            <p>A list of invoices for <?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?> project.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->

                                                    <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                        <thead>
                                                            <tr class="nk-tb-item nk-tb-head">
                                                                <th class="nk-tb-col text-center">#</th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Project</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Items</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Date / Due</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Total / Balance</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                                <th class="nk-tb-col nk-tb-col-tools text-right">
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($invoices)) { ?>
                                                            <?php foreach ($invoices as $index => $invoice) { ?>
                                                            <tr class="nk-tb-item">
                                                                <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <div class="user-card">
                                                                        <div class="user-info">
                                                                            <span class="tb-lead"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?></span>
                                                                            <span><?=  $project->registration_number ; ?></span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span class="tb-amount"><?=  $invoice->items ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span><?=  date("M j, Y", strtotime($invoice->invoice_date)) ; ?></span><br>
                                                                    <?php if ($invoice->due_date < date("Y-m-d") && $invoice->status != "Paid") { ?>
                                                                    <span class="text-danger">
                                                                        <?=  date("M j, Y", strtotime($invoice->due_date)) ; ?> 
                                                                        <span data-toggle="tooltip" title="Overdue"><em class="icon ni ni-info-fill"></em></span>
                                                                    </span>
                                                                    <?php } else { ?>
                                                                    <span><?=  date("M j, Y", strtotime($invoice->due_date)) ; ?></span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span class="tb-amount"><?=  money($invoice->total, $user->parent->currency) ; ?></span>
                                                                    <span><?=  money($invoice->balance, $user->parent->currency) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <?php if ($invoice->status == "Paid") { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Paid</span>
                                                                    <?php } else if ($invoice->status == "Partial") { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">Partial</span>
                                                                    <?php } else { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Unpaid</span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="nk-tb-col nk-tb-col-tools">
                                                                    <ul class="nk-tb-actions gx-1">
                                                                        <li>
                                                                            <div class="drodown">
                                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <ul class="link-list-opt no-bdr">
                                                                                    <li><a href="<?=  url('Invoice@view', array('invoiceid' => $invoice->id)) ; ?>"><em class="icon ni ni-eye"></em><span>View Invoice</span></a></li>
                                                                                    <li><a href="<?=  url('Invoice@render', array('invoiceid' => $invoice->id)) ; ?>" download="Invoice #<?=  $invoice->id ; ?>.pdf"><em class="icon ni ni-download-cloud"></em><span>Download</span></a></li>
                                                                                    <li><a href="" class="send-via-email" data-url="<?=  url('Invoice@send') ; ?>" data-id="<?=  $invoice->id ; ?>" data-subject="Invoice #<?=  $invoice->id ; ?>"><em class="icon ni ni-mail"></em><span>Send Via Email</span></a></li>
                                                                                    <?php if ($invoice->status != "Paid") { ?>
                                                                                    <li><a href="" class="add-payment" data-id="<?=  $invoice->id ; ?>"><em class="icon ni ni-coin-alt"></em><span>Add Receipt</span></a></li>
                                                                                    <?php } ?>
                                                                                    <li><a class="fetch-display-click" data="invoiceid:<?=  $invoice->id ; ?>" url="<?=  url('Invoice@updateview') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-pen"></em><span>Edit Invoice</span></a></li>
                                                                                    <?php if ($user->role == "Owner") { ?>
                                                                                    <li class="divider"></li>
                                                                                    <li><a class="send-to-server-click"  data="invoiceid:<?=  $invoice->id ; ?>" url="<?=  url('Invoice@delete') ; ?>" warning-title="Are you sure?" warning-message="This invoice will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Invoice</span></a></li>
                                                                                    <?php } ?>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr><!-- .nk-tb-item  -->
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <tr>
                                                                <td class="text-center" colspan="6">It's empty here!</td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div><!-- .card-inner -->
                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "payments") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <a href="" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addpayment"><em class="icon ni ni-plus"></em><span>Add Receipt</span></a>
                                                            <h5 class="title">Project Receipts</h5>
                                                            <p>A list of payments for <?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?> project.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->

                                                    <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                        <thead>
                                                            <tr class="nk-tb-item nk-tb-head">
                                                                <th class="nk-tb-col text-center">#</th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Project</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Invoice</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Receipt Date</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Amount</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                                <th class="nk-tb-col nk-tb-col-tools text-right">
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($payments)) { ?>
                                                            <?php foreach ($payments as $index => $payment) { ?>
                                                            <tr class="nk-tb-item">
                                                                <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <div class="user-card">
                                                                        <div class="user-info">
                                                                            <span class="tb-lead"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?></span>
                                                                            <span><?=  $project->registration_number ; ?></span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span class="tb-amount">Invoice #<?=  $payment->invoice ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span><?=  date("F j, Y", strtotime($payment->payment_date)) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span class="tb-amount"><?=  money($payment->amount, $user->parent->currency) ; ?></span>
                                                                    <span><?=  $payment->method ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Paid</span>
                                                                </td>
                                                                <td class="nk-tb-col nk-tb-col-tools">
                                                                    <ul class="nk-tb-actions gx-1">
                                                                        <li>
                                                                            <div class="drodown">
                                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <ul class="link-list-opt no-bdr">
                                                                                    <li><a href="<?=  url('Invoice@view', array('invoiceid' => $payment->invoice)) ; ?>"><em class="icon ni ni-eye"></em><span>View Invoice</span></a></li>
                                                                                    <li><a class="fetch-display-click" data="paymentid:<?=  $payment->id ; ?>" url="<?=  url('Projectpayment@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Payment</span></a></li>
                                                                                    <?php if ($user->role == "Owner") { ?>
                                                                                    <li class="divider"></li>
                                                                                    <li><a class="send-to-server-click"  data="paymentid:<?=  $payment->id ; ?>" url="<?=  url('Projectpayment@delete') ; ?>" warning-title="Are you sure?" warning-message="This payment will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Payment</span></a></li>
                                                                                    <?php } ?>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr><!-- .nk-tb-item  -->
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <tr>
                                                                <td class="text-center" colspan="7">It's empty here!</td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div><!-- .card-inner -->

                                                <!-- This is supplier Payment -->
                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "s_payments") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <a href="" class="btn btn-primary pull-right" data-toggle="modal" data-target="#add_s_payment"><em class="icon ni ni-plus"></em><span>Add Payment</span></a>
                                                            <h5 class="title">Project Payments</h5>
                                                            <p>A list of payments for <?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?> project.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->

                                                    <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                        <thead>
                                                            <tr class="nk-tb-item nk-tb-head">
                                                                <th class="nk-tb-col text-center">#</th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Project</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Expense</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Payment Date</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Amount</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                                <th class="nk-tb-col nk-tb-col-tools text-right">
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($s_payments)) { ?>
                                                            <?php foreach ($s_payments as $index => $payment) { ?>
                                                            <tr class="nk-tb-item">
                                                                <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <div class="user-card">
                                                                        <div class="user-info">
                                                                            <span class="tb-lead"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?></span>
                                                                            <span><?=  $project->registration_number ; ?></span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span class="tb-amount">Expense #<?=  $payment->expense ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span><?=  date("F j, Y", strtotime($payment->payment_date)) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span class="tb-amount"><?=  money($payment->amount, $user->parent->currency) ; ?></span>
                                                                    <span><?=  $payment->method ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Paid</span>
                                                                </td>
                                                                <td class="nk-tb-col nk-tb-col-tools">
                                                                    <ul class="nk-tb-actions gx-1">
                                                                        <li>
                                                                            <div class="drodown">
                                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <ul class="link-list-opt no-bdr">
                                                                                    <!-- <li><a href="<?=  url('Invoice@view', array('invoiceid' => $payment->invoice)) ; ?>"><em class="icon ni ni-eye"></em><span>View Invoice</span></a></li> -->
                                                                                    <li><a class="fetch-display-click" data="paymentid:<?=  $payment->id ; ?>" url="<?=  url('Supplierpayment@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Payment</span></a></li>
                                                                                    <?php if ($user->role == "Owner") { ?>
                                                                                    <li class="divider"></li>
                                                                                    <li><a class="send-to-server-click"  data="paymentid:<?=  $payment->id ; ?>" url="<?=  url('Supplierpayment@delete') ; ?>" warning-title="Are you sure?" warning-message="This payment will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Payment</span></a></li>
                                                                                    <?php } ?>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr><!-- .nk-tb-item  -->
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <tr>
                                                                <td class="text-center" colspan="7">It's empty here!</td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div><!-- .card-inner -->

                                                <?php } ?>
                                                <?php } ?>
                                            </div><!-- .card-content -->
                                        </div><!-- .card-aside-wrap -->
                                    </div><!-- .card -->
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

    <!-- Create Note Modal -->
    <div class="modal fade" tabindex="-1" id="createnote">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add a Note</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Notes@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add a note on this client's account</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Write your note</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg" placeholder="Write your note" name="note" rows="5" required=""></textarea>
                                        <input type="hidden" name="item" value="<?=  $project->id ; ?>" required="">
                                        <input type="hidden" name="type" value="Project" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Save Note</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" tabindex="-1" id="update">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Manage Info</h5>
                </div>
                <div class="update-holder"></div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" tabindex="-1" id="jobcards-select">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Select Jobcard</h5>
                </div>
                <form class="simcy-form jobcard-select-form" action="" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Select a jobcard to import from.</p>
                        <input type="hidden" name="jobcardid">
                        <div class="bq-note">
                            <?php if (!empty($jobcards)) { ?> 
                            <?php foreach ($jobcards as $jobcard) { ?>
                            <?php if ($jobcard->approved == "Yes") { ?>
                            <div class="bq-note-item">
                                <div class="bq-note-text">

                                    <h6 class="title">Body Report</h6>
                                    <ol class="styled-list">
                                        <?php if (!empty($jobcard->body_report)) { ?>
                                        <?php foreach (json_decode($jobcard->body_report) as $index => $report) { ?>
                                            <li><?=  $report ; ?></li>
                                        <?php } ?>
                                        <?php } ?>
                                    </ol>
                                    <div class="nk-divider divider md"></div>
                                    <h6 class="title">Mechanical Report</h6>
                                    <ol class="styled-list">
                                        <?php if (!empty($jobcard->mechanical_report)) { ?>
                                        <?php foreach (json_decode($jobcard->mechanical_report) as $index => $report) { ?>
                                            <li><?=  $report ; ?></li>
                                        <?php } ?>
                                        <?php } ?>
                                    </ol>
                                    <div class="nk-divider divider md"></div>
                                    <h6 class="title">Electrical Report</h6>
                                    <ol class="styled-list">
                                        <?php if (!empty($jobcard->electrical_report)) { ?>
                                        <?php foreach (json_decode($jobcard->electrical_report) as $index => $report) { ?>
                                            <li><?=  $report ; ?></li>
                                        <?php } ?>
                                        <?php } ?>
                                    </ol>
                                    <div class="nk-divider divider md"></div>
                                    <button class="btn btn-dim btn-outline-primary select-jobcard" data-id="<?=  $jobcard->id ; ?>"><em class="icon ni ni-check-circle-cut"></em><span>Select Jobcard</span></button>
                                </div>
                            </div><!-- .bq-note-item -->
                            <div class="nk-divider divider md"></div>

                            <?php } ?>
                            <?php } ?>
                            <?php } else { ?>
                            <div class="empty text-center text-muted">
                                <em class="icon ni ni-info"></em>
                                <p>No job card created yet!</p>
                            </div>
                            <?php } ?>
                        </div><!-- .bq-note -->
                    </div>
                </form>
                <div class="modal-footer bg-light">
                    <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal create job card -->
    <div class="modal fade" tabindex="-1" id="createjobcard">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Create Job Card</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Jobcards@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create a project job card for <span class="fw-bold"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?></span>.</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Work requested</label>
                                    <div class="form-control-wrap">
                                        <ol class="styled-list">
                                            <?php if (!empty($project->work_requested)) { ?>
                                            <?php foreach ($project->work_requested as $index => $work_requested) { ?>
                                                <li><?=  $work_requested ; ?></li>
                                            <?php } ?>
                                            <?php } ?>
                                        </ol>
                                        <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                                        <input type="hidden" name="jobcardid">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Body Report</label>
                                    <div class="asilify-stack">
                                        <div class="stacked-inputs">
                                            <div class="form-control-wrap stacked">
                                                <input type="text" class="form-control form-control-lg" placeholder="Body Report" name="body_report[]">
                                            </div>
                                            <div class="form-control-wrap stacked">
                                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                                <input type="text" class="form-control form-control-lg" placeholder="Body Report" name="body_report[]">
                                            </div>
                                            <div class="form-control-wrap stacked">
                                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                                <input type="text" class="form-control form-control-lg" placeholder="Body Report" name="body_report[]">
                                            </div>
                                        </div>
                                        <div class="">
                                            <a href="" class="btn btn-dim btn-primary add-stack" data-name="body_report[]" data-placeholder="Body Report"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Mechanical Report</label>
                                    <div class="asilify-stack">
                                        <div class="stacked-inputs">
                                            <div class="form-control-wrap stacked">
                                                <input type="text" class="form-control form-control-lg" placeholder="Mechanical Report" name="mechanical_report[]">
                                            </div>
                                            <div class="form-control-wrap stacked">
                                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                                <input type="text" class="form-control form-control-lg" placeholder="Mechanical Report" name="mechanical_report[]">
                                            </div>
                                            <div class="form-control-wrap stacked">
                                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                                <input type="text" class="form-control form-control-lg" placeholder="Mechanical Report" name="mechanical_report[]">
                                            </div>
                                        </div>
                                        <div class="">
                                            <a href="" class="btn btn-dim btn-primary add-stack" data-name="mechanical_report[]" data-placeholder="Mechanical Report"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Electrical Report</label>
                                    <div class="asilify-stack">
                                        <div class="stacked-inputs">
                                            <div class="form-control-wrap stacked">
                                                <input type="text" class="form-control form-control-lg" placeholder="Electrical Report" name="electrical_report[]">
                                            </div>
                                            <div class="form-control-wrap stacked">
                                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                                <input type="text" class="form-control form-control-lg" placeholder="Electrical Report" name="electrical_report[]">
                                            </div>
                                            <div class="form-control-wrap stacked">
                                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                                <input type="text" class="form-control form-control-lg" placeholder="Electrical Report" name="electrical_report[]">
                                            </div>
                                        </div>
                                        <div class="">
                                            <a href="" class="btn btn-dim btn-primary add-stack" data-name="electrical_report[]" data-placeholder="Electrical Report"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Is this job card approved by the client?</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="approved">
                                                <option value="No">Not yet</option>
                                                <option value="Yes">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Create Job Card</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal create task -->
    <div class="modal fade" tabindex="-1" id="createtask">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Create Project Task</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Tasks@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create a project task</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Task Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Task Name" name="title" required="">
                                        <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Assign to</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2" name="member" required="">
                                                <option value="">Select Staff</option>
                                                <?php if (!empty($staffmembers)) { ?>
                                                <?php foreach ($staffmembers as $staffmember) { ?>
                                                <option value="<?=  $staffmember->id ; ?>"><?=  $staffmember->fname ; ?> <?=  $staffmember->lname ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="status">
                                                <option value="In Progress">In Progress</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Task Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg" placeholder="Task Description" rows="5" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Task Cost</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Task Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost" value="0.00" step="0.01" required="">
                                    </div>
                                    <div class="form-note">Task cost can't be updated once saved.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Due Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Due Date" name="due_date" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Due Time</label>
                                    <div class="form-control-wrap">
                                        <input type="time" class="form-control form-control-lg" placeholder="Due Time" name="due_time">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="nk-divider divider mt-0 mb-0"></div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Select required parts</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2" name="parts_required[]" multiple="">
                                                <?php if (!empty($expenses)) { ?>
                                                <?php foreach ($expenses as $expense) { ?>
                                                <option value="<?=  $expense->id ; ?>"><?=  $expense->expense ; ?> - ( <?=  $expense->status ; ?> )</option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-note">If this task requires parts recorded on the system, please select the parts below. A task can't be completed if any of the required parts are not delivered. Parts are added on <a href="<?=  url('Projects@details', array('projectid' => $project->id)) ; ?>?view=expenses">Expenses Tab</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Create Task</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal add expense -->
    <div class="modal fade" tabindex="-1" id="addexpense">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add Expense</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Expenses@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body modal-section">
                        <p>Add a project expense</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Source</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg source-select" name="source">
                                                <option value="Suppliers">External Suppliers</option>
                                                <option value="Inventory">From Inventory</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row inventory-source gy-4" style="display: none;">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Item / Part</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2 inventory-select" name="inventory">
                                                <option value="">Select Item</option>
                                                <?php if (!empty($inventory)) { ?>
                                                <?php foreach ($inventory as $item) { ?>
                                                <option value="<?=  $item->id ; ?>" quantity="<?=  $item->quantity ; ?>" units="<?=  $item->quantity_unit ; ?>" cost="<?=  $item->unit_cost ; ?>"><?=  $item->name ; ?> ( <?=  $item->quantity ; ?> <?=  $item->quantity_unit ; ?> )</option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Units Consumed</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title units">Units</span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg expense-consumed" placeholder="Units Consumed" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="consumed" value="0.00" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Amount</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg expense-total-amount" placeholder="Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="totalamount" value="0.00" step="0.01" required="" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Expense Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Expense Date" name="expensedate" value="<?=  date('Y-m-d') ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row external-source gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Expense / Item name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Expense / Item name" name="expense" required="">
                                        <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Supplier</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2 supplier-select" name="supplier">
                                                <option value="">Select Supplier</option>
                                                <optgroup label="New Supplier">
                                                    <option value="create" class="fw-bold">Create New Supplier</option>
                                                </optgroup>
                                                <optgroup label="Select from suppliers">
                                                    <?php if (!empty($suppliers)) { ?>
                                                    <?php foreach ($suppliers as $supplier) { ?>
                                                    <option value="<?=  $supplier->id ; ?>"><?=  $supplier->name ; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 new-supplier-input" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Supplier Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Supplier Name" name="suppliername">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 new-supplier-input" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number">
                                        <input class="hidden-phone" type="hidden" name="phonenumber">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Quantity</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Quantity" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="quantity" value="1" step="0.01" min="0.01" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Quantity Unit</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-control-lg" name="quantity_unit">
                                            <option value="Pieces">Pieces</option>
                                            <option value="Units">Units</option>
                                            <option value="Litres">Litres</option>
                                            <option value="Kilograms">Kilograms</option>
                                            <option value="Pounds">Pounds</option>
                                            <option value="Gallons">Gallons</option>
                                            <option value="Meters">Meters</option>
                                            <option value="Set">Set</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Total Amount</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Total Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="amount" value="0.00" step="0.01" min="0" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Expense Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Expense Date" name="expense_date" value="<?=  date('Y-m-d') ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Type</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="type">
                                                <option value="Part">Part</option>
                                                <option value="Service">Service</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg expense-status" name="status">
                                                <option value="Delivered">Delivered</option>
                                                <option value="Ordered">Ordered</option>
                                                <option value="Awaiting Delivery">Awaiting Delivery</option>
                                                <option value="To Order">To Order</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 expense-delivery" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Expected Delivery Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Expected Delivery Date" name="expected_delivery_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 expense-delivery" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Expected Delivery Time</label>
                                    <div class="form-control-wrap">
                                        <input type="time" class="form-control form-control-lg" placeholder="Expected Delivery Time" name="expected_delivery_time">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                     <div class="custom-control custom-switch">
                                        <input type="checkbox" name="paid" id="expense-paid" class="custom-control-input expense-paid" value="Yes" checked="">
                                        <label class="custom-control-label" for="expense-paid">Expense / Item supplier paid</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 expense-payment" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Payment due on</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Payment due on" name="payment_due">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Save Expense</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal create quote -->
    <div class="modal fade" tabindex="-1" id="createquote">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Create Quote</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Quote@create_at_project') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create a quote for this project</p>
                        <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                        <div class="item-lines quote-items" data-type="quote">
                            <div class="row gy-4">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Item Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Work</label>
                                        <div class="form-control-wrap">
                                            <select class="form-control" name="workType[]">
                                                <option value="0">Select Work</option>
                                                <option value="body_work">Body Work</option>
                                                <option value="mechanical_work">Mechanical Work</option>
                                                <option value="electrical_work">Electrical Work</option>
                                                <option value="ac_work">AC Work</option>
                                                <option value="other_work">Other Work</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="form-label">Qty</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-quantity" value="1" min="1" placeholder="Quantity" name="quantity[]" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost[]" value="0.00" step="0.01" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="form-label">Tax (%)</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-tax"  placeholder="Tax (%)" min="0" name="tax[]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Total ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control form-control-lg line-total" placeholder="Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="total[]" value="0.00" step="0.01" required="" readonly="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                </div>
                            </div>
                            <div class="row gy-4">

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Item Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Work</label>
                                        <div class="form-control-wrap">
                                            <select class="form-control" name="workType[]">
                                                <option value="0">Select Work</option>
                                                <option value="body_work">Body Work</option>
                                                <option value="mechanical_work">Mechanical Work</option>
                                                <option value="electrical_work">Electrical Work</option>
                                                <option value="ac_work">AC Work</option>
                                                <option value="other_work">Other Work</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="form-label">Qty</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-quantity" value="1" min="1" placeholder="Quantity" name="quantity[]" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost[]" value="0.00" step="0.01" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="form-label">Tax (%)</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-tax"  placeholder="Tax (%)" min="0" name="tax[]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Total ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control form-control-lg line-total" placeholder="Total" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="total[]" value="0.00" step="0.01" required="" readonly="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <a href="#" class="btn btn-icon btn-lg btn-round btn-dim btn-outline-danger mt-gs remove-line" data-toggle="tooltip" title="Remove Item"><em class="icon ni ni-cross-circle-fill"></em></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item-totals border-top mt-2 pt-2">
                            <div class="row gy-4 d-flex justify-content-end">
                                <div class="col-sm-7">
                                    <a href="" class="btn btn-dim btn-outline-primary mt-2 add-item-at-project" data-type="invoice"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <div class="fw-normal">Sub Total:<div class="fw-bold sub-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <div class="fw-normal">Tax:<div class="fw-bold tax-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <div class="fw-bold fs-19px border-top">Total:<div class="fw-bold grand-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                </div>
                                <div class="col-sm-1">
                                </div>
                            </div>
                        </div>
                        <div class="border-top mt-2">
                            <div class="row gy-4">
                                <div class="col-12">
                                    <div class="form-group mt-1">
                                        <label class="form-label">Notes</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Notes" rows="2" name="notes"></textarea>
                                        </div>
                                        <div class="form-note">Notes will be printed on the quote.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Create Quote</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal create invoice -->
    <div class="modal fade" tabindex="-1" id="createinvoice">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Create Invoice</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Invoice@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create an invoice for this project</p>
                        <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                        <div class="item-lines invoice-items" data-type="quote">
                            <div class="row gy-4">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="form-label">Item Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="form-label">Qty</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-quantity" value="1" min="1" placeholder="Quantity" name="quantity[]" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost[]" value="0.00" step="0.01" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Tax (%)</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-tax"  placeholder="Tax (%)" min="0" name="tax[]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Total ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control form-control-lg line-total" placeholder="Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="total[]" value="0.00" step="0.01" required="" readonly="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                </div>
                            </div>
                            <div class="row gy-4">

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="form-label">Item Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="form-label">Qty</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-quantity" value="1" min="1" placeholder="Quantity" name="quantity[]" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost[]" value="0.00" step="0.01" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Tax (%)</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-tax"  placeholder="Tax (%)" min="0" name="tax[]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Total ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control form-control-lg line-total" placeholder="Total" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="total[]" value="0.00" step="0.01" required="" readonly="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <div class="form-control-wrap">
                                            <a href="#" class="btn btn-icon btn-lg btn-round btn-dim btn-outline-danger mt-gs remove-line" data-toggle="tooltip" title="Remove Item"><em class="icon ni ni-cross-circle-fill"></em></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item-totals border-top mt-2 pt-2">
                            <div class="row gy-4 d-flex justify-content-end">
                                <div class="col-sm-7">
                                    <a href="" class="btn btn-dim btn-outline-primary mt-2 add-item" data-type="invoice"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <div class="fw-normal">Sub Total:<div class="fw-bold sub-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <div class="fw-normal">Tax:<div class="fw-bold tax-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <div class="fw-bold fs-19px border-top">Total:<div class="fw-bold grand-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                </div>
                                <div class="col-sm-1">
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="row gy-4 border-top mt-1">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Invoice Date</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Invoice Date" value="<?=  date('Y-m-d') ; ?>" name="invoice_date" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Receipt Due</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Payment Due" value="<?=  date('Y-m-d') ; ?>" name="due_date" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Notes</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Notes" rows="2" name="notes"></textarea>
                                        </div>
                                        <div class="form-note">Notes will be printed on the invoice.</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Receipt Details</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Payment Details" rows="2" name="payment_details"><?=  $user->parent->payment_details ; ?></textarea>
                                        </div>
                                        <div class="form-note">receipt details will be printed on the invoice.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Create Invoice</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal add payment -->
    <div class="modal fade" tabindex="-1" id="addpayment">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add Receipt</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Projectpayment@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add Receipt</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Select Invoice</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2" name="invoice" required="">
                                                <option value="">Select Invoice</option>
                                                <?php if (!empty($invoices)) { ?>
                                                <?php foreach ($invoices as $invoice) { ?>
                                                    <?php if ($invoice->status != "Paid") { ?>
                                                    <option value="<?=  $invoice->id ; ?>"><?=  $client->fullname ; ?> • <?=  $project->registration_number ; ?> • Invoice #<?=  $invoice->id ; ?> ( <?=  currency($user->parent->currency) ; ?><?=  $invoice->balance ; ?> )</option>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <div class="form-note">The amout in brackets is the balance due.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Amount</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="amount" value="0.00" step="0.01" min="0.01" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Receipt Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Payment Date" value="<?=  date('Y-m-d') ; ?>" name="payment_date" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Method</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="method">
                                                <option value="Cash">Cash</option>
                                                <option value="Card">Card</option>
                                                <option value="Mobile Money">Mobile Money</option>
                                                <option value="Bank">Bank</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Online Payment">Online Payment</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Note</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg" placeholder="Note" rows="2" name="note"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Add Receipt</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal add payment -->
    <div class="modal fade" tabindex="-1" id="add_s_payment">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Supplierpayment@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                <!-- <form class="simcy-form" action="/project/payments/createspayments" data-parsley-validate="" method="POST" > -->
                    <div class="modal-body">
                        <p>Add Payment</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Select Expense</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2" name="s_expense" required="">
                                                <option value="">Select Expense</option>
                                                <?php if (!empty($pay_expenses)) { ?>
                                                    <?php foreach ($pay_expenses as $pay_expense) { ?>
                                                        <?php if ($pay_expense->paid == "No") { ?>
                                                        <option value="<?=  $pay_expense->id ; ?>">Expense #<?=  $pay_expense->id ; ?> ( <?=  currency($user->parent->currency) ; ?><?=  $pay_expense->amount ; ?> )</option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <div class="form-note">The amout in brackets is the balance due.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Amount</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="amount" value="0.00" step="0.01" min="0.01" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Payment Date" value="<?=  date('Y-m-d') ; ?>" name="payment_date" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Method</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="method">
                                                <option value="Cash">Cash</option>
                                                <option value="Card">Card</option>
                                                <option value="Mobile Money">Mobile Money</option>
                                                <option value="Bank">Bank</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Online Payment">Online Payment</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Note</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg" placeholder="Note" rows="2" name="note"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Add Payment</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Modal add payment -->
    <div class="modal fade" tabindex="-1" id="invoicepayment">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add Receipt</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Projectpayment@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add receipt</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Amount</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="amount" value="0.00" step="0.01" min="0.01" required="">
                                        <input type="hidden" name="invoice" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Payment Date" value="<?=  date('Y-m-d') ; ?>" name="payment_date" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Method</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="method">
                                                <option value="Cash">Cash</option>
                                                <option value="Card">Card</option>
                                                <option value="Mobile Money">Mobile Money</option>
                                                <option value="Bank">Bank</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Online Payment">Online Payment</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Note</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg" placeholder="Note" rows="2" name="note"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Add Receipt</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

    <!-- Modal create project -->
    <div class="modal fade" tabindex="-1" id="convertquote">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Convert Quote to Invoice</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Quote@convert') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Convert Quote to Invoice</p>
                        <div class="row gy-4">

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Invoice Date</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Invoice Date" value="<?=  date('Y-m-d') ; ?>" name="invoice_date" required="">
                                            <input type="hidden" name="quote" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Payment Due</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Payment Due" value="<?=  date('Y-m-d') ; ?>" name="due_date" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Notes</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Notes" rows="2" name="notes"></textarea>
                                        </div>
                                        <div class="form-note">Notes will be printed on the invoice.</div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Payment Details</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Payment Details" rows="4" name="payment_details"><?=  $user->parent->payment_details ; ?></textarea>
                                        </div>
                                        <div class="form-note">Payment details will be printed on the invoice.</div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Convert Quote</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
    <script src="<?=  asset('assets/libs/summernote/summernote-lite.min.js') ; ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote({
                placeholder: 'Write something',
                height: 120,
            });

            if(<?= $Isqt; ?> == true){
                $("#createquote").modal("show");
            }
        });
        
    </script>
</body>

</html>
<?php return;
