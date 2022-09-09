<?php global $s_v_data, $project, $insurance, $clients, $user, $makes; ?>
                <form class="simcy-form" action="<?=  url('Projects@create') ; ?>" models-url="<?=  url('Projects@models') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Create a new project</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Client</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic project-client-select" name="client" required="">
                                                <option value="">Select Client</option>
                                                <optgroup label="New Client">
                                                    <option value="create" class="fw-bold">Create New Client</option>
                                                </optgroup>
                                                <optgroup label="Select from clients">
                                                    <?php if (!empty($clients)) { ?>
                                                    <?php foreach ($clients as $client) { ?>
                                                    <option value="<?=  $client->id ; ?>"><?=  $client->fullname ; ?> - <?=  $client->phonenumber ; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-4 new-client-inputs" style="display: none;">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Full Name" name="fullname">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number">
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
                                    <label class="form-label">Address</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Address" name="address">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row gy-4">
                            <div class="col-sm-12" style="padding-bottom: 0 !important;">
                                <div class="nk-divider divider mt-0 mb-1"></div>
                                <p>Vehicle details.</p>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Make</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic project-make-select" name="make" required="">
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
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Model</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic project-model-select" name="model" required="">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Registration Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Registration Number" name="registration_number" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">VIN / Chasis No</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="VIN / Chasis No" name="vin">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Milleage</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Milleage" name="milleage">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Milleage Unit</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="milleage_unit">
                                                <option value="Kilometers">Kilometers</option>
                                                <option value="Miles">Miles</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Engine No</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Engine No" name="engine_number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="status">
                                                <option value="In Progress">In Progress</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Start Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Start Date" name="start_date" value="<?=  date('Y-m-d') ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Completion Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Completion Date" name="end_date" value="<?=  date('Y-m-d', strtotime(date('Y-m-d').' + 1weeks')) ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <?php if ($user->parent->insurance == "Enabled") { ?>
                            <div class="col-sm-12">
                                <div class="nk-divider divider mt-0 mb-0"></div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                     <div class="custom-control custom-switch">
                                        <input type="checkbox" name="covered" id="covered" class="custom-control-input" value="Yes">
                                        <label class="custom-control-label" for="covered">Covered by insurance</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 covered" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Select Insurance Company</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic" name="insurance">
                                                <?php if (!empty($insurance)) { ?>
                                                <?php foreach ($insurance as $insuranceco) { ?>
                                                <option value="<?=  $insuranceco->id ; ?>"><?=  $insuranceco->name ; ?></option>
                                                <?php } ?>
                                                <?php } else { ?>
                                                <option value="">No Insurance company added</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Create Project</span></button>
                    </div>
                </form>
                <script type="text/javascript">
                    initPhoneInput();
                    $(".select2-dynamic").select2();
                </script>
<?php return;
