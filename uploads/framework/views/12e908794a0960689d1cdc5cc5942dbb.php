<?php global $s_v_data, $user, $title, $partslist; ?>
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
                                                <?php if (!empty($partslist)) { ?>
                                                <p>A total of <?=  number_format(count($partslist)) ; ?> parts.</p>
                                                <?php } else { ?>
                                                <p>Create and manage your parts to check on vehicle booking.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">  
                                                        <li>
                                                         <a href="" class="btn btn-outline-light bg-white d-none d-sm-inline-flex go-back"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                                                            <a href="" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none go-back"><em class="icon ni ni-arrow-left"></em></a>
                                                        </li>
                                                        <li>
                                                            <div class="drodown">
                                                                <a href="#" class="dropdown-toggle btn btn-primary" data-toggle="dropdown"><em class="icon ni ni-more-h"></em> <span>Add & Order</span></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="" data-toggle="modal" data-target="#create"><em class="icon ni ni-plus"></em><span>Add Part</span></a></li>
                                                                        <li><a href="" data-toggle="modal" data-target="#order"><em class="icon ni ni-sort"></em><span>Order List</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </li>           
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
                                                        <th class="nk-tb-col"><span class="sub-text">Input</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Added On</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($partslist)) { ?>
                                                    <?php foreach ($partslist as $index => $part) { ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <span class="tb-amount"><?=  $part->name ; ?> </span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <?php if ($part->has_input == "Yes") { ?>
                                                            <span class="tb-amount"><?=  $part->input_name ; ?> </span>
                                                            <?php } else { ?>
                                                            <span class="tb-amount">-|-</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="nk-tb-col">
                                                            <span><?=  date("F j, Y", strtotime($part->created_at)) ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Enabled</span>
                                                        </td>
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="drodown">
                                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <ul class="link-list-opt no-bdr">
                                                                                <li><a class="fetch-display-click" data="partid:<?=  $part->id ; ?>" url="<?=  url('Bookingparts@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Part</span></a></li>
                                                                                <li class="divider"></li>
                                                                                <li><a href="" class="send-to-server-click"  data="partid:<?=  $part->id ; ?>" url="<?=  url('Bookingparts@delete') ; ?>" warning-title="Are you sure?" warning-message="This part will be deleted permanently." warning-button="Yes, delete!"><em class="icon ni ni-trash"></em><span>Delete Part</span></a></li>
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
                    <h5 class="modal-title">Add Part</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Bookingparts@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add a part to check on vehicle check in and check out</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Part Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Part Name" name="name" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                     <div class="custom-control custom-switch">
                                        <input type="checkbox" name="has_input" id="has-input" class="custom-control-input has-input" value="Yes">
                                        <label class="custom-control-label" for="has-input">Show input field when checked</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 input-name" style="display:none;">
                                <div class="form-group">
                                    <label class="form-label">Input Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Input Name" name="input_name">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Add Part</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Create Modal -->
    <div class="modal fade" tabindex="-1" id="order">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Order Parts</h5>
                </div>
                <form class="simcy-form parts-order-form" action="<?=  url('Bookingparts@reorder') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Arrange parts order</p>
                        <div class="row gy-4">
                            <div class="col-md-12 reorder">
                                <?php if ( !empty($partslist) ) { ?>
                                <?php foreach ( $partslist as $index => $part ) { ?>
                                <div class="part-order-item">
                                    <div class="mt-1 mb-0">
                                        <div class="part-drag"><i class="ni ni-sort"></i></div>
                                        <span class="index"><?=  $index + 1 ; ?></span>.) <?=  $part->name ; ?>
                                    </div>
                                    <input type="hidden" name="part_<?=  $part->id ; ?>" value="<?=  $index + 1 ; ?>">
                                </div>
                                <?php } ?>
                                <?php } else { ?>
                                <p>It's empty here, no parts to order.</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
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
                    <h5 class="modal-title">Update Part</h5>
                </div>
                <div class="update-holder"></div>
            </div>
        </div>
    </div>

    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
    <script type="text/javascript">
        $( ".reorder" ).sortable({
          stop: function( event, ui ) {
            reorderParts();
          }
        })
    </script>
</body>

</html>
<?php return;
