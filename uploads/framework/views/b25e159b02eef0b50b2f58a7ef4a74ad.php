<?php global $s_v_data, $user, $title, $payments, $clients, $pay_expenses, $employees; ?>
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
                                            <h3 class="nk-block-title page-title">Payments</h3>
                                            <div class="nk-block-des text-soft">
                                                <?php if (!empty($payments)) { ?>
                                                <p>A total of <?=  number_format(count($payments)) ; ?> payments.</p>
                                                <?php } else { ?>
                                                <p>Create and manage your clients' payments here.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1"
                                                    data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li><a href="" class="btn btn-primary" data-toggle="modal"
                                                                data-target="#create"><em
                                                                    class="icon ni ni-plus"></em><span>Add
                                                                    Payment</span></a></li>
                                                    </ul>
                                                </div>
                                            </div><!-- .toggle-wrap -->
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-stretch">
                                        <div class="card-inner">
                                            <table class="datatable-init nk-tb-list nk-tb-ulist"
                                                data-auto-responsive="false">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col text-center">#</th>
                                                        <th class="nk-tb-col"><span class="sub-text">Supplier</span>
                                                        </th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Item /
                                                                Remark</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span
                                                                class="sub-text">Payment#</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Payment
                                                                Date</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span
                                                                class="sub-text">Total</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span
                                                                class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- <?= $total = 0; ?> -->
                                                    <?php if (!empty($payments)) { ?>
                                                    <?php foreach ($payments as $index => $payment) { ?>
                                                    <!-- <?=  $total =  $total + $payment->amount; ?> -->
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <div
                                                                    class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                    <span>
                                                                        <?php if ($payment->isEmployeeExpense == 1) { ?>
                                                                            <?=  mb_substr($payment->employee->fname .' '. $payment->employee->lname, 0, 2,"UTF-8") ; ?>
                                                                        <?php } else { ?>
                                                                            <?=  mb_substr($payment->supplier->name, 0, 2,"UTF-8") ; ?>
                                                                        <?php } ?>
                                                                    
                                                                    </span>
                                                                </div>
                                                                <div class="user-info">
                                                                    <span class="tb-lead">
                                                                        <?php if ($payment->isEmployeeExpense == 1) { ?>
                                                                            <?=  $payment->employee->fname .' '. $payment->employee->lname ; ?>
                                                                        <?php } else { ?>
                                                                            <?=  $payment->supplier->name ; ?>
                                                                        <?php } ?>
                                                                    </span>
                                                                    <span>
                                                                        <?php if ($payment->isEmployeeExpense  == 1) { ?>
                                                                            <?=  $payment->employee->phonenumber ; ?>
                                                                        <?php } else { ?>
                                                                            <?=  $payment->supplier->phonenumber ; ?>
                                                                        <?php } ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <div class="user-card">
                                                                <div class="user-info">
                                                                    <span class="tb-lead">
                                                                        <?php if ($payment->isEmployeeExpense  == 1) { ?>
                                                                            <?=  $payment->note ; ?>
                                                                        <?php } else { ?>
                                                                            <?=  isset($payment->expense->expense) ? carmake($payment->expense->expense) : $payment->note ; ?>
                                                                        <?php } ?>
                                                                    </span>
                                                                    <span>
                                                                        <?php if (!$payment->isEmployeeExpense  == 1) { ?>
                                                                            <?=  isset($payment->expense->status) ? carmake($payment->expense->status) : '' ; ?>
                                                                        <?php } ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span class="tb-amount">Payment #<?=  $payment->id ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span><?=  date("F j, Y", strtotime($payment->payment_date))
                                                                ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span class="tb-amount"><?=  money($payment->amount,
                                                                $user->parent->currency) ; ?></span>
                                                            <span><?=  $payment->method ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span
                                                                class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Paid</span>
                                                        </td>
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="drodown">
                                                                        <a href="#"
                                                                            class="dropdown-toggle btn btn-icon btn-trigger"
                                                                            data-toggle="dropdown"><em
                                                                                class="icon ni ni-more-h"></em></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <ul class="link-list-opt no-bdr">
                                                                                <li><a
                                                                                        href="<?=  url('Supplierpayment@view', array('paymentid' => $payment->id)) ; ?>"><em
                                                                                            class="icon ni ni-eye"></em><span>View
                                                                                            Payment</span></a></li>
                                                                                <li><a class="fetch-display-click"
                                                                                        data="paymentid:<?=  $payment->id ; ?>"
                                                                                        url="<?=  url('Supplierpayment@updateview') ; ?>"
                                                                                        holder=".update-holder"
                                                                                        modal="#update" href=""><em
                                                                                            class="icon ni ni-pen"></em><span>Edit
                                                                                            Payment</span></a></li>
                                                                                <?php if ($user->role == "Owner") { ?>
                                                                                <li class="divider"></li>
                                                                                <li><a class="send-to-server-click"
                                                                                        data="paymentid:<?=  $payment->id ; ?>"
                                                                                        url="<?=  url('Supplierpayment@delete') ; ?>"
                                                                                        warning-title="Are you sure?"
                                                                                        warning-message="This payment will be deleted permanently."
                                                                                        warning-button="Yes, delete!"
                                                                                        href=""><em
                                                                                            class="icon ni ni-trash"></em><span>Delete
                                                                                            Payment</span></a></li>
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
                                            <!-- <?= $total; ?> -->
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

    <!-- Modal add payment -->
    <div class="modal fade" tabindex="-1" id="create">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Supplierpayment@create') ; ?>" data-parsley-validate=""
                    method="POST" loader="true">
                    <!-- <form class="simcy-form" action="/project/payments/createspayments" data-parsley-validate="" method="POST" > -->
                    <div class="modal-body modal-section">
                        <p>Add Payment</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_exployee_expense" id="is_exployee_expense"
                                            class="custom-control-input is_exployee_expense" value="Yes">
                                        <label class="custom-control-label" for="is_exployee_expense">Employee
                                            Expense</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 from-expenses">
                                <div class="form-group">
                                    <label class="form-label">Select Expense</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2" name="s_expense">
                                                <option value="">Select Expense</option>
                                                <?php if (!empty($pay_expenses)) { ?>
                                                echo $pay_expense;
                                                <?php foreach ($pay_expenses as $pay_expense) { ?>
                                                <?php if ($pay_expense->paid == "No") { ?>
                                                <option value="<?=  $pay_expense->id ; ?>">Expense #<?=  $pay_expense->id ; ?> (
                                                    <?=  currency($user->parent->currency) ; ?><?=  $pay_expense->amount ; ?> )
                                                </option>
                                                <?php } ?>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-note">The amout in brackets is the balance due.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 employee-expense" style="display: none;">
                                <div class="form-group">
                                    <label for="form-label">Select Employee</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2" name="employee_id">
                                                <option value="">Select Employee</option>
                                                <?php if (!empty($employees)) { ?>
                                                echo 'No data found...';
                                                    <?php foreach ($employees as $employee) { ?>
                                                    <option value="<?=  $employee->id ; ?>">
                                                            <?=  $employee->fname .' '. $employee->lname; ?>
                                                    </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
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
                                        <input type="number" class="form-control form-control-lg" placeholder="Amount"
                                            data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="amount" value="0.00"
                                            step="0.01" min="0.01" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Payment Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg"
                                            placeholder="Payment Date" value="<?=  date('Y-m-d') ; ?>" name="payment_date"
                                            required="">
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
                                        <textarea class="form-control form-control-lg" placeholder="Note" rows="2"
                                            name="note"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em
                                class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em
                                class="icon ni ni-check-circle-cut"></em><span>Add Payment</span></button>
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


    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.grouped').select2({
                dropdownParent: $('#create'),
                matcher: function (params, data) {
                    var original_matcher = $.fn.select2.defaults.defaults.matcher;
                    var result = original_matcher(params, data);
                    if (result && data.children && result.children && data.children.length != result.children.length
                        && data.text.toLowerCase().includes(params.term)) {
                        result.children = data.children;
                    }
                    return result;
                }
            });
        });
    </script>
</body>

</html>
<?php return;
