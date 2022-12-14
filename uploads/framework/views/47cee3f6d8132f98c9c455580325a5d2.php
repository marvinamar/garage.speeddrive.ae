<?php global $s_v_data, $project, $user, $suppliers, $inventory; ?>

                <form class="simcy-form" action="<?=  url('Expenses@addbulk') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Add project expenses</p>
                        <input type="hidden" name="project" value="<?=  $project->id ; ?>" required="">
                        <?php if (!empty($project->work_requested)) { ?>
                        <?php foreach ($project->work_requested as $index => $work_requested) { ?>
                        <div class="row m-0 mb-3 modal-section">
                            <div class="col-sm-12 border pt-3 pb-3">
                                <div class="row gy-4">
                                    <div class="col-sm-12 border-bottom">
                                        <a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-bulk-imported pull-right"><em class="icon ni ni-trash"></em></a>
                                        <h6 class="mt-1">#<?=  $index + 1 ; ?> <?=  $work_requested ; ?></h6>
                                    </div>
                                </div>
                                <div class="row gy-4 pt-2">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label">Source</label>
                                            <div class="form-control-wrap ">
                                                <div class="form-control-select">
                                                    <select class="form-control form-control-lg source-select" name="source<?=  $index ; ?>">
                                                        <option value="Suppliers">External Suppliers</option>
                                                        <option value="Inventory">From Inventory</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row gy-4 pt-2 external-source">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Expense / Item name</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control form-control-lg" placeholder="Expense / Item name" name="expense<?=  $index ; ?>" required="" value="<?=  $work_requested ; ?>">
                                                <input type="hidden" name="indexing[]" value="<?=  $index ; ?>" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Supplier</label>
                                            <div class="form-control-wrap">
                                                <div class="form-control-select">
                                                    <select class="form-control select2-dynamic supplier-select" name="supplier<?=  $index ; ?>">
                                                        <option value="">Select Supplier</option>
                                                        <optgroup label="New Supplier">
                                                            <option value="create" class="fw-bold">Create New Supplier</option>
                                                        </optgroup>
                                                        <optgroup label="Select from suppliers">
                                                            <?php if (!empty($suppliers)) { ?>
                                                            <?php foreach ($suppliers as $supplier) { ?>
                                                            <option value="<?=  $supplier->id ; ?>"><?=  $supplier->name ; ?></option>
                                                            <?php } ?>
                                                            <?php } ?>
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 new-supplier-input" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label">Supplier Name</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control form-control-lg" placeholder="Supplier Name" name="suppliername<?=  $index ; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 new-supplier-input" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label">Phone Number</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number">
                                                <input class="hidden-phone" type="hidden" name="phonenumber<?=  $index ; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Quantity</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control form-control-lg" placeholder="Quantity" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="quantity<?=  $index ; ?>" value="1" step="0.01" min="0.01" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Quantity Unit</label>
                                            <div class="form-control-wrap">
                                                <select class="form-control form-control-lg" name="quantity_unit<?=  $index ; ?>">
                                                    <option value="Pieces">Pieces</option>
                                                    <option value="Units">Units</option>
                                                    <option value="Litres">Litres</option>
                                                    <option value="Kilograms">Kilograms</option>
                                                    <option value="Pounds">Pounds</option>
                                                    <option value="Gallons">Gallons</option>
                                                    <option value="Meters">Meters</option>
                                                    <option value="Set">Set</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Total Amount</label>
                                            <div class="form-control-wrap">
                                                <div class="form-text-hint">
                                                    <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                                </div>
                                                <input type="number" class="form-control form-control-lg" placeholder="Total Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="amount<?=  $index ; ?>" value="0.00" step="0.01" min="0" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Expense Date</label>
                                            <div class="form-control-wrap">
                                                <input type="date" class="form-control form-control-lg" placeholder="Expense Date" name="expense_date<?=  $index ; ?>" value="<?=  date('Y-m-d') ; ?>" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="form-label">Type</label>
                                            <div class="form-control-wrap ">
                                                <div class="form-control-select">
                                                    <select class="form-control form-control-lg" name="type<?=  $index ; ?>">
                                                        <option value="Part">Part</option>
                                                        <option value="Service">Service</option>
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
                                                    <select class="form-control form-control-lg expense-status" name="status<?=  $index ; ?>">
                                                        <option value="Delivered">Delivered</option>
                                                        <option value="Ordered">Ordered</option>
                                                        <option value="Awaiting Delivery">Awaiting Delivery</option>
                                                        <option value="To Order">To Order</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 expense-delivery" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label">Expected Delivery Date</label>
                                            <div class="form-control-wrap">
                                                <input type="date" class="form-control form-control-lg" placeholder="Expected Delivery Date" name="expected_delivery_date<?=  $index ; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 expense-delivery" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label">Expected Delivery Time</label>
                                            <div class="form-control-wrap">
                                                <input type="time" class="form-control form-control-lg" placeholder="Expected Delivery Time" name="expected_delivery_time<?=  $index ; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                             <div class="custom-control custom-switch aligned">
                                                <input type="checkbox" name="paid<?=  $index ; ?>" id="expense-paid-<?=  $index ; ?>" class="custom-control-input expense-paid" value="Yes" checked="">
                                                <label class="custom-control-label" for="expense-paid-<?=  $index ; ?>">Expense / Item supplier paid</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 expense-payment" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label">Payment due on</label>
                                            <div class="form-control-wrap">
                                                <input type="date" class="form-control form-control-lg" placeholder="Payment due on" name="payment_due<?=  $index ; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row gy-4 pt-2 inventory-source" style="display: none;">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Item / Part</label>
                                            <div class="form-control-wrap">
                                                <div class="form-control-select">
                                                    <select class="form-control select2-dynamic inventory-select" name="inventory<?=  $index ; ?>">
                                                        <option value="">Select Item</option>
                                                        <?php if (!empty($inventory)) { ?>
                                                        <?php foreach ($inventory as $item) { ?>
                                                        <option value="<?=  $item->id ; ?>" quantity="<?=  $item->quantity ; ?>" units="<?=  $item->quantity_unit ; ?>" cost="<?=  $item->unit_cost ; ?>"><?=  $item->name ; ?> ( <?=  $item->quantity ; ?> <?=  $item->quantity_unit ; ?> )</option>
                                                        <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Units Consumed</label>
                                            <div class="form-control-wrap">
                                                <div class="form-text-hint">
                                                    <span class="overline-title units">Units</span>
                                                </div>
                                                <input type="number" class="form-control form-control-lg expense-consumed" placeholder="Units Consumed" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="consumed<?=  $index ; ?>" value="0.00" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Amount</label>
                                            <div class="form-control-wrap">
                                                <div class="form-text-hint">
                                                    <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                                </div>
                                                <input type="number" class="form-control form-control-lg expense-total-amount" placeholder="Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="totalamount<?=  $index ; ?>" value="0.00" step="0.01" required="" readonly="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Expense Date</label>
                                            <div class="form-control-wrap">
                                                <input type="date" class="form-control form-control-lg" placeholder="Expense Date" name="expensedate<?=  $index ; ?>" value="<?=  date('Y-m-d') ; ?>" required="">
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
                                <div class="alert alert-fill alert-warning alert-icon"><em class="icon ni ni-alert-circle"></em> No work requested added on this project.</div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <?php if (!empty($project->work_requested)) { ?>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Save Expense</span></button>
                        <?php } ?>
                    </div>
                </form>
                <script type="text/javascript">
                    NioApp.initPhoneInput();
                    $(".select2-dynamic").select2();
                </script>
<?php return;
