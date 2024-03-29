<?php global $s_v_data, $quote, $quoteitems, $user, $inventorys; ?>
<form class="simcy-form" action="<?=  url('Quote@update_at_project') ; ?>" data-parsley-validate="" method="POST" loader="true">
    <div class="modal-body">
        <p>Update quote info.</p>
        <div class="item-lines" data-type="quote">
            <?php if (!empty($quoteitems)) { ?>
            <?php foreach ($quoteitems as $index => $quoteitem) { ?>
            <div class="row gy-4">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="form-label">Item</label>
                        <div class="form-control-wrap">
                            <select name="item[]" class="select_<?= $index; ?> form-control form-control-lg selectpicker" data-live-search="true" onchange="get_item_details(this)">
                                <option value="0" selected>Select Item</option>
                                <?php foreach ($inventorys as $inventory) { ?>
                                    <?php if ($inventory->id == $quoteitem->item) { ?>
                                        <option value="<?= $inventory->id; ?>" selected><?= $inventory->name; ?></option>
                                    <?php } else { ?>
                                        <option value="<?= $inventory->id; ?>" ><?= $inventory->name; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="itemid[]" value="<?=  $quoteitem->id ; ?>" required="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <div class="form-control-wrap">
                            <input type="text" class="form-control form-control-lg" name="item_description[]" value="<?= $quoteitem->item_description; ?>">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="form-label">Work</label>
                        <div class="form-control-wrap">
                            <select class="form-control form-control-lg" name="workType[]">
                                <option value="0" <?= $quoteitem->workType == '0' ? 'selected' : ''; ?>>Select Work</option>
                                <option value="body_work" <?= $quoteitem->workType == 'body_work' ? 'selected' : ''; ?>>Body Work</option>                                                
                                <option value="mechanical_work" <?= $quoteitem->workType == 'mechanical_work' ? 'selected' : ''; ?>>Mechanical Work</option>                                                
                                <option value="electrical_work" <?= $quoteitem->workType == 'electrical_work' ? 'selected' : ''; ?>>Electrical Work</option>
                                <option value="ac_work" <?= $quoteitem->workType == 'ac_work' ? 'selected' : ''; ?>>AC Work</option>                                                
                                <option value="other_work" <?= $quoteitem->workType == 'other_work' ? 'selected' : ''; ?>>Other Work</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="form-group">
                        <label class="form-label">Qty</label>
                        <div class="form-control-wrap hide-arrows">
                            <input type="number" class="form-control form-control-lg line-quantity" value="<?=  $quoteitem->quantity ; ?>" min="1" placeholder="Quantity" name="quantity[]" required="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
                        <div class="form-control-wrap hide-arrows">
                            <input type="number" class="form-control form-control-lg line-cost cost_<?= $index; ?>" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="<?=  $quoteitem->cost ; ?>" step="0.01" required="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="form-group">
                        <label class="form-label">Tax (%)</label>
                        <div class="form-control-wrap hide-arrows">
                            <input type="number" class="form-control form-control-lg line-tax" value="<?=  $quoteitem->tax ; ?>"  placeholder="Tax (%)" min="0" name="tax[]">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="form-label">Total ( <?=  currency($user->parent->currency) ; ?> )</label>
                        <div class="form-control-wrap">
                            <input type="number" class="form-control form-control-lg line-total" placeholder="Total" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="total[]" value="<?=  $quoteitem->total ; ?>" step="0.01" required="" readonly="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-1">
                    <?php if ($index > 0) { ?>
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <a href="#" class="btn btn-icon btn-lg btn-round btn-dim btn-outline-danger mt-gs remove-line" data-toggle="tooltip" title="Remove Item"><em class="icon ni ni-cross-circle-fill"></em></a>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <?php } ?>
        </div>
        <div class="item-totals border-top mt-2 pt-2">
            <div class="row gy-4 d-flex justify-content-end">
                <div class="col-sm-7">
                    <a href="" class="btn btn-dim btn-outline-primary mt-2 add-item-quote" data-type="invoice"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                </div>
                <div class="col-sm-4 text-right">
                    <div class="fw-normal">Sub Total:<div class="fw-bold sub-total"><?=  currency($user->parent->currency) ; ?> <?=  $quote->subtotal ; ?></div></div>
                    <div class="fw-normal">VAT Tax:<div class="fw-bold tax-total"><?=  currency($user->parent->currency) ; ?> <?=  $quote->tax_amount ; ?></div></div>
                    <div class="fw-bold fs-19px border-top">Total:<div class="fw-bold grand-total"><?=  currency($user->parent->currency) ; ?> <?=  $quote->total ; ?></div></div>
                </div>
                <div class="col-sm-1">
                </div>
            </div>
        </div>
        <div class="item-totals border-top mt-2">
            <div class="row gy-4">
                <div class="col-12">
                    <div class="form-group mt-1">
                        <label class="form-label">Notes</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control form-control-lg unset-mh" placeholder="Notes" rows="2" name="notes"><?=  $quote->notes ; ?></textarea>
                            <input type="hidden" name="quoteid" value="<?=  $quote->id ; ?>" required="">
                        </div>
                        <div class="form-note">Notes will be printed on the quote.</div>
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
<script>$('.selectpicker').selectpicker('refresh');</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.grouped').select2({
            dropdownParent: $('#create'),
            matcher: function(params, data) {
                var original_matcher = $.fn.select2.defaults.defaults.matcher;
                var result = original_matcher(params, data);
                if (result && data.children && result.children && data.children.length != result.children.length
                    && data.text.toLowerCase().includes(params.term)) {
                    result.children = data.children;
                }
                return result;
            }
        });
    });
</script>

<script>
$("body").on("click", ".add-item-quote", function(event){
event.preventDefault();
var count = parseFloat($('#count').val()) + 1;
var holder = $(this).closest(".modal").find(".item-lines");
var line = ' <div class="row gy-4"> '
                +'<div class="col-sm-3">'
                    +'<div class="form-group">'
                        +'<label class="form-label">Item Description</label> '
                            +'<div class="form-control-wrap"> '
                                +'<select name="item[]" id="item[]" class="select_'+count+' form-control form-control-lg  selectpicker" data-live-search="true" onchange="get_item_details(this)">'
                                +'<?php foreach ($inventorys as $inventory) { ?>'
                                +'<option value="<?= $inventory->id; ?>" ><?= $inventory->name; ?></option>'
                                +'<?php } ?>'
                                +'</select>'
                                +'<input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">'
            +'</div></div></div>'
            +'<div class="col-sm-3">'
                        +'        <div class="form-group">'
                        +'            <label class="form-label">Description</label>'
                        +'            <div class="form-control-wrap">'
                        +'                <input type="text" class="form-control form-control-lg" name="item_description[]" value="<?= $quoteitem->item_description; ?>">'
                        +'            </div>'
                        +'        </div>'
                        +'</div>'
            +'<div class="col-sm-2">'
        +'    <div class="form-group">'
        +'        <label class="form-label">Work</label>'
        +'        <div class="form-control-wrap">'
        +'            <select class="form-control form-control-lg" name="workType[]">'
        +'                <option value="0">Select Work</option>'
        +'                <option value="body_work">Body Work</option>'
        +'                <option value="mechanical_work">Mechanical Work</option>'
        +'                <option value="electrical_work">Electrical Work</option>'
        +'                <option value="ac_work">AC Work</option>'
        +'                <option value="other_work">Other Work</option>'
        +'            </select>'
        +'        </div>'
        +'    </div>'
        +'</div>'
        +'<div class="col-sm-1"> <div class="form-group"> <label class="form-label">Qty</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-quantity" value="1" min="1" placeholder="Quantity" name="quantity[]" required=""> </div></div></div><div class="col-sm-2"> <div class="form-group"> <label class="form-label">Unit Cost ( '+currency+' )</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-cost cost_'+count+'" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required=""> </div></div></div><div class="col-sm-1"> <div class="form-group"> <label class="form-label">Tax (%)</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-tax" placeholder="Tax (%)" min="0" name="tax[]"> </div></div></div><div class="col-sm-2"> <div class="form-group"> <label class="form-label">Total ( '+currency+' )</label> <div class="form-control-wrap"> <input type="number" class="form-control form-control-lg line-total" placeholder="Amount" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="total[]" value="0.00" step="0.01" required="" readonly=""> </div></div></div><div class="col-sm-1"> <div class="form-group"> <div class="form-control-wrap"> <a href="#" class="btn btn-icon btn-lg btn-round btn-dim btn-outline-danger mt-gs remove-line" data-toggle="tooltip" title="Remove Item"><em class="icon ni ni-cross-circle-fill"></em></a> </div></div></div></div>';
            

holder.append(line);
$('#count').val(count)
$('[data-toggle="tooltip"]').tooltip();
$('.selectpicker').selectpicker('refresh');

});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<script>
function get_item_details(select){
    var selected = select.value;
    var selectedClass = select.classList[0];
    var count = selectedClass.replace('select_','');
    
    $.ajax({
        url: '<?=  url("Quote@get_item_details") ; ?>' + selected,
        data: [],
        dataType: 'json',
        success: function( data ) {
                $('.cost_'+count).val(data[0].unit_cost);
            },
            error: function() {
                alert('Error');
            }
    })
}
</script>

<?php return;
