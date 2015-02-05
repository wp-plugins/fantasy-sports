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
            '<b>Name: </b>' + aLeague.name + '\n\
            <br><b>Entry Fee: $</b>' + aLeague.entry_fee + '\n\
            <br><b>Prizes: $</b>' + aLeague.prizes + '\n\
            <br><b>Prize Structure: </b>' + aLeague.prize_structure_name + '\n\
            <br><b>Size: </b>' + aLeague.size + '\n\
            <br><b>Creator: </b>' + aLeague.creator_name + '\n\
            <br><b>Organization: </b>' + aLeague.organization_name + '\n\
            <br><b>Sport: </b>' + aLeague.type + '\n\
            <br><b>Game Type: </b>' + aLeague.gameType + '\n\
            <br><b>Start: </b>' + aLeague.startDate + '\n\
            <br><b>End: </b>Prizes paid next day';
        
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
                        '<tr>\n\
                            <td>' + aLeague.leagueID+ '</td>\n\
                            <td>' + aLeague.startDate+ '</td>\n\
                            <td>\n\
                                <span >' + aLeague.name+ '</span>\n\
                            </td>\n\
                            <td>' + aLeague.size+ '</td>\n\
                            <td>' + aLeague.entries+ '</td>\n\
                            <td>$' + aLeague.entry_fee+ '</td>\n\
                            <td>$' + aLeague.prizes+ '</td>\n\
                            <td>' + aLeague.rank+ '</td>\n\
                            <td style="text-align: center">\n\
                                <input type="button" class="btn btn-success btn-xs" value="View" onclick="window.location = \' ' + url_ranking + aLeague.leagueID + '\'">\n\
                            </td>\n\
                        </tr>';
                }
            }
            jQuery('#tableLiveEntries tbody').empty().append(html);
        })
    },
    
    liveEntriesResult: function(poolID, leagueID)
    {
         var data = {
            action: 'liveEntriesResult',
            poolID: poolID,
        };
        jQuery.post(ajaxurl, data, function(result) {
            jQuery.playerdraft.loadContestScores(leagueID);
        })
    }
}