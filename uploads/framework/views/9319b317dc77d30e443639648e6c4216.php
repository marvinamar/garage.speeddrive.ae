<?php global $s_v_data, $user, $title, $expenses; ?>
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
                                                <p>List of services and parts that are not paid for.</p>
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
                                                            <th class="nk-tb-col"><span class="sub-text">Expense / Supplier</span></th>
                                                            <th class="nk-tb-col tb-col-md"><span class="sub-text">Date / Due Date</span></th>
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
                                                                        <span class="tb-lead"><?=  carmake($expense->project->make) ; ?> <?=  carmodel($expense->project->model) ; ?></span>
                                                                        <span><?=  $expense->project->registration_number ; ?></span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="nk-tb-col">
                                                                <span class="tb-amount"><?=  $expense->expense ; ?></span>
                                                                <?php if (!empty($expense->supplier)) { ?>
                                                                <span><?=  $expense->supplier->name ; ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="nk-tb-col tb-col-md">
                                                                <span><?=  date("F j, Y", strtotime($expense->expense_date)) ; ?></span><br>
                                                                <?php if ($expense->payment_due < date("Y-m-d") && $expense->status != "To Order") { ?>
                                                                <span class="text-danger">
                                                                    <?=  date("F j, Y", strtotime($expense->payment_due)) ; ?> 
                                                                    <span data-toggle="tooltip" title="Overdue"><em class="icon ni ni-info-fill"></em></span>
                                                                </span>
                                                                <?php } else if ($expense->status != "To Order") { ?>
                                                                <span><?=  date("F j, Y", strtotime($expense->payment_due)) ; ?></span>
                                                                <?php } ?>
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
                                                                                <li><a class="fetch-display-click" data="expenseid:<?=  $expense->id ; ?>" url="<?=  url('Expenses@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Expense</span></a></li>
                                                                                <li><a href="<?=  url('Projects@details', array('projectid' => $expense->project->id)) ; ?>"><em class="icon ni ni-eye"></em><span>View Project</span></a></li>
                                                                                <?php if ($user->role == "Owner") { ?>
                                                                                <li class="divider"></li>
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
