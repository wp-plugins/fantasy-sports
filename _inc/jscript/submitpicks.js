var nonRoundValues = new Array(2,4,6,7,8,9,15,17,18,19,20);
function importPicks(picks, methods, rounds, minutes)
{
    picks = picks.split(",");
    methods =  methods.split(",");
    rounds =  rounds.split(",");
    minutes =  minutes.split(",");
    jQuery(".fightID").each(function(){
        var index = picks.indexOf(jQuery(this).val());
        if(index > -1)
        {
            var fightID = jQuery(this).attr("data-fightid");
            jQuery(this).attr("checked", "checked");
            jQuery("#method" + fightID).val(methods[index]);
            jQuery("#round" + fightID).val(rounds[index]);
            jQuery("#minute" + fightID).val(minutes[index]);
            checkMethod(methods[index], fightID);
        }
    });
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
                alert(wpfs['input_picks']);
                return false;
        }
        return true;
}
function checkMethod(value,fightID)
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

jQuery(window).load(function(){
    jQuery(".method").each(function(){
        checkMethod(jQuery(this).val(), jQuery(this).attr("data-id"));
    })
})