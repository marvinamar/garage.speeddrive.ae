<?php global $s_v_data, $user, $title, $inventory, $suppliers; ?>
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
                                                <p>A total of <?=  number_format(count($insurance)) ; ?> items.</p>
                                                <?php } else { ?>
                                                <p>Create and manage your inventory here.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li><a href="" class="btn btn-primary"  data-toggle="modal" data-target="#create"><em class="icon ni ni-plus"></em><span>Create Item</span></a></li>
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
                                                        <th class="nk-tb-col"><span class="sub-text">Item / Shelf No.</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Quantity / Item Code</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Unit Cost</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Supplier</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($inventory)) { ?>
                                                    <?php foreach ($inventory as $index => $item) { ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                        <td class="nk-tb-col">
                                                            <span class="tb-lead"><?=  $item->name ; ?></span>
                                                            <span><?=  $item->shelf_number ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                                <span class="tb-lead"><?=  $item->quantity ; ?> <?=  $item->quantity_unit ; ?></span>
                                                                <span><?=  $item->item_code ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb" data-order="<?=  $item->unit_cost ; ?>">
                                                            <span class="tb-amount"><?=  money($item->unit_cost, $user->parent->currency) ; ?> </span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <?php if (!empty($item->supplier)) { ?>
                                                            <span><?=  $item->supplier->name ; ?></span>
                                                            <?php } else { ?>
                                                            <span>--|--</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <?php if ($item->quantity > $item->restock_quantity) { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">In Stock</span>
                                                            <?php } else if ($item->quantity < $item->restock_quantity && $item->quantity > 0) { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">Restock</span>
                                                            <?php } else { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Out of Stock</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="drodown">
                                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <ul class="link-list-opt no-bdr">
                                                                               <?php if ($item->project_specific == "No") { ?>
                                                                                <li><a class="fetch-display-click" data="inventoryid:<?=  $item->id ; ?>" url="<?=  url('Inventory@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Details</span></a></li>
                                                                                <?php } ?>
                                                                               <?php if ($item->quantity > 0) { ?>
                                                                                <li><a class="fetch-display-click" data="inventoryid:<?=  $item->id ; ?>" url="<?=  url('Inventory@issueview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-check-circle-cut"></em><span>Issue</span></a></li>
                                                                                <?php } ?>
                                                                                <li><a class="add-stock" data-id="<?=  $item->id ; ?>"href=""><em class="icon ni ni-plus-circle"></em><span>Add Stock</span></a></li>
                                                                                <li><a href="<?=  url('Inventoryreport@view', array('inventoryid' => $item->id)) ; ?>"><em class="icon ni ni-reports"></em><span>View report</span></a></li>
                                                                                <?php if ($user->role == "Owner") { ?>
                                                                                <li><a class="send-to-server-click"  data="inventoryid:<?=  $item->id ; ?>" url="<?=  url('Inventory@delete') ; ?>" warning-title="Are you sure?" warning-message="This item will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Item</span></a></li>
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
                    <h5 class="modal-title">Create Item</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Inventory@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add an item</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Item Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Item Name" name="name" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Quantity</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Quantity" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="quantity" step="0.01" min="0.00" required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Quantity Unit</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-control-lg" name="quantity_unit">
                                            <option value="Units">Units</option>
                                            <option value="Litres">Litres</option>
                                            <option value="Kilograms">Kilograms</option>
                                            <option value="Pounds">Pounds</option>
                                            <option value="Gallons">Gallons</option>
                                            <option value="Meters">Meters</option>
                                            <option value="Pieces">Pieces</option>
                                            <option value="Set">Set</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Restock Quantity</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Restock Quantity" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="restock_quantity" step="0.01" min="0.00">
                                    </div>
                                    <div class="form-note">Item will show restock if item quantity is below this level.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Unit Cost</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  $user->parent->currency ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="unit_cost" value="0.00" step="0.01" min="0.00" required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Select Supplier</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-control-lg select2" name="supplier" >
                                            <option value="">Select Supplier</option>
                                            <?php if (!empty($suppliers)) { ?>
                                            <?php foreach ($suppliers as $supplier) { ?>
                                                <option value="<?=  $supplier->id ; ?>"><?=  $supplier->name ; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Item Code</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Item Code" name="shelf_number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Shelf Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Shelf Number" name="item_code">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Add Item</span></button>
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
                    <h5 class="modal-title">Manage Inventory</h5>
                </div>
                <div class="update-holder"></div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" tabindex="-1" id="addstock">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Add Stock</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Inventory@addstock') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add stock</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Quantity</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Quantity" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="quantity" step="0.01" min="0.00" required="required">
                                        <input type="hidden" name="inventoryid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Add Stock</span></button>
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
