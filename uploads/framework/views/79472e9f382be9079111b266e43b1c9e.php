<?php global $s_v_data, $expenses, $user, $instance, $company, $tax, $inventorys; ?>
<?php if (!empty($expenses)) { ?>
<?php foreach ($expenses as $index => $expense) { ?>
<div class="row gy-4 ">
    <div class="col-sm-3">
        <div class="form-group">
            <label class="form-label">Item</label>
            <!-- <div class="form-control-wrap">
                <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" value="<?=  $expense->expense ; ?>" required="">
            </div> -->
            <select name="item[]" class="select_<?= $index; ?> form-control form-control-lg" data-live-search="true" onchange="get_item_details(this)">
                <option value="0" selected>Select Item</option>
                <?php foreach ($inventorys as $inventory) { ?>
                    <?php if ($inventory->id == $expense->expense) { ?>
                        <option value="<?= $inventory->id; ?>" selected><?= $inventory->name; ?></option>
                    <?php } else { ?>
                        <option value="<?= $inventory->id; ?>" ><?= $inventory->name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label class="form-label">Description</label>
            <div class="form-control-wrap">
                <input type="text" class="form-control form-control-lg" name="item_description[]" value="<?= $expense->item_description; ?>">
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label class="form-label">Work</label>
            <div class="form-control-wrap">
                <select class="form-control form-control-lg" name="workType[]">
                    <option value="0">Select Work</option>
                    <option value="body_work" >Body Work</option>                                                
                    <option value="mechanical_work">Mechanical Work</option>                                                
                    <option value="electrical_work">Electrical Work</option>
                    <option value="ac_work">AC Work</option>                                                
                    <option value="other_work">Other Work</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
            <label class="form-label">Qty</label>
            <div class="form-control-wrap hide-arrows">
                <input type="number" class="form-control form-control-lg line-quantity" min="1" placeholder="Quantity" name="quantity[]" value="<?=  $expense->quantity ; ?>" required="">
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
            <div class="form-control-wrap hide-arrows">
                <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost[]" value="<?= $expense->amount; ?>" step="0.01" required="">
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label class="form-label">Tax (%)</label>
            <div class="form-control-wrap hide-arrows">
                <input type="number" class="form-control form-control-lg line-tax nl-line-tax"  placeholder="Tax (%)" min="0" name="tax[]">
            </div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label class="form-label">Total ( <?=  currency($user->parent->currency) ; ?> )</label>
            <div class="form-control-wrap">
                <input type="number" class="form-control form-control-lg line-total from-import" placeholder="Total" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="total[]" value="<?=  $expense->amount ; ?>" step="0.01" required="" readonly="">
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

<div class="<?=  $instance ; ?>"></div>

<!-- <script type="text/javascript">
    NioApp.initTotals("<?=  $instance ; ?>");
</script> -->
<?php return;
