<?php global $s_v_data, $title, $user, $quote, $owner; ?>
<?= view( 'includes/head', $s_v_data ); ?>

<script src="<?=  asset('assets/js/jscolor.js') ; ?>"></script>

<body class="nk-body bg-lighter npc-default has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <?= view( 'includes/sidebar', $s_v_data ); ?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <?= view( 'includes/header', $s_v_data ); ?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title"><?=  $title ; ?></h3>
                                            <div class="nk-block-des text-soft">
                                                <p>You are viewing quote #<?=  $quote->id ; ?>.</p>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <ul class="nk-block-tools g-3">
                                                <li>                                                   
                                                 <a href="" class="btn btn-outline-light bg-white d-none d-sm-inline-flex go-back"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
                                                    <a href="" class="btn btn-icon btn-outline-light bg-white d-inline-flex d-sm-none go-back"><em class="icon ni ni-arrow-left"></em></a>
                                                </li>
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-dim btn-outline-primary" data-toggle="dropdown"><em class="icon ni ni-more-h"></em> <span>More</span></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li><a href="<?=  url('Quote@render', array('quoteid' => $quote->id)) ; ?>" download="Quote #<?=  $quote->id ; ?>.pdf"><em class="icon ni ni-download-cloud"></em><span>Download</span></a></li>
                                                                <li><a href="<?=  url('Quote@render', array('quoteid' => $quote->id)) ; ?>" target="_blank"><em class="icon ni ni-printer"></em><span>Print</span></a></li>
                                                                <li><a href="" class="send-via-email" data-url="<?=  url('Quote@send') ; ?>" data-id="<?=  $quote->id ; ?>" data-subject="Quote #<?=  $quote->id ; ?>" data-email="<?=  $owner->email ; ?>"><em class="icon ni ni-mail"></em><span>Send Via Email</span></a></li>
                                                                <li><a class="fetch-display-click" data="quoteid:<?=  $quote->id ; ?>" url="<?=  url('Quote@updateview') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-pen"></em><span>Edit Quote</span></a></li>
                                                                <?php if ($user->parent->quote_signing == "Enabled" && $quote->signed == "No") { ?>
                                                                <li><a data-toggle="modal" data-target="#sign"href=""><em class="icon ni ni-edit"></em><span>Client Signature</span></a></li>
                                                                <?php } ?>
                                                                <li><a class="convert-quote" data-id="<?=  $quote->id ; ?>" href=""><em class="icon ni ni-cc"></em><span>Convert to Invoice</span></a></li>
                                                                <li class="divider"></li>
                                                                <li><a class="send-to-server-click"  data="quoteid:<?=  $quote->id ; ?>" url="<?=  url('Quote@delete') ; ?>" warning-title="Are you sure?" warning-message="This quote will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Quote</span></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <div class="nk-block">
                                    <div class="card card-stretch">
                                        <div class="card-inner">
                                        <!-- start document render -->
                                        <div class="document">
                                            <div class="signer-document">
                                                <!-- open PDF docements -->
                                                <div class="document-pagination d-flex justify-content-between">
                                                    <div class="pull-left">
                                                        <a href="" id="prev" class="btn btn-round btn-dim btn-icon btn-sm btn-light btn-viewer"><em class="icon ni ni-chevron-left-circle"></em></a>
                                                        <a href="" id="next" class="btn btn-round btn-dim btn-icon btn-sm btn-light btn-viewer"><em class="icon ni ni-chevron-right-circle"></em></a>
                                                        <span class="text-muted ml-15">Page <span id="page_num">0</span> / <span id="page_count">0</span></span>
                                                    </div>
                                                    <div>
                                                        <a href="" class="btn btn-round btn-dim btn-icon btn-sm btn-light btn-zoom" zoom="plus"><em class="icon ni ni-plus-circle"></em></a>
                                                        <a href="" class="btn btn-round btn-dim btn-icon btn-sm btn-light btn-zoom" zoom="minus"><em class="icon ni ni-minus-circle"></em></a>

                                                    </div>
                                                </div>
                                                <div class="document-load">
                                                    <div class="loader-box">
                                                        <div class="circle-loader"></div>
                                                    </div>
                                                </div>
                                                <div class="document-error">
                                                    <i class="uil uil-info-circle text-danger"></i>
                                                    <p class="text-muted"><strong>Oops! </strong> <span class="error-message"> Something went wrong.</span></p>
                                                </div>
                                                <div class="text-center">
                                                    <div class="document-map"></div>
                                                    <canvas id="document-viewer"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end document render -->
                                        </div>
                                    </div><!-- .card -->
                                </div><!-- .nk-block -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->
                <?= view( 'includes/footer', $s_v_data ); ?>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>

    

    <!-- Modal create project -->
    <div class="modal fade" tabindex="-1" id="convertquote">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Convert Quote to Invoice</h5>
                </div>
                <form class="simcy-form" action="<?=  url('Quote@convert') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Convert Quote to Invoice</p>
                        <div class="row gy-4">

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Invoice Date</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Invoice Date" value="<?=  date('Y-m-d') ; ?>" name="invoice_date" required="">
                                            <input type="hidden" name="quote" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Payment Due</label>
                                        <div class="form-control-wrap">
                                            <input type="date" class="form-control form-control-lg" placeholder="Payment Due" value="<?=  date('Y-m-d') ; ?>" name="due_date" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Notes</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Notes" rows="2" name="notes"></textarea>
                                        </div>
                                        <div class="form-note">Notes will be printed on the invoice.</div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="form-label">Payment Details</label>
                                        <div class="form-control-wrap">
                                            <textarea class="form-control form-control-lg unset-mh" placeholder="Payment Details" rows="4" name="payment_details"><?=  $user->parent->payment_details ; ?></textarea>
                                        </div>
                                        <div class="form-note">Payment details will be printed on the invoice.</div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary" type="submit"><em class="icon ni ni-check-circle-cut"></em><span>Convert Quote</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($user->parent->quote_signing == "Enabled" && $quote->signed == "No") { ?>
    <!-- Modal add expense -->
    <div class="modal fade" tabindex="-1" id="sign">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header">
                    <h5 class="modal-title">Client Signature</h5>
                </div>
                <form class="simcy-form signing-form" action="<?=  url('Quote@sign') ; ?>" data-parsley-validate="" method="POST" loader="true">
                    <div class="modal-body">
                        <p>Once the client has signed, any changes made on this quote will erase the client signature and will require a new signature.</p>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="draw-signature-holder"><canvas width="320" height="150" id="draw-signature"></canvas></div>
                                    <input type="hidden" name="signature">
                                    <input type="hidden" name="quoteid" value="<?=  $quote->id ; ?>" required="">
                                    <div class="signature-tools text-center" id="controls">
                                        <div class="signature-tool-item with-picker">
                                            <div><button class="jscolor { valueElement:null,borderRadius:'1px', borderColor:'#e6eaee',value:'1418FF',zIndex:'99999', onFineChange:'modules.color(this)'}"></button></div>
                                        </div>
                                        <div class="signature-tool-item" id="signature-stroke" stroke="5">
                                            <div class="tool-icon tool-stroke"></div>
                                        </div>
                                        <div class="signature-tool-item" id="undo">
                                            <div class="tool-icon tool-undo"></div>
                                        </div>
                                        <div class="signature-tool-item" id="clear">
                                            <div class="tool-icon tool-erase"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <div class="nk-divider divider mt-0 mb-0"></div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form-control-lg" placeholder="Full Name" name="fullname" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button class="btn btn-white btn-dim btn-outline-light" type="button" data-dismiss="modal"><em class="icon ni ni-cross-circle"></em><span>Cancel</span></button>
                        <button class="btn btn-primary sign-document" type="button"><em class="icon ni ni-check-circle-cut"></em><span>Sign Quote</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>

    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
    <script src="<?=  asset('assets/js/pdf.js') ; ?>"></script>

    <script src="<?=  asset('assets/libs/jcanvas/signature.min.js') ; ?>"></script>

    <script type="text/javascript">

        var pdfDocument = "<?=  url('Quote@render', array('quoteid' => $quote->id)) ; ?>";
        PDFJS.workerSrc = "<?=  asset('assets/js/pdf.worker.min.js') ; ?>";

    </script>
    <script src="<?=  asset('assets/js/render.js') ; ?>"></script>
    <script src="<?=  asset('assets/js/sign-documents.js') ; ?>"></script>
</body>

</html>
<?php return;
