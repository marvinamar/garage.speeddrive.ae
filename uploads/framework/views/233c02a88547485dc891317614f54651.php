<?php global $s_v_data, $user, $title, $invoices, $clients; ?>
<div class="nk-footer">
    <div class="container-fluid">
        <div class="nk-footer-wrap">
            <div class="nk-footer-copyright"> &copy; <?=  date("Y") ; ?> <?=  env("APP_NAME") ; ?> • All Rights Reserved.
            </div>
        </div>
    </div>
</div>

<!-- Modal send via email -->
<div class="modal fade" tabindex="-1" id="sendviaemail">
	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
	            <em class="icon ni ni-cross"></em>
	        </a>
	        <div class="modal-header">
	            <h5 class="modal-title">Send Via Email</h5>
	        </div>
	        <form class="simcy-form" action="" data-parsley-validate="" method="POST" loader="true">
	            <div class="modal-body">
	                <p>Send via email</p>
	                <div class="row gy-4">
	                    <div class="col-sm-12">
	                        <div class="form-group">
	                            <label class="form-label">Email Address</label>
	                            <div class="form-control-wrap">
	                                <input type="email" class="form-control form-control-lg" placeholder="Email Address" name="email" required="">
	                                <input type="hidden" name="itemid" required="">
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col-sm-12">
	                        <div class="form-group">
	                            <label class="form-label">Subject</label>
	                            <div class="form-control-wrap">
	                                <input type="text" class="form-control form-control-lg" placeholder="Subject" name="subject" required="">
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col-sm-12">
	                        <div class="form-group">
	                            <label class="form-label">Message</label>
	                            <div class="form-control-wrap">
	                                <textarea class="form-control form-control-lg" placeholder="Message" rows="5" name="message" required=""></textarea>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="modal-footer bg-light">
	                <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
	                <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Send Email</span></button>
	            </div>
	        </form>
	    </div>
	</div>
</div>

<!-- Modal send via email -->
<div class="modal fade" tabindex="-1" id="sendviasms">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Send Via SMS</h5>
            </div>
            <form class="simcy-form" action="" data-parsley-validate="" method="POST" loader="true">
                <div class="modal-body">
                    <p>Send via SMS</p>
                    <div class="row gy-4">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number" required="">
                                    <input class="hidden-phone" type="hidden" name="phonenumber">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">Message</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control form-control-lg" placeholder="Message" rows="5" name="message" required=""></textarea>
                                    <input type="hidden" name="sendto" value="enternumber" required="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                    <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Send SMS</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal send via email -->
<div class="modal fade" tabindex="-1" id="sendviawhatsapp">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Send Via Whatsapp</h5>
            </div>
            <form class="" action="https://api.whatsapp.com/send" data-parsley-validate="" method="GET" loader="true" target="_blank">
                <div class="modal-body">
                    <p>Send via Whatsapp</p>
                    <div class="row gy-4">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control form-control-lg phone-input" placeholder="Phone Number" required="">
                                    <input class="hidden-phone" type="hidden" name="phone">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">Message</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control form-control-lg" placeholder="Message" rows="5" name="text" required=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                    <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Send Message</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update XL Modal -->
<div class="modal fade" tabindex="-1" id="update-xl">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Manage Info</h5>
            </div>
            <div class="update-holder-xl"></div>
        </div>
    </div>
</div>

<!-- Update XL Modal -->
<div class="modal fade" tabindex="-1" id="update-lg">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Manage Info</h5>
            </div>
            <div class="update-holder-lg"></div>
        </div>
    </div>
</div>


<!-- Update Modal -->
<div class="modal fade" tabindex="-1" id="update-project">
    <?php if ($user->parent->booking_form == "Detailed") { ?>
    <div class="modal-dialog modal-xl" role="document">
    <?php } else { ?>
    <div class="modal-dialog" role="document">
    <?php } ?>
        <div class="modal-content">
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Project</h5>
            </div>
            <div class="update-project-holder"></div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var currency = "<?=  currency($user->parent->currency) ; ?>";
    var carsketch = "data:image/jpeg;base64,<?=  base64_encode(file_get_contents('uploads/app/sedan-min.png')) ; ?>";
</script>
<?php return;
