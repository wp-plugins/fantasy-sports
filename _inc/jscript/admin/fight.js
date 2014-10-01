jQuery.fight =
{
    addFight : function(oObj)
    {
        var fightItem = jQuery(oObj).parents('.fight_container');
        var cloneItem = fightItem.clone();
        cloneItem.find('select').val('');
        cloneItem.find('input[type=text]').val('');
        cloneItem.find('input[type=checkbox]').removeAttr('checked');
        cloneItem.find('input[data-name=fightID]').val('');
        fightItem.after(cloneItem);
        this.fixFightIndexs();
        return false;
    },
    
    removeFight : function(oObj)
    {
        if(confirm('Are you sure?'))
        {
            jQuery(oObj).parents('.fight_container').remove();
            this.fixFightIndexs();
        }
        return false;
    },
    
    fixFightIndexs: function(){
        var index = 0;
        jQuery('.fight_container').each(function(){
            index++;
            jQuery(this).find('.fight_number_title').empty().append('*Fixture ' + index);
            jQuery(this).find('.fight').val(index);
            
            //parse index for fight data
            jQuery(this).find('select').each(function(){
                jQuery(this).attr('name', 'val[' + jQuery(this).attr('data-name') + '][' + index + ']')
            })
            jQuery(this).find('input:not(.fight)').each(function(){
                jQuery(this).attr('name', 'val[' + jQuery(this).attr('data-name') + '][' + index + ']')
            })
        })
        if(jQuery('.fight_container').length == 1)
        {
            jQuery('.fight_container .fight_remove').hide();
        }
        else 
        {
            jQuery('.fight_container .fight_remove').show();
        }
    },
    
    displayType: function(){
        var sport = jQuery('#poolSport').val();
        if(sport == 'MMA' || sport == "BOXING")
        {
            jQuery('.for_fighter').show();
            jQuery('.for_team').hide();
            jQuery('select.for_fighter').removeAttr('disabled');
            jQuery('select.for_team').attr('disabled', 'true');
        }
        else
        {
            jQuery('.for_fighter').hide();
            jQuery('.for_team').show();
            jQuery('select.for_team').removeAttr('disabled');
            jQuery('select.for_fighter').attr('disabled', 'true');
        }
        return false;
    },
    
    loadOrgsBySport: function(sel){
        var sport = jQuery('#poolSport').val();
        var sel = jQuery('#selOrgs').val();
        var data = {
            action: 'loadCbOrgs',
            sport: sport,
            sel: sel
        };
        jQuery.post(ajaxurl, data, function(result) {
            jQuery('#poolOrgs').empty().append(result);
            jQuery('#selOrgs').val('');
            jQuery.fight.loadFightersOrTeams();
	});
    },
    
    loadFightersOrTeams: function(){
        var sport = jQuery('#poolSport').val();
        var orgs = jQuery('#poolOrgs').val();
        if(sport == "MMA" || sport == "BOXING")
        {
            var data = {
                action: 'loadCbFighters',
                orgsID: orgs,
            };
            jQuery.post(ajaxurl, data, function(result){
                jQuery('.cbfighter').empty().append(result);
                jQuery('.cbfighter').each(function(){
                    jQuery(this).val(jQuery(this).attr('data-sel'));
                });
            })
        }
        else
        {
            var data = {
                action: 'loadCbTeams',
                orgsID: orgs,
            };
            jQuery.post(ajaxurl, data, function(result){
                jQuery('.cbteam').empty().append(result);
                jQuery('.cbteam').each(function(){
                    jQuery(this).val(jQuery(this).attr('data-sel'));
                });
            })
        }
    },
    
    viewResult: function(iPoolID, sTitle){
        jQuery("#resultDialog").empty().append("<center>Loading...Please wait!</center>");
        var dialog = jQuery("#resultDialog").dialog({
            maxHeight: 500,
            width:800,
            minWidth:600,
            modal:true,
            title:sTitle,
            open: function() {
                jQuery('.ui-widget-overlay').addClass('custom-overlay');
            }
        });
        
        var data = {
            action: 'viewResult',
            iPoolID: iPoolID,
        };
        jQuery.post(ajaxurl, data, function(result){
            jQuery("#resultDialog").empty().append(result);
            jQuery("#resultDialog").dialog({
                buttons: {
                    "Update": function() {
                        jQuery.fight.updateResult();
                    },
                    "Close": function() {
                        dialog.dialog( "close" );
                    }
                }
            });
        })
    },
    
    updateResult: function(){
        var data = 'action=updateResult&' + jQuery('#formResult').serialize();
        jQuery.post(ajaxurl, data, function(result){
            alert(result);
            jQuery("#resultDialog").dialog('close');
        })
    },
    
    updatePoolStatus: function(iPoolID, oObj, curValue){
        if(confirm('Are you sure? This is not reversible'))
        {
            var data = {
                action: 'updatePoolComplete',
                iPoolID: iPoolID,
                status: jQuery(oObj).val(),
            };
            jQuery.post(ajaxurl, data, function(result){
                var data = JSON.parse(result);
                if(data.notice)
                {
                    alert(data.notice);
                    jQuery(oObj).val(curValue);
                }
                else
                {
                    alert(data.result);
                    if(jQuery(oObj).val().toLowerCase() == 'complete')
                    {
                        jQuery(oObj).attr('disabled', true);
                        jQuery(oObj).parents('tr').find('.column-result').empty();
                        jQuery(oObj).parents('tr').find('.column-edit').empty();
                    }
                }
            })
        }
        else 
        {
            jQuery(oObj).val(curValue);
        }
    }
}