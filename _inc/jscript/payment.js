jQuery(document).on('submit', "#formAddCredits", function(e){
    e.preventDefault();
});

jQuery.payment =
{
    sendCredits: function()
    {
        var dataString = jQuery('#formAddCredits').serialize();
        jQuery('#formAddCredits').find('.waiting').show();
        jQuery.post(ajaxurl, 'action=addCredits&' + dataString, function(result) {
            var data = JSON.parse(result);
            if(data.notice)
            {
                jQuery('#msgAddCredits').empty().append(data.notice).show();
            }
            if(data.result)
            {
                window.location = data.result;
            }
            jQuery('#formAddCredits').find('.waiting').hide();
        })
    },
    
    requestPayment : function(sTitle)
    {
        var payment = this;
        var dialog = jQuery("#dlgRequestPayment").dialog({
            height: 'auto',
            width:'500',
            modal:true,
            title:sTitle,
            open:function(){
                jQuery('#msgRequestPayment').empty().hide(); 
                jQuery('#formRequestPayment')[0].reset();
                payment.loadUserBalance();
            },
            buttons: {
                "Send": function() {
                    payment.sendRequestPayment();
                },
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
        return false;
    },
    
    loadUserBalance: function()
    {
        jQuery.post(ajaxurl, 'action=loadUserBalance', function(result) {
            jQuery('#formRequestPayment .balance').empty().append(result);
        })
        return false;
    },
    
    sendRequestPayment: function()
    {
        jQuery(".ui-dialog").find('button').addClass('ui-state-disabled').attr('disabled', 'true');
        jQuery(".ui-dialog").find('button:last').prev().find('span').empty().append('Processing');
        var dataString = jQuery('#formRequestPayment').serialize();
        jQuery.post(ajaxurl, 'action=requestPayment&' + dataString, function(result) {
            var data = JSON.parse(result);
            if(data.notice)
            {
                jQuery('#msgRequestPayment').empty().append(data.notice).show();
            }
            else if(data.result)
            {
                alert(data.result);
                jQuery("#dlgRequestPayment").dialog( "close" );
                window.location = data.redirect;
            }
            jQuery(".ui-dialog").find('button').removeClass('ui-state-disabled').removeAttr('disabled');
            jQuery(".ui-dialog").find('button:last').prev().find('span').empty().append('Send');
        })
    },
}