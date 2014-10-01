var nonRoundValues = new Array(2,4,6,7,8,9,15,17,18,19,20);
function importPicks(leagueID,t,p)
{
    var data = {
        action: 'userpicks',
        leagueId : leagueID
    };
    jQuery.post(ajaxurl, data, function(result) {
        data = JSON.parse(result);
        if ( !data )
        {
            alert("Sorry, error occured.");
            return;
        }
        jQuery.each(data, function (index, value) {
            var fightID = value.fightID;	
            var fighterElement = '#fighter' + value.winnerID;	
            var methodElement = '#method' + fightID;
            var roundElement = '#round' + fightID;
            var minuteElement = '#minute' + fightID;
            if ( jQuery(fighterElement).length )
            {
                    jQuery(fighterElement).attr( "checked", "checked" );
                    //jQuery(fighterElement).iCheck('check');
                    //enableDetails(fightID);
            }
            if ( jQuery(methodElement).length  )
            {
                    jQuery(methodElement).val(value.methodID);
            }
            if ( jQuery(roundElement).length  )
            {
            jQuery(roundElement).val(value.roundID);
            }
            if ( jQuery(roundElement).length )  //check if element exists
            {
                    jQuery(minuteElement).val(value.minuteID);
            }
            //console.log(value);
        });
    })
}
function pickSelected(leagueID)
{
        var ifAnyBoutChecked = false;
        var cForm = document.getElementById('submitPicksForm');
        for ( v = 0; v < cForm.elements.length; v++ )
        {
                if ( "radio" == cForm.elements[v].type )
                {
                        if ( cForm.elements[v].checked )
                        {
                                ifAnyBoutChecked = true;
                                break;
                        }
                }
        }
        if ( leagueID )
        {
                jQuery('#submitPicksForm input[name="is_league"]').val(1);
        }
        if ( !ifAnyBoutChecked )
        {
                alert('Please enter your picks');
                return false;
        }
        return true;
}
function checkMethod(value,fightID,champFight)
{
        var roundSelectName = "#round" + fightID;
        var minuteSelectName = "#minute" + fightID;
        for (i=0; i < nonRoundValues.length; i++)
        {
                if ( nonRoundValues[i] == value )
                {
                        jQuery(roundSelectName).val(-1);
                        jQuery(minuteSelectName).val(-1);
                        jQuery(roundSelectName).attr('disabled', 'disabled');
                        jQuery(minuteSelectName).attr('disabled', 'disabled');
                        return true;
                }
        }
        jQuery(roundSelectName).removeAttr('disabled');
        jQuery(minuteSelectName).removeAttr('disabled');
        return true;
}
