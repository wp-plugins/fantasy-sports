function showLeagueDetails(leagueID){
    jQuery("#dlgLeagueDetail").dialog({
        width:600,
        height:500,
        resizable:false,
        modal:true,
    });
    var data = {
        action: 'showLeagueDetails',
        leagueId: leagueID
    };
    jQuery.post(ajaxurl, data, function(result) {
        var data = JSON.parse(result);
        jQuery("#dlgLeagueDetail").empty().append(data.html);
        jQuery("#myTab").tabs({ selected: 0 });
        jQuery("#dlgLeagueDetail").dialog({
            title:data.name,
        });
    });
}

function setLeaguePoolID(leagueID, poolID)
{
        jQuery('.leagueID').val(leagueID);
        jQuery('.poolID').val(poolID);
        return true;
}
function updateNewContests()
{
    var data = {
        action: 'updateNewContests'
    };
    jQuery.post(ajaxurl, data, function(result) {
        jQuery("#leagues_list tr:not(:first-child)").remove();
        jQuery("#leagues_list tr:first-child").after(result);
    });
}

function viewPoolFixture(iPoolID, sTitle)
{
    jQuery("#dlgFixture").empty().append("<center>Loading...Please wait!</center>");
    var dialog = jQuery("#dlgFixture").dialog({
        maxHeight: 500,
        width:500,
        minWidth:500,
        modal:true,
        title:sTitle,
        open: function() {
            jQuery('.ui-widget-overlay').addClass('custom-overlay');
        }
    });
    var data = {
        action: 'viewPoolFixture',
        iPoolID: iPoolID
    };
    jQuery.post(ajaxurl, data, function(result) {
        jQuery("#dlgFixture").empty().append(result);
        jQuery("#dlgFixture").dialog({
            buttons: {
                "Close": function() {
                    dialog.dialog( "close" );
                }
            }
        });
    })
}