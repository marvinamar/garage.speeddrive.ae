<?php global $s_v_data, $payment, $user; ?>
<form class="simcy-form" action="<?=  url('Projectpayment@update') ; ?>" data-parsley-validate="" method="POST" loader="true">
    <div class="modal-body">
        <p>Update payment</p>
        <div class="row gy-4">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Amount</label>
                    <div class="form-control-wrap">
                        <div class="form-text-hint">
                            <span class="overline-title"><?=  currency($user->parent->currency) ; ?></span>
                        </div>
                        <input type="number" class="form-control form-control-lg" placeholder="Amount" data-parsley-pattern="^[0-9]\d*(\.\d+)?$" name="amount" value="<?=  $payment->amount ; ?>" step="0.01" min="0.01" required="">
                        <input type="hidden" name="paymentid" value="<?=  $payment->id ; ?>">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Payment Date</label>
                    <div class="form-control-wrap">
                        <input type="date" class="form-control form-control-lg" placeholder="Payment Date" value="<?=  $payment->payment_date ; ?>" name="payment_date" required="">
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <div class="form-control-wrap ">
                        <div class="form-control-select">
                            <select class="form-control form-control-lg" name="method">
                                <option value="Cash" <?php if ($payment->method == "Cash") { ?> selected <?php } ?>>Cash</option>
                                <option value="Card" <?php if ($payment->method == "Card") { ?> selected <?php } ?>>Card</option>
                                <option value="Mobile Money" <?php if ($payment->method == "Mobile Money") { ?> selected <?php } ?>>Mobile Money</option>
                                <option value="Bank" <?php if ($payment->method == "Bank") { ?> selected <?php } ?>>Bank</option>
                                <option value="Cheque" <?php if ($payment->method == "Cheque") { ?> selected <?php } ?>>Cheque</option>
                                <option value="Online Payment" <?php if ($payment->method == "Online Payment") { ?> selected <?php } ?>>Online Payment</option>
                                <option value="Other" <?php if ($payment->method == "Other") { ?> selected <?php } ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Note</label>
                    <div class="form-control-wrap">
                        <textarea class="form-control form-control-lg" placeholder="Note" rows="2" name="note"><?=  $payment->note ; ?></textarea>
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
<?php return;
