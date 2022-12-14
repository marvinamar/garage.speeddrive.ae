<?php global $s_v_data, $title, $user, $jobcard; ?>
<?= view( 'includes/head', $s_v_data ); ?>
<link rel="stylesheet" href="<?=  asset('assets/libs/summernote/summernote-lite.min.css') ; ?>" />

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
                                                <p>You are viewing job card #<?=  $jobcard->id ; ?>.</p>
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
                                                                <li><a href="<?=  url('Jobcards@render', array('jobcardid' => $jobcard->id)) ; ?>" download="Job Card #<?=  $jobcard->id ; ?>.pdf"><em class="icon ni ni-download-cloud"></em><span>Download</span></a></li>
                                                                <li><a href="<?=  url('Jobcards@render', array('jobcardid' => $jobcard->id)) ; ?>" target="_blank"><em class="icon ni ni-printer"></em><span>Print</span></a></li>
                                                                <li><a href="" class="send-via-email" data-url="<?=  url('Jobcards@send') ; ?>" data-id="<?=  $jobcard->id ; ?>" data-subject="Job Card #<?=  $jobcard->id ; ?>"><em class="icon ni ni-mail"></em><span>Send Via Email</span></a></li>
                                                                <li><a class="fetch-display-click" data="jobcardid:<?=  $jobcard->id ; ?>" url="<?=  url('Jobcards@updateview') ; ?>" holder=".update-holder-xl" modal="#update-xl" href=""><em class="icon ni ni-pen"></em><span>Edit Job Card</span></a></li>
                                                                <li class="divider"></li>
                                                                <li><a class="send-to-server-click"  data="jobcardid:<?=  $jobcard->id ; ?>" url="<?=  url('Jobcards@delete') ; ?>" warning-title="Are you sure?" warning-message="This job card will be deleted permanently." warning-button="Yes, delete!" href=""><em class="icon ni ni-trash"></em><span>Delete Job Card</span></a></li>
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

    <!-- app-root @e -->
    <!-- JavaScript -->
    <?= view( 'includes/scripts', $s_v_data ); ?>
    <script src="<?=  asset('assets/libs/summernote/summernote-lite.min.js') ; ?>"></script>
    <script src="<?=  asset('assets/js/pdf.js') ; ?>"></script>

    <script type="text/javascript">

        var pdfDocument = "<?=  url('Jobcards@render', array('jobcardid' => $jobcard->id)) ; ?>";
        PDFJS.workerSrc = "<?=  asset('assets/js/pdf.worker.min.js') ; ?>";

    </script>
    <script src="<?=  asset('assets/js/render.js') ; ?>"></script>
</body>

</html>
<?php return;
