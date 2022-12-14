<?php global $s_v_data, $task, $user, $expenses; ?>

                <form class="simcy-form" action="<?=  url('Tasks@update') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Update project tasks</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Task Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Task Name" value="<?=  $task->title ; ?>" name="title" required="">
                                        <input type="hidden" name="taskid" value="<?=  $task->id ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="status">
                                                <option value="In Progress" <?php if ($task->status == "In Progress") { ?> selected <?php } ?>>In Progress</option>
                                                <option value="Completed" <?php if ($task->status == "Completed") { ?> selected <?php } ?>>Completed</option>
                                                <option value="Cancelled" <?php if ($task->status == "Cancelled") { ?> selected <?php } ?>>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Task Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control form-control-lg" placeholder="Task Description" rows="5" name="description"><?=  $task->description ; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Task Cost</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Task Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost" value="<?=  $task->cost ; ?>" step="0.01" readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Due Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" value="<?=  $task->due_date ; ?>" placeholder="Due Date" name="due_date" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Due Time</label>
                                    <div class="form-control-wrap">
                                        <input type="time" class="form-control form-control-lg" placeholder="Due Time" value="<?=  $task->due_time ; ?>" name="due_time">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="nk-divider divider mt-0 mb-0"></div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Select required parts</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic" name="parts_required[]" multiple="">
                                                <?php if (!empty($expenses)) { ?>
                                                <?php foreach ($expenses as $expense) { ?>
                                                <option value="<?=  $expense->id ; ?>" <?=  $expense->required ; ?>><?=  $expense->expense ; ?> - ( <?=  $expense->status ; ?> )</option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-note">If this task requires parts recorded on the system, please select the parts below. A task can't be completed if any of the required parts are not delivered. Parts are added on <a href="<?=  url('Projects@details', array('projectid' => $project->id)) ; ?>?view=expenses">Expenses Tab</a></div>
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
                    $(".select2-dynamic").select2();
                </script>
<?php return;
