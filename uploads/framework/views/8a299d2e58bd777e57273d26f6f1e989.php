<?php global $s_v_data, $user, $title, $payments, $clients, $pay_expenses, $employees; ?>
                <div class="nk-header nk-header-fixed is-light">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ml-n1">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="<?=  url('Overview@get') ; ?>" class="logo-link">
                                    <img class="logo-dark logo-img" src="<?=  asset('assets/images/logo-dark.png') ; ?>" srcset="<?=  asset('assets/images/logo-dark.png') ; ?> 2x" alt="logo-dark">
                                </a>
                            </div><!-- .nk-header-brand -->
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <?php if ($user->role == "Owner" || $user->role == "Manager" || $user->role == "Booking Manager") { ?>
                                    <li class="dropdown">
                                        <a href="" class="btn btn-sm btn-primary fetch-display-click record-booking-desktop" data="secure:true" url="<?=  url('Projects@booking') ; ?>" holder=".update-project-holder" modal="#update-project"><em class="icon ni ni-plus"></em><span> New Vehicle</span></a>
                                        <a href="" class="btn btn-icon btn-sm btn-primary btn-round fetch-display-click record-booking-mobile" data="secure:true" url="<?=  url('Projects@booking') ; ?>" holder=".update-project-holder" modal="#update-project"><em class="icon ni ni-plus"></em></a>
                                    </li>
                                    <?php } ?>
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle mr-n1" data-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <em class="icon ni ni-user-alt"></em>
                                                </div>
                                                <div class="user-info d-none d-xl-block">
                                                    <div class="user-status user-status-verified"><?=  $user->role ; ?></div>
                                                    <div class="user-name dropdown-indicator"><?=  $user->fname ; ?> <?=  $user->lname ; ?></div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span><?=  mb_substr($user->fname, 0, 1, "UTF-8").mb_substr($user->lname, 0, 1, "UTF-8") ; ?></span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text"><?=  $user->fname ; ?> <?=  $user->lname ; ?></span>
                                                        <span class="sub-text"><?=  $user->phonenumber ; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="<?=  url('Settings@get') ; ?>"><em class="icon ni ni-setting-alt"></em><span>Account Setting</span></a></li>
                                                    <li><a href="https://asilify.com/documentation/" target="_blank"><em class="icon ni ni-help-alt"></em><span>Help Center</span></a></li>
                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="<?=  url('Auth@signout') ; ?>"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- .nk-header-wrap -->
                    </div><!-- .container-fliud -->
                </div>
<?php return;
