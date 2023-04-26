<?php global $s_v_data, $user, $title, $projects, $clients, $staffmembers, $insurance, $inventorys; ?>
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
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">Projects List</h3>
                                            <div class="nk-block-des text-soft">
                                                <?php if (!empty($projects)) { ?>
                                                <p>A total of <?=  number_format(count($projects)) ; ?> projects.</p>
                                                <?php } else { ?>
                                                <p>Create and manage your projects here.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li><a href="" class="btn btn-primary fetch-display-click" data="secure:true" url="<?=  url('Projects@booking') ; ?>" holder=".update-project-holder" modal="#update-project" ><em class="icon ni ni-plus"></em><span>Create Project</span></a></li>
                                                    </ul>
                                                </div>
                                            </div><!-- .toggle-wrap -->
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-stretch">
                                        <div class="card-inner table-responsive">
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
                                            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false" id="datatable_init_projects" style="width: 100%;">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col text-center">#</th>
                                                        <th class="nk-tb-col"><span class="sub-text">Date</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Client Name</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Vehicle Number/Status</span></th>
                                                        <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                        <th class="nk-tb-col"><span class="sub-text">Total Invoice</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Paid</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Balance</span></th>
                                                        <?php } ?>
                                                        <!-- <th class="nk-tb-col text-center">
                                                            <span class="sub-text" data-toggle="tooltip" title="Tasks In Progress">
                                                                P. Tasks <em class="icon ni ni-info-fill"></em>
                                                            </span>
                                                        </th> -->
                                                        
                                                        <th class="nk-tb-col nk-tb-col-tools text-right"></th>
                                                        <th style="display: none;"></th>
                                                        <th style="display: none;"></th>
                                                        <th style="display: none;"></th>
                                                        <th style="display: none;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($projects)) { ?>
                                                    <?php foreach ($projects as $index => $project) { ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                        <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                        <td class="nk-tb-col">
                                                            <span><?=  date("M d, y", strtotime($project->start_date)) ; ?> <br> <?=  date("M d, y", strtotime($project->end_date)) ; ?></span>
                                                        </td>
                                                        <?php } ?>
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                    <span><?=  mb_substr($project->client->fullname, 0, 2, "UTF-8") ; ?></span>
                                                                </div>
                                                                <div class="user-info">
                                                                    <span class="tb-lead"><?=  $project->client->fullname ; ?> <span class="dot dot-success d-md-none ml-1"></span></span>
                                                                    <span><?=  $project->client->phonenumber ; ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <span class="tb-amount"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?>
                                                                <?php if (!empty($project->insurance)) { ?>
                                                                <span class="text-success" data-toggle="tooltip" title="Covered by Insurance"><em class="icon ni ni-shield-check-fill"></em></span>
                                                                <?php } ?>
                                                            </span>
                                                            <span><?=  $project->registration_number ; ?></span>
                                                            <br>
                                                            <?php if ($project->status == "Completed") { ?>
                                                                <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Completed</span>
                                                            <?php } else if ($project->status == "Cancelled") { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-secondary d-mb-inline-flex">Cancelled</span>
                                                            <?php } else if ($project->status == "Booked In") { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Booked In</span>
                                                            <?php } else { ?>
                                                                <?php if (date('Y-m-d', strtotime($project->end_date)) < date('Y-m-d')) { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Over due</span>
                                                                <?php } else { ?>
                                                                    <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">In Progress</span>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </td>
                                                        <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                        <td class="nk-tb-col">
                                                            <span><?=  money($project->invoiced, $user->parent->currency) ; ?></span><br>
                                                        </td>    
                                                        <td class="nk-tb-col">
                                                                <span><?=  money(($project->receipt), $user->parent->currency) ; ?></span>
                                                            </td>
                                                            <td class="nk-tb-col">
                                                                <span class="tb-amount"><?=  money(($project->invoiced - $project->receipt), $user->parent->currency) ; ?></span>
                                                            </td>
                                                        <?php } ?>
                                                        <!-- <td class="nk-tb-col text-center">
                                                            <span><?=  $project->pending_tasks ; ?> / <?=  $project->total_tasks ; ?></span>
                                                        </td> -->
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="drodown">
                                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <ul class="link-list-opt no-bdr">
                                                                            <li><a href="<?=  url('Projects@details', array('projectid' => $project->id,'Isqt' => 'false')) ; ?>"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                            <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                                            <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Projects@updateview') ; ?>" holder=".update-project-holder" modal="#update-project" href=""><em class="icon ni ni-pen"></em><span>Edit Details</span></a></li>
                                                                            <li><a href=""  class="create-jobcard" data-id="<?=  $project->id ; ?>"><em class="icon ni ni-property-add"></em><span>Create Job Card</span></a></li>
                                                                            <li class="divider"></li>
                                                                            <li><a href="" class="create-quote" data-type="project" data-id="<?=  $project->id ; ?>"><em class="icon ni ni-cards"></em><span>Create Quote</span></a></li>
                                                                            <li><a href="" class="create-invoice" data-type="project" data-id="<?=  $project->id ; ?>"><em class="icon ni ni-cc"></em><span>Create Invoice</span></a></li>
                                                                            <?php if ($project->status == "In Progress") { ?>
                                                                            <li><a href="" class="send-to-server-click"  data="projectid:<?=  $project->id ; ?>" url="<?=  url('Projects@cancel') ; ?>" warning-title="Are you sure?" warning-message="This project will be marked as cancelled." warning-button="Yes, Cancel!"><em class="icon ni ni-na"></em><span>Cancel Project</span></a></li>
                                                                            <?php } ?>
                                                                            <?php } ?>
                                                                            <?php if ($user->role == "Owner") { ?>
                                                                            <li><a class="send-to-server-click"  data="projectid:<?=  $project->id ; ?>" url="<?=  url('Projects@delete') ; ?>" warning-title="Are you sure?" warning-message="This project will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Project</span></a></li>
                                                                            <?php } ?>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                        <td style="display: none;"><?=  money($project->invoiced, $user->parent->currency) ; ?></td>
                                                        <td style="display: none;"><?=  money($project->receipt, $user->parent->currency) ; ?></td>
                                                        <td style="display: none;"><?=  money(($project->invoiced - $project->receipt), $user->parent->currency) ; ?></td>
                                                        <td style="display: none;"><?=  date("Y-m-d", strtotime($project->start_date)) ; ?></td>
                                                    </tr><!-- .nk-tb-item  -->
                                                    <?php } ?>
                                                    <?php } else { ?>
                                                    <tr>
                                                        <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                                        <td class="text-center" colspan="8">It's empty here!</td>
                                                        <?php } else { ?>
                                                        <td class="text-center" colspan="6">It's empty here!</td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="text-right nk-tb-col tb-col-md" colspan="4"><span class="fw-bold">Total:</span></td>
                                                        <td class="nk-tb-col tb-col-md"> <span class="tb-amount"><?=  money(0, $user->parent->currency) ; ?></span> </td>
                                                        <td class="nk-tb-col tb-col-md"> <span class="tb-amount"><?=  money(0, $user->parent->currency) ; ?></span> </td>
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
    <div class="modal fade" tabindex="-1" id="createquote">
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
                        <div class="item-lines" data-type="quote">
                            <div class="row gy-4">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Item Description</label>
                                        <div class="form-control-wrap">
                                            <select name="item[]" id="item[]" class="select_1 form-control" data-live-search="true" onchange="get_item_details(this)">
                                                <option value="0">Select Item</option>
                                                <?php foreach ($inventorys as $inventory) { ?>
                                                <option value="<?= $inventory->id; ?>"><?= $inventory->name; ?></option>
                                                <?php } ?>
                                            </select>
                                            <!-- <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required=""> -->
                                            <input type="hidden" name="project" required="">
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
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required="">
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
                            <div class="row gy-4">

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Item Description</label>
                                        <div class="form-control-wrap">
                                            <!-- <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required=""> -->
                                            <select name="item[]" id="item[]" class="select_1 form-control" data-live-search="true" onchange="get_item_details(this)">
                                                <option value="0">Select Item</option>
                                                <?php foreach ($inventorys as $inventory) { ?>
                                                <option value="<?= $inventory->id; ?>"><?= $inventory->name; ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" name="project" required="">
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
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required="">
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
                                <div class="col-sm-4">
                                    <a href="" class="btn btn-dim btn-outline-primary mt-2 add-item-quote" data-type="invoice"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">VAT Tax (%)</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg total-vat"  placeholder="Tax (%)" value="<?=  $user->parent->vat ; ?>" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" step="0.01" min="0" name="vat">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <div class="fw-normal">Sub Total:<div class="fw-bold sub-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <div class="fw-normal">VAT Tax:<div class="fw-bold tax-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
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
                        <p>Create a project job card.</p>

                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Body Report</label>
                                    <input type="hidden" name="project" required="">
                                    <input type="hidden" name="jobcardid">
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
                        <div class="item-lines" data-type="quote">
                            <div class="row gy-4">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="form-label">Item Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required="">
                                            <input type="hidden" name="project" required="">
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
                                            <input type="hidden" name="project" required="">
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
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">VAT Tax (%)</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg total-vat"  placeholder="Tax (%)" value="<?=  $user->parent->vat ; ?>" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" step="0.01" min="0" name="vat">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 text-right">
                                    <div class="fw-normal">Sub Total:<div class="fw-bold sub-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
                                    <div class="fw-normal">VAT Tax:<div class="fw-bold tax-total"><?=  currency($user->parent->currency) ; ?> 0.00</div></div>
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
                                        <label class="form-label">Payment Due</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Payment Due" name="due_date" required="">
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

            $('#datatable_init_projects').DataTable({
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
                        .column(8, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                $( api.column(4).footer() ).html('AED '+ pageTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

                var pageTotal = api
                        .column(9, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                $( api.column(5).footer() ).html('AED '+ pageTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

                var pageTotal = api
                        .column(10, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                $( api.column(6).footer() ).html('AED '+ pageTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                
            }
            });

            $('#from_date, #to_date').on('change',function(){
                // DataTables initialisation
                var table = $('#datatable_init_projects').DataTable();
                // Refilter the table
                table.draw();
            });

            $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var date = new Date(data[11]).getDate();
                var month = new Date(data[11]).getMonth() + 1;
                var year = new Date(data[11]).getFullYear();

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

        });
    </script>
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
                                            +'<option value="0" >Select Item</option>'
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
<?php return;
