<?php global $s_v_data, $user, $title, $quotes, $clients, $inventorys; ?>
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
                                            <h3 class="nk-block-title page-title">Quotes</h3>
                                            <div class="nk-block-des text-soft">
                                                <?php if (!empty($quotes)) { ?>
                                                <p>A total of <?=  number_format(count($quotes)) ; ?> quotes.</p>
                                                <?php } else { ?>
                                                <p>Create and manage your clients' quotes here.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li><a href="" class="btn btn-primary"  data-toggle="modal" data-target="#create"><em class="icon ni ni-plus"></em><span>Create a Quote</span></a></li>
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
                                                <div class="col-sm-3">
                                                    <span>From: <input type="date" name="from_date" id="from_date" class="form-control" value="<?=  date('Y-m-d') ; ?>"></span>
                                                </div>
                                                <div class="col-sm-3">
                                                    <span>To: <input type="date" name="to_date" id="to_date" class="form-control" value="<?=  date('Y-m-d') ; ?>"> </span>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <br>
                                            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false" id="datatable_init_quotes" style="width: 100%;">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col text-center">#</th>
                                                        <th class="nk-tb-col"><span class="sub-text">Client</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Project</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Items</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Date</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Total</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        </th>
                                                        <th style="display: none;"></th>
                                                        <th style="display: none;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (!empty($quotes)) { ?>
                                                <?php foreach ($quotes as $index => $quote) { ?>
                                                <tr class="nk-tb-item">
                                                    <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                    <td class="nk-tb-col">
                                                        <div class="user-card">
                                                            <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                <span><?=  mb_substr($quote->client->fullname, 0, 2, "UTF-8") ; ?></span>
                                                            </div>
                                                            <div class="user-info">
                                                                <span class="tb-lead"><?=  $quote->client->fullname ; ?></span>
                                                                <span><?=  $quote->client->phonenumber ; ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <div class="user-card">
                                                            <div class="user-info">
                                                                <span class="tb-lead"><?=  carmake($quote->project->make) ; ?> <?=  carmodel($quote->project->model) ; ?></span>
                                                                <span><?=  $quote->project->registration_number ; ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <span class="tb-amount"><?=  $quote->items ; ?></span>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <span><?=  date("F j, Y", strtotime($quote->created_at)) ; ?></span>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <span class="tb-amount"><?=  money($quote->total, $user->parent->currency) ; ?></span>
                                                    </td>
                                                    <td class="nk-tb-col nk-tb-col-tools">
                                                        <ul class="nk-tb-actions gx-1">
                                                            <li>
                                                                <div class="drodown">
                                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <ul class="link-list-opt no-bdr">
                                                                        <li><a href="<?=  url('Quote@view', array('quoteid' => $quote->id)) ; ?>"><em class="icon ni ni-eye"></em><span>View Quote</span></a></li>
                                                                        <li><a href="<?=  url('Quote@render', array('quoteid' => $quote->id)) ; ?>" download="Quote #<?=  $quote->id ; ?>.pdf"><em class="icon ni ni-download-cloud"></em><span>Download</span></a></li>
                                                                        <li><a href="" class="send-via-email" data-url="<?=  url('Quote@send') ; ?>" data-id="<?=  $quote->id ; ?>" data-subject="Quote #<?=  $quote->id ; ?>"><em class="icon ni ni-mail"></em><span>Send Via Email</span></a></li>
                                                                        <li><a class="fetch-display-click" data="quoteid:<?=  $quote->id ; ?>" url="<?=  url('Quote@updateviewv2') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-pen"></em><span>Edit Quote</span></a></li>
                                                                        <li><a class="convert-quote" data-id="<?=  $quote->id ; ?>" href=""><em class="icon ni ni-cc"></em><span>Convert to Invoice</span></a></li>
                                                                        <?php if ($user->role == "Owner") { ?>
                                                                        <li class="divider"></li>
                                                                        <li><a class="send-to-server-click"  data="quoteid:<?=  $quote->id ; ?>" url="<?=  url('Quote@delete') ; ?>" warning-title="Are you sure?" warning-message="This quote will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Quote</span></a></li>
                                                                        <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td style="display: none;"><?=  money($quote->total, $user->parent->currency) ; ?></td>
                                                    <td style="display: none;"><?=  date("Y-m-d", strtotime($quote->created_at)) ; ?></td>
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
                                                        <td class="text-right nk-tb-col tb-col-md" colspan="5"><span class="fw-bold">Total:</span></td>
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



    <!-- Modal create quote -->
    <div class="modal fade" id="create">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Create Quote</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Quote@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create a quote for this project</p>
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
                        <div class="item-lines mt-2" data-type="quote">
                            <div class="row gy-4 item_list">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Item</label>
                                        <div class="form-control-wrap">
                                            <select name="item[]" id="item[]" class="select_1 form-control form-control-lg" data-live-search="true" onchange="get_item_details(this)">
                                                <option value="0">Select Item</option>
                                                <?php foreach ($inventorys as $inventory) { ?>
                                                <option value="<?= $inventory->id; ?>"><?= $inventory->name; ?></option>
                                                <?php } ?>
                                            </select>
                                            <!-- <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required=""> -->
                                            <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" name="item_description[]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Work</label>
                                        <div class="form-control-wrap">
                                            <select class="form-control form-control-lg" name="workType[]">
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
                                <div class="col-sm-2 cost_line">
                                    <div class="form-group">
                                        <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-cost cost_1" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required="">
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
                                            <input type="number" class="form-control form-control-lg line-total" placeholder="Amount" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="total[]" value="0.00" step="0.01" required="" readonly="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                </div>
                            </div>
                            <div class="row gy-4 item_list">

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Item</label>
                                        <div class="form-control-wrap">
                                            <select name="item[]" id="item[]" class="select_2 form-control form-control-lg" data-live-search="true" onchange="get_item_details(this)">
                                                <option value="0">Select Item</option>
                                                <?php foreach ($inventorys as $inventory) { ?>
                                                <option value="<?= $inventory->id; ?>"><?= $inventory->name; ?></option>
                                                <?php } ?>
                                            </select>
                                            <!-- <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required=""> -->
                                            <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" name="item_description[]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Work</label>
                                        <div class="form-control-wrap">
                                            <select class="form-control form-control-lg" name="workType[]">
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
                                            <input type="number" class="form-control form-control-lg line-cost cost_2" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required="">
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
                                <div class="col-sm-7">
                                    <a href="" class="btn btn-dim btn-outline-primary mt-2 add-item-quote" data-type="invoice"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
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
                        <div class="item-totals border-top mt-2">
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
                <div class="col-md-12">
                    <input type="hidden" name="count" id="count" value="2">
                </div>
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

            $('#datatable_init_quotes').DataTable({
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

                var pageTotal = api
                        .column(7, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                $( api.column(5).footer() ).html('AED '+ pageTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                
                // alert(pageTotal);k
                
            }
            });

            $('#from_date, #to_date').on('change',function(){
                // DataTables initialisation
                var table = $('#datatable_init_quotes').DataTable();
                // Refilter the table
                table.draw();
            });

            $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var date = new Date(data[8]).getDate();
                var month = new Date(data[8]).getMonth() + 1;
                var year = new Date(data[8]).getFullYear();

                if(month <= 9){
                    month = '0'+month;
                }

                var full_date = year+'-'+month+'-'+date;
        
                
                if (full_date >= from_date && full_date <= to_date) 
                {
                    return true;
                }
                return false;
            });
            
        });// --END
    </script>
    <script>
        function get_item_details(select){
            var selected = select.value;
            var selectedClass = select.classList[0];
            var count = selectedClass.replace('select_','');
            
            $.ajax({
                url: '<?=  url("Quote@get_item_details") ; ?>' + selected,
                data: [],
                dataType: 'json',
                success: function( data ) {
                        $('.cost_'+count).val(data[0].unit_cost);
					},
					error: function() {
						alert('Error');
					}
            })
        }
    </script>
</body>

</html>

<script>
$("body").on("click", ".add-item-quote", function(event){
    event.preventDefault();
    var count = parseFloat($('#count').val()) + 1;
    var holder = $(this).closest(".modal").find(".item-lines");
    var line = ' <div class="row gy-4"> '
                    +'<div class="col-sm-3">'
                        +'<div class="form-group">'
                            +'<label class="form-label">Item Description</label> '
                                +'<div class="form-control-wrap"> '
                                    +'<select name="item[]" id="item[]" class="select_'+count+' form-control" data-live-search="true" onchange="get_item_details(this)">'
                                    +'<?php foreach ($inventorys as $inventory) { ?>'
                                    +'<option value="<?= $inventory->id; ?>" ><?= $inventory->name; ?></option>'
                                    +'<?php } ?>'
                                    +'</select>'
                                    +'<input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">'
                +'</div></div></div>'
                +'<div class="col-sm-2">'
            +'    <div class="form-group">'
            +'        <label class="form-label">Work</label>'
            +'        <div class="form-control-wrap">'
            +'            <select class="form-control" name="workType[]">'
            +'                <option value="0">Select Work</option>'
            +'                <option value="body_work">Body Work</option>'
            +'                <option value="mechanical_work">Mechanical Work</option>'
            +'                <option value="electrical_work">Electrical Work</option>'
            +'                <option value="ac_work">AC Work</option>'
            +'                <option value="other_work">Other Work</option>'
            +'            </select>'
            +'        </div>'
            +'    </div>'
            +'</div>'
            +'<div class="col-sm-1"> <div class="form-group"> <label class="form-label">Qty</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-quantity" value="1" min="1" placeholder="Quantity" name="quantity[]" required=""> </div></div></div><div class="col-sm-2"> <div class="form-group"> <label class="form-label">Unit Cost ( '+currency+' )</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-cost cost_'+count+'" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required=""> </div></div></div><div class="col-sm-1"> <div class="form-group"> <label class="form-label">Tax (%)</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-tax" placeholder="Tax (%)" min="0" name="tax[]"> </div></div></div><div class="col-sm-2"> <div class="form-group"> <label class="form-label">Total ( '+currency+' )</label> <div class="form-control-wrap"> <input type="number" class="form-control form-control-lg line-total" placeholder="Amount" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="total[]" value="0.00" step="0.01" required="" readonly=""> </div></div></div><div class="col-sm-1"> <div class="form-group"> <div class="form-control-wrap"> <a href="#" class="btn btn-icon btn-lg btn-round btn-dim btn-outline-danger mt-gs remove-line" data-toggle="tooltip" title="Remove Item"><em class="icon ni ni-cross-circle-fill"></em></a> </div></div></div></div>';
                

    holder.append(line);
    $('#count').val(count)
    $('[data-toggle="tooltip"]').tooltip();
    
});
    
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<?php return;
