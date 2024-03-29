<?php global $s_v_data, $project, $insurance, $user, $makes, $models; ?>
                <form class="simcy-form" action="<?=  url('Projects@update') ; ?>" models-url="<?=  url('Projects@models') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <input type="hidden" name="projectid" value="<?=  $project->id ; ?>" required="">
                        <p>Update project details.</p>
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Make</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic project-make-select" name="make" required="">
                                                <option value="">Select Make</option>
                                                <?php if (!empty($makes)) { ?>
                                                <?php foreach ($makes as $make) { ?>
                                                <option value="<?=  $make->id ; ?>" <?php if ($project->make == $make->id) { ?> selected <?php } ?>><?=  $make->name ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Model</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic project-model-select" name="model" required="">
                                                <option value="">Select Model</option>
                                                <?php if (!empty($models)) { ?>
                                                <?php foreach ($models as $model) { ?>
                                                <option value="<?=  $model->id ; ?>" <?php if ($project->model == $model->id) { ?> selected <?php } ?>><?=  $model->name ; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Registration Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Registration Number" value="<?=  $project->registration_number ; ?>" name="registration_number" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">VIN No</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="VIN No" value="<?=  $project->vin ; ?>" name="vin">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Milleage</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Milleage" value="<?=  $project->milleage ; ?>" name="milleage">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Milleage Unit</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="milleage_unit">
                                                <option value="Kilometers" <?php if ($project->milleage_unit == "Kilometers") { ?> selected <?php } ?>>Kilometers</option>
                                                <option value="Miles" <?php if ($project->milleage_unit == "Miles") { ?> selected <?php } ?>>Miles</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Engine No</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Engine No" value="<?=  $project->engine_number ; ?>" name="engine_number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="status">
                                                <option value="In Progress" <?php if ($project->status == "In Progress") { ?> selected <?php } ?>>In Progress</option>
                                                <option value="Completed" <?php if ($project->status == "Completed") { ?> selected <?php } ?>>Completed</option>
                                                <option value="Cancelled" <?php if ($project->status == "Cancelled") { ?> selected <?php } ?>>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Start Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Start Date" name="start_date" value="<?=  $project->start_date ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Completion Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Completion Date" name="end_date" value="<?=  $project->end_date ; ?>" required="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 covered">
                                <div class="form-group">
                                    <label class="form-label">Select Insurance Company</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic" name="insurance">
                                                <option value="0">Select Insurance</option>
                                                <?php if (!empty($insurance)) { ?>
                                                <?php foreach ($insurance as $insuranceco) { ?>
                                                    <?php if ($insuranceco->id == $project->insurance) { ?>
                                                        <option value="<?=  $insuranceco->id ; ?>" selected><?=  $insuranceco->name ; ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?=  $insuranceco->id ; ?>"><?=  $insuranceco->name ; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php } else { ?>
                                                    <option value="">No Insurance company added</option>
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
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Save Changes</span></button>
                    </div>
                </form>
                <script type="text/javascript">
                    $(".select2-dynamic").select2();
                </script>
<?php return;
