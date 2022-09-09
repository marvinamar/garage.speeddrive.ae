<?php global $s_v_data, $jobcard, $approved; ?>
<form class="simcy-form" action="<?=  url('Jobcards@create') ; ?>" data-parsley-validate="" method="POST" loader="true">
    <div class="modal-body">
        <p>You can create an approved version of this job card removing items not needed and adding more items if necessary.</p>
        <?php if (empty($approved)) { ?>
        <div class="row gy-4">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Body Report</label>
                    <input type="hidden" name="jobcardid" value="<?=  $jobcard->id ; ?>" required="">
                    <input type="hidden" name="project" value="<?=  $jobcard->project ; ?>" required="">
                    <input type="hidden" name="approved" value="Yes" required="">
                    <div class="asilify-stack">
                        <div class="stacked-inputs">
                            <?php if (!empty($jobcard->body_report)) { ?>
                            <?php foreach (json_decode($jobcard->body_report) as $index => $report) { ?>
                            <div class="form-control-wrap stacked">
                                <?php if ($index > 0) { ?>
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <?php } ?>
                                <input type="text" class="form-control form-control-lg" placeholder="Body Report" name="body_report[]" value="<?=  $report ; ?>">
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                            <div class="form-control-wrap stacked">
                                <input type="text" class="form-control form-control-lg" placeholder="Body Report" name="body_report[]">
                            </div>
                            <div class="form-control-wrap stacked">
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <input type="text" class="form-control form-control-lg" placeholder="Body Report" name="body_report[]">
                            </div>
                            <?php } ?>
                            <div class="form-control-wrap stacked">
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <input type="text" class="form-control form-control-lg" placeholder="Body Report" name="body_report[]">
                            </div>
                        </div>
                        <div class="">
                            <a href="" class="btn btn-dim btn-primary add-stack" data-name="body_report[]" data-placeholder="Body Report"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Mechanical Report</label>
                    <div class="asilify-stack">
                        <div class="stacked-inputs">
                            <?php if (!empty($jobcard->mechanical_report)) { ?>
                            <?php foreach (json_decode($jobcard->mechanical_report) as $index => $report) { ?>
                            <div class="form-control-wrap stacked">
                                <?php if ($index > 0) { ?>
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <?php } ?>
                                <input type="text" class="form-control form-control-lg" placeholder="Mechanical Report" name="mechanical_report[]" value="<?=  $report ; ?>">
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                            <div class="form-control-wrap stacked">
                                <input type="text" class="form-control form-control-lg" placeholder="Mechanical Report" name="mechanical_report[]">
                            </div>
                            <div class="form-control-wrap stacked">
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <input type="text" class="form-control form-control-lg" placeholder="Mechanical Report" name="mechanical_report[]">
                            </div>
                            <?php } ?>
                            <div class="form-control-wrap stacked">
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <input type="text" class="form-control form-control-lg" placeholder="Mechanical Report" name="mechanical_report[]">
                            </div>
                        </div>
                        <div class="">
                            <a href="" class="btn btn-dim btn-primary add-stack" data-name="mechanical_report[]" data-placeholder="Mechanics Report"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Electrical Report</label>
                    <div class="asilify-stack">
                        <div class="stacked-inputs">
                            <?php if (!empty($jobcard->electrical_report)) { ?>
                            <?php foreach (json_decode($jobcard->electrical_report) as $index => $report) { ?>
                            <div class="form-control-wrap stacked">
                                <?php if ($index > 0) { ?>
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <?php } ?>
                                <input type="text" class="form-control form-control-lg" placeholder="Electrical Report" name="electrical_report[]" value="<?=  $report ; ?>">
                            </div>
                            <?php } ?>
                            <?php } else { ?>
                            <div class="form-control-wrap stacked">
                                <input type="text" class="form-control form-control-lg" placeholder="Electrical Report" name="electrical_report[]">
                            </div>
                            <div class="form-control-wrap stacked">
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <input type="text" class="form-control form-control-lg" placeholder="Electrical Report" name="electrical_report[]">
                            </div>
                            <?php } ?>
                            <div class="form-control-wrap stacked">
                                <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a>
                                <input type="text" class="form-control form-control-lg" placeholder="Electrical Report" name="electrical_report[]">
                            </div>
                        </div>
                        <div class="">
                            <a href="" class="btn btn-dim btn-primary add-stack" data-name="electrical_report[]" data-placeholder="Electrical Report"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <div class="row gy-4">
            <div class="col-sm-12">
                <div class="alert alert-fill alert-warning alert-icon"><em class="icon ni ni-alert-circle"></em> An approved version of this job card has already been created </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="modal-footer bg-light">
        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
        <?php if (empty($approved)) { ?>
        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Create Approved Job Card</span></button>
        <?php } ?>
    </div>
</form>
<?php return;
