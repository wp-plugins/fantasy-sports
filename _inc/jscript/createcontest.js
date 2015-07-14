var lastPID = "";
var activeID = new Array();

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

function setOptions(matchWith)
{
    if ( ! isNaN(matchWith) )
    {
            return true;
    }
    switch ( matchWith ) 
    {
        case "head2head":
            jQuery('.leagueDiv').hide();
            jQuery('#addPayouts').hide();
            jQuery('#payoutExample').hide();
            jQuery('#payouts').empty();
            break;
        case "league":
            jQuery('.leagueDiv').show();
            jQuery('#addPayouts').hide();
            jQuery('#payoutExample').hide();
            jQuery('#payouts').empty();
            break;
        case "multi_payout":
            jQuery('#addPayouts').show();
            jQuery('#payoutExample').show();
            break;
        case "top3":
            jQuery('#addPayouts').hide();
            jQuery('#payoutExample').hide();
            jQuery('#payouts').empty();
            break;
        case "winnertakeall":
            jQuery('#addPayouts').hide();
            jQuery('#payouts').empty();
            jQuery('#payoutExample').hide();
            break;
        case "winnertakeall":
        case "top3":
        case "public":
        case "private":
        case "on":
        break;
    }
    jQuery.createcontest.calculatePrizes();
}

jQuery.createcontest =
{
    setData : function(aPools, aFights, aRounds, aPositions, lineup, lineup_no_position)
    {
        this.aPools = aPools;
        this.aFights = aFights;
        this.aRounds = aRounds;
        this.aPositions = aPositions;
        this.lineup = lineup;
        this.lineup_no_position = lineup_no_position;
        jQuery.parseJSON(this.aPools);
    },
    
    init: function()
    {
        var aPools = jQuery.parseJSON(this.aPools);
        if(aPools != null)
        {
            for(var i = 0; i < aPools.length; i++)
            {
                jQuery('#sportRadios' + aPools[i].organization).removeAttr('disabled');
            }
        }
    },
    
    loadPools: function(org_id, is_playerdraft, only_playerdraft, is_round, is_team)
    {
        var aPools = jQuery.parseJSON(this.aPools);
        var selectPool = jQuery('#selectPool').val();
        if(aPools != null)
        {
            var html = '<select class="form-control" name="poolID" onchange="jQuery.createcontest.loadFights(jQuery(this).val());jQuery.createcontest.loadRounds(jQuery(this).val());">';
            for(var i = 0; i < aPools.length; i++)
            {
                var aPool = aPools[i];
                var selected = '';
                if(selectPool == aPool.poolID)
                {
                    selected = 'selected="true"';
                }
                if(aPool.organization == org_id)
                {
                    html += '<option value="' + aPool.poolID + '" ' + selected + '>' + aPool.poolName + '</option>';
                    if(aPool.type == 'MMA')
                    {
                        jQuery('.minutes').show();
                    }
                    else
                    {
                        jQuery('.minutes').hide();
                    }
                }
            }
            html += '</select>';
            jQuery('#poolDates').empty().append(html);
            this.loadFights(jQuery('#poolDates select').val());
            this.loadRounds(jQuery('#poolDates select').val())
        }
        
        if(only_playerdraft == 0)
        {
            jQuery('#game_type option').show();
            jQuery('#wrapFixtures').show();
            if(is_playerdraft == 1)
            {
                jQuery('#playerdraftType').show();
            }
            else 
            {
                jQuery('#playerdraftType').hide();
                if(jQuery("#game_type").val() == "playerdraft")
                {
                    jQuery('#game_type>option:selected').next().attr('selected', 'true');
                }
            }
        }
        else 
        {
            jQuery('#wrapFixtures').hide();
            jQuery('#game_type #playerdraftType').show();
            jQuery('#game_type option:not(#playerdraftType)').hide();
        }
        jQuery('#game_type option:first:visible').attr("selected", "true");
        if(is_round == 0)
        {
            jQuery('#wrapRounds').hide();
        }
        else 
        {
            jQuery('#wrapRounds').show();
        }
        if(is_team == 1 || (is_team == 0 && is_playerdraft == 0))
        {
            jQuery('.for_team').show();
        }
        else 
        {
            jQuery('.for_team').hide();
        }
        if(org_id == 44) //golf
        {
            jQuery('#wrapOptionType').show();
            jQuery('#optionType').removeAttr('disabled');
        }
        else
        {
            jQuery('#wrapOptionType').hide();
            jQuery('#optionType').attr('disabled', true);
        }
        jQuery('#selectPool').val('');
    },
    
    loadFights: function(poolID)
    {
        var aFights = jQuery.parseJSON(this.aFights);
        var selectFight = '';
        if(jQuery('#selectFight').length > 0 && jQuery('#selectFight').val() != '')
        {
            selectFight = jQuery.parseJSON(jQuery('#selectFight').val());
        }
        var result = '';
        if(aFights != null)
        {
            for(var i = 0; i < aFights.length; i++)
            {
                var aFight = aFights[i];
                var selected = '';
                if((selectFight != null && selectFight.indexOf(aFight.fightID) > -1) || selectFight == '' || selectFight == null)
                {
                    selected = 'checked="true"';
                }
                if(aFight.poolID == poolID)
                {
                    result += '<input type="checkbox" ' + selected + ' id="fixture_' + poolID + '_' + aFight.fightID + '" name="fightID[]" value="' + aFight.fightID + '">';
                    result += '<label for="fixture_' + poolID + '_' + aFight.fightID + '">' + aFight.name + '</label><br/>';
                }
            }
        }
        jQuery('#selectFight').val('');
        jQuery('#fixtureDiv').empty().append(result);
    },
    
    loadRounds: function(poolID)
    {
        var aRounds = jQuery.parseJSON(this.aRounds);
        var selectRound = '';
        if(jQuery('#selectRound').length > 0 && jQuery('#selectRound').val() != '')
        {
            selectRound = jQuery.parseJSON(jQuery('#selectRound').val());
        }
        var result = '';
        if(aRounds != null)
        {
            for(var i in aRounds)
            {
                var aRound = aRounds[i];
                var selected = '';
                if((selectRound != null && selectRound.indexOf(aRound.id) > -1) || selectRound == '' || selectRound == null)
                {
                    selected = 'checked="true"';
                }
                if(aRound.poolID == poolID)
                {
                    result += '<input type="checkbox" ' + selected + ' id="round_' + poolID + '_' + aRound.id + '" name="roundID[]" value="' + aRound.id + '">';
                    result += '<label for="round_' + poolID + '_' + aRound.id + '">' + aRound.name + '</label><br/>';
                }
            }
        }
        jQuery('#selectRound').val('');
        jQuery('#roundDiv').empty().append(result);
    },
    
    loadSpreadPoint: function()
    {
        if(jQuery('#game_type').val() == 'pickspread')
        {
            jQuery('#spreadpoint').show();
            var poolID = jQuery('#poolDates select').val();
            var aFights = jQuery.parseJSON(this.aFights);
            var html = '';
            if(aFights != null)
            {
                for(var i in aFights)
                {
                    var aFight = aFights[i];
                    if(aFight.poolID == poolID)
                    {
                        html += 
                            '<tr>\n\
                                <td>' + aFight.name + '</td>\n\
                                <td><input type="text" name="team1_spread_points[' + aFight.fightID + ']" value="' + aFight.team1_spread_points + '" /></td>\n\
                                <td><input type="text" name="team2_spread_points[' + aFight.fightID + ']" value="' + aFight.team2_spread_points + '" /></td>\n\
                            </tr>';
                    }
                }
            }
            jQuery('#spreadpoint table tbody').empty().append(html);
        }
        else 
        {
            jQuery('#spreadpoint').hide();
            jQuery('#spreadpoint table tbody').empty();
        }
    },
    
    gameTypeAttr: function()
    {
        var gametype = jQuery('#game_type').val();
        switch (gametype)
        {
            case 'playerdraft':
                jQuery('.for_playerdraft').show();
                break;
            default :
                jQuery('.for_playerdraft').hide();
        }
        this.loadSpreadPoint();
    },
    
    calculatePrizes: function()
    {
        var winnerPercent = jQuery('#winnerPercent').val();
        var firstPercent = jQuery('#firstPercent').val();
        var secondPercent = jQuery('#secondPercent').val();
        var thirdPercent = jQuery('#thirdPercent').val();
        var size = jQuery('#leagueSize').val();
        var entryFee = jQuery('#entry_fee').val();
        var structure = jQuery('input:radio[name=structure]:checked').val();
        var type = jQuery('input:radio[name=type]:checked').val();
        
        //calculate
        var prizes = [];
        var poss = [];
        if(type == 'head2head')
        {
            size = 2;
            structure = "winnertakeall";
        }
        if(parseInt(entryFee) > 0)
        {
            prize = size * entryFee * winnerPercent / 100;
            switch(structure)
            {
                case "winnertakeall":
                    poss.push("1st");
                    prizes.push(prize.toFixed(2));
                    break;
                case "top3":
                    prizes.push((prize * firstPercent / 100).toFixed(2));//1st
                    prizes.push((prize * secondPercent / 100).toFixed(2));//2nd
                    prizes.push((prize * thirdPercent / 100).toFixed(2));//3th
                    poss.push("1st");
                    poss.push("2nd");
                    poss.push("3rd");
                    break;
                case 'multi_payout':
                    if(jQuery("#payouts input[name='percentage[]']").length > 0)
                    {
                        var index = -1;
                        jQuery("#payouts input[name='payouts_from[]']").each(function(){
                            index++;
                            var from = parseInt(jQuery(this).val());
                            var to = parseInt(jQuery("#payouts input[name='payouts_to[]']:eq(" + index + ")").val());
                            if(from > 0 && to > 0)
                                {
                                from = jQuery.createcontest.parsePosition(from);
                                to = jQuery.createcontest.parsePosition(to);
                                pos = from + " - " + to;
                                if(from == to)
                                {
                                    pos = from;
                                }
                                poss.push(pos);
                            }
                        })
                        jQuery("#payouts input[name='percentage[]']").each(function(){
                            var percentage = jQuery(this).val();
                            if(percentage != '')
                            {
                                percentage = parseInt(jQuery(this).val());
                            }
                            else 
                            {
                                percentage = 0;
                            }
                            prizes.push((prize * percentage / 100).toFixed(2));//1st
                        })
                    }
                    break;
                /*default :
                    break;*/
            }
        }
        
        //view result
        var html = 
            '<table style="width:100%">\n\
                <tr><td style="text-align:left">Pos</td><td style="text-align:right">Prize</td></tr>';
        var count = 0;
        for(var i in poss)
        {
            var prize = prizes[i];
            var pos = poss[i];
            count++;
            /*place = null;
            switch (count)
            {
                case 1:
                    place = '1st';
                    break;
                case 2:
                    place = '2nd';
                    break;
                case 3:
                    place = '3rd';
                    break;
            }*/
            html += '<tr><td style="text-align:left">' + pos + '</td><td style="text-align:right">' + prize + '</td></tr>';
        }
        html += '</table>';
        jQuery("#prizesum").empty().append(html);	
    },
    
    parsePosition: function(num)
    {
        switch (num)
        {
            case 1:
                num = num + "st";
                break;
            case 2:
                num = num + "nd";
                break;
            case 3:
                num = num + "rd";
                break;
            default :
                num = num + "th";
                break;
        }
        return num;
    },
    
    addInsufficientZeroToMoneyFormat: function(str)
    {
        str = str.toFixed(2);
        if(str.substring(-2, 1) == '.' )
        {
            str += '0';
        }
        return str;
    },
    
    loadPosition: function()
    {
        var aPositions = jQuery.parseJSON(this.aPositions);
        var data = '';
        if(this.lineup != '')
        {
            data = jQuery.parseJSON(this.lineup);
        }
        var org_id = jQuery('#sports').val();
        var result = '<table>';
        var hasPosition = false;
        if(aPositions != null)
        {
            for(var i = 0; i < aPositions.length; i++)
            {
                var aPosition = aPositions[i];
                if(aPosition.org_id == org_id)
                {
                    hasPosition = true;
                    var total = 0;
                    var checked = 'checked="true"';
                    if(data != '')
                    {
                        for(var j = 0; j < data.length; j++)
                        {
                            if(data[j].id == aPosition.id)
                            {
                                total = data[j].total;
                                if(data[j].enable == 1)
                                {
                                    checked = 'checked="true"';
                                }
                                else 
                                {
                                    checked = '';
                                }
                                break;
                            }
                        }
                    }
                    result +=   '<tr>\n\
                                    <td>' + aPosition.name + '</td>\n\
                                    <td><input type="text" name="lineup[' + aPosition.id + '][total]" value="' + total + '" /></td>\n\
                                    <td><input type="checkbox" name="lineup[' + aPosition.id + '][enable]" ' + checked + ' value="1" /></td>\n\
                                </tr>';
                }
            }
        }
        result += '</table>';
        if(!hasPosition)
        {
            jQuery('.for_playerdraft').hide();
        }
        else 
        {
            jQuery('.for_playerdraft').show();
        }
        if(jQuery('option:selected', "#poolOrgs").attr('only_playerdraft') == 1)
        {
            jQuery('.salary_cap').show();
        }
        jQuery('#lineupResult').empty().append(result);
    },
    
    optionType: function()
    {
        var is_round = jQuery('#sports option:selected').attr('is_round');
        if(is_round == 1)
        {
            var type = jQuery('#optionType').val();
            if(type == 'salary')
            {
                //jQuery('.for_group').hide();
                var html = '<input type="text" value="' + this.lineup_no_position + '" name="lineup_no_position">';
                jQuery('#lineupResult').empty().append(html);
            }
            else
            {
                //jQuery('.for_group').show();
                this.loadPosition();
            }
        }
        else 
        {
            jQuery('.for_playerdraft.for_group').show();
        }
        this.gameTypeAttr();
    },
    
    addPayouts: function()
    {
        var plugin_url_image = jQuery("#plugin_url_image").val();
        var html = 
            '<div>\n\
                <label style="display: inline-block;width: auto">' + wpfs['from'] + '</label>\n\
                <input type="text" name="payouts_from[]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
                <label style="display: inline-block;width: auto">' + wpfs['to'] + '</label>\n\
                <input type="text" name="payouts_to[]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
                <label style="display: inline-block;width: auto">:</label>\n\
                <input type="text" name="percentage[]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
                <label style="display: inline-block;width: auto">%</label>\n\
                <a onclick="return jQuery.createcontest.removePayouts(jQuery(this).parent());" href="#">\n\
                    <img title="' + wpfs['delete'] + '" alt="' + wpfs['delete'] + '" src="' + plugin_url_image + 'delete.png"\>\n\
                </a>\n\
            </div>';
        jQuery("#payouts").append(html);
        return false;
    },
    
    removePayouts: function(item)
    {
        item.remove();
        this.calculatePrizes();
        return false;
    },
    
    create: function()
    {
        jQuery('#btn_create_contest').attr('disabled', 'true').text(wpfs['working'] + '...');
        jQuery.post(ajaxurl, 'action=createContest&' + jQuery('#formCreateContest').serialize(), function(result) {
            result = jQuery.parseJSON(result);
            if(result.result == 0)
            {
                jQuery('.public_message').empty().append(result.msg).show();
                jQuery('#btn_create_contest').removeAttr('disabled').text(wpfs['create_contest']);
            }
            else 
            {
                window.location = result.url;
            }
        })
    }
}

jQuery(window).load(function(){
    jQuery.createcontest.setData(
        jQuery("#poolData").val(), 
        jQuery("#fightData").val(), 
        jQuery("#roundData").val(), 
        jQuery("#positionData").val(), 
        jQuery("#lineupData").val(),
        jQuery("#lineupNoPositionData").val());
    jQuery.createcontest.init();
    jQuery.createcontest.loadPools(jQuery("#sports").val(), jQuery('#sports option:selected').attr('playerdraft'), jQuery('#sports option:selected').attr('only_playerdraft'), jQuery('#sports option:selected').attr('is_round'), jQuery('#sports option:selected').attr('is_team'));
    jQuery.createcontest.gameTypeAttr();
    if(typeof jQuery("#leagueIDData").val() != 'undefined' && jQuery("#leagueIDData").val() != '')
    {
        jQuery.createcontest.calculatePrizes();
        jQuery('#game_type').val(jQuery('#gameTypeData').val());
    }
    jQuery.createcontest.loadPosition();
    jQuery.createcontest.optionType();
})

jQuery(document).on('click', '.radio input', function(event){
    setOptions(this.value);
});

jQuery(document).on('submit', '#formCreateContest', function(e){
    e.preventDefault();
    jQuery.createcontest.create();
})