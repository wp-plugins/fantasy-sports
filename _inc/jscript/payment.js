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
    },
    
    sendRequestPayment: function()
    {
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
        })
    },
    
    accountInfo : function(sTitle)
    {
        var payment = this;
        var dialog = jQuery("#dlgAccountInfo").dialog({
            height: 'auto',
            width:'500',
            modal:true,
            title:sTitle,
            open:function(){
                jQuery('#msgAccountInfo').empty().hide(); 
                jQuery('#formAccountInfo')[0].reset();
            },
            buttons: {
                "Submit": function() {
                    payment.sendAccountInfo();
                },
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
        return false;
    },
    
    sendAccountInfo: function()
    {
        var dataString = jQuery('#formAccountInfo').serialize();
        jQuery.post(ajaxurl, 'action=accountInfo&' + dataString, function(result) {
            var data = JSON.parse(result);
            if(data.notice)
            {
                jQuery('#msgAccountInfo').empty().append(data.notice).show();
            }
            else if(data.result)
            {
                alert(data.result);
                jQuery("#dlgAccountInfo").dialog( "close" );
                location.reload();
            }
        })
    }
}