<?php global $s_v_data, $jobcard; ?>
<form class="simcy-form" action="<?=  url('Jobcards@update') ; ?>" data-parsley-validate="" method="POST" loader="true">
    <div class="modal-body">
        <p>Update job card details.</p>

        <div class="row gy-4">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Body Report</label>
                    <input type="hidden" name="jobcardid" value="<?=  $jobcard->id ; ?>" required="">
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
        <div class="row gy-4">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Is this job card approved by the client?</label>
                    <div class="form-control-wrap ">
                        <div class="form-control-select">
                            <select class="form-control form-control-lg" name="approved">
                                <option value="Yes" <?php if ($jobcard->approved == "Yes") { ?> selected <?php } ?>>Yes</option>
                                <option value="No" <?php if ($jobcard->approved == "No") { ?> selected <?php } ?>>Not yet</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer bg-light">
        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Update Job Card</span></button>
    </div>
</form>
<?php return;
