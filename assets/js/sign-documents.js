/*!
 * Asilify
 * Version 1.0 - built Sat, Oct 6th 2018, 01:12 pm
 * https://dottedcraft.com
 * Dotted Craft- <hello@dottedcraft.com>
 * Private License
 */

 (function(NioApp, $) {
"use strict";

 var modules = {};

/*
 *  Initialize signature drawing
 */
$('#sign, #signout').on('shown.bs.modal', function (e) {
		initDrawing();
		modules.original = $('#draw-signature').getCanvasImage("image/png");
});


/*
 *  Validate form and signature
 */
$("body").on("click", ".sign-document", function(event){
	event.preventDefault();

    $(".signing-form").parsley().validate();
    if (($(".signing-form").parsley().isValid())) {

        var signature =  $('#draw-signature').getCanvasImage("image/png");

        if (modules.original === signature) {
            notify("Hmmm!", "Please sign to continue.", "warning", "Okay")
            return false;
        }

        $("input[name=signature]").val(signature);

        $(".signing-form").submit();

    }

});

 })(NioApp, jQuery);
