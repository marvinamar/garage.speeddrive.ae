<?php global $s_v_data, $user, $title, $members, $clients, $campaigns, $makes; ?>
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
                                            <h3 class="nk-block-title page-title">Marketing</h3>
                                            <div class="nk-block-des text-soft">
                                                <?php if (!empty($campaigns)) { ?>
                                                <p>A total of <?=  number_format(count($campaigns)) ; ?> campaigns.</p>
                                                <?php } else { ?>
                                                <p>Create and manage your campaigns here.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li>
                                                            <div class="drodown">
                                                                <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" data-toggle="dropdown"><em class="icon ni ni-coins"></em> <span>Balance & History</span></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="<?=  url('Marketing@recipients') ; ?>"><em class="icon ni ni-histroy"></em><span>SMS History</span></a></li>
                                                                        <li><a href="" class="fetch-display-click" data="secure:true" url="<?=  url('Marketing@balance') ; ?>" holder=".balance-holder" modal="#balance"><em class="icon ni ni-coins"></em><span>SMS Balance</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li><a href="" class="btn btn-primary"  data-toggle="modal" data-target="#create"><em class="icon ni ni-plus"></em><span>Create Campaign</span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><!-- .toggle-wrap -->
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">

                                    <?php if (isset($_GET["payment"]) && $_GET["payment"] == "error") { ?>
                                    <div class="alert alert-danger alert-icon">
                                        <em class="icon ni ni-check-circle"></em> 
                                        <strong>Hmmm!</strong> There was an error processing your payment, please contact support if issue persists. 
                                    </div>
                                    <?php } else if (isset($_GET["payment"]) && $_GET["payment"] == "success") { ?>
                                    <div class="alert alert-success alert-icon">
                                        <em class="icon ni ni-check-circle"></em> 
                                        <strong>Top up successful!</strong> Your new SMS credit balance is <?=  $user->parent->sms_balance ; ?>. 
                                    </div>
                                    <?php } ?>
                                    <div class="card card-stretch">
                                        <div class="card-inner">
                                            <table class="datatable-init nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col text-center">#</th>
                                                        <th class="nk-tb-col"><span class="sub-text">Campaign</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Date</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Sent SMS</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Credits</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (!empty($campaigns)) { ?>
                                                <?php foreach ($campaigns as $index => $campaign) { ?>
                                                <tr class="nk-tb-item">
                                                    <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                    <td class="nk-tb-col">
                                                        <span class="tb-amount"><?=  $campaign->title ; ?></span>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <span><?=  date("F j, Y", strtotime($campaign->created_at)) ; ?></span>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <span class="tb-amount"><?=  $campaign->sent ; ?> / <?=  $campaign->messages ; ?></span>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <span class="tb-amount"><?=  number_format($campaign->cost, 2) ; ?></span>
                                                    </td>
                                                    <td class="nk-tb-col tb-col-md">
                                                        <?php if ($campaign->status == "Completed") { ?>
                                                        <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Completed</span>
                                                        <?php } else { ?>
                                                        <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">Sending</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="nk-tb-col nk-tb-col-tools">
                                                        <ul class="nk-tb-actions gx-1">
                                                            <li>
                                                                <div class="drodown">
                                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <ul class="link-list-opt no-bdr">
                                                                        <li><a href="<?=  url('Marketing@recipients') ; ?>?campaign=<?=  $campaign->id ; ?>"><em class="icon ni ni-eye"></em><span>View Recipients</span></a></li>
                                                                        <?php if ($user->role == "Owner") { ?>
                                                                        <li class="divider"></li>
                                                                        <li><a class="send-to-server-click"  data="campaignid:<?=  $campaign->id ; ?>" url="<?=  url('Marketing@delete') ; ?>" warning-title="Are you sure?" warning-message="This campaign will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Campaign</span></a></li>
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
                                                    <td class="text-center" colspan="7">It's empty here!</td>
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

    <!-- Modal add payment -->
    <div class="modal fade" tabindex="-1" id="create">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Create Campaign</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Marketing@create') ; ?>" models-url="<?=  url('Projects@models') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create a campaign</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Campaign Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Campaign Title" name="title" required="">
                                    </div>
                                    <div class="form-note">This will not be shown to customers.</div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Send To</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="sendto" required="">
                                                <option value="">Select</option>
                                                <option value="clients">All Clients</option>
                                                <option value="selectedclients">Selected Clients</option>
                                                <option value="members">All Team Members</option>
                                                <option value="selectedmembers">Selected Team Members</option>
                                                <option value="enternumber">Enter Number Manually</option>
                                                <option value="filterbycar">Filtered Clients by Car Make / Model</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 campaign-sendto" data-type="clients" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Select Clients</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg select2" name="clients[]" multiple="">
                                                <?php if (!empty($clients)) { ?>
                                                <?php foreach ($clients as $client) { ?>
                                                <option value="<?=  $client->id ; ?>"><?=  $client->fullname ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 campaign-sendto" data-type="members" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Select Team Members</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg select2" name="members[]" multiple="">
                                                <?php if (!empty($members)) { ?>
                                                <?php foreach ($members as $member) { ?>
                                                <option value="<?=  $member->id ; ?>"><?=  $member->fname ; ?> <?=  $member->lname ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 campaign-sendto" data-type="manually" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Enter Number Manually</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number">
                                            <input class="hidden-phone" type="hidden" name="phonenumber">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 campaign-sendto" data-type="filterbycar" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Make</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2 project-make-select" name="make" required="">
                                                <option value="">Select Make</option>
                                                <?php if (!empty($makes)) { ?>
                                                <?php foreach ($makes as $make) { ?>
                                                <option value="<?=  $make->id ; ?>"><?=  $make->name ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 campaign-sendto" data-type="filterbycar" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Model</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2 project-model-select" name="model">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Message</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg" placeholder="Message" rows="3" name="message" required=""></textarea>
                                    </div>
                                    <div class="form-note">We'll include your company name <strong><?=  $user->parent->name ; ?></strong> at the end of every message.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Send Campaign</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <!-- balance Modal -->
    <div class="modal fade" tabindex="-1" id="balance">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Balance</h5>
                </div>
                <div class="balance-holder"></div>
            </div>
        </div>
    </div>


    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>


</body>

</html>
<?php return;
