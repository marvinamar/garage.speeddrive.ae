<?php global $s_v_data, $supplier; ?>
                <form class="simcy-form" action="<?=  url('Suppliers@update') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Update supplier.</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Supplier Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Supplier Name" name="name" value="<?=  $supplier->name ; ?>" required="">
                                        <input type="hidden" name="supplierid" value="<?=  $supplier->id ; ?>" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number" required="" value="<?=  $supplier->phonenumber ; ?>">
                                        <input class="hidden-phone" type="hidden" name="phonenumber" value="<?=  $supplier->phonenumber ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <div class="form-control-wrap">
                                        <input type="email" class="form-control form-control-lg" placeholder="Email Address" name="email" value="<?=  $supplier->email ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Address" name="address" value="<?=  $supplier->address ; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">VAT PIN Number</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="VAT PIN Number" name="vat_pin" value="<?=  $supplier->vat_pin ; ?>">
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
                    NioApp.initPhoneInput();
                </script>
<?php return;
