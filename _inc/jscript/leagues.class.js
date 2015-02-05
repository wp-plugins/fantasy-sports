function leaguesClass()
{
        this.flexgrid_leagues = {};
        this.flexgrid_leagues.rows = [];
}
leaguesClass.prototype.enterLeague = function(leagueID)
{
	var that = this;
	var ifAnyBoutChecked = false;
	var string = "";
	var cForm = document.getElementById('submitPicksForm');
	for ( v = 0; v < cForm.elements.length; v++ )
	{
		if ( "radio" == cForm.elements[v].type )
		{
			if ( cForm.elements[v].checked )
			{
				ifAnyBoutChecked = true;
				string = string + "&" + cForm.elements[v].name + "=" + encodeURIComponent(cForm.elements[v].value);
			}
		}
		else
		{
			string = string + "&" + cForm.elements[v].name + "=" + encodeURIComponent(cForm.elements[v].value);
		}
	}
	if ( leagueID )
	{
		string += "&leagueID=" + encodeURIComponent(leagueID) + "&is_league=1";
	}
	if ( !ifAnyBoutChecked )
	{
		alert('Please set your picks.');
		return false;
	}
	jQuery('#submitPicksForm .myButton2').attr('disabled', 'disabled');
	jQuery.ajax({
		type: "POST",
		url: "/api/userpicks?phpfoxsess=1",
		data: string,
		dataType: "json",
		cache: false,
		error: function(xhr, ajaxOptions, thrownError)
		{
			//alert("Error status: " + xhr.status + "; message: " + thrownError);
		},
		complete: function()
		{
			jQuery('#submitPicksForm .myButton2').removeAttr('disabled');
		},
		success: function(data)
		{
			if ( data )
			{
				if ( data.success )
				{
					jQuery('#contentElement').html(data.summary).fadeIn("slow");
					if ( data.newBalance )
					{
						jQuery('#Balance').text('jQuery' + data.newBalance);
					}
					jQuery('#backtotop').click();
					inviteFriends();
				}
				else
				{
					if ( data.reason && data.reason == 'pool_expired' )
					{
						alert("Sorry, you can't save your picks for this pool because the cut date is expired.");
						window.location.hash = 'leaguesList';
						return;
					}
					jQuery('#backtotop').click();
				}
			}
		}
	});
}
leaguesClass.prototype.putCompetitorIntoRankingGrid = function(userID, status)
{
	if ( myData && myData.users && myData.users[userID] )
	{
		var data = myData;
		if ( data.league_not_run )
		{
			if ( status && status == 'click' )
			{
				alert("You can see another users' picks after league start only.");
				return;
			}
		}
		jQuery("#league_history .competitor_name").html(data.id_name.users[userID]);
		for ( var id in data.fights )
		{
			var color;
			var fFight = data.fights[id];
			var s = '';
			if ( data.users[userID].fights && data.users[userID].fights[id] )
			{
				var uFight = data.users[userID].fights[id];
				if ( data.id_name.fighters[uFight.winnerID] )
				{
					color = ( uFight.winnerID == fFight.winnerID ) ? 'green' : 'red';
					s += '<div style="color:' + color + '">' + data.id_name.fighters[uFight.winnerID].replace(/\s/gi, "&nbsp;") + '</div>';
				}
				else
				{
					s += '<div>&nbsp;</div>';
				}
				if ( data.id_name.methods[uFight.methodID] )
				{
					color = ( uFight.methodID == fFight.methodID ) ? 'green' : 'red';
					s += '<div style="color:' + color + '">' + data.id_name.methods[uFight.methodID].replace(/\s/gi, "&nbsp;") + '</div>';
				}
				else
				{
					s += '<div>&nbsp;</div>';
				}
				if ( uFight.roundID && uFight.roundID != '0' && uFight.roundID != '-1' )
				{
					color = ( uFight.roundID == fFight.roundID ) ? 'green' : 'red';
					s += '<div style="color:' + color + '">' + uFight.roundID + '</div>';
				}
				else
				{
					s += '<div>&nbsp;</div>';
				}
				if ( this.allowMinutes && data.id_name.minutes[uFight.minuteID] )
				{
					color = ( uFight.minuteID == fFight.minuteID ) ? 'green' : 'red';
					s += '<div style="color:' + color + '">' + data.id_name.minutes[uFight.minuteID] + '</div>';
				}
				else
				{
					s += '<div>&nbsp;</div>';
				}
				var points = uFight.points;
				if ( points )
				{
					s += '<div>Points: ' + points + '</div>';
				}
				else
				{
					s += '<div>No points</div>';
				}
			}
			else
			{
				s += '<div>No results for this fight</div>';
			}
			
			jQuery("#league_history .fight_" + id).html(s);
		}
		jQuery("#league_history .competitor_total_points").html("Total points: " + data.users[userID].total_points);
	}
	else
	{
		alert("Cannot display picks.");
	}
}
function enterLeagueHistory(leagueID, showInvite, isLive)
{
	var that = this;
	var data = {};
	data.leagueID = leagueID;
        var dataajax = {
            action: 'LeagueResults',
            leagueId : leagueID,
            isLive : isLive
        };
        jQuery.post(ajaxurl, dataajax, function(result) {
            data = JSON.parse(result);
            if ( !data )
            {
                    return;
            }
            if ( data.success )
            {
                    if ( data.league_not_run )
                    {
                            //jQuery("#js_block_border_mmavictor_leaguesummarybtns").show();
                            /*if ( showInvite )
                            {
                                
                                    inviteFriends();
                            }*/
                    }
                    var leagues_ranking = new leaguesClass();
                    leagues_ranking.page_info = data;
                    myData = data;
                    var poolID = data.poolID;
                    leagues_ranking.currentLeagueID = leagueID;
                    leagues_ranking.currentPoolID = poolID;
                    //rankings grid
                    var bootstrap_html = '<table class="table table-striped table-bordered table-responsive table-condensed"><tr>';
                    //bootstrap_html  +=      '<th>&nbsp;</th>';
                    bootstrap_html  +=      '<th>User</th>';
                    bootstrap_html  +=      '<th>Rank</th>';
                    bootstrap_html  +=      '<th>Points</th>';
                    bootstrap_html  +=      '<th>Winners</th>';

                    var mobile = false;	
                    if(window.location.href.indexOf("mobile") > -1) {
                              mobile = true;
                    }

                    if ( ("BOXING" == data.type || "MMA" == data.type) && ! mobile )
                    {
                            bootstrap_html  +=      '<th>Methods</th>';
                            bootstrap_html  +=      '<th>Rounds</th>';
                            bootstrap_html  +=      '<th>Minutes</th>';
                            bootstrap_html  +=      '<th>Bonuses</th>';
                    }
                    bootstrap_html  +=      '<th>Winnings</th></tr>';

                    for ( var id in data.bootstrap_leagues )
                    {
                            //alert(id);
                            //alert(data.bootstrap_leagues[id].username);
                            //alert(data.bootstrap_leagues[id].points);
                    bootstrap_html+='<tr>';
                    //bootstrap_html+='<td>'+data.bootstrap_leagues[id].select+'</td>';
                    bootstrap_html+='<td>'+data.bootstrap_leagues[id].username+'<br>'+data.bootstrap_leagues[id].select+'</td>';
                    bootstrap_html+='<td>'+data.bootstrap_leagues[id].rank+'</td>';
                    bootstrap_html+='<td>'+data.bootstrap_leagues[id].points+'</td>';
                    bootstrap_html+='<td>'+data.bootstrap_leagues[id].winners+'</td>';
                    if ( ("BOXING" == data.type || "MMA" == data.type) && ! mobile  )
                    {
                            bootstrap_html+='<td>'+data.bootstrap_leagues[id].methods+'</td>';
                            bootstrap_html+='<td>'+data.bootstrap_leagues[id].rounds+'</td>';
                            bootstrap_html+='<td>'+data.bootstrap_leagues[id].minutes+'</td>';
                            bootstrap_html+='<td>'+data.bootstrap_leagues[id].bonuses+'</td>';
                    }
                    bootstrap_html+='<td>'+data.bootstrap_leagues[id].winnings+'</td></tr>';
                    }
                    bootstrap_html+='</table>';
                    //

                    var s = '<table class="table table-striped table-bordered table-responsive table-condensed" border="0" ><tr><th>Fixture</th><th>My Pick (' + data.id_name.users[data.siteID + '' + data.userID] + ')</th><th>Competitor Pick</th><th>Actual Result</th></tr>';
                    
                    for ( var id in data.fights )
                    {
                            s += '<tr>';
                            var color;
                            var fFight = data.fights[id];

                            // bout
                            s += '<td>' + data.id_name.fighters[fFight.fighterID1].replace(/\s/gi, "&nbsp;");

                            if ( data.type != "MMA" && data.type != "BOXING" && data.gameType != "PICKEM" )
                            {
                                    s += ' '+fFight.team1_spread_points;
                            }
                            s += '<div>VS</div>';
                            s += data.id_name.fighters[fFight.fighterID2].replace(/\s/gi, "&nbsp;");

                            if ( data.type != "MMA" && data.type != "BOXING" && data.gameType != "PICKEM")
                            {
                                    s += ' '+fFight.team2_spread_points;
                            }

                            s += '</td>';
                            // my pick
                            s += '<td>';

                            if ( data.users[data.siteID + '' + data.userID].fights )
                            {
                                    if ( data.users[data.siteID + '' + data.userID].fights[id] )
                                    {
                                            var uFight = data.users[data.siteID + '' + data.userID].fights[id];
                                            if ( data.id_name.fighters[uFight.winnerID] )
                                            {
                                                    color = ( uFight.winnerID == fFight.winnerID ) ? 'green' : 'red';
                                                    s += '<div style="color:' + color + '">' + data.id_name.fighters[uFight.winnerID].replace(/\s/gi, "&nbsp;") + '</div>';
                                            }
                                            else
                                            {
                                                    s += '<div>&nbsp;</div>';
                                            }
                                            if ( data.id_name.methods[uFight.methodID] )
                                            {
                                                    color = ( uFight.methodID == fFight.methodID ) ? 'green' : 'red';
                                                    s += '<div style="color:' + color + '">' + data.id_name.methods[uFight.methodID].replace(/\s/gi, "&nbsp;") + '</div>';
                                            }
                                            else
                                            {
                                                    s += '<div>&nbsp;</div>';
                                            }
                                            if ( uFight.roundID && uFight.roundID != '0' && uFight.roundID != '-1' )
                                            {
                                                    color = ( uFight.roundID == fFight.roundID ) ? 'green' : 'red';
                                                    s += '<div style="color:' + color + '">' + uFight.roundID + '</div>';
                                            }
                                            else
                                            {
                                                    s += '<div>&nbsp;</div>';
                                            }
                                            if ( that.allowMinutes && data.id_name.minutes[uFight.minuteID] )
                                            {
                                                    color = ( uFight.minuteID == fFight.minuteID ) ? 'green' : 'red';
                                                    s += '<div style="color:' + color + '">' + data.id_name.minutes[uFight.minuteID] + '</div>';
                                            }
                                            else
                                            {
                                                    s += '<div>&nbsp;</div>';
                                            }
                                            var points = uFight.points;
                                            if ( points )
                                            {
                                                    s += '<div>Points: ' + points + '</div>';
                                            }
                                            else
                                            {
                                                    s += '<div>No points</div>';
                                            }
                                    }
                                    else
                                    {
                                            s += '<div>No results for this bout</div>';
                                    }
                            }
                            s += '</td>';


                            // competitor pick
                            s += '<td><div class="h_column competitor_pick fight_' + id + '"></div></td>';

                            // actual result
                            color = 'green';
                            s += '<td><div class="h_column actual_result">';
                            if ( data.id_name.fighters[fFight.winnerID] )
                            {
                                    if ( data.type == "MMA" || data.type == "BOXING" )
                                    {					
                                            s += '<div style="color:' + color + '">' + data.id_name.fighters[fFight.winnerID].replace(/\s/gi, "&nbsp;") + '</div>';
                                    }
                                    else
                                    {
                                            if ( fFight.winnerID == fFight.fighterID1  )
                                            {
                                                    color = 'green';
                                            }
                                            else
                                            {
                                                    color = 'red';
                                            }

                                            s += '<div style="color:' + color + '">' + data.id_name.fighters[fFight.fighterID1].replace(/\s/gi, "&nbsp;");
                                            s += ' '+fFight.team1score + '</div>';

                                            if ( fFight.winnerID == fFight.fighterID2  )
                                            {
                                                    color = 'green';
                                            }
                                            else
                                            {
                                                    color = 'red';
                                            }



                                            s += '<div style="color:' + color + '">' + data.id_name.fighters[fFight.fighterID2].replace(/\s/gi, "&nbsp;");
                                            s += ' '+fFight.team2score;

                                            s += '</div>';

                                    }
                            }
                            else
                            {
                                    s += '<div>&nbsp;</div>';
                            }
                            if ( data.id_name.methods[fFight.methodID] )
                            {
                                    s += '<div style="color:' + color + '">' + data.id_name.methods[fFight.methodID].replace(/\s/gi, "&nbsp;") + '</div>';
                            }
                            else
                            {
                                    s += '<div>&nbsp;</div>';
                            }
                            if ( fFight.roundID && fFight.roundID != '0' && fFight.roundID != '-1' )
                            {
                                    s += '<div style="color:' + color + '">' + fFight.roundID + '</div>';
                            }
                            else
                            {
                                    s += '<div>&nbsp;</div>';
                            }
                            if ( that.allowMinutes && data.id_name.minutes[fFight.minuteID] )
                            {
                                    s += '<div style="color:' + color + '">' + data.id_name.minutes[fFight.minuteID] + '</div>';
                            }
                            else
                            {
                                    s += '<div>&nbsp;</div>';
                            }
                            s += '<div>&nbsp;</div></div></td>';
                            s += '</tr>';
                    }

                    s += '<tr><td>&nbsp;</td><td><div class="h_column tp">Total points: ' + data.users[data.siteID + '' +data.userID].total_points + '</div></td><td><div class="h_column tp competitor_total_points"></div></td><td>&nbsp;</td></tr>';

                    s += '</table>';

            //	jQuery("#league_history .leaguesHeader").html(data.html);
                    jQuery("#league_history .results_grid").html(s);
                    jQuery(".bootstrap_grid").html(bootstrap_html);

                    if ( leagues_ranking.lastSelectedUserID )
                    {
                            jQuery("#league_history .radio_rank_flexigrid_row_" + leagues_ranking.lastSelectedUserID).attr("checked", "checked");
                            jQuery("#rowuser_" + leagues_ranking.lastSelectedUserID).toggleClass('trSelected', true);
                            leagues_ranking.putCompetitorIntoRankingGrid(leagues_ranking.lastSelectedUserID);
                    }
            }
            else
            {
                    alert("Error: " + data.msg);
            }
        })
}

jQuery(document).on('click', '#myModal .modal-footer button, #myModal .modal-header button', function(){
    jQuery('#myModal').hide();
})

leaguesClass.prototype.enterLeaguePage = function(leagueID, poolID, newLeagueStuff, auth)
{
	var data = {};
	data.where = "poolID:" + poolID;
	data.mode = "htmlSelect";
	if ( leagueID )
	{
		data.is_league = 1;
		data.leagueID = leagueID;
	}
	if ( newLeagueStuff )
	{
		data.create_new_league = 1;
		data.new_league_stuff = newLeagueStuff;
	}
	if ( auth )
	{
		data.auth = "yes";
	}
	jQuery.ajax({
		type: "GET",
		url: "/api/fights?phpfoxsess=1",
		data: data,
		cache: false,
		error: function(xhr, ajaxOptions, thrownError)
		{
			//alert("Error status: " + xhr.status + "; message: " + thrownError);
		},
		success: function(data)
		{
			// whether league is expired
			if ( data == 'pool_expired' )
			{
				alert("Sorry, you can't put your picks for this pool because the cut date is expired.");
				window.location.hash = 'leaguesList';
				return;
			}
			//jQuery("#contentElementWrap").css('visibility', 'hidden');
			//jQuery("#contentElementWrap").css('display', 'none');
			/*if ( newLeagueStuff )
			{
				var cutPoint = data.indexOf(">");
				var part_1 = data.slice(0, cutPoint + 1);
				var part_2 = data.slice(cutPoint + 1);
				var hiddenLeaguePart = "<input type='hidden' name='new_league' value='1'/>";
				hiddenLeaguePart += '<input type="hidden" name="new_league_properties" value="' + newLeagueStuff + '"/>';
				data = part_1 + hiddenLeaguePart + part_2;
			}*/
			//jQuery('#contentElement').html(data).fadeIn("slow");
			closePopup();
			// close popup
			/*jQuery('#fade, .popup_block').fadeOut(function() {
				jQuery('#fade, a.close_popup').remove();
			});
			jQuery('#backtotop').click();*/
		}
	});
}
leaguesClass.prototype.viewLeaguePage = function(leagueID, poolID, newLeagueStuff, auth)
{
	var data = {};
	data.where = "poolID:" + poolID;
	data.mode = "htmlSelect";
	if ( leagueID )
	{
		data.is_league = 1;
		data.leagueID = leagueID;
	}
	if ( newLeagueStuff )
	{
		data.create_new_league = 1;
		data.new_league_stuff = newLeagueStuff;
	}
	if ( auth )
	{
		data.auth = "yes";
	}
	jQuery.ajax({
		type: "GET",
		url: "/api/view_fights?phpfoxsess=1",
		data: data,
		cache: false,
		error: function(xhr, ajaxOptions, thrownError)
		{
			alert("Error status: " + xhr.status + "; message: " + thrownError);
		},
		success: function(data)
		{
			if ( newLeagueStuff )
			{
				var cutPoint = data.indexOf(">");
				var part_1 = data.slice(0, cutPoint + 1);
				var part_2 = data.slice(cutPoint + 1);
				var hiddenLeaguePart = "<input type='hidden' name='new_league' value='1'/>";
				hiddenLeaguePart += '<input type="hidden" name="new_league_properties" value="' + newLeagueStuff + '"/>';
				data = part_1 + hiddenLeaguePart + part_2;
			}
			jQuery('#contentElement').html(data).fadeIn("slow");
			// close popup
			jQuery('#fade, .popup_block').fadeOut(function() {
				jQuery('#fade, a.close_popup').remove();
			});
			jQuery('#backtotop').click();
		}
	});
}
leaguesClass.prototype.changeDisplayOptions = function(type, id, value)
{
	var that = this;
	var data = {};
	data.type = type;
	data.id = id;
	data.value = value;
	jQuery.ajax({
		type: "POST",
		url: "/api/LeaguesDisplaySettings?phpfoxsess=1",
		dataType: "json",
		data: data,
		cache: false,
		success: function(data)
		{
			if ( !data )
			{
				return;
			}
			
			if ( data.success )
			{
				currentPoolsGlobal = data.pools;
				
				// pools
				var s = '';
				for ( var id in data.pools )
				{
					var selected = (data.pools[id].selected === 'selected') ? ' selected="selected"' : '';
					s += "<option" + selected + " value='" + data.pools[id].poolID + "'>" + htmlspecialchars(data.pools[id].poolName) + '</option>\n';
				}
				jQuery("#pleagsrch .pools_list").html(s);
				
				leagues.flexgrid_leagues = data.flexgrid_leagues;
				leagues.flexgrid_leagues.rows = that.sortFlexgridRows(data.flexgrid_leagues.rows, 0, 'asc');
				jQuery(".leagues_list").flexAddData(leagues.flexgrid_leagues);
			}
			else
			{
				alert("Error: " + data.msg);
			}
		}
	});
}
leaguesClass.prototype.sortFlexgridRows = function(rows, col, order, type)		// asc/desc
{
	var sortedRows = rows;
	function sortString(a, b)
	{
		var A = (a.cell[col] + '').toLowerCase();
		var B = (b.cell[col] + '').toLowerCase();
		if ( order == 'asc' )
		{
			if ( A < B ) {
				return -1;
			} else if (A > B) {
				return 1;
			} else {
				return 0;
			}
		}
		else
		{
			if ( A > B ) {
				return -1;
			} else if (A < B) {
				return 1;
			} else {
				return 0;
			}
		}
	}
	
	function sortInt(a, b)
	{
		var A = parseInt(a.cell[col]) || 0;
		var B = parseInt(b.cell[col]) || 0;
		if ( order == 'asc' )
		{
			return A - B;
		}
		else
		{
			return B - A;
		}
	}
	
	function sortBucks(a, b)
	{
		var A = parseFloat(a.cell[col].substring(1).replace(",", ".")) || 0;
		var B = parseFloat(b.cell[col].substring(1).replace(",", ".")) || 0;
		if ( order == 'asc' )
		{
			return A - B;
		}
		else
		{
			return B - A;
		}
	}
	
	function sortDatetime(a, b)
	{
		var A = new Date(a.cell[col]);
		var B = new Date(b.cell[col]);
		if ( order == 'asc' )
		{
			return A - B;
		}
		else
		{
			return B - A;
		}
	}
	
	if ( !type )
	{
		type = 'string';
	}
	
	switch ( type )
	{
		case 'int':
		{
			sortedRows.sort(sortInt);
		} break;
		case 'bucks':
		{
			sortedRows.sort(sortBucks);
		} break;
		case 'datetime':
		{
			sortedRows.sort(sortDatetime);
		} break;
		case 'string':
		default:
		{
			sortedRows.sort(sortString);
		} break;
	}
	
	return sortedRows;
}

leaguesClass.prototype.addLeague = function()
{
	var strPools = '';
	if ( window.currentPoolsGlobal !== undefined && currentPoolsGlobal )
	{
		for ( var id in currentPoolsGlobal )
		{
			strPools += "<option value='" + currentPoolsGlobal[id].poolID + "'>" + htmlspecialchars(currentPoolsGlobal[id].poolName) + "</option>";
		}
	}
	else
	{
		return;
	}
	jQuery("#popup_leagues .pools_selector").html(strPools);
	
	jQuery("#popup_leagues form")[0].reset();
	leagues.newLeagueChangePrizeStructure('popup_leagues');
	
	showPopup('popup_leagues', 700);

}
leaguesClass.prototype.invitefoxfriends = function()
{
        showPopup('popup_invitefoxfriends', 700);
}
leaguesClass.prototype.getLeaguesList = function()
{
	//console.log("POS_1");
	var that = this;
	jQuery.ajax({
		type: "GET",
		url: "/api/LeaguesInfo?phpfoxsess=1",
		dataType: "json",
		cache: false,
		success: function(data)
		{
			if ( data )
			{
				if ( data.success )
				{
					//jQuery("#contentElementWrap").css('visibility', 'hidden');
					//jQuery("#contentElementWrap").css('display', 'block');
					//console.log("POS_2");
					//jQuery("#leagues_list").css('visibility', 'hidden');
					//jQuery("#leagues_list").css('display', 'block');
					currentPoolsGlobal = data.pools;
					
					jQuery('#ddcl-fl_organizations').remove();
					jQuery('#ddcl-fl_organizations-ddw').remove();
					jQuery('#ddcl-fl_size_row').remove();
					jQuery('#ddcl-fl_size_row-ddw').remove();
					jQuery('#ddcl-fl_pools_list').remove();
					jQuery('#ddcl-fl_pools_list-ddw').remove();
					//jQuery('fl_organizations').show();
					//jQuery('fl_size_row').show();
					//jQuery('fl_pools_list').show();
					
					// change entry fee
					jQuery('.entry_fee_selector').html(data.entryFeeSelect);
					jQuery('.fee_range_min').html(data.feeRangeMinSelect);
					jQuery('.fee_range_max').html(data.feeRangeMaxSelect);
					
					//organization
					jQuery('#fl_organizations option[value="UFC"]').attr('selected', (!data.organization['UFC'] || data.organization['UFC'] === 'selected'));
					//jQuery('#fl_organizations option[value="Strikeforce"]').attr('selected', (!data.organization['Strikeforce'] || data.organization['Strikeforce'] === 'selected'));
					jQuery('#fl_organizations option[value="Dream"]').attr('selected', (!data.organization['Dream'] || data.organization['Dream'] === 'selected'));
					jQuery('#fl_organizations option[value="Bellator"]').attr('selected', (!data.organization['Bellator'] || data.organization['Bellator'] === 'selected'));
					//jQuery('#fl_organizations option[value="AFO"]').attr('selected', (!data.organization['AFO'] || data.organization['AFO'] === 'selected'));
					jQuery('#fl_organizations option[value="XFC"]').attr('selected', (!data.organization['XFC'] || data.organization['XFC'] === 'selected'));
					jQuery('#fl_organizations option[value="Cage Warriors"]').attr('selected', (!data.organization['Cage Warriors'] || data.organization['Cage Warriors'] === 'selected'));
					jQuery('#fl_organizations option[value="Invicta"]').attr('selected', (!data.organization['Invicta'] || data.organization['Invicta'] === 'selected'));
					jQuery('#fl_organizations option[value="Legacy"]').attr('selected', (!data.organization['Legacy'] || data.organization['Legacy'] === 'selected'));
					jQuery('#fl_organizations option[value="MFC"]').attr('selected', (!data.organization['MFC'] || data.organization['MFC'] === 'selected'));
					jQuery('#fl_organizations option[value="One FC"]').attr('selected', (!data.organization['One FC'] || data.organization['One FC'] === 'selected'));
					jQuery('#fl_organizations option[value="World Series of Fighting"]').attr('selected', (!data.organization['World Series of Fighting'] || data.organization['World Series of Fighting'] === 'selected'));

					
					// pools
					var s = '';
					for ( var id in data.pools )
					{
						var selected = (data.pools[id].selected === 'selected') ? ' selected="selected"' : '';
						s += "<option" + selected + " value='" + data.pools[id].poolID + "'>" + htmlspecialchars(data.pools[id].poolName) + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>\n';
					}
					jQuery("#pleagsrch .pools_list").html(s);
					// sizes
					jQuery("#fl_size_row .type_2").attr('selected', (!data.sizes['2'] || data.sizes['2'] === 'selected'));
					jQuery("#fl_size_row .type_3_6").attr('selected', (!data.sizes['3-6'] || data.sizes['3-6'] === 'selected'));
					jQuery("#fl_size_row .type_7_10").attr('selected', (!data.sizes['7-10'] || data.sizes['7-10'] === 'selected'));
					jQuery("#fl_size_row .type_11_20").attr('selected', (!data.sizes['11-20'] || data.sizes['11-20'] === 'selected'));
					jQuery("#fl_size_row .type_21_plus").attr('selected', (!data.sizes['21+'] || data.sizes['21+'] === 'selected'));
					
					// entry fee range
					jQuery('#pleagsrch .fee_range_min option[value="' + data.entry_fee.from + '"]').attr("selected", "selected");
					jQuery('#pleagsrch .fee_range_max option[value="' + data.entry_fee.to + '"]').attr("selected", "selected");
					
					// private flexigrid
					leagues_private.flexgrid_leagues = data.flexgrid_leagues_private;
					if ( leagues_private.sortType )
					{
						leagues_private.flexgrid_leagues.rows = leagues_private.sortFlexgridRows(data.flexgrid_leagues.rows, leagues_private.sortType.private_num_col, leagues_private.sortType.dir, leagues_private.sortType.sortType);
					}
					else
					{
						leagues_private.flexgrid_leagues.rows = that.sortFlexgridRows(data.flexgrid_leagues_private.rows, 0, 'asc', 'int');
					}
					jQuery(".leagues_list_private").flexAddData(leagues_private.flexgrid_leagues);
					
					// flexigrid
					leagues.flexgrid_leagues = data.flexgrid_leagues;
					if ( leagues.sortType )
					{
						leagues.flexgrid_leagues.rows = leagues.sortFlexgridRows(data.flexgrid_leagues.rows, leagues.sortType.num_col, leagues.sortType.dir, leagues.sortType.sortType);
					}
					else
					{
						leagues.flexgrid_leagues.rows = that.sortFlexgridRows(data.flexgrid_leagues.rows, 7, 'asc', 'int');
					}
					jQuery(".leagues_list").flexAddData(leagues.flexgrid_leagues);
					
					//jQuery("#leagues_list").css('visibility', 'visible');
					
					jQuery("#pleagsrch .organizations").dropdownchecklist({ width: 170,emptyText: "Please select ...",icon: {}, textFormatFunction: function(options) {
						var selectedOptions = options.filter(":selected");
						var countOfSelected = selectedOptions.size();
						var size = options.size();
						switch(countOfSelected) {
							//case 0: return "<i>Nobody<i>";
							//case 1: return selectedOptions.text();
							//case options.size(): return "<b>Everybody</b>";
							//default: return countOfSelected + " People";
							default: return "Organizations";
						}
					},onItemClick: function(checkbox, selector){
						leagues.changeDisplayOptions('organization', checkbox.val(), checkbox.prop("checked") ? 'selected' : 'not selected');
					}});
					jQuery("#pleagsrch .size_row").dropdownchecklist({ width: 170,emptyText: "Please select ...",icon: {},textFormatFunction: function(options) {
						var selectedOptions = options.filter(":selected");
						var countOfSelected = selectedOptions.size();
						var size = options.size();
						switch(countOfSelected) {
						   //case 0: return "<i>Nobody<i>";
						   //case 1: return selectedOptions.text();
						   //case options.size(): return "<b>Everybody</b>";
						   //default: return countOfSelected + " People";
						   default: return "Number of Players";
						}
					},onItemClick: function(checkbox, selector){
						leagues.changeDisplayOptions('size', checkbox.val(), checkbox.prop("checked") ? 'selected' : 'not selected');
					}});
					jQuery("#pleagsrch .pools_list").dropdownchecklist({width: 170,emptyText: "Please select ...",icon: {},maxDropHeight: 150,textFormatFunction: function(options) {
						var selectedOptions = options.filter(":selected");        var countOfSelected = selectedOptions.size();
						var size = options.size();              
						switch(countOfSelected) {               
						   //case 0: return "<i>Nobody<i>";                        //case 1: return selectedOptions.text();
						   //case options.size(): return "<b>Everybody</b>";
						   //default: return countOfSelected + " People";
						   default: return "Events";
						}                               
					},onItemClick: function(checkbox, selector){
						leagues.changeDisplayOptions('pool', checkbox.val(), checkbox.prop("checked") ? 'selected' : 'not selected');
					}});
					//}
					
					//jQuery("#leagues_list").hide();
					//jQuery("#leagues_list").show().fadeIn("fast");
					jQuery("#js_controller_core_index-member #content_holder #left #js_block_border_mmavictor_search").css('visibility', 'visible');
				}
				else
				{
					alert("Error: " + data.msg);
				}
			}
		}
	});
}
leaguesClass.prototype.selectLeagues = function(data)
{
	var selectedLeagues = {};
	jQuery.extend(selectedLeagues, this.flexgrid_leagues);
	for ( var id in selectedLeagues )
	{
		// remove unchecked sizes
//		if ( data.sizes['2'] && data.sizes['2'] === 'selected' )
	}
	
	return selectedLeagues;
}

leaguesClass.prototype.newLeagueChangeSize = function()
{
	var selVal = jQuery("#popup_leagues .size_selector").val();
	/*if ( selVal !== '0' )
	{
		jQuery("#popup_leagues .league_size").val(selVal);
		jQuery("#popup_leagues .size_selector [value='0']").attr("selected", "selected");
	}*/
	if ( selVal == 2 )
	{
		jQuery("#popup_leagues .prize_structure_selector [value='winner']").attr("selected", "selected");
		jQuery("#popup_leagues .prize_structure_selector [value='top3']").hide();
	}
	else
	{
		jQuery("#popup_leagues .prize_structure_selector [value='top3']").show();
	}
	this.newLeagueChangePrizeStructure();
}

/*leaguesClass.prototype.newLeagueTextSizeBlur = function()
{
	var size = jQuery("#popup_leagues .league_size").val().trim();
	size = parseInt(size) || 0;
	if ( size < 2 )
	{
		size = 2;
	}
	jQuery("#popup_leagues .league_size").val(size);
	this.newLeagueChangePrizeStructure();
}*/

leaguesClass.prototype.newLeagueChangePrizeStructure = function(block_id)
{
	if ( block_id === undefined )
	{
		block_id = 'popup_block_show';
	}
	
	//var size = parseInt(jQuery("#popup_leagues .league_size").val()) || 0;
	var size = parseInt(jQuery("#" + block_id + " select[name='size'] option:selected").text()) || 0;
	if ( size < 2 )
	{
		return;
	}
	
	var tbl = '';
	
	var entry_fee = parseInt(jQuery("#" + block_id + " .entry_fee_selector").val());
	if ( entry_fee )
	{
		jQuery("#" + block_id + " .prize_structure").css('visibility', 'visible');
		
		var prize = (size * entry_fee) * 0.9;
				
		tbl = '<table style="width:100%"><tr><td align="left">Pos</td><td align="right">Prize</td></tr>';
		switch ( jQuery("#" + block_id + " .prize_structure_selector").val() )
		{
			case 'winner':
			{
				tbl += '<tr><td align="left">1st</td><td align="right">jQuery' + Math.floor(prize) + '</td></tr>';
			} break;
			case 'top3':
			{
				if ( size == 2 )
				{
					tbl += '<tr><td align="left">1st</td><td align="right">jQuery' + Math.floor(prize) + '</td></tr>';
				}
				/*else if ( size == 3 )
				{
					// only 2 players will get prizes
					//var prize1st = prize * 1.7 / 2.7;
					//var prize2nd = prize - prize1st;
					//tbl += '<tr><td align="left">1st</td><td align="right">jQuery' + Math.floor(prize1st) + '</td></tr>'
					//	+ '<tr><td align="left">2nd</td><td align="right">jQuery' + Math.floor(prize2nd) + '</td></tr>';
					tbl += '<tr><td align="left">1st</td><td align="right">jQuery' + Math.floor(calculatePrize(prize, 1, 2)) + '</td></tr>'
						+ '<tr><td align="left">2nd</td><td align="right">jQuery' + Math.floor(calculatePrize(prize, 2, 2)) + '</td></tr>';
				}*/
				else
				{
					// 1st - 50%; 2nd - 30%; 3rd = 20%
					var prize1st = prize / 2.;
					var prize2nd = prize * 0.3;
					var prize3rd = prize * 0.2;
					tbl += '<tr><td align="left">1st</td><td align="right">jQuery' + Math.floor(prize1st) + '</td></tr>'
						+ '<tr><td align="left">2nd</td><td align="right">jQuery' + Math.floor(prize2nd) + '</td></tr>'
						+ '<tr><td align="left">3rd</td><td align="right">jQuery' + Math.floor(prize3rd) + '</td></tr>';
					/*tbl += '<tr><td align="left">1st</td><td align="right">jQuery' + Math.floor(calculatePrize(prize, 1, 3)) + '</td></tr>'
						+ '<tr><td align="left">2nd</td><td align="right">jQuery' + Math.floor(calculatePrize(prize, 2, 3)) + '</td></tr>'
						+ '<tr><td align="left">3rd</td><td align="right">jQuery' + Math.floor(calculatePrize(prize, 3, 3)) + '</td></tr>';*/
				}
			} break;
			/*case '3rd':
			{
				var prizePlaces = Math.floor(size / 3.);
				for ( var i = 1; i <= prizePlaces; ++i )
				{
					var place = (i == 1) ? '1st' : (i == 2) ? '2nd' : (i == 3) ? '3rd' : i + 'th';
					tbl += '<tr><td align="left">' + place + '</td><td align="right">jQuery' + Math.floor(calculatePrize(prize, i, prizePlaces)) + '</td></tr>';
				}
			} break;*/
			default:
				break;
		}
		
		tbl += '</table>';
	}
	else
	{
		jQuery("#" + block_id + " .prize_structure").css('visibility', 'hidden');
	}
		
	jQuery("#" + block_id + " .prize_structure_table").html(tbl);
}
leaguesClass.prototype.createNewLeague = function()
{
	//var poolID = parseInt(jQuery("#pools_selector :selected").val()) || 0;
	// IE fix
	//var s = document.getElementById('pools_selector');
	var s = jQuery('#popup_block_show .pools_selector')[0];
	var poolID = parseInt(s.options[s.selectedIndex].value) || 0;
	if ( !poolID )
	{
		// reload page
		window.location.reload();
		//this.getLeaguesList();
		return false;
	}
	//this.enterLeaguePage(0, poolID, jQuery('#popup_leagues form').serialize());
	//this.enterLeaguePage(0, poolID, jQuery('#popup_block_show form').serialize());
	// submit form
	
	/*var new_league_stuff = "pools_selector=" + poolID; + "&name=tempName";
	new_league_stuff += "&cardType=" + jQuery('#popup_block_show select[name="cardType"]').val();
	new_league_stuff += "&size=" + jQuery('#popup_block_show select[name="size"]').val();
	new_league_stuff += "&entry_fee=" + jQuery('#popup_block_show select[name="entry_fee"]').val();
	new_league_stuff += "&is_private=" + jQuery('#popup_block_show input[name="is_private"]:checked').val();
	new_league_stuff += "&prize_structure=" + jQuery('#popup_block_show select[name="prize_structure"]').val();
	new_league_stuff += "&prize_structure=" + jQuery('#popup_block_show select[name="prize_structure"]').val();*/
	jQuery('#popup_block_show input[name="new_league_stuff"]').val(jQuery('#popup_block_show form').serialize());
	jQuery('#popup_block_show input[name="where"]').val('poolID:' + poolID);
	jQuery('#popup_block_show form').submit();
}
leaguesClass.prototype.displayMinRangeChange = function()
{
	var min = parseInt(jQuery("#pleagsrch .fee_range_min :selected").val()) || 0;
	var max = parseInt(jQuery("#pleagsrch .fee_range_max :selected").val()) || 0;
	if ( min > max )
	{
		jQuery('#pleagsrch .fee_range_min [value="' + max + '"]').attr("selected", "selected");
	}
}
leaguesClass.prototype.displayMaxRangeChange = function()
{
	var min = parseInt(jQuery("#pleagsrch .fee_range_min :selected").val()) || 0;
	var max = parseInt(jQuery("#pleagsrch .fee_range_max :selected").val()) || 0;
	if ( max < min )
	{
		jQuery('#pleagsrch .fee_range_max [value="' + min + '"]').attr("selected", "selected");
	}
}
