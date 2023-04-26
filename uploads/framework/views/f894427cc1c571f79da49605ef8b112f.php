<?php global $s_v_data, $user, $title, $members, $projects; ?>
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
                                            <h3 class="nk-block-title page-title">Team Members</h3>
                                            <div class="nk-block-des text-soft">
                                                <?php if (!empty($members)) { ?>
                                                <p>A total of <?=  number_format(count($members)) ; ?> team members.</p>
                                                <?php } else { ?>
                                                <p>Create and manage your team here.</p>
                                                <?php } ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li><a href="" class="btn btn-primary"  data-toggle="modal" data-target="#create"><em class="icon ni ni-plus"></em><span>Create Member</span></a></li>
                                                    </ul>
                                                </div>
                                            </div><!-- .toggle-wrap -->
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-stretch">
                                        <div class="card-inner">
                                            <!-- <div class="row">
                                                <div class="col-sm-3">
                                                    <span>From: <input type="date" name="from_date" id="from_date" class="form-control" value="<?=  date('Y-m-d') ; ?>"></span>
                                                </div>
                                                <div class="col-sm-3">
                                                    <span>To: <input type="date" name="to_date" id="to_date" class="form-control" value="<?=  date('Y-m-d') ; ?>"> </span>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <br> -->
                                            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false" id="datatable_init_teams" style="width: 100%;">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col text-center">#</th>
                                                        <th class="nk-tb-col"><span class="sub-text">Member</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Role</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Balance</span></th>
                                                        <th class="nk-tb-col tb-col-md text-center">
                                                            <span class="sub-text" data-toggle="tooltip" title="Tasks In Progress">
                                                                P. Tasks <em class="icon ni ni-info-fill"></em>
                                                            </span>
                                                        </th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Created On</span></th>
                                                        <th class="nk-tb-col tb-col-md"><span class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right">
                                                        </th>
                                                        <th style="display: none;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($members)) { ?>
                                                    <?php foreach ($members as $index => $member) { ?>
                                                    <tr class="nk-tb-item">
                                                        <td class="nk-tb-col text-center"><?=  $index + 1 ; ?></td>
                                                        <td class="nk-tb-col">
                                                            <div class="user-card">
                                                                <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                                                                    <span><?=  mb_substr($member->fname, 0, 2, "UTF-8") ; ?></span>
                                                                </div>
                                                                <div class="user-info">
                                                                    <span class="tb-lead"><?=  $member->fname ; ?> <?=  $member->lname ; ?> <span class="dot dot-success d-md-none ml-1"></span></span>
                                                                    <span><?=  $member->phonenumber ; ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb">
                                                            <?php if ($member->role == "Owner") { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-success d-mb-inline-flex">Owner</span>
                                                            <?php } else if ($member->role == "Manager") { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-primary d-mb-inline-flex">Manager</span>
                                                            <?php } else if ($member->role == "Inventory Manager") { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-danger d-mb-inline-flex">Inventory Manager</span>
                                                            <?php } else if ($member->role == "Booking Manager") { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-secondary d-mb-inline-flex">Booking Manager</span>
                                                            <?php } else { ?>
                                                            <span class="badge badge-sm badge-dot has-bg badge-warning d-mb-inline-flex">Staff</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-mb" data-order="<?=  $member->balance ; ?>">
                                                            <span class="tb-amount"><?=  money($member->balance, $user->parent->currency) ; ?> </span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md text-center">
                                                            <span><?=  $member->pending_tasks ; ?> / <?=  $member->total_tasks ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <span><?=  date("F j, Y", strtotime($member->created_at)) ; ?></span>
                                                        </td>
                                                        <td class="nk-tb-col tb-col-md">
                                                            <?php if ($member->status == "Active") { ?>
                                                            <span class="text-success">Active</span>
                                                            <?php } else if ($member->status == "On Leave") { ?>
                                                            <span class="text-warning">On Leave</span>
                                                            <?php } else { ?>
                                                            <span class="text-danger">Unavailable</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="nk-tb-col nk-tb-col-tools">
                                                            <ul class="nk-tb-actions gx-1">
                                                                <li>
                                                                    <div class="drodown">
                                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <ul class="link-list-opt no-bdr">
                                                                                <li><a href="<?=  url('Team@details', array('teamid' => $member->id)) ; ?>"><em class="icon ni ni-eye"></em><span>View Details</span></a></li>
                                                                                <li><a class="fetch-display-click" data="teamid:<?=  $member->id ; ?>" url="<?=  url('Team@updateview') ; ?>" holder=".update-holder" modal="#update" href=""><em class="icon ni ni-pen"></em><span>Edit Details</span></a></li>
                                                                                <li><a href="" class="send-sms" data-phonenumber="<?=  $member->phonenumber ; ?>" data-name="<?=  $member->fname ; ?> <?=  $member->lname ; ?>"><em class="icon ni ni-chat-circle"></em><span>Send SMS</span></a></li>
                                                                                <li><a href="" class="team-report" data-id="<?=  $member->id ; ?>" data-name="<?=  $member->fname ; ?> <?=  $member->lname ; ?>"><em class="icon ni ni-reports"></em><span>View Report</span></a></li>
                                                                                <?php if ($user->role == "Owner") { ?>
                                                                                <li class="divider"></li>
                                                                                <li><a href="" class="send-to-server-click"  data="teamid:<?=  $member->id ; ?>" url="<?=  url('Team@delete') ; ?>" warning-title="Are you sure?" warning-message="This member's profile and data will be deleted permanently." warning-button="Yes, delete!"><em class="icon ni ni-trash"></em><span>Delete Member</span></a></li>
                                                                                <?php } ?>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                        <td style="display: none;"><?=  money($member->balance, $user->parent->currency) ; ?></td>
                                                    </tr><!-- .nk-tb-item  -->
                                                    <?php } ?>
                                                    <?php } else { ?>
                                                    <tr>
                                                        <td class="text-center" colspan="8">It's empty here!</td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-right">Total</td>
                                                        <td><?=  money(0, $user->parent->currency) ; ?></td>
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


    <!-- Create Modal -->
    <div class="modal fade" tabindex="-1" id="create">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Create Team Member</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Team@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create a team member account</p>
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">First Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="First Name" name="fname" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Last Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Last Name" name="lname" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number" required="">
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
                                    <label class="form-label">Role</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="role">
                                                <option value="Staff">Staff</option>
                                                <option value="Manager">Manager</option>
                                                <option value="Booking Manager">Booking Manager</option>
                                                <option value="Inventory Manager">Inventory Manager</option>
                                                <option value="Owner">Owner</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-note"><code>Owner</code> has full access of the system.  <code>Manager</code> have full access but has no delete permissions. <code>Booking Manager</code> books vehicles IN and OUT. <code>Inventory Manager</code> has access to inventory module. <code>Staff</code> have no access to the system.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Type</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="type">
                                                <option value="Full Time">Full Time</option>
                                                <option value="Part Time">Part Time</option>
                                                <option value="Subcontractor">Subcontractor</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="status">
                                                <option value="Active">Active</option>
                                                <option value="On Leave">On Leave</option>
                                                <option value="Unavailable">Unavailable</option>
                                            </select>
                                        </div>
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
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Create Member</span></button>
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
                    <h5 class="modal-title">Update Team</h5>
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
    <div class="modal fade" tabindex="-1" id="teamreport">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">View Report</h5>
                </div>
                <form action="<?=  url('Teamreport@view') ; ?>" data-parsley-validate="" method="GET" loader="true">
                    <div class="modal-body">
                        <p>Generate and view report</p>
                        <input type="hidden" name="staff" required="">
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
                                    <label class="form-label">Tasks Status</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="status">
                                                <option value="All">All</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Cancelled">Cancelled</option>
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
    <script>
        $(document).ready(function(){
            $('#datatable_init_teams').DataTable({
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

                $( api.column(3).footer() ).html('AED '+ pageTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                
                // alert(pageTotal);k
                
            }
            });

            $('#from_date, #to_date').on('change',function(){
                // DataTables initialisation
                var table = $('#datatable_init_teams').DataTable();
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
        });
    </script>
</body>

</html>
<?php return;
