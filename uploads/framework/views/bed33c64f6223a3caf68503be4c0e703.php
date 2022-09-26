<?php global $s_v_data, $invoice, $invoiceitems, $user, $project, $inventorys; ?>
                <form class="simcy-form" action="<?=  url('Invoice@update') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body update-invoice-calculation">
                        <p>Update invoice info.</p>
                        <div class="item-lines asd-add-invoice-alls" data-type="invoice">
                            <?php if (!empty($invoiceitems)) { ?>
                            <?php foreach ($invoiceitems as $index => $invoiceitem) { ?>
                            <div class="row gy-4">

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Item</label>
                                        <div class="form-control-wrap">
                                            <select name="item[]" class="select_<?= $index; ?> form-control form-control-lg" data-live-search="true" onchange="get_item_details(this)">
                                                <option value="0" selected>Select Item</option>
                                                <?php foreach ($inventorys as $inventory) { ?>
                                                    <?php if ($inventory->id == $invoiceitem->item) { ?>
                                                        <option value="<?= $inventory->id; ?>" selected><?= $inventory->name; ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?= $inventory->id; ?>" ><?= $inventory->name; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" name="itemid[]" value="<?=  $invoiceitem->id ; ?>" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control form-control-lg" name="item_description[]" value="<?= $invoiceitem->item_description; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Work</label>
                                        <div class="form-control-wrap">
                                            <select class="form-control form-control-lg" name="workType[]">
                                                <option value="0" <?= $invoiceitem->workType == '0' ? 'selected' : ''; ?>>Select Work</option>
                                                <option value="body_work" <?= $invoiceitem->workType == 'body_work' ? 'selected' : ''; ?>>Body Work</option>                                                
                                                <option value="mechanical_work" <?= $invoiceitem->workType == 'mechanical_work' ? 'selected' : ''; ?>>Mechanical Work</option>                                                
                                                <option value="electrical_work" <?= $invoiceitem->workType == 'electrical_work' ? 'selected' : ''; ?>>Electrical Work</option>
                                                <option value="ac_work" <?= $invoiceitem->workType == 'ac_work' ? 'selected' : ''; ?>>AC Work</option>                                                
                                                <option value="other_work" <?= $invoiceitem->workType == 'other_work' ? 'selected' : ''; ?>>Other Work</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="form-label">Qty</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-quantity" value="<?=  $invoiceitem->quantity ; ?>" min="1" placeholder="Quantity" name="quantity[]" id="qtyt" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Unit Cost ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="cost[]" value="<?=  $invoiceitem->cost ; ?>" step="0.01" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="form-label">Tax (%)</label>
                                        <div class="form-control-wrap hide-arrows">
                                            <input type="number" class="form-control form-control-lg line-tax nl-line-tax" value="<?=  $invoiceitem->tax ; ?>"  placeholder="Tax (%)" min="0" name="tax[]">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Total ( <?=  currency($user->parent->currency) ; ?> )</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control form-control-lg line-total new-line-total" placeholder="Total" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="total[]" value="<?=  $invoiceitem->total ; ?>" step="0.01" required="" readonly="">
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
                                <div class="col-sm-2">
                                    <a href="" class="btn btn-dim btn-outline-primary mt-2 add-item-quote" data-type="invoice"><em class="icon ni ni-plus"></em><span>Add Item</span> </a>
                                </div>

                                
                                
                                 <div class="col-sm-5 mt-2" >
                                    <div class="drodown mr-1">
                                        <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" id="asd-add-invoiceitem-as" data-toggle="dropdown" aria-expanded="false"><em class="icon ni ni-swap"></em> <span>Import From</span></a>
                                        <div class="dropdown-menu dropdown-menu-right" style="">
                                            <ul class="link-list-opt no-bdr">
                                                <!-- <li><a class="fetch-display-click" data="projectid:314" url="/garage/garageroot/invoices/workrequested/" holder=".item-lines.invoice-items" modal="#createinvoice" href=""><em class="icon ni ni-clipboad-check"></em><span>Work Requested</span></a></li>
                                                <li><a class="select-from-jobcard" data-url="/garage/garageroot/invoices/jobcards/" holder=".item-lines.invoice-items" modal="#createinvoice" href=""><em class="icon ni ni-todo"></em><span>Approved Jobcard</span></a></li> -->
                                                <li><a class="fetch-display-click" data="projectid:<?=  $project->id ; ?>" url="<?=  url('Invoice@expenses'); ?>" holder=".item-lines.invoice-items" modal="#importtoinvoice" href=""><em class="icon ni ni-cart"></em><span>Parts &amp; Expenses</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class="col-sm-4 text-right">
                                    <div class="fw-normal">Sub Totalsss: <?=  currency($user->parent->currency) ; ?> <div class="fw-bold sub-total"><?=  $invoice->subtotal ; ?></div></div>
                                    <div class="fw-normal">VAT Tax: <?=  currency($user->parent->currency) ; ?> <div class="fw-bold tax-total"><?=  $invoice->tax_amount ; ?></div></div>
                                    <div class="fw-bold fs-19px border-top">Total: <?=  currency($user->parent->currency) ; ?> <div class="fw-bold grand-total"><?=  $invoice->total ; ?></div></div>
                                    <input type="hidden"  value="<?=  $invoice->total ; ?>" id="gtotal">
                                    <input type="hidden"  value="<?=  $invoice->subtotal ; ?>" id="gsubtotal">
                                    <input type="hidden"  value="<?=  $invoice->tax_amount ; ?>" id="gtaxtotal">
                                </div>
                                <div class="col-sm-1">
                                </div>
                            </div>
                        </div>
                        <div class="item-totals mt-2">
                            <div class="row gy-4 border-top mt-1">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Invoice Date</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Invoice Date" value="<?=  $invoice->invoice_date ; ?>" name="invoice_date" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Payment Due</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Payment Due" value="<?=  $invoice->due_date ; ?>" name="due_date" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mt-1">
                                        <label class="form-label">Notes</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Notes" rows="2" name="notes"><?=  $invoice->notes ; ?></textarea>
                                            <input type="hidden" name="invoiceid" value="<?=  $invoice->id ; ?>" required="">
                                        </div>
                                        <div class="form-note">Notes will be printed on the invoice.</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Payment Details</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Payment Details" rows="2" name="payment_details"><?=  $invoice->payment_details ; ?></textarea>
                                        </div>
                                        <div class="form-note">Payment details will be printed on the invoice.</div>
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
                
                
                 <!-- Modal add expense to invoice (edit) -->
    <div class="modal fade" tabindex="-1" id="importtoinvoice">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Import Expsense</h5>
                </div>
                <!-- <?=  url('Invoice@create') ; ?> -->
                <form class="simcy-form" action="" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add existing expense to the invoice </p>
                        <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                        <div class="item-lines invoice-items asd-add-invoice-all orange" data-type="quote">
                
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary asd-add-invoiceitem-as" ><em class="icon ni ni-check-circle-cut"></em><span>Import to invoice</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    
<?php return;
