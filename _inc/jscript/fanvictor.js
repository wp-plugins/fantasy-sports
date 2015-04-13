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
		var close = wpfs['close'];
        jQuery("#dlgFixture").dialog({
            buttons: {
                close: function() {
                    dialog.dialog("close");
                }
            }
        });
    })
    return false;
}

jQuery.league =
{
    lobby: function()
    {
        var data = {
            action: 'updateNewContests'
        };
        var leagueData;
        
        //laod data
        jQuery.ajaxSetup({async:false});
        jQuery.post(ajaxurl, data, function(result) {
            leagueData = result;
        });
        jQuery.ajaxSetup({async:true});
        this.aLeagues = leagueData;
        
        //show lobby
        var html = '';
        if(this.aLeagues)
        {
            var aLeagues = jQuery.parseJSON(this.aLeagues);
            if(aLeagues != null)
            {
                var aLeague = '';
                for(var i = 0;i < aLeagues.length; i++)
                {
                    aLeague = aLeagues[i];
                    var enterLable = 'Enter';
                    if(aLeague.enter)
                    {
                        enterLable = 'Edit'
                    }
                    html +=
                        '<tr>\n\
                            <td>\n\
                                <img src="' +  aLeague.icon + '" width="16px" style="box-shadow:none;border-radius:0;margin-right:5px;" />\n\
                                <a href="#" onclick="return jQuery.league.dlgLobbyInfo(' + aLeague.leagueID + ')">' + aLeague.name + '</a>\n\
                            </td>\n\
                            <td>' + aLeague.gameType + '</td>\n\
                            <td>' + aLeague.entries + ' / ' + aLeague.size + '</td>\n\
                            <td>$' + aLeague.entry_fee + ' / $' + aLeague.prizes + '</td>\n\
                            <td>' + aLeague.startDate + '</td>\n\
                            <th style="text-align:center">\n\
                                <input type="submit" class="btn btn-success" value="' + enterLable + '" onclick="window.location = \'' + jQuery('#submitUrl').val() + aLeague.leagueID + '\'">\n\
                            </th>\n\
                        </tr>';
                }
            }
        }
        jQuery('#homeLobby tbody').empty().append(html);
    },
    
    findLeague: function(id)
    {
        var aLeagues = jQuery.parseJSON(this.aLeagues);
        for(var i = 0; i < aLeagues.length; i++)
        {
            if(aLeagues[i].leagueID == id)
            {
                return aLeagues[i];
            }
        }
    },
    
    dlgLobbyInfo: function(leagueID)
    {
        var aLeague = this.findLeague(leagueID);
        jQuery("#dlgLeagueDetail").empty().append("<center>Loading...Please wait!</center>");
        jQuery("#dlgLeagueDetail").dialog({
            width:600,
            height:500,
            resizable:false,
            modal:true,
        });
        
        //genral info
        var htmlGeneral = 
            '<b>Name:' + wpfs['Name Fee'] + ': </b>' + aLeague.name + '\n\
            <br><b>' + wpfs['Entry Fee'] + ': $</b>' + aLeague.entry_fee + '\n\
            <br><b>' + wpfs['Prizes'] + ': $</b>' + aLeague.prizes + '\n\
            <br><b>' + wpfs['Prize Structure'] + ': </b>' + aLeague.prize_structure_name + '\n\
            <br><b>' + wpfs['h_Methods'] + ': </b>' + aLeague.size + '\n\
            <br><b>' + wpfs['Creator'] + ': </b>' + aLeague.creator_name + '\n\
            <br><b>Organization </b>' + aLeague.organization_name + '\n\
            <br><b>' + wpfs['Sport'] + ': </b>' + aLeague.type + '\n\
            <br><b>' + wpfs['Game Type'] + ': </b>' + aLeague.gameType + '\n\
            <br><b>' + wpfs['Start'] + ': </b>' + aLeague.startDate + '\n\
            <br><b>' + wpfs['End1'] + ': </b>Prizes paid next day';
        
        //other info
        var htmlFixtures = htmlEntries = htmlScoring = '';
        var data = {
            action: 'loadPoolInfo',
            leagueID: aLeague.leagueID
        };
        jQuery.ajaxSetup({async:false});
        jQuery.post(ajaxurl, data, function(result) {
            if(result)
            {
                //fixture
                result = jQuery.parseJSON(result);
                var aFights = result.fights;
                var aFight = '';
                for(var i = 0; i < aFights.length; i++)
                {
                    aFight = aFights[i];
                    if(aLeague.gameType == 'PICKSPREAD')
                    {
                        htmlFixtures += aFight.name1 + ' (' + aFight.team1_spread_points + ') vs ' + aFight.name2 + ' (' + aFight.team2_spread_points + ')<br>';
                    }
                    else 
                    {
                        htmlFixtures += aFight.name1 + ' vs ' + aFight.name2 + ' - ' + aFight.startDate + '<br>';
                    }
                }
                
                //entries
                var aEntries = result.entries;
                if(aEntries != null && aEntries.length > 0)
                {
                    var aEntry = '';
                    for(var i = 0; i < aEntries.length; i++)
                    {
                        aEntry = aEntries[i];
                        htmlEntries += aEntry.username + '<br>';
                    }
                }
                else 
                {
                    htmlEntries = "This game doesn't have any entries yet";
                }
                
                //scoring
                var aPlayerDraftScorings = result.scorings.playerdraft;
                var aNormalScorings = result.scorings.normal;
                var aScoring = scorings = '';
                if(aNormalScorings != null)
                {
                    if(aPlayerDraftScorings != null)
                    {
                        htmlScoring += '<b>Normal:</b>';
                    }
                    for(var i = 0; i < aNormalScorings.length; i++)
                    {
                        aScoring = aNormalScorings[i];
                        htmlScoring += '<div style="margin-left:20px;">' + aScoring + '</div>';
                    }
                }
                if(aPlayerDraftScorings != null)
                {
                    htmlScoring += '<b>Playerdraft:</b><br/>';
                    if(aPlayerDraftScorings.length > 1)
                    {
                        for(var i = 0; i < aPlayerDraftScorings.length; i++)
                        {
                            aScoring = aPlayerDraftScorings[i];
                            htmlScoring += aScoring.name + ':<br>';
                            for(var j = 0; j < aScoring.scoring_category.length; j++)
                            {
                                scorings = aScoring.scoring_category[j];
                                htmlScoring += '<div style="margin-left:20px;">' + scorings.name + ' = ' + scorings.points + '</div>';
                            }
                        }
                    }
                    else
                    {
                        htmlScoring += '<div style="margin-left:20px;">';
                        aPlayerDraftScorings = aPlayerDraftScorings[0].scoring_category;
                        var aScoring = '';
                        for(var i = 0; i < aPlayerDraftScorings.length; i++)
                        {
                            aScoring = aPlayerDraftScorings[i];
                            htmlScoring += aScoring.name + ' = ' + aScoring.points + ', '; 
                        }
                        htmlScoring += '</div>';
                    }
                }
            }
        });
        jQuery.ajaxSetup({async:true});
        
        var html =
            '<div id="myTab">\n\
                <ul>\n\
                   <li><a href="#tabs-1">Info</a></li>\n\
                   <li><a href="#tabs-2">Fixture</a></li>\n\
                   <li><a href="#tabs-3">Entries</a></li>\n\
                   <li><a href="#tabs-4">Scoring</a></li>\n\
               </ul>\n\
               <div id="tabs-1">' + htmlGeneral + '</div>\n\
               <div id="tabs-2">' + htmlFixtures + '</div>\n\
               <div id="tabs-3">' + htmlEntries + '</div>\n\
               <div id="tabs-4">' + htmlScoring + '</div>\n\
           </div>';
        
        jQuery("#dlgLeagueDetail").empty().append(html);
        jQuery("#myTab").tabs({ selected: 0 });
        jQuery("#dlgLeagueDetail").dialog({
            title: aLeague.name,
        });
        return false;
    },
    
    loadLobbyEntries: function(leagueID)
    {
        var data = {
            action: 'loadLeagueEntries',
            leagueID: leagueID
        };
        jQuery.post(ajaxurl, data, function(result) {
            var html = "";
            var aEntries = jQuery.parseJSON(result);
            if(aEntries != null && aEntries.length > 0)
            {
                var aEntry = '';
                for(var i = 0; i < aEntries.length; i++)
                {
                    aEntry = aEntries[i];
                    html += aEntry.username + '<br>';
                }
            }
            else 
            {
                html = "This game doesn't have any entries yet";
            }
            jQuery('#dlgLeagueDetail #tabs-3').empty().append(html);
        });
    },
    
    loadLiveEntries: function(url_ranking)
    {
        var data = {
            action: 'loadLiveEntries',
        };
        jQuery.post(ajaxurl, data, function(result) {
            var aLeagues = jQuery.parseJSON(result);
            var html = '';
            if(aLeagues != null && aLeagues.length > 0)
            {
                for(var i = 0; i < aLeagues.length; i++)
                {
                    aLeague = aLeagues[i];
                    html += 
                        '<div>\n\
                            <div style="width: 6%"><span>ID</span>' + aLeague.leagueID+ '</div>\n\
                            <div style="width: 15%"><span>DATE</span>' + aLeague.startDate+ '</div>\n\
                            <div style="width: 38%">\n\
                                <span>NAME</span>' + aLeague.name+ '\n\
                            </div>\n\
                            <div style="width: 10%"><span>ENTRIES</span>' + aLeague.entries+ ' / ' + aLeague.size+ '</div>\n\
                            <div style="width: 10%"><span>ENTRY FEE</span>$' + aLeague.entry_fee+ '</div>\n\
                            <div style="width: 7%"><span>PRIZES</span>$' + aLeague.prizes+ '</div>\n\
                            <div style="width: 6%"><span>RANK</span>' + aLeague.rank+ '</div>\n\
                            <div style="text-align: center;width: 8%">\n\
                                <input type="button" class="btn btn-success btn-xs" value="' + wpfs['view'] + '" onclick="window.location = \' ' + url_ranking + aLeague.leagueID + '\'">\n\
                            </div>\n\
                        </div>';
                }
            }
            jQuery('#tableLiveEntriesContent').empty().append(html);
        })
    },
    
    liveEntriesResult: function(poolID, leagueID, entry_number)
    {
         var data = {
            action: 'liveEntriesResult',
            poolID: poolID,
            leagueID: leagueID
        };
        jQuery.post(ajaxurl, data, function(result) {
            jQuery.playerdraft.loadContestScores(leagueID, entry_number);
        })
    }
}