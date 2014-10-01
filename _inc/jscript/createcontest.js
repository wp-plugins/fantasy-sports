var lastPID = "";
var activeID = new Array();
jQuery(window).load(function(){
    jQuery('.sportRadio:enabled:first').trigger('click');
    setOptions(jQuery('#typeRadios7').val());
    jQuery(document).on('click', '.radio input', function(event){
        setOptions(this.value);
    });
})

function setOptions(matchWith)
{
    if ( ! isNaN(matchWith) )
    {
            return true;
    }
    switch ( matchWith ) 
    {
            case "head2head":
            {
                    jQuery('#leagueDiv').hide();
            }break;
            case "league":
            {
                    jQuery('#leagueDiv').show();
            }break;
            case "winnertakeall":
            case "top3":
            case "public":
            case "private":
            case "on":
            break;
    }
    resumPrize();
}

function validateContest()
{
	// Need to ensure at least one fixture was checked
	// Need to uncheck other fixutures from other ID
	var oneChecked = false;
	var fixtureList = fixtures.games[lastPID];
	for (a = 0; a < fixtureList.length; a++)
	{
		var fixture = fixtureList[a];
		if(jQuery('#fixture_'+lastPID+"_" + fixture.fightID).is(':checked'))
		{
			oneChecked = true;
			break;
		}
	}
	if ( oneChecked )
	{
		for (var i = 0; i < activeID.length; i++) 
		{
			if ( lastPID == activeID[i] )
			{
				continue;
			}
			else
			{
				var fixtureList = fixtures.games[activeID[i]];
				for (b = 0; b < fixtureList.length; b++)
				{
					var fixture = fixtureList[b];
					jQuery('#fixture_'+activeID[i]+"_" + fixture.fightID).iCheck('uncheck');	
				}	
			}
		}
	}
	else
	{
		alert("Please select at least one fixture to be part of your contest");
	}
	return  oneChecked;
} 

function resumPrize()
{
    var poolID = jQuery('#poolDates').val();
    var size = jQuery('#leagueSize').val();
    var entry_fee = jQuery('#entry_fee').val();
    var structure = jQuery('input:radio[name=structure]:checked').val();
    var type = jQuery('input:radio[name=type]:checked').val();
    var data = 'poolID=' + poolID + '&type=' + type + '&structure=' + structure + '&size=' + size + '&entry_fee=' + entry_fee;
    
    jQuery.post(ajaxurl, "action=calculatePrizes&" + data, function(result) {
        jQuery("#prizesum").empty().append(result);	
    })
}

jQuery(document).on('click', '.sportRadio', function(){
    if(!jQuery(this).is('checked'))
    {
        var id = jQuery(this).val();
        var data = {
            action: 'loadPoolsByOrg',
            orgID : id
        };
        jQuery.post(ajaxurl, data, function(result) {
            result = JSON.parse(result);
            jQuery('#poolDates').empty().append(result.resultPools);
            if(result.sport == 'MMA')
            {
                jQuery('.minutes').show();
            }
            else
            {
                jQuery('.minutes').hide();
            }
            loadFights();
        })
    }
})

function loadFights()
{
    var id = jQuery('#poolDates').val();
    var data = {
        action: 'loadFights',
        poolID : id
    };
    jQuery.post(ajaxurl, data, function(result) {
        jQuery('#fixtureDiv').empty().append(result);
    })
}