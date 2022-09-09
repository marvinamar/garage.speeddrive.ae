<?php global $s_v_data, $member, $user; ?>
                <form class="simcy-form" action="<?=  url('Team@update') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create a team member account</p>
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">First Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="First Name" name="fname" value="<?=  $member->fname ; ?>" required="">
                                        <input type="hidden" name="teamid" value="<?=  $member->id ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Last Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Last Name" name="lname" value="<?=  $member->lname ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number" value="<?=  $member->phonenumber ; ?>" required="">
                                        <input class="hidden-phone" type="hidden" name="phonenumber" value="<?=  $member->phonenumber ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <div class="form-control-wrap">
                                        <input type="email" class="form-control form-control-lg" placeholder="Email Address" name="email" value="<?=  $member->email ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Role</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="role">
                                                <option value="Staff" <?php if ($member->role == "Staff") { ?> selected <?php } ?>>Staff</option>
                                                <option value="Manager" <?php if ($member->role == "Manager") { ?> selected <?php } ?>>Manager</option>
                                                <option value="Inventory Manager" <?php if ($member->role == "Inventory Manager") { ?> selected <?php } ?>>Inventory Manager</option>
                                                <option value="Booking Manager" <?php if ($member->role == "Booking Manager") { ?> selected <?php } ?>>Booking Manager</option>
                                                <option value="Owner" <?php if ($member->role == "Owner") { ?> selected <?php } ?>>Owner</option>
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
                                                <option value="Full Time" <?php if ($member->type == "Full Time") { ?> selected <?php } ?>>Full Time</option>
                                                <option value="Part Time" <?php if ($member->type == "Part Time") { ?> selected <?php } ?>>Part Time</option>
                                                <option value="Subcontractor" <?php if ($member->type == "Subcontractor") { ?> selected <?php } ?>>Subcontractor</option>
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
                                                <option value="Active" <?php if ($member->status == "Active") { ?> selected <?php } ?>>Active</option>
                                                <option value="On Leave" <?php if ($member->status == "On Leave") { ?> selected <?php } ?>>On Leave</option>
                                                <option value="Unavailable" <?php if ($member->status == "Unavailable") { ?> selected <?php } ?>>Unavailable</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Address" name="address" value="<?=  $member->address ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Balance</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Balance" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="balance" step="0.01" value="<?=  $member->balance ; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Save Changes</span></button>
                    </div>
                </form>
                <script type="text/javascript">
                    NioApp.initPhoneInput();
                </script>
<?php return;
