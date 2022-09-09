<?php global $s_v_data, $user, $title, $inventory; ?>
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
                                            <h3 class="nk-block-title page-title">Issueables</h3>
                                            <div class="nk-block-des text-soft">
                                                <p>Inventory items to be issued to specific vehicles.</p>
                                            </div>
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
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Supplier</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Vehicle</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Item</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Quantity</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Unit Cost</span></th>
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
                                                        <td class="nk-tb-col tb-col-md">
                                                            <?php if (!empty($item->supplier)) { ?>
                                                            <span class="tb-lead"><?=  $item->supplier->name ; ?></span>
                                                            <?php } else { ?>
                                                            <span>--|--</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <span class="tb-lead"><?=  carmake($item->project->make) ; ?> • <?=  $item->project->registration_number ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <span class="tb-lead"><?=  $item->name ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                                <span class="tb-lead"><?=  $item->quantity ; ?> <?=  $item->quantity_unit ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb" data-order="<?=  $item->unit_cost ; ?>">
                                                            <span class="tb-amount"><?=  money($item->unit_cost, $user->parent->currency) ; ?> </span>
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
                                                                               <?php if ($item->quantity > 0) { ?>
                                                                                <li><a class="fetch-display-click" data="inventoryid:<?=  $item->id ; ?>" url="<?=  url('Inventory@issueview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-check-circle-cut"></em><span>Issue</span></a></li>
                                                                                <?php } ?>
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

    
    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
</body>

</html>
<?php return;
