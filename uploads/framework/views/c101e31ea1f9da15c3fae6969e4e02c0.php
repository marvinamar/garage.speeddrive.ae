<?php global $s_v_data, $user, $title, $tasks; ?>
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
                                            <h3 class="nk-block-title page-title"><?=  $title ; ?></h3>
                                            <div class="nk-block-des text-soft">
                                                <p>List of pending tasks.</p>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-stretch">
                                        <div class="card-inner">
                                            <table class="datatable-init nk-tb-list nk-tb-ulist mt" data-auto-responsive="false">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col text-center">#</th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Project</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Assigned To</span></th>
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
                                                                    <span><?=  $task->project->registration_number ; ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <?php if (!empty($task->member)) { ?>
                                                            <span class="tb-amount"><?=  $task->member->fname ; ?> <?=  $task->member->lname ; ?></span>
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
                                                                <?php if ($task->due_date < date("Y-m-d")) { ?>
                                                                <span class="text-danger">
                                                                        <?=  date("M j, Y", strtotime($task->due_date)) ; ?>
                                                                        <?php if (!empty($task->due_time)) { ?>
                                                                        • <?=  date("h:ia", strtotime($task->due_time)) ; ?>
                                                                        <?php } ?>
                                                                    <span data-toggle="tooltip" title="Overdue"><em class="icon ni ni-info-fill"></em></span>
                                                                </span>
                                                                <?php } else { ?>
                                                                <span>
                                                                        <?=  date("M j, Y", strtotime($task->due_date)) ; ?>
                                                                        <?php if (!empty($task->due_time)) { ?>
                                                                        • <?=  date("h:ia", strtotime($task->due_time)) ; ?>
                                                                        <?php } ?>
                                                                </span>
                                                                <?php } ?>
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
                                                                            <li><a href="<?=  url('Projects@details', array('projectid' => $task->project->id)) ; ?>"><em class="icon ni ni-eye"></em><span>View Project</span></a></li>
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
                                        </div>
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


    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
</body>

</html>
<?php return;
