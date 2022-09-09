<?php global $s_v_data, $inventory, $suppliers, $user; ?>
                <form class="simcy-form" action="<?=  url('Inventory@update') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Update Item</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Item Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Item Name" value="<?=  $inventory->name ; ?>" name="name" required="">
                                        <input type="hidden" name="inventoryid" value="<?=  $inventory->id ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Quantity</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Quantity" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="quantity" value="<?=  $inventory->quantity ; ?>" step="0.01" min="0.00" required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Quantity Unit</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-control-lg" name="quantity_unit">
                                            <option value="Units" <?php if ($inventory->quantity_unit == "Units") { ?> selected <?php } ?>>Units</option>
                                            <option value="Litres" <?php if ($inventory->quantity_unit == "Litres") { ?> selected <?php } ?>>Litres</option>
                                            <option value="Kilograms" <?php if ($inventory->quantity_unit == "Kilograms") { ?> selected <?php } ?>>Kilograms</option>
                                            <option value="Pounds" <?php if ($inventory->quantity_unit == "Pounds") { ?> selected <?php } ?>>Pounds</option>
                                            <option value="Gallons" <?php if ($inventory->quantity_unit == "Gallons") { ?> selected <?php } ?>>Gallons</option>
                                            <option value="Meters" <?php if ($inventory->quantity_unit == "Meters") { ?> selected <?php } ?>>Meters</option>
                                            <option value="Pieces" <?php if ($inventory->quantity_unit == "Pieces") { ?> selected <?php } ?>>Pieces</option>
                                            <option value="Pieces" <?php if ($inventory->quantity_unit == "Pieces") { ?> selected <?php } ?>>Pieces</option>
                                            <option value="Set" <?php if ($inventory->quantity_unit == "Set") { ?> selected <?php } ?>>Set</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Restock Quantity</label>
                                    <div class="form-control-wrap">
                                        <input type="number" class="form-control form-control-lg" placeholder="Restock Quantity" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" value="<?=  $inventory->restock_quantity ; ?>" name="restock_quantity" step="0.01" min="0.00">
                                    </div>
                                    <div class="form-note">Item will show restock if item quantity is below this level.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Unit Cost</label>
                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title"><?=  $user->parent->currency ; ?></span>
                                        </div>
                                        <input type="number" class="form-control form-control-lg" placeholder="Unit Cost" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="unit_cost" value="<?=  $inventory->unit_cost ; ?>" step="0.01" min="0.00" required="required">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Select Supplier</label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-control-lg select2-dynamic" name="supplier" >
                                            <option value="">Select Supplier</option>
                                            <?php if (!empty($suppliers)) { ?>
                                            <?php foreach ($suppliers as $supplier) { ?>
                                                <option value="<?=  $supplier->id ; ?>" <?php if ($inventory->supplier == $supplier->id) { ?> selected <?php } ?>><?=  $supplier->name ; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Item Code</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Item Code" name="shelf_number" value="<?=  $inventory->shelf_number ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Shelf Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Shelf Number" name="item_code" value="<?=  $inventory->item_code ; ?>">
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
