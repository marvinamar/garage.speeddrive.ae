<?php global $s_v_data, $inventory, $projects, $user, $project, $staffmembers; ?>
                <form class="simcy-form" action="<?=  url('Inventory@issue') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Issue an item to a vehicle.</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Item;</label>
                                    <div class="form-control-wrap">
                                        <h5><?=  $inventory->name ; ?></h5>
                                    </div>
                                </div>
                            </div>
                            <?php if (empty($inventory->expense)) { ?>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Select Vehicle / Project issue to</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic" name="project" required="">
                                                <option value="">Select Vehicle / Project</option>
                                                <?php if (!empty($projects)) { ?>
                                                <?php foreach ($projects as $project) { ?>
                                                <option value="<?=  $project->id ; ?>"><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> •  <?=  $project->registration_number ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Issue only to;</label>
                                    <div class="form-control-wrap">
                                        <h5><?=  carmake($project->make) ; ?> <?=  carmodel($project->model) ; ?> •  <?=  $project->registration_number ; ?></h5>
                                        <input type="hidden" name="project" value="<?=  $project->id ; ?>">
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Units Consumed (<span class="fw-bold">Available: <?=  $inventory->quantity ; ?></span>)</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title units"><?=  $inventory->quantity_unit ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Units Consumed" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="consumed" value="0.00" step="0.01" max="<?=  $inventory->quantity ; ?>">
                                        <input type="hidden" name="inventoryid" value="<?=  $inventory->id ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Issue Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Issue Date" name="issued_on" value="<?=  date('Y-m-d') ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Staff issued to</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic" name="issued_to" required="">
                                                <option value="">Select Staff</option>
                                                <?php if (!empty($staffmembers)) { ?>
                                                <?php foreach ($staffmembers as $staffmember) { ?>
                                                <option value="<?=  $staffmember->fname ; ?> <?=  $staffmember->lname ; ?>"><?=  $staffmember->fname ; ?> <?=  $staffmember->lname ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Issue Item</span></button>
                    </div>
                </form>
                <script type="text/javascript">
                $(".select2-dynamic").select2();
                </script>
<?php return;
