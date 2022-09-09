<?php global $s_v_data, $project, $user; ?>
<?php if (!empty($project->work_requested)) { ?>
<?php foreach ($project->work_requested as $index => $work_requested) { ?>
<div class="row gy-4">
    <div class="col-sm-4">
        <div class="form-group">
            <label class="form-label">Item Description</label>
            <div class="form-control-wrap">
                <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" value="<?=  $work_requested ; ?>" required="">
            </div>
        </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
            <label class="form-label">Qty</label>
            <div class="form-control-wrap hide-arrows">
                <input type="number" class="form-control form-control-lg line-quantity" value="1" min="1" placeholder="Quantity" name="quantity[]" required="">
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
            <div class="form-control-wrap hide-arrows">
                <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost[]" value="0.00" step="0.01" required="">
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label class="form-label">Tax (%)</label>
            <div class="form-control-wrap hide-arrows">
                <input type="number" class="form-control form-control-lg line-tax"  placeholder="Tax (%)" min="0" name="tax[]">
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label class="form-label">Total ( <?=  currency($user->parent->currency) ; ?> )</label>
            <div class="form-control-wrap">
                <input type="number" class="form-control form-control-lg line-total" placeholder="Total" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="total[]" value="0.00" step="0.01" required="" readonly="">
            </div>
        </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
            <div class="form-control-wrap">
                <a href="#" class="btn btn-icon btn-lg btn-round btn-dim btn-outline-danger mt-gs remove-line" data-toggle="tooltip" title="Remove Item"><em class="icon ni ni-cross-circle-fill"></em></a>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php } ?>
<?php return;
