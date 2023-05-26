<?php global $s_v_data, $user, $title, $invoices, $clients; ?>
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
                                            <h3 class="nk-block-title page-title">Invoices</h3>
                                            <div class="nk-block-des text-soft">
                                                <?php if (!empty($invoices)) { ?>
                                                <p>A total of <?=  number_format(count($invoices)) ; ?> invoices.</p>
                                                <?php } else { ?>
                                                <p>Create and manage your clients' invoices here.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li><a href="" class="btn btn-primary"  data-toggle="modal" data-target="#create"><em class="icon ni ni-plus"></em><span>Create Invoice</span></a></li>
                                                    </ul>
                                                </div>
                                            </div><!-- .toggle-wrap -->
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-stretch">
                                        <div class="card-inner">
                                            <div class="row">
                                                <div class="col-sm-12" style="display: flex; flex-direction: row-reverse;">
                                                    <div class="form-group">
                                                         <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="filter_by_date" id="filter_by_date" class="custom-control-input expense-paid" value="Yes">
                                                            <label class="custom-control-label" for="filter_by_date">Include Date</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <span>From: <input type="date" name="from_date" id="from_date" class="form-control" value="<?=  date('Y-m-d') ; ?>"></span>
                                                </div>
                                                <div class="col-md-3">
                                                    <span>To: <input type="date" name="to_date" id="to_date" class="form-control" value="<?=  date('Y-m-d') ; ?>"> </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="status"></label>
                                                    <select class="form-control" name="status" id="status">
                                                        <option value="all">Select Status</option>
                                                        <option value="paid">Paid</option>
                                                        <option value="partial">Partial</option>
                                                        <option value="unpaid">Unpaid</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <br>
                                            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false" id="datatable_init_invoice" style="width: 100%;">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col text-center">#</th>
                                                        <th class="nk-tb-col"><span class="sub-text">Client</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Project</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Items</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Date / Due</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Total / Balance</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        <th style="display: none;"></th>
                                                        <th style="display: none;"></th>
                                                        <th style="display: block;"></th>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (!empty($invoices)) { ?>
                                                <?php foreach ($invoices as $index => $invoice) { ?>
                                                <tr class="nk-tb-item">
                                                    <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                    <td class="nk-tb-col">
                                                        <div class="user-card">
                                                            <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                <span><?=  mb_substr($invoice->client->fullname, 0, 2, "UTF-8") ; ?></span>
                                                            </div>
                                                            <div class="user-info">
                                                                <span class="tb-lead"><?=  $invoice->client->fullname ; ?> <span class="dot dot-success d-md-none ml-1"></span></span>
                                                                <span><?=  $invoice->client->phonenumber ; ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <div class="user-card">
                                                            <div class="user-info">
                                                                <span class="tb-lead"><?=  $invoice->project->make ; ?> <?=  $invoice->project->model ; ?></span>
                                                                <span><?=  $invoice->project->registration_number ; ?></span>
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
                                                    <td class="nk-tb-col tb-col-md">
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
                                                                            <li><a href="" class="add-payment" data-id="<?=  $invoice->id ; ?>"><em class="icon ni ni-coin-alt"></em><span>Add Payment</span></a></li>
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
                                                    <td style="display: none;">
                                                        <?= money($invoice->total, $user->parent->currency) ; ?>
                                                    </td>
                                                    <td style="display: none;">
                                                        <?=  date("Y-m-d", strtotime($invoice->invoice_date)) ; ?>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <?php if ($invoice->status == "Paid") { ?>
                                                            Paid
                                                        <?php } else if ($invoice->status == "Partial") { ?>
                                                            Partial
                                                        <?php } else { ?>
                                                            Unpaid
                                                        <?php } ?>
                                                    </td>
                                                </tr><!-- .nk-tb-item  -->
                                                <?php } ?>
                                                <?php } else { ?>
                                                <tr>
                                                    <td class="text-center" colspan="6">It's empty here!</td>
                                                </tr>
                                                <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="text-right nk-tb-col tb-col-md" colspan="5">
                                                            <span class="fw-bold">Total:</span> <small>perpage</small> <br> 
                                                            <span class="fw-bold">Total:</span> <small>all</small></td>
                                                        <td class="nk-tb-col tb-col-md"> <span class="tb-amount"><?=  money(0, $user->parent->currency) ; ?></span> </td>
                                                    </tr>
                                                </tfoot>
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



        <!-- Modal create invoice -->
    <div class="modal fade" tabindex="-1" id="create">
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
                        <p>Create an invoice</p>
                        <div class="item-totals border-bottom">
                            <div class="row gy-4">
                                <div class="col-12">
                                    <div class="form-group mb-2">
                                        <label class="form-label">Select Project</label>
                                        <div class="form-control-wrap">
                                            <select class="form-control form-control-lg grouped" name="project" required="">
                                                <option value="">Select Project</option>
                                                <?php if (!empty($clients)) { ?>
                                                <?php foreach ($clients as $client) { ?>
                                                  <optgroup label="<?=  $client->fullname ; ?>">
                                                    <?php if (!empty($client->projects)) { ?>
                                                    <?php foreach ($client->projects as $project) { ?>
                                                    <option value="<?=  $project->id ; ?>"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> - <?=  $project->registration_number ; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                  </optgroup>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item-lines" data-type="quote">
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
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required="">
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
                                            <input type="number" class="form-control form-control-lg line-total" placeholder="Amount" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="total[]" value="0.00" step="0.01" required="" readonly="">
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
                                            <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
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
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required="">
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
                                            <input type="number" class="form-control form-control-lg line-total" placeholder="Total" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="total[]" value="0.00" step="0.01" required="" readonly="">
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
                                <div class="col-sm-4">
                                    <a href="" class="btn btn-dim btn-outline-primary mt-2 add-item" data-type="invoice"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                                </div>
                                <div class="col-sm-7 text-right">
                                    <div class="fw-normal">Sub Total:<div class="fw-bold sub-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <div class="fw-normal">Tax:<div class="fw-bold tax-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <!--  <div class="fw-nor<div class="fw-normal" style="margin-top: 5px;">Discount Description <div class="fw-bold insurance-exception-amount"> <input type="text" name="" id=""> </div> </div> <input type="number"  class="form-control text-right" name="insurance_exception_amount" id="insurance_exception_amount" value="0.00"> -->mal">Discount: <div class="fw-bold discount"> <input type="number"  class="form-control text-right" name="discount" id="discount" value="0.00"> </div> </div>
                                    <div class="fw-bold fs-19px border-top">Total:<div class="fw-bold grand-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <input type="text" name="SubTotal" id="SubTotal">
                                    <input type="text" name="GrandTotal" id="GrandTotal">
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
                                        <label class="form-label">Payment Due</label>
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
                                        <label class="form-label">Payment Details</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Payment Details" rows="2" name="payment_details"><?=  $user->parent->payment_details ; ?></textarea>
                                        </div>
                                        <div class="form-note">Payment details will be printed on the invoice.</div>
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
    <div class="modal fade" tabindex="-1" id="invoicepayment">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Projectpayment@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add payment</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Amount</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Amount" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="amount" value="0.00" step="0.01" min="0.01" required="">
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
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Add Payment</span></button>
                    </div>
                </form>
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
                matcher: function(params, data) {
                    var original_matcher = $.fn.select2.defaults.defaults.matcher;
                    var result = original_matcher(params, data);
                    if (result && data.children && result.children && data.children.length != result.children.length
                        && data.text.toLowerCase().includes(params.term)) {
                        result.children = data.children;
                    }
                    return result;
                }
            });

            $('#datatable_init_invoice').DataTable({
    paging:true,
    ordering:true,
    info: true,
    "footerCallback": function(row, data){
        var total = 0;
        console.log(data);
        var api = this.api(), data;
        
        // Remove the formatting to get integer data for summation
        var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\AED,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };

        var total= api
                        .column( 8 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        },0);

        var pageTotal = api
                .column(8, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

        $( api.column(5).footer() ).html('AED '+ pageTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</br>' + 'AED ' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        
        // alert(pageTotal);k
        
    }
});

$('#from_date, #to_date, #status').on('change',function(){

    // DataTables initialisation
    var table = $('#datatable_init_invoice').DataTable();
    // Refilter the table
    table.draw();
});

$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var date = new Date(data[9]).getDate();
        var month = new Date(data[9]).getMonth() + 1;
        var year = new Date(data[9]).getFullYear();
        var status = $('#status').val();
        var data_status = data[10];


        if(month <= 9){
            month = '0'+month;
        }

        if(date <= 9){
            date = '0'+date;
        }

        var full_date = year+'-'+month+'-'+date;
        
        if($('#filter_by_date').is(':checked')){
            
            if (full_date >= from_date && full_date <= to_date) 
            {
                if(status == 'all'){
                    return true
                }
                if(status == 'paid' && data_status == 'Paid'){
                        return true
                }
                if(status == 'partial' && data_status == 'partial'){
                        return true
                }
                if(status == 'unpaid' && data_status == 'Unpaid'){
                        return true
                }
            }
        }else{
            if(status == 'all'){
                return true
            }
            if(status == 'paid' && data_status == 'Paid'){
                    return true
            }
            if(status == 'partial' && data_status == 'partial'){
                        return true
            }
            if(status == 'unpaid' && data_status == 'Unpaid'){
                    return true
            }

        }
        
        return false;
        });

        });
    </script>
</body>

</html>
<?php return;
