<?php global $s_v_data, $expense, $user, $suppliers, $inventorys; ?>
                <form class="simcy-form  modal-section" action="<?=  url('Expenses@update') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Update a project expense</p>
                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Expense</label>
                                    <div class="form-control-wrap">
                                        <!-- <input type="text" class="form-control form-control-lg" placeholder="Expense" name="expense" value="<?=  $expense->expense ; ?>" required=""> -->
                                        <select name="expense" class="select_<?= $index; ?> form-control form-control-lg" data-live-search="true" > <!-- onchange="get_item_details(this)"-->
                                            <option value="0" selected>Expense / Item name</option>
                                            <?php foreach ($inventorys as $inventory) { ?>
                                            <?php if ($inventory->id == $expense->expense) { ?>
                                                <option value="<?= $inventory->id; ?>" selected><?= $inventory->name; ?></option>
                                            <?php } else { ?>
                                                <option value="<?= $inventory->id; ?>" ><?= $inventory->name; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="expenseid" value="<?=  $expense->id ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Supplier</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control select2-dynamic supplier-select" name="supplier">
                                                <option value="">Select Supplier</option>
                                                <optgroup label="New Supplier">
                                                    <option value="create" class="fw-bold">Create New Supplier</option>
                                                </optgroup>
                                                <optgroup label="Select from suppliers">
                                                    <?php if (!empty($suppliers)) { ?>
                                                    <?php foreach ($suppliers as $supplier) { ?>
                                                    <option value="<?=  $supplier->id ; ?>" <?php if ($expense->supplier == $supplier->id) { ?> selected <?php } ?>><?=  $supplier->name ; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 new-supplier-input" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Supplier Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Supplier Name" name="suppliername">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 new-supplier-input" style="display: none;">
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
                                    <label class="form-label">Quantity</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Quantity" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="quantity" value="<?=  $expense->quantity ; ?>" step="0.01" min="0.01" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Total Amount</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Total Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="amount"  value="<?=  $expense->amount ; ?>" step="0.01" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Expense Date</label>
                                    <div class="form-control-wrap">
                                        <input type="date" class="form-control form-control-lg" placeholder="Expense Date" name="expense_date" value="<?=  $expense->expense_date ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Type</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg" name="type">
                                                <option value="Part" <?php if ($expense->type == "Part") { ?> selected <?php } ?>>Part</option>
                                                <option value="Service" <?php if ($expense->type == "Service") { ?> selected <?php } ?>>Service</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-wrap ">
                                        <div class="form-control-select">
                                            <select class="form-control form-control-lg expense-status" name="status">
                                                <option value="Delivered" <?php if ($expense->status == "Delivered") { ?> selected <?php } ?>>Delivered</option>
                                                <option value="Ordered" <?php if ($expense->status == "Ordered") { ?> selected <?php } ?>>Ordered</option>
                                                <option value="Awaiting Delivery" <?php if ($expense->status == "Awaiting Delivery") { ?> selected <?php } ?>>Awaiting Delivery</option>
                                                <option value="To Order" <?php if ($expense->status == "To Order") { ?> selected <?php } ?>>To Order</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($expense->status != "Delivered") { ?>
                            <div class="col-sm-6 expense-delivery">
                            <?php } else { ?>
                            <div class="col-sm-6 expense-delivery" style="display: none;">
                            <?php } ?>
                                <div class="form-group">
                                    <label class="form-label">Expected Delivery Date</label>
                                    <div class="form-control-wrap">
                                        <?php if ($expense->status != "Delivered") { ?>
                                        <input type="date" class="form-control form-control-lg" placeholder="Expected Delivery Date" name="expected_delivery_date" value="<?=  $expense->expected_delivery_date ; ?>" required="">
                                        <?php } else { ?>
                                        <input type="date" class="form-control form-control-lg" placeholder="Expected Delivery Date" name="expected_delivery_date" value="<?=  $expense->expected_delivery_date ; ?>">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($expense->status != "Delivered") { ?>
                            <div class="col-sm-6 expense-delivery">
                            <?php } else { ?>
                            <div class="col-sm-6 expense-delivery" style="display: none;">
                            <?php } ?>
                                <div class="form-group">
                                    <label class="form-label">Expected Delivery Time</label>
                                    <div class="form-control-wrap">
                                        <input type="time" class="form-control form-control-lg" placeholder="Expected Delivery Time" name="expected_delivery_time" value="<?=  $expense->expected_delivery_time ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                     <div class="custom-control custom-switch">
                                        <?php if ($expense->paid == "Yes") { ?>
                                        <input type="checkbox" name="paid" id="expense-paid" class="custom-control-input expense-paid" value="Yes" checked="">
                                        <?php } else { ?>
                                        <input type="checkbox" name="paid" id="expense-paid" class="custom-control-input expense-paid" value="Yes">
                                        <?php } ?>
                                        <label class="custom-control-label" for="expense-paid">Expense / Item supplier paid</label>
                                    </div>
                                </div>
                            </div>
                            <?php if ($expense->paid == "Yes") { ?>
                            <div class="col-sm-12 expense-payment" style="display: none;">
                            <?php } else { ?>
                            <div class="col-sm-12 expense-payment">
                            <?php } ?>
                                <div class="form-group">
                                    <label class="form-label">Payment due on</label>
                                    <div class="form-control-wrap">
                                        <?php if ($expense->paid == "Yes") { ?>
                                        <input type="date" class="form-control form-control-lg" placeholder="Payment due on" name="payment_due" value="<?=  $expense->payment_due ; ?>">
                                        <?php } else { ?>
                                        <input type="date" class="form-control form-control-lg" placeholder="Payment due on" name="payment_due" value="<?=  $expense->payment_due ; ?>" required="">
                                        <?php } ?>
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
                    NioApp.initPhoneInput();
            </script>
<?php return;
