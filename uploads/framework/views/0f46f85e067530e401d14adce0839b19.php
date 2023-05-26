<?php global $s_v_data, $user, $title, $inventory, $suppliers; ?>
            <div class="nk-sidebar nk-sidebar-fixed is-light " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="<?=  url('Overview@get') ; ?>" class="logo-link nk-sidebar-logo">
                            <img class="logo-dark logo-img" src="<?=  asset('assets/images/logo-dark.png') ; ?>" srcset="<?=  asset('assets/images/logo-dark.png') ; ?> 2x" alt="logo-dark">
                        </a>
                    </div>
                    <div class="nk-menu-trigger mr-n2">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div><!-- .nk-sidebar-element -->
                <div class="nk-sidebar-element">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Overview</h6>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-item">
                                    <a href="<?=  env('APP_URL') ; ?>" class="nk-menu-link overview">
                                        <span class="nk-menu-icon"><em class="icon ni ni-activity-round-fill"></em></span>
                                        <span class="nk-menu-text">Overview</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Application</h6>
                                </li><!-- .nk-menu-item -->

                                <?php if ($user->role == "Owner" || $user->role == "Booking Manager" || $user->role == "Manager") { ?>
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-note-add-fill"></em></span>
                                        <span class="nk-menu-text">Projects</span>
                                        <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                        <span class="nk-menu-badge badge-danger"><?=  $user->pendingtasks + $user->expectedparts + $user->unpaidparts ; ?></span>
                                        <?php } ?>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Projects@get') ; ?>" class="nk-menu-link"><span class="nk-menu-text">Projects / Vehicles</span></a>
                                        </li>
                                        <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Tasks@pending') ; ?>" class="nk-menu-link">
                                                <span class="nk-menu-text">Pending Tasks</span>
                                                <span class="nk-menu-badge badge-danger"><?=  $user->pendingtasks ; ?></span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Expenses@expected') ; ?>" class="nk-menu-link">
                                                <span class="nk-menu-text">Expected Parts</span>
                                                <span class="nk-menu-badge badge-danger"><?=  $user->expectedparts ; ?></span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Expenses@unpaid') ; ?>" class="nk-menu-link">
                                                <span class="nk-menu-text">Unpaid Parts</span>
                                                <span class="nk-menu-badge badge-danger"><?=  $user->unpaidparts ; ?></span>
                                            </a>
                                        </li>
                                        <?php if ($user->parent->insurance == "Enabled") { ?>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Insurance@get') ; ?>" class="nk-menu-link"><span class="nk-menu-text">Insurance Co.</span></a>
                                        </li>
                                        <?php } ?>
                                        <?php } ?>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->
                                <?php } ?>

                                <?php if ($user->role == "Owner" || $user->role == "Inventory Manager" || $user->role == "Manager") { ?>
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-clipboad-check-fill"></em></span>
                                        <span class="nk-menu-text">Inventory</span>
                                        <?php if ($user->parent->parts_to_inventory == "Enabled") { ?>
                                        <span class="nk-menu-badge badge-danger"><?=  $user->receiveables + $user->issueables ; ?></span>
                                        <?php } ?>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Inventory@get') ; ?>" class="nk-menu-link"><span class="nk-menu-text">Inventory List</span></a>
                                        </li>
                                        <?php if ($user->parent->parts_to_inventory == "Enabled") { ?>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Inventory@receiveables') ; ?>" class="nk-menu-link">
                                                <span class="nk-menu-text">Receiveables</span>
                                                <span class="nk-menu-badge badge-danger"><?=  $user->receiveables ; ?></span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Inventory@issueables') ; ?>" class="nk-menu-link">
                                                <span class="nk-menu-text">Issueables</span>
                                                <span class="nk-menu-badge badge-danger"><?=  $user->issueables ; ?></span>
                                            </a>
                                        </li>
                                        <?php } ?>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Suppliers@get') ; ?>" class="nk-menu-link"><span class="nk-menu-text">Suppliers</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->
                                <?php } ?>

                                <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-sign-bgp-alt"></em></span>
                                        <span class="nk-menu-text">Accounting</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Quote@get') ; ?>" class="nk-menu-link"><span class="nk-menu-text">Quotes</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Invoice@get') ; ?>" class="nk-menu-link"><span class="nk-menu-text">Invoices</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Projectpayment@get') ; ?>" class="nk-menu-link"><span class="nk-menu-text">Receipts</span></a>
                                        </li>
                                        <li class="nk-menu-item">
                                            <a href="<?=  url('Supplierpayment@get') ; ?>" class="nk-menu-link"><span class="nk-menu-text">Payments</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->
                                <?php } ?>
                                <?php } ?>
                                
                                <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>
                                
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Management</h6>
                                </li><!-- .nk-menu-heading -->
                                <li class="nk-menu-item">
                                    <a href="<?=  url('Team@get') ; ?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                                        <span class="nk-menu-text">Employee</span> <!--Team Member renamed to Employee-->
                                    </a>
                                </li><!-- .nk-menu-item -->
                                
                                <?php if (false) { ?>
                                <li class="nk-menu-item">
                                    <a href="<?=  url('Billing@get') ; ?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-sign-cc-alt2"></em></span>
                                        <span class="nk-menu-text">Billing</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php } ?>
                                <?php } ?>
                                <li class="nk-menu-item">
                                    <a href="<?=  url('Settings@get') ; ?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-setting-fill"></em></span>
                                        <span class="nk-menu-text">Settings</span>
                                    </a>
                                </li><!-- .nk-menu-item -->

                                <?php if ($user->role == "Owner" || $user->role == "Manager") { ?>

                                <li class="nk-menu-item">
                                    <a href="<?=  url('Clients@get') ; ?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-users-fill"></em></span>
                                        <span class="nk-menu-text">Clients</span>
                                    </a>
                                </li><!-- .nk-menu-item -->

                                <li class="nk-menu-item">
                                    <a href="<?=  url('Marketing@get') ; ?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-chat-circle-fill"></em></span>
                                        <span class="nk-menu-text">Marketing</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php } ?>

                            </ul><!-- .nk-menu -->
                        </div><!-- .nk-sidebar-menu -->
                    </div><!-- .nk-sidebar-content -->
                </div><!-- .nk-sidebar-element -->
            </div>
<?php return;
