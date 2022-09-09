<?php global $s_v_data, $user, $title, $suppliers, $projects; ?>
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
                                                <?php if (!empty($insurance)) { ?>
                                                <p>A total of <?=  number_format(count($insurance)) ; ?> suppliers.</p>
                                                <?php } else { ?>
                                                <p>Create and manage list of your suppliers here.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li><a href="" class="btn btn-primary"  data-toggle="modal" data-target="#create"><em class="icon ni ni-plus"></em><span>Add Supplier</span></a></li>
                                                    </ul>
                                                </div>
                                            </div><!-- .toggle-wrap -->
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-stretch">
                                        <div class="card-inner">
                                            <table class="datatable-init nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col text-center">#</th>
                                                        <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Email</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Supplied</span></th>
                                                        <th class="nk-tb-col tb-col-md">
                                                            <span class="sub-text" data-toggle="tooltip" title="Items awaiting delivery">
                                                                A.D <em class="icon ni ni-info-fill"></em>
                                                            </span>
                                                        </th>
                                                        <th class="nk-tb-col tb-col-md">
                                                            <span class="sub-text" data-toggle="tooltip" title="Amount owed to the supplier">
                                                                Owed <em class="icon ni ni-info-fill"></em>
                                                            </span>
                                                        </th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($suppliers)) { ?>
                                                    <?php foreach ($suppliers as $index => $supplier) { ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                    <span><?=  mb_substr($supplier->name, 0, 2, "UTF-8") ; ?></span>
                                                                </div>
                                                                <div class="user-info">
                                                                    <span class="tb-lead"><?=  $supplier->name ; ?></span>
                                                                    <span><?=  $supplier->phonenumber ; ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <?php if (!empty($supplier->email)) { ?>
                                                            <span><?=  $supplier->email ; ?></span>
                                                            <?php } else { ?>
                                                            <span>--|--</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md text-center">
                                                            <span><?=  number_format($supplier->supplied) ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-lg">
                                                            <span><?=  number_format($supplier->awaitingdelivery) ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb" data-order="<?=  $supplier->balance ; ?>">
                                                            <span class="tb-amount"><?=  money($supplier->owed, $user->parent->currency) ; ?> </span>
                                                        </td>
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="drodown">
                                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <ul class="link-list-opt no-bdr">
                                                                            <li><a class="fetch-display-click" data="supplierid:<?=  $supplier->id ; ?>" url="<?=  url('Suppliers@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Details</span></a></li>
                                                                                <li><a href="" class="supplier-report" data-id="<?=  $supplier->id ; ?>" data-name="<?=  $supplier->name ; ?>"><em class="icon ni ni-reports"></em><span>View Report</span></a></li>
                                                                            <?php if ($user->role == "Owner") { ?>
                                                                            <li class="divider"></li>
                                                                            <li><a class="send-to-server-click"  data="supplierid:<?=  $supplier->id ; ?>" url="<?=  url('Suppliers@delete') ; ?>" warning-title="Are you sure?" warning-message="This supplier's profile will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Supplier</span></a></li>
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

    <!-- Create Modal -->
    <div class="modal fade" tabindex="-1" id="create">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add a Supplier</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Suppliers@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add a supplier</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Supplier Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Supplier Name" name="name" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
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
                                    <label class="form-label">Email Address</label>
                                    <div class="form-control-wrap">
                                        <input type="email" class="form-control form-control-lg" placeholder="Email Address" name="email">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Address" name="address">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">VAT PIN Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="VAT PIN Number" name="vat_pin">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Add Supplier</span></button>
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
                    <h5 class="modal-title">Update Suppliers</h5>
                </div>
                <div class="update-holder"></div>
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
    <div class="modal fade" tabindex="-1" id="supplierreport">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">View Report</h5>
                </div>
                <form action="<?=  url('Supplierreport@view') ; ?>" data-parsley-validate="" method="GET" loader="true">
                    <div class="modal-body">
                        <p>Generate and view report</p>
                        <input type="hidden" name="supplier" required="">
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
                                    <label class="form-label">Delivery Status</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="status">
                                                <option value="All">All</option>
                                                <option value="Delivered">Delivered</option>
                                                <option value="Ordered">Ordered</option>
                                                <option value="Awaiting Delivery">Awaiting Delivery</option>
                                                <option value="To Order">To Order</option>
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
