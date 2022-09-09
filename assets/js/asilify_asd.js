(function(NioApp, $) {
"use strict";

var modules = {};

/*
* Custom tabs for settings page
*/
$("body").on("click", ".custom-tab-trigger", function(event){
    event.preventDefault();

    $(".custom-tab-trigger").removeClass("active");
    $(this).addClass("active");

    $(".custom-tab-body").hide();
    $($(this).attr("href")).show();

});

/*
* Initialize international phone
*/
NioApp.initPhoneInput = function() {
    $(".phone-input").intlTelInput({
        autoPlaceholder: "polite",
        initialCountry: "ke",
        placeholderNumberType: "FIXED_LINE",
        utilsScript: "https://app.asilify.com/assets/js/utils.js"
    });
}
NioApp.initPhoneInput();

/*
* Get international phone 
*/
$("body").on("change", ".phone-input", function() {
    $(this).closest(".intl-tel-input").siblings(".hidden-phone").val($(this).intlTelInput("getNumber"));
});

/*
* Detect phone number change
*/
$("body").on("blur", ".phone-input", function() {
    if ($.trim($(this).val())) {
        if (!$(this).intlTelInput("isValidNumber")) {
            $(this).val('');
            toastr.error("Invalid phone number.", "Oops!", {
                timeOut: null,
                closeButton: true
            });
        } else {
            toastr.clear();
        }
    }
});

/*
* Detect Unsaved changes and warn
*/
$("body").on("change", "input, textarea, select", function(){
    window.onbeforeunload = function() {
        return true;
    };
});

/*
* Create Task
*/
$("body").on("click", ".create-task", function(event){
    event.preventDefault();

    if ($(this).attr("data-type") == "team") {
        $("#createtask").find("input[name=member]").val($(this).attr("data-id"));
    }else if ($(this).attr("data-type") == "project") {
        $("#createtask").find("input[name=project]").val($(this).attr("data-id"));
    }

    $("#createtask").modal("show");

});

/*
* Create jobcard
*/
$("body").on("click", ".create-jobcard", function(event){
    event.preventDefault();

    $("#createjobcard").find("input[name=project]").val($(this).attr("data-id"));
    $("#createjobcard").modal("show");

});


/*
* Add an expense
*/
$("body").on("click", ".add-expense", function(event){
    event.preventDefault();

    $("#addexpense").find("input[name=project]").val($(this).attr("data-id"));
    $("#addexpense").modal("show");

});


/*
* Create Quote
*/
$("body").on("click", ".create-quote", function(event){
    event.preventDefault();

    $("#createquote").find("input[name=project]").val($(this).attr("data-id"));
    $("#createquote").modal("show");

});


/*
* Convert Quote to Invoice
*/
$("body").on("click", ".convert-quote", function(event){
    event.preventDefault();

    $("#convertquote").find("input[name=quote]").val($(this).attr("data-id"));
    $("#convertquote").modal("show");

});


/*
* Create Invoice
*/
$("body").on("click", ".create-invoice", function(event){
    event.preventDefault();

    $("#createinvoice").find("input[name=project]").val($(this).attr("data-id"));
    $("#createinvoice").modal("show");

});


/*
* Add Invoice payment
*/
$("body").on("click", ".add-payment", function(event){
    event.preventDefault();

    $("#invoicepayment").find("input[name=invoice]").val($(this).attr("data-id"));
    $("#invoicepayment").modal("show");

});


/*
* Add a line on invoice / quote
*/
$("body").on("click", ".add-item", function(event){
    event.preventDefault();

    var holder = $(this).closest(".modal").find(".item-lines");
    var line = ' <div class="row gy-4"> <div class="col-sm-4"> <div class="form-group"> <label class="form-label">Item Description</label> <div class="form-control-wrap"> <input type="text" class="form-control form-control-lg" placeholder="Item Description" name="item[]" required=""><input type="hidden" name="itemid[]" value="" > </div></div></div><div class="col-sm-1"> <div class="form-group"> <label class="form-label">Qty</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-quantity" value="1" min="1" placeholder="Quantity" name="quantity[]" required=""> </div></div></div><div class="col-sm-2"> <div class="form-group"> <label class="form-label">Unit Cost ( '+currency+' )</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-cost" placeholder="Unit Cost" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="cost[]" value="0.00" step="0.01" required=""> </div></div></div><div class="col-sm-2"> <div class="form-group"> <label class="form-label">Tax (%)</label> <div class="form-control-wrap hide-arrows"> <input type="number" class="form-control form-control-lg line-tax" placeholder="Tax (%)" min="0" name="tax[]"> </div></div></div><div class="col-sm-2"> <div class="form-group"> <label class="form-label">Total ( '+currency+' )</label> <div class="form-control-wrap"> <input type="number" class="form-control form-control-lg line-total" placeholder="Amount" data-parsley-pattern="[0-9]*(\.?[0-9]{2}$)" name="total[]" value="0.00" step="0.01" required="" readonly=""> </div></div></div><div class="col-sm-1"> <div class="form-group"> <div class="form-control-wrap"> <a href="#" class="btn btn-icon btn-lg btn-round btn-dim btn-outline-danger mt-gs remove-line" data-toggle="tooltip" title="Remove Item"><em class="icon ni ni-cross-circle-fill"></em></a> </div></div></div></div>';

    holder.append(line);
    $('[data-toggle="tooltip"]').tooltip();

});


/*
* Remove a line from invoice / quote
*/
$("body").on("click", ".remove-line", function(event){
    event.preventDefault();

    var items = $(this).closest(".item-lines");

    $(this).closest(".row").remove();
    $(".tooltip").remove();

    NioApp.calculatetotal(items);

});


/*
* Calculate line totals on change
*/
$("body").on("keyup", ".line-quantity, .line-cost,  .line-tax", function(event){

    NioApp.linetotal($(this).closest(".row"));

});


/*
* Calculate totals on tax change
*/
$("body").on("keyup", ".total-vat", function(event){

    items = $(this).closest(".modal").find(".item-lines");

    NioApp.calculatetotal(items);

});


/*
* Calculate line total
*/
NioApp.linetotal = function(line) {

    var quantity = line.find(".line-quantity").val();
    var cost = line.find(".line-cost").val();
    var tax = line.find(".line-tax").val();
    var totalHolder = line.find(".line-total");

    if (quantity === "" || cost === "") {
        totalHolder.val("0.00");
        NioApp.calculatetotal(line.closest(".item-lines"));
        return;
    }

    if (quantity < 0 || cost < 0) {
        totalHolder.val("0.00");
        NioApp.calculatetotal(line.closest(".item-lines"));
        return;
    }

    total = parseFloat(quantity) * parseFloat(cost);

    totalHolder.attr("sub-total", total.toFixed(2));

    if (parseFloat(tax) > 0) {
        totaltax = (parseFloat(tax) / 100) * total;
        withtaxtotal = ((parseFloat(tax) + 100) / 100) * total;
    }else{
        totaltax = 0;
        withtaxtotal = total;
    }

    totalHolder.attr("tax-total", totaltax.toFixed(2));

    totalHolder.val(withtaxtotal.toFixed(2));
    NioApp.calculatetotal(line.closest(".item-lines"));
    return;

}


/*
* Initiate totals Calculate
*/
NioApp.initTotals = function(instance) {

    items = $("."+instance).closest(".modal").find(".item-lines");

    NioApp.calculatetotal(items);

}


/*
* Calculate line total
*/
NioApp.calculatetotal = function(items) {

    var grandtotal = taxtotal = subtotal = 0;

    items.children(".row").each(function () {

        rowsubtotal = Number($(this).find("input.line-total").attr("sub-total"));
        if (isNaN(rowsubtotal)) { rowsubtotal = 0; }

        rowtaxtotal = Number($(this).find("input.line-total").attr("tax-total"));
        if (isNaN(rowtaxtotal)) { rowtaxtotal = 0; }

        grandtotal = grandtotal + Number($(this).find("input.line-total").val());
        subtotal = subtotal + rowsubtotal;
        taxtotal = taxtotal + rowtaxtotal;

    });

    items.siblings(".item-totals").find(".sub-total").text(currency+" "+subtotal.toFixed(2));
    items.siblings(".item-totals").find(".tax-total").text(currency+" "+taxtotal.toFixed(2));
    items.siblings(".item-totals").find(".grand-total").text(currency+" "+grandtotal.toFixed(2));
    return;

}


/*
* Import froma jobcard
*/
$("body").on("click", ".select-from-jobcard", function(event){
    event.preventDefault();

    $(".jobcard-select-form").attr("action", $(this).attr("data-url"));
    $(".jobcard-select-form").attr("modal", $(this).attr("modal"));
    $(".jobcard-select-form").attr("holder", $(this).attr("holder"));
    $("#jobcards-select").modal("show");

});


/*
* Select a jobcard
*/
$("body").on("click", ".select-jobcard", function(event){
    event.preventDefault();
    showLoader();

    $("#jobcards-select").modal("hide");
    var posting = $.post($(".jobcard-select-form").attr("action"), { "jobcardid": $(this).attr("data-id") });
    posting.done(function (response) {
        hideLoader();
        $($(".jobcard-select-form").attr("holder")).html(response);
        $($(".jobcard-select-form").attr("modal")).modal("show");
    });

});


/*
* Add more stock
*/
$("body").on("click", ".add-stock", function(event){
    event.preventDefault();

    $("#addstock").find("input[name=inventoryid]").val($(this).attr("data-id"));
    $("#addstock").modal("show");

});


/*
* Go Back
*/
$("body").on("click", ".go-back", function(event){
    event.preventDefault();

    window.history.back();

});

/*
* Send Via Email
*/
$("body").on("click", ".send-via-email", function(event){
    event.preventDefault();

    var url = $(this).attr("data-url");
    var itemid = $(this).attr("data-id");
    var subject = $(this).attr("data-subject");
    var email = $(this).attr("data-email");

    $("#sendviaemail").find("form").attr("action", url);
    $("#sendviaemail").find("input[name=itemid]").val(itemid);
    $("#sendviaemail").find("input[name=subject]").val(subject);
    if (email !== undefined) {
        $("#sendviaemail").find("input[name=email]").val(email);
    }

    $("#sendviaemail").modal("show");

});

/*
* Send Via SMS
*/
$("body").on("click", ".send-via-sms", function(event){
    event.preventDefault();

    var url = $(this).attr("data-url");
    var itemid = $(this).attr("data-id");
    var message = $(this).attr("data-message");
    var phonenumber = $(this).attr("data-phonenumber");

    $("#sendviasms").find("form").attr("action", url);
    $("#sendviasms").find("input[name=itemid]").val(itemid);
    $("#sendviasms").find("textarea[name=message]").val(message);
    if (phonenumber !== undefined) {
        $("#sendviasms").find("input[name=phonenumber], .phone-input").val(phonenumber);
    }

    $("#sendviasms").modal("show");

});

/*
* Send Via whatsapp
*/
$("body").on("click", ".send-via-whatsapp", function(event){
    event.preventDefault();

    var message = $(this).attr("data-message");

    $("#sendviawhatsapp").find("textarea[name=text]").val(message);

    $("#sendviawhatsapp").modal("show");

});

/*
* Send SMS
*/
$("body").on("click", ".send-sms", function(event){
    event.preventDefault();

    var phonenumber = $(this).attr("data-phonenumber");
    var name = $(this).attr("data-name");

    $("#sendsms").find("input[name=phonenumber]").val(phonenumber);
    $("#sendsms").find("input[name=name]").val(name);

    $("#sendsms").modal("show");

});

/*
* Team Report
*/
$("body").on("click", ".team-report", function(event){
    event.preventDefault();

    var staffid = $(this).attr("data-id");
    var name = $(this).attr("data-name");

    $("#teamreport").find(".modal-title").html('Report: <strong class="fw-bold text-primary">'+name+'</strong>');
    $("#teamreport").find("input[name=staff]").val(staffid);

    $("#teamreport").modal("show");

});

/*
* Supplier Report
*/
$("body").on("click", ".supplier-report", function(event){
    event.preventDefault();

    var supplierid = $(this).attr("data-id");
    var name = $(this).attr("data-name");

    $("#supplierreport").find(".modal-title").html('Report: <strong class="fw-bold text-primary">'+name+'</strong>');
    $("#supplierreport").find("input[name=supplier]").val(supplierid);

    $("#supplierreport").modal("show");

});

/*
* Select a plan
*/
$("body").on("click", ".select-plan", function(event){
    event.preventDefault();

    var subscription_plan = $("input[name=subscription_plan]:checked").val();
    var plancycle = $("input[name=subscription_plan]:checked").attr("id");
    var amount = 1999;

    if (plancycle === "monthly") {
        amount = 1999;
        planname = "Premium Monthly";
    }else if (plancycle === "biannually") {
        amount = 9995;
        planname = "Premium Biannually";
    }else if (plancycle === "annually") {
        amount = 19990;
        planname = "Premium Annually";
    }

    swal({
        title: "Confirm Subscription",
        text: "Your are subscribing to "+planname+" and you will be charged KSh"+amount+ " "+plancycle,
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#007bff",
        confirmButtonText: "Continue",
        closeOnConfirm: true
    }, function () {
        $("#subscription-change").modal("show");
        NioApp.makePayment(amount, subscription_plan);
    });

});

/*
* When payment plan is changed
*/
$("body").on("change", "input[name=subscription_plan].sp-package-choose", function(event){
    event.preventDefault();
    
    var subscription_plan = $("input[name=subscription_plan].sp-package-choose:checked");
    var plan = subscription_plan.val();
    var price = subscription_plan.attr("data-price");
    var initial = subscription_plan.attr("initial");
    var company = subscription_plan.attr("data-company");
    
    $(".subscription-price").text("Ksh"+price);
    $("input[name=amount].subscription-amount").val(price);
    $(".payment-reference").text("s."+company+"."+initial);
    $("input[name=AccountReference]").val("s."+company+"."+initial);

});


/*
* Select campagn receivers
*/
$("body").on("change", "select[name=sendto]", function(event){

    var sendto = $(this).val();

    if (sendto === "" || sendto === "clients" || sendto === "members") {
        $(".campaign-sendto").hide();
        $(".campaign-sendto").find("select, input").attr("required", false);
    }else if (sendto === "selectedclients") {
        $(".campaign-sendto").hide();
        $(".campaign-sendto").find("select, input").attr("required", false);

        $(".campaign-sendto[data-type=clients]").find("select").attr("required", true);
        $(".campaign-sendto[data-type=clients]").show();
    }else if (sendto === "selectedmembers") {
        $(".campaign-sendto").hide();
        $(".campaign-sendto").find("select, input").attr("required", false);

        $(".campaign-sendto[data-type=members]").find("select").attr("required", true);
        $(".campaign-sendto[data-type=members]").show();
    }else if (sendto === "enternumber") {
        $(".campaign-sendto").hide();
        $(".campaign-sendto").find("select, input").attr("required", false);

        $(".campaign-sendto[data-type=manually]").find("input[type=text]").attr("required", true);
        $(".campaign-sendto[data-type=manually]").show();
    }else if (sendto === "filterbycar") {
        $(".campaign-sendto").hide();
        $(".campaign-sendto").find("select, input").attr("required", false);

        $(".campaign-sendto[data-type=filterbycar]").find("select[name=make]").attr("required", true);
        $(".campaign-sendto[data-type=filterbycar]").show();
    }

});

/*
* When user submits top up form
*/
$("body").on("submit", ".sms-topup-form", function(event){
    event.preventDefault();

    $(this).parsley().validate();
    if (($(this).parsley().isValid())) {
        NioApp.makePayment($("input[name=creditsamount]").val());
    }

})

/*
* payment option
*/
$("body").on("change", "input[name=paymentmethod]", function(event){
    event.preventDefault();
    
    var option = $("input[name=paymentmethod]:checked").val();
    
    if(option === "standard"){
        $(".standard-option").show();
        $(".express-option").hide();
    }else{
        $(".express-option").show();
        $(".standard-option").hide();
    }

});

/*
* job card form submission
*/
$("body").on("submit", ".jobcard-form", function(event){
    event.preventDefault();
    
    if($(this).hasClass("select-input")){
        var projectid = $(this).find("select[name=project]").val();
    }else{
        var projectid = $(this).find("input[name=project]").val();
    }

    $(this).parsley().validate();
    if (($(this).parsley().isValid())) {
        server({ 
            url: $(this).attr("action"),
            data: {
                "project": projectid,
                "jobcardid": $(this).find("input[name=jobcardid]").val(),
                "work_requested": btoa($(this).find("textarea[name=work_requested]").val()),
                "mechanics_report": btoa($(this).find("textarea[name=mechanics_report]").val())
            },
            loader: true
        });
    }

});

/*
* When booking part with input is checked
*/
$("body").on("change", "input[has-input=Yes]", function() {
    var parent = $(this).closest(".part-item-holder");
    if ($(this).prop("checked")) {
        parent.find(".part-input-field").show();
    }else{
        parent.find(".part-input-field").hide();
    }

});

/*
* When booking part has a input field
*/
$("body").on("change", "input[name=has_input]", function() {
    var parentModal = $(this).closest(".modal");
    if ($(this).prop("checked")) {
        parentModal.find(".input-name").show();
        parentModal.find("input[name=input_name]").attr("required", true);
    }else{
        parentModal.find(".input-name").hide();
        parentModal.find("input[name=input_name]").attr("required", false);
    }

});

/*
* Car covered by insurance
*/
$("body").on("change", "input[name=covered]", function() {
    var parentModal = $(this).closest(".modal");
    if ($(this).prop("checked")) {
        parentModal.find(".covered").show();
        parentModal.find("select[name=insurance]").attr("required", true);
    }else{
        parentModal.find(".covered").hide();
        parentModal.find("select[name=insurance]").attr("required", false);
    }

});

/*
* Car brought in by someone else
*/
$("body").on("change", "input[name=delivered_by]", function() {
    var parentModal = $(this).closest(".modal");
    if ($(this).prop("checked")) {
        parentModal.find(".deliveredby-inputs").hide();
        parentModal.find("input[name=deliveredby_fullname], input[name=deliveredby_phonenumber], .phone-input.deliveredby").attr("required", false);
    }else{
        parentModal.find(".deliveredby-inputs").show();
        parentModal.find("input[name=deliveredby_fullname], input[name=deliveredby_phonenumber], .phone-input.deliveredby").attr("required", true);
    }

});

/*
* Create client on car booking
*/
$("body").on("change", "select.project-client-select", function() {
    var parentModal = $(this).closest(".modal");
    if ($(this).val() === "create") {
        parentModal.find(".new-client-inputs").show();
        parentModal.find("input[name=fullname], input[name=phonenumber], .phone-input.client").attr("required", true);
    }else{
        parentModal.find(".new-client-inputs").hide();
        parentModal.find("input[name=fullname], input[name=phonenumber], .phone-input.client").attr("required", false);
    }
    
});


/*
* Fuel level slider
*/
$("body").on("input",".fuel-slider", function(){
    var parentModal = $(this).closest(".modal");
    parentModal.find(".fuel-level").attr("transform", "rotate("+$(this).val()+", 635, -139)");
});


/*
* Select Dent or scratches
*/
$("body").on("click",".highlight-color", function(){
    var color = $(this).attr("color");
    modules.color(color);
});

/*
* Change expense status
*/
$("body").on("change",".expense-status", function(){
    var parentModal = $(this).closest(".modal-section");
    if ($(this).val() == "Delivered" || $(this).val() == "To Order") {
        parentModal.find(".expense-delivery").hide();
        parentModal.find(".expense-delivery").find("input[type=date]").attr("required", false);
    }else{
        parentModal.find(".expense-delivery").show();
        parentModal.find(".expense-delivery").find("input[type=date]").attr("required", true);
    }
});

/*
* Change expense payment status
*/
$("body").on("change",".expense-paid", function(){
    var parentModal = $(this).closest(".modal-section");
    if ($(this).prop("checked")) {
        parentModal.find(".expense-payment").hide();
        parentModal.find(".expense-payment").find("input[type=date]").attr("required", false);
    }else{
        parentModal.find(".expense-payment").show();
        parentModal.find(".expense-payment").find("input[type=date]").attr("required", true);
    }
});



/*
* Create supplier on expense addition
*/
$("body").on("change", "select.supplier-select", function() {
    var parentModal = $(this).closest(".modal-section");
    if ($(this).val() === "create") {
        parentModal.find(".new-supplier-input").show();
        parentModal.find("input[name=suppliername]").attr("required", true);
    }else{
        parentModal.find(".new-supplier-input").hide();
        parentModal.find("input[name=suppliername]").attr("required", false);
    }
    
});



/*
* Expense source select
*/
$("body").on("change", "select.source-select", function() {
    var parentModal = $(this).closest(".modal-section");
    if ($(this).val() === "Suppliers") {
        parentModal.find(".inventory-source").hide();
        parentModal.find(".external-source").show();
        parentModal.find(".inventory-source").find("input, select").attr("required", false);
        parentModal.find(".external-source").find("input, select").attr("required", true);
    }else{
        parentModal.find(".external-source").hide();
        parentModal.find(".inventory-source").show();
        parentModal.find(".inventory-source").find("input, select").attr("required", true);
        parentModal.find(".external-source").find("input, select").attr("required", false);
    }
    
});


/*
* Inventory item select
*/
$("body").on("change", "select.inventory-select", function() {
    var parentModal = $(this).closest(".modal-section");

    units = $("option:selected", this).attr("units");
    quantity = $("option:selected", this).attr("quantity");

    parentModal.find(".inventory-source").find(".overline-title.units").text(units);
    parentModal.find(".inventory-source").find("input.expense-consumed").attr("max", quantity);
    
    NioApp.calculateCost(parentModal);
    
});


/*
* Calculate line totals on change
*/
$("body").on("keyup", "input.expense-consumed", function(event){
    var parentModal = $(this).closest(".modal-section");
    
    NioApp.calculateCost(parentModal);

});


/*
* Calculate cost
*/
NioApp.calculateCost = function(parentModal){

    var cost = parentModal.find("select.inventory-select").find("option:selected").attr("cost");

    var totalcost = cost * parentModal.find("input.expense-consumed").val();
        
    const newLocal = "input.expense-total-amount";
    var tamount = parentModal.find(newLocal).val();
   
    if( tamount != totalcost){
        totalcost = tamount;
    }
    
    parentModal.find("input.expense-total-amount").val(totalcost.toFixed(2));

};


/*
* Initialize Wizard
*/
 NioApp.asilifywizard = function(finish, diagram){

    delete window.$wizard;
    var $wizard = $(".nk-wizard").show();
    $wizard.steps({
        headerTag: ".nk-wizard-head",
        bodyTag: ".nk-wizard-content",
        labels: {
            finish: finish,
            next: "Next",
            previous: "Prev",
            loading: "Loading ..."
        },
        onStepChanging: function (event, currentIndex, newIndex)
        {
            // Allways allow previous action even if the current form is not valid!
            if (currentIndex > newIndex)
            {
                return true;
            }
            // Needed in some cases if the user went back (clean up)
            if (currentIndex < newIndex)
            {
                // To remove error styles
                $wizard.find(".body:eq(" + newIndex + ") label.error").remove();
                $wizard.find(".body:eq(" + newIndex + ") .error").removeClass("error");
            }

            $wizard.validate().settings.ignore = ":disabled,:hidden";
            return $wizard.valid();
        },
        onStepChanged: function (event, currentIndex)
        {
            if (currentIndex == 2 && diagram == "Enabled") {
                if (window.cardiagram === undefined){
                    NioApp.initCarDiagram();
                }else{
                    initMarking();
                }
                
            }
            if (currentIndex == 3 && diagram == "Enabled") {
                window.cardiagram = false;
                $("input[name=car_diagram]").val($('#car-diagram').getCanvasImage("image/png"));
            }
        },
        onFinishing: function (event, currentIndex)
        {
            $wizard.validate().settings.ignore = ":disabled";
            return $wizard.valid();
        },
        onFinished: function (event, currentIndex){ $wizard.submit(); }
        
    }).validate({
        errorElement: "span",
        errorClass: "invalid",
        errorPlacement: function(error, element) {
            error.appendTo( element.parent() );
        }
    });
    
}



/*
* Remove bulk imported section
*/
$("body").on("click", ".remove-bulk-imported", function(event) {

    event.preventDefault();
    $(this).closest(".modal-section").remove();

});

/*
* Remove a stacked input
*/
$("body").on("click", ".remove-stack", function(event) {

    event.preventDefault();
    $(this).closest(".stacked").remove();

});


/*
* Add Stacked Input
*/
$("body").on("click", ".add-stack", function(event) {

    event.preventDefault();
    var stack = $(this).closest(".asilify-stack");
    var name = $(this).attr("data-name");
    var placeholder = $(this).attr("data-placeholder");

    stack.find(".stacked-inputs").append('<div class="form-control-wrap stacked"><a href="" class="btn btn-round btn-sm btn-icon btn-dim btn-danger remove-stack"><em class="icon ni ni-trash"></em></a><input type="text" class="form-control form-control-lg" placeholder="'+placeholder+'" name="'+name+'"></div>');

});


/*
* Add Stacked Input
*/
$("body").on("change", ".project-make-select", function(event) {

    var make = $(this).val();
    var form = $(this).closest("form");
    var modelsurl = form.attr("models-url");

    if (make > 0) {

        showLoader();
        var posting = $.post(modelsurl, { "makeid": make });
        posting.done(function (response) {
            hideLoader();
            $(".project-model-select").empty().trigger("change");
            $(".project-model-select").select2({ data: response });
        });

    }

});


/*
* Switching between dents and scratch marker
*/
$("body").on("click", ".dent-scratch-color", function(event) {

    $("body").find(".dent-scratch-color").removeClass("active");
    $(this).addClass("active");

    if(modules.diagramcolor !== undefined){
        modules.diagramcolor($(this).attr("color-code"));
    }

    var selectedColor = $(this).attr("color-label");

    $("body").find(".selected-label").hide();
    $("body").find(selectedColor).show();

});


/*
 * Re-order parts
 */
 NioApp.reorderParts = function(){
 	$(".part-order-item").each(function(i) {
 		var i = i + 1;
 		$(this).find(".index").text(i);
 		$(this).find("input").val(i);
 	});

 	$(".parts-order-form").submit();
 }


/*
 * Init car diagram
 */
 NioApp.initCarDiagram = function(first = false){

        let canvas = document.getElementById("car-diagram");
        let ctx = canvas.getContext("2d");

        var width = $(".car-diagram-holder").width();
        var height = parseInt(width / 2);
        $("#car-diagram").attr("width", width).width(width);
        $("#car-diagram").attr("height", height).height(height);

        const image = new Image();
        image.src = carsketch;
        image.onload = function() {
            ctx.drawImage(image, 0, 0, width, height);
            initMarking();
        }
        if (!first) {
            window.cardiagram = true;
        }
        

 }

 $('#update-project').on("scroll", function() {      
    if (window.cardiagram !== undefined && window.cardiagram) {
        initMarking();
    }
});

 })(NioApp, jQuery);



