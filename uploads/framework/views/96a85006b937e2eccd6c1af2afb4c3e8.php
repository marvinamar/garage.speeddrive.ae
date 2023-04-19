<?php global $s_v_data, $user, $title, $member, $notes, $tasks, $projects, $payments, $expenses, $incomes; ?>
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
                                    <div class="nk-block-between g-3">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Team / <strong class="text-primary small"><?=  $member->fname ; ?> <?=  $member->lname ; ?></strong></h3>
                                            <div class="nk-block-des text-soft">
                                                <ul class="list-inline">
                                                    <li>Team ID: <span class="text-base">AT<?=  str_pad($member->id, 4, '0', STR_PAD_LEFT) ; ?></span></li>
                                                    <li>Created On: <span class="text-base"><?=  date("F j, Y h:ia", strtotime(timezoned($member->created_at, $user->parent->timezone))) ; ?></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="nk-block-head-content">
                                            <ul class="nk-block-tools g-3">
                                                <li>                                                  
                                                 <a href="<?=  url('Team@get') ; ?>" class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                                                    <a href="<?=  url('Team@get') ; ?>" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none"><em class="icon ni ni-arrow-left"></em></a>
                                                </li>
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" data-toggle="dropdown"><em class="icon ni ni-more-h"></em> <span>More</span></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a class="fetch-display-click" data="teamid:<?=  $member->id ; ?>" url="<?=  url('Team@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Details</span></a></li>
                                                                <li><a href="" class="send-sms" data-phonenumber="<?=  $member->phonenumber ; ?>" data-name="<?=  $member->fname ; ?> <?=  $member->lname ; ?>"><em class="icon ni ni-chat-circle"></em><span>Send SMS</span></a></li>
                                                                <li><a href="" data-toggle="modal" data-target="#teamreport"><em class="icon ni ni-reports"></em><span>View Report</span></a></li>
                                                                <?php if ($user->role == "Owner") { ?>
                                                                <li class="divider"></li>
                                                                <li><a href="" class="send-to-server-click"  data="teamid:<?=  $member->id ; ?>" url="<?=  url('Team@delete') ; ?>" warning-title="Are you sure?" warning-message="This member's profile and data will be deleted permanently." warning-button="Yes, delete!"><em class="icon ni ni-trash"></em><span>Delete Member</span></a></li>
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
                                                         <?php } ?>" href="<?=  url('Team@details', array('teamid' => $member->id)) ; ?>?details"><em class="icon ni ni-file-text"></em><span>Details</span></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'tasks') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Team@details', array('teamid' => $member->id)) ; ?>?view=tasks"><em class="icon ni ni-todo"></em><span>Tasks</span></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'payments') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Team@details', array('teamid' => $member->id)) ; ?>?view=payments"><em class="icon ni ni-align-left"></em><span>Payments</span></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link
                                                        <?php if (isset($_GET['view']) && $_GET['view'] == 'income_expense') { ?>
                                                         active
                                                         <?php } ?>" href="<?=  url('Team@details', array('teamid' => $member->id)) ; ?>?view=income_expense"><em class="icon ni ni-align-left"></em><span>Income / Expense Report</span></a>
                                                    </li>
                                                </ul><!-- .nav-tabs -->
                                                <?php if (!isset($_GET["view"])) { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block">
                                                        <div class="nk-block-head">
                                                            <h5 class="title">Team Information</h5>
                                                            <p>Basic team info, that gives team member summary.</p>
                                                        </div><!-- .nk-block-head -->
                                                        <div class="profile-ud-list">
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Member Name</span>
                                                                    <span class="profile-ud-value"><?=  $member->fname ; ?> <?=  $member->lname ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Phone Number</span>
                                                                    <span class="profile-ud-value"><?=  $member->phonenumber ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Email</span>
                                                                    <?php if (!empty($member->email)) { ?>
                                                                    <span class="profile-ud-value"><?=  $member->email ; ?></span>
                                                                    <?php } else { ?>
                                                                    <span class="profile-ud-value">--|--</span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Active Tasks</span>
                                                                    <span class="profile-ud-value"><?=  $member->pending_tasks ; ?> / <?=  $member->total_tasks ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Address</span>
                                                                    <?php if (!empty($member->address)) { ?>
                                                                    <span class="profile-ud-value"><?=  $member->address ; ?></span>
                                                                    <?php } else { ?>
                                                                    <span class="profile-ud-value">--|--</span>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Balance</span>
                                                                    <span class="profile-ud-value"><?=  money($member->balance, $user->parent->currency) ; ?></span>
                                                                </div>
                                                            </div>
                                                        </div><!-- .profile-ud-list -->
                                                    </div><!-- .nk-block -->
                                                    <div class="nk-block">
                                                        <div class="nk-block-head nk-block-head-line">
                                                            <h6 class="title overline-title text-base">Additional Information</h6>
                                                        </div><!-- .nk-block-head -->
                                                        <div class="profile-ud-list">
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Role</span>
                                                                    <span class="profile-ud-value">
                                                                        <?php if ($member->role == "Owner") { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Owner</span>
                                                                        <?php } else if ($member->role == "Manager") { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-primary d-mb-inline-flex">Manager</span>
                                                                        <?php } else if ($member->role == "Inventory Manager") { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Inventory Manager</span>
                                                                        <?php } else if ($member->role == "Booking Manager") { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-secondary d-mb-inline-flex">Booking Manager</span>
                                                                        <?php } else { ?>
                                                                        <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">Staff</span>
                                                                        <?php } ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Type</span>
                                                                    <span class="profile-ud-value"><?=  $member->type ; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="profile-ud-item">
                                                                <div class="profile-ud wider">
                                                                    <span class="profile-ud-label">Status</span>
                                                                    <span class="profile-ud-value">
                                                                        <?php if ($member->status == "Active") { ?>
                                                                        <span class="text-success">Active</span>
                                                                        <?php } else if ($member->status == "On Leave") { ?>
                                                                        <span class="text-warning">On Leave</span>
                                                                        <?php } else { ?>
                                                                        <span class="text-danger">Unavailable</span>
                                                                        <?php } ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div><!-- .profile-ud-list -->
                                                    </div><!-- .nk-block -->
                                                    <div class="nk-divider divider md"></div>
                                                    <div class="nk-block">
                                                        <div class="nk-block-head nk-block-head-sm nk-block-between">
                                                            <h5 class="title">Team Notes
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
                                                </div><!-- .card-inner -->
                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "tasks") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <h5 class="title">Assigned Tasks</h5>
                                                            <p>A list of tasks assigned to <?=  $member->fname ; ?> <?=  $member->lname ; ?>.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->

                                                    <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                        <thead>
                                                            <tr class="nk-tb-item nk-tb-head">
                                                                <th class="nk-tb-col text-center">#</th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Project</span></th>
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
                                                                            <span class="tb-lead"><?=  carmake($task->project->make) ; ?> <?=  carmodel($task->project->model) ; ?></span>
                                                                        </div>
                                                                    </div>
                                                                    <span><?=  $task->project->registration_number ; ?></span>
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
                                                                                    <li class="divider"></li>
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
                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "payments") { ?>
                                                <div class="card-inner">
                                                    <div class="nk-block mb-2">
                                                        <div class="nk-block-head">
                                                            <a href="" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addpayment"><em class="icon ni ni-plus"></em><span>Add Payment</span></a>
                                                            <h5 class="title">Payments</h5>
                                                            <p>A list of payments made to <?=  $member->fname ; ?> <?=  $member->lname ; ?>.</p>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->

                                                    <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                        <thead>
                                                            <tr class="nk-tb-item nk-tb-head">
                                                                <th class="nk-tb-col text-center">#</th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Member</span></th>
                                                                <th class="nk-tb-col"><span class="sub-text">Amount</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Date</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Mode</span></th>
                                                                <th class="nk-tb-col tb-col-md"><span class="sub-text">Note</span></th>
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
                                                                            <span class="tb-lead"><?=  $member->fname ; ?> <?=  $member->lname ; ?> <span class="dot dot-success d-md-none ml-1"></span></span>
                                                                        </div>
                                                                    </div>
                                                                    <span><?=  $member->phonenumber ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span class="tb-amount"><?=  money($payment->amount, $user->parent->currency) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span><?=  date("F j, Y", strtotime($payment->payment_date)) ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <span><?=  $payment->mode ; ?></span>
                                                                </td>
                                                                <td class="nk-tb-col tb-col-md">
                                                                    <p class="text-ellipsis w-100px"><span><?=  $payment->note ; ?></span></p>
                                                                </td>
                                                                <td class="nk-tb-col nk-tb-col-tools">
                                                                    <ul class="nk-tb-actions gx-1">
                                                                        <li>
                                                                            <div class="drodown">
                                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                                    <ul class="link-list-opt no-bdr">
                                                                                    <li><a class="fetch-display-click" data="paymentid:<?=  $payment->id ; ?>" url="<?=  url('Teampayment@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Details</span></a></li>
                                                                                    <?php if ($user->role == "Owner") { ?>
                                                                                    <li class="divider"></li>
                                                                                    <li><a class="send-to-server-click"  data="paymentid:<?=  $payment->id ; ?>" url="<?=  url('Teampayment@delete') ; ?>" warning-title="Are you sure?" warning-message="This payment will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Payment</span></a></li>
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
                                                <?php } else if (isset($_GET["view"]) && $_GET["view"] == "income_expense") { ?>
                                                <div class="card-inner row">

                                                    <!-- Income Part -->

                                                    <div class="col-md-6">
                                                        
                                                        <div class="nk-block mb-2">
                                                            <div class="nk-block-head">
                                                                <h5 class="title">Income</h5>
                                                                <!-- <p>A list of payments made to <?=  $member->fname ; ?> <?=  $member->lname ; ?>.</p> -->
                                                            </div><!-- .nk-block-head -->
                                                        </div><!-- .nk-block -->
    
                                                        <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                            <thead>
                                                                <tr class="nk-tb-item nk-tb-head">
                                                                    <th class="nk-tb-col text-center">#</th>
                                                                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Date</span></th>
                                                                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Note</span></th>
                                                                    <th class="nk-tb-col"><span class="sub-text">Amount</span></th>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (!empty($incomes)) { ?>
                                                                <?php foreach ($incomes as $index => $income) { ?>
                                                                <?= $total_invoice = $income->cost; ?>
                                                                <tr class="nk-tb-item">
                                                                    <td class="nk-tb-col text-center"><?=  $index + 1; ?></td>
                                                                    <td class="nk-tb-col tb-col-md">
                                                                        <span><?=  date("F j, Y", strtotime($income->due_date)) ; ?></span>
                                                                    </td>
                                                                    <td class="nk-tb-col tb-col-md">
                                                                        <p class="text-ellipsis w-100px"><span><?=  $income->title ; ?></span></p>
                                                                    </td>
                                                                    <td class="nk-tb-col">
                                                                        <span class="tb-amount"><?=  money($income->cost, $user->parent->currency) ; ?></span>
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
                                                        
                                                        <div class="col-md-12" style="text-align: right;">
                                                            <span>Total Income: <?=  $total_invoice ; ?></span>
                                                        </div>
                                                    </div>


                                                    <!-- Expense Part -->

                                                    <div class="col-md-6">
                                                        <div class="nk-block mb-2">
                                                            <div class="nk-block-head">
                                                                <h5 class="title">Expense</h5>
                                                            </div><!-- .nk-block-head -->
                                                        </div><!-- .nk-block -->
    
                                                        <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                            <thead>
                                                                <tr class="nk-tb-item nk-tb-head">
                                                                    <th class="nk-tb-col text-center">#</th>
                                                                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Date</span></th>
                                                                    <th class="nk-tb-col tb-col-md"><span class="sub-text">Details</span></th>
                                                                    <th class="nk-tb-col"><span class="sub-text">Amount</span></th>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (!empty($expenses)) { ?>
                                                                <?php foreach ($expenses as $index => $expense) { ?>
                                                                <tr class="nk-tb-item">
                                                                    <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                                    <td class="nk-tb-col tb-col-md">
                                                                        <span><?=  date("F j, Y", strtotime($expense->payment_date)) ; ?></span>
                                                                    </td>
                                                                    <td class="nk-tb-col tb-col-md">
                                                                        <p class="text-ellipsis w-100px"><span><?=  $expense->note ; ?></span></p>
                                                                    </td>
                                                                    <td class="nk-tb-col">
                                                                        <span class="tb-amount"><?=  money($expense->amount, $user->parent->currency) ; ?></span>
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
                                                    </div>

                                                </div><!-- .card-inner -->
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
                                        <input type="hidden" name="item" value="<?=  $member->id ; ?>" required="">
                                        <input type="hidden" name="type" value="Team" required="">
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


    <!-- Modal add payment -->
    <div class="modal fade" tabindex="-1" id="addpayment">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Teampayment@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Record a payment</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Amount</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Amount" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="amount" value="0.00" step="0.01" required="">
                                        <input type="hidden" name="member" value="<?=  $member->id ; ?>" required="">
                                    </div>
                                    <div class="form-note">Payment amount can't be updated once saved.</div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Payment Note</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Payment Note" name="note">
                                    </div>
                                    <div class="form-note">Optional</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Payment Date" name="payment_date" value="<?=  date('Y-m-d') ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Mode</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="mode">
                                                <option value="Cash">Cash</option>
                                                <option value="Bank">Bank</option>
                                                <option value="Mobile Payment">Mobile Payment</option>
                                                <option value="Online Payment">Online Payment</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                     <div class="custom-control custom-switch">
                                        <input type="checkbox" name="deduct" id="deduct" class="custom-control-input" value="Yes" checked="">
                                        <label class="custom-control-label" for="deduct">Deduct from <?=  $member->fname ; ?>'s balance of <strong><?=  money($member->balance, $user->parent->currency) ; ?></strong>?</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Save Payment</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal send sms -->
    <div class="modal fade" tabindex="-1" id="sendsms">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Send SMS</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Marketing@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create an SMS</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Send To</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Send To" name="name" required="" readonly="">
                                        <input type="hidden" name="sendto" value="enternumber" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <div class="form-control-wrap ">
                                        <input type="text" class="form-control form-control-lg" name="phonenumber" placeholder="Phone Number" readonly="" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Message</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg" placeholder="Message" rows="3" name="message" required=""></textarea>
                                    </div>
                                    <div class="form-note">We'll include your company name <strong><?=  $user->parent->name ; ?></strong> at the end of the message.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Send Message</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Report Modal -->
    <div class="modal fade" tabindex="-1" id="teamreport">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">View Report</h5>
                </div>
                <form action="<?=  url('Teamreport@view') ; ?>" data-parsley-validate="" method="GET" loader="true">
                    <div class="modal-body">
                        <p>Generate and view report</p>
                        <input type="hidden" name="staff" value="<?=  $member->id ; ?>" required="">
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Project</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg select2" name="project" required="">
                                                <option value="All">All Projects</option>
                                                <?php if (!empty($projects)) { ?>
                                                <?php foreach ($projects as $project) { ?>
                                                <option value="<?=  $project->id ; ?>"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Tasks Status</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="status">
                                                <option value="All">All</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>View Report</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
</body>

</html>
<?php return;
