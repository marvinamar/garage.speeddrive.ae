<?php global $s_v_data, $items, $user, $staffmembers, $jobcard; ?>

                <form class="simcy-form" action="<?=  url('Tasks@addbulk') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create project tasks</p>
                        <input type="hidden" name="project" value="<?=  $jobcard->project ; ?>" required="">
                        <?php if (!empty($items)) { ?>
                        <?php foreach ($items as $index => $item) { ?>
                        <div class="row m-0 mb-3 modal-section">
                            <div class="col-sm-12 border pt-3 pb-3">
                                <div class="row gy-4">
                                    <div class="col-sm-12 border-bottom">
                                        <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-bulk-imported pull-right"><em class="icon ni ni-trash"></em></a>
                                        <h6 class="mt-1">#<?=  $index + 1 ; ?> <?=  $item ; ?></h6>
                                    </div>
                                </div>

                                <div class="row gy-4  pt-2">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Task Title</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control form-control-lg" placeholder="Task Name" name="title<?=  $index ; ?>" required="" value="<?=  $item ; ?>">
                                                <input type="hidden" name="indexing[]" value="<?=  $index ; ?>" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Assign to</label>
                                            <div class="form-control-wrap ">
                                                <div class="form-control-select">
                                                    <select class="form-control select2-dynamic" name="member<?=  $index ; ?>" required="">
                                                        <option value="">Select Staff</option>
                                                        <?php if (!empty($staffmembers)) { ?>
                                                        <?php foreach ($staffmembers as $staffmember) { ?>
                                                        <option value="<?=  $staffmember->id ; ?>"><?=  $staffmember->fname ; ?> <?=  $staffmember->lname ; ?></option>
                                                        <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <div class="form-control-wrap ">
                                                <div class="form-control-select">
                                                    <select class="form-control form-control-lg" name="status<?=  $index ; ?>">
                                                        <option value="In Progress">In Progress</option>
                                                        <option value="Completed">Completed</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Task Cost <span data-toggle="tooltip" title="Task cost can't be updated once saved."><em class="icon ni ni-info-fill"></em></span></label>
                                            <div class="form-control-wrap">
                                                <div class="form-text-hint">
                                                    <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                                </div>
                                                <input type="number" class="form-control form-control-lg" placeholder="Task Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost<?=  $index ; ?>" value="0.00" step="0.01" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Task Description</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control form-control-lg unset-min-height" placeholder="Task Description" rows="3" name="description<?=  $index ; ?>"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Due Date</label>
                                            <div class="form-control-wrap">
                                                <input type="date" class="form-control form-control-lg" placeholder="Due Date" name="due_date<?=  $index ; ?>" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Due Time</label>
                                            <div class="form-control-wrap">
                                                <input type="time" class="form-control form-control-lg" placeholder="Due Time" name="due_time">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Select required parts</label>
                                            <div class="form-control-wrap ">
                                                <div class="form-control-select">
                                                    <select class="form-control form-control-lg select2-dynamic" name="parts_required<?=  $index ; ?>[]" multiple="">
                                                        <?php if (!empty($expenses)) { ?>
                                                        <?php foreach ($expenses as $expense) { ?>
                                                        <option value="<?=  $expense->id ; ?>"><?=  $expense->expense ; ?> - ( <?=  $expense->status ; ?> )</option>
                                                        <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php } else { ?>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="alert alert-fill alert-warning alert-icon"><em class="icon ni ni-alert-circle"></em> No items added on this job card.</div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <?php if (!empty($items)) { ?>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Create Tasks</span></button>
                        <?php } ?>
                    </div>
                </form>
                <script type="text/javascript">
                    $(".select2-dynamic").select2();
                    $('[data-toggle="tooltip"]').tooltip();
                </script>
<?php return;
