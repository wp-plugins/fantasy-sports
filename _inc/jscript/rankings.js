jQuery.ranking = {
    enterLeagueHistory:function()
    {
        var leagueID = jQuery("#importleagueID").val()
        var data = {
            action: 'getNormalGameResult',
            leagueID : leagueID,
        };
        jQuery.ajaxSetup({async:false});
        jQuery.post(ajaxurl, data, function(result) {
            jQuery("#dataResult").val(result);
            result = jQuery.parseJSON(result);
            var league = result.league;
            var pool = result.pool;
            
            //list players
            var users = result.users;
            var current_user = '';
            var htmlForMethod = '';
            if(pool.allow_method == 1)
            {
                htmlForMethod = 
                    '<th>Methods</th>\n\
                    <th>Rounds</th>\n\
                    <th>Minutes</th>\n\
                    <th>Bonuses</th>';
            }
            var htmlPlayers = 
                '<table class="table table-bordered table-responsive table-condensed">\n\
                    <tr>\n\
                        <th>User</th>\n\
                        <th>Rank</th>\n\
                        <th>Points</th>\n\
                        <th>Winners</th>\n\
                        ' + htmlForMethod + '\n\
                        <th>Winnings</th>\n\
                    </tr>';
            if(users != null)
            {
                for(var i in users)
                {
                    user = users[i];
                    var htmlSelect = '';
                    if(!user.current)
                    {
                        htmlSelect = '<input type="radio" id="userinfo' + user.userID + '" name="user" onclick="jQuery.ranking.selectUser(' + user.userID + ');">';
                    }
                    if(pool.allow_method == 1)
                    {
                        htmlForMethod = 
                            '<td>' + user.methods + '</td>\n\
                            <td>' + user.rounds + '</td>\n\
                            <td>' + user.minutes + '</td>\n\
                            <td>' + user.bonuses + '</td>';
                    }
                    htmlPlayers += 
                        '<tr>\n\
                            <td>\n\
                                ' + htmlSelect + '\n\
                                <label for="userinfo' + user.userID + '">' + user.user_login + '</label>\n\
                            </td>\n\
                            <td>' + user.rank + '</td>\n\
                            <td>' + user.points + '</td>\n\
                            <td>' + user.winners + '</td>\n\
                            ' + htmlForMethod + '\n\
                            <td>' + user.winnings + '</td>\n\
                        </tr>';
                    if(user.current == 1)
                    {
                        current_user = user;
                    }
                }
            }
            htmlPlayers += '</table>';
            jQuery("#listPlayers").empty().append(htmlPlayers);
            
            //fixtures
            var fights = result.fights;
            var htmlFixtures = 
                '<table class="table table-bordered table-responsive table-condensed">\n\
                    <tr>\n\
                        <th>Fixture</th>\n\
                        <th style="width:25%" id="myResultHeader">My Pick (' + current_user.user_login + ')</th>\n\
                        <th style="width:25%" id="yourResultHeader">Competitor Pick</th>\n\
                        <th style="width:25%">Actual Result</th>\n\
                    </tr>';
            if(fights != null)
            {
                for(var i in fights)
                {
                    fight = fights[i];
                    var styleTeam1Win = styleTeam2Win = 'style="color:red"';
                    var htmlComplete = '';
                    if(fight.winnerID == fight.fighterID1)
                    {
                        styleTeam1Win = 'style="color:green"';
                    }
                    if(fight.winnerID == fight.fighterID2)
                    {
                        styleTeam2Win = 'style="color:green"';
                    }
                    if(league.is_complete)
                    {
                        htmlComplete = 
                            '<div ' + styleTeam1Win + '>' + fight.name1 + ' ' + fight.team1score + '</div>\n\
                            <div ' + styleTeam2Win + '>' + fight.name2 + ' ' + fight.team2score + '&nbsp;</div>\n\
                            <div>&nbsp;</div>\n\
                            <div>&nbsp;</div>\n\
                            <div>&nbsp;</div>';
                    }
					var team1_spread_points = team2_spread_points = '';
                    if(league.gameType == "PICKSPREAD")
                    {
                        team1_spread_points = ' ' + fight.team1_spread_points;
                        team2_spread_points = ' ' + fight.team2_spread_points;
                    }
                    else if(league.gameType == "PICKMONEY")
                    {
                        team1_spread_points = ' ' + fight.team1_moneyline;
                        team2_spread_points = ' ' + fight.team2_moneyline;
                    }
                    htmlFixtures += 
                        '<tr>\n\
                            <td>' + fight.name1 + team1_spread_points + '\n\
                                <div>VS</div>' + fight.name2 + team2_spread_points + '</td>\n\
                            <td id="myresult_' + fight.fightID + '">\n\
                            </td>\n\
                            <td id="yourresult_' + fight.fightID + '"></td>\n\
                            <td>\n\
                                <div class="h_column actual_result">\n\
                                    ' + htmlComplete + '\n\
                                </div>\n\
                            </td>\n\
                        </tr>';
                }
                htmlFixtures += 
                    '<tr>\n\
                        <td>&nbsp;</td>\n\
                        <td>\n\
                            <div id="myTotalPoints"></div>\n\
                        </td>\n\
                        <td>\n\
                            <div class="YourTotalPoints"></div>\n\
                        </td>\n\
                        <td>&nbsp;</td>\n\
                    </tr>';
            }
            htmlFixtures += '</table>';
            jQuery("#listFixtures").empty().append(htmlFixtures);
            
            jQuery.ranking.showUserResult(current_user, 1);
        })
        jQuery.ajaxSetup({async:true});
    },
    
    selectUser: function(selID)
    {
        var result = jQuery("#dataResult").val();
        result = jQuery.parseJSON(result);
        var league = result.league;
        var users = result.users;
        if(!league.can_view_user)
        {
            alert("You can see another users' picks after league start only.");
        }
        else 
        {
            if(users != null)
            {
                for(var i in users)
                {
                    if(users[i].userID == selID)
                    {
                        jQuery.ranking.showUserResult(users[i], 0);
                    }
                }
            }
        }
    },
    
    showUserResult: function(user, mypick)
    {
        var result = jQuery("#dataResult").val();
        result = jQuery.parseJSON(result);
        var league = result.league;
        var header = headerName = body = totalPoints = '';
        if(mypick == 1)
        {
            header = jQuery("#myResultHeader");
            headerName = "My Pick";
            body = "myresult_";
            totalPoints = "myTotalPoints";
        }
        else 
        {
            header = jQuery("#yourResultHeader");
            headerName = "Competitor Pick";
            body = "yourresult_";
            totalPoints = "YourTotalPoints";
        }
        header.empty().append(headerName + ' (' + user.user_login + ')');
        if(user.picks != null)
        {
            var html = '';
            var fixture = '';
            for(var i in user.picks)
            {
                fixture = user.picks[i];
                var styleWinner = styleMethod = styleMinute = styleRound = 'style="color:red"';
                var htmlPoint = "No points";
                if(league.is_complete)
                {
                    if(fixture.matchWinner)
                    {
                        styleWinner = 'style="color:green"';
                    }
                    if(fixture.matchMethod)
                    {
                        styleMethod = 'style="color:green"';
                    }
                    if(fixture.matchMinute)
                    {
                        styleMinute = 'style="color:green"';
                    }
                    if(fixture.styleRound)
                    {
                        styleRound = 'style="color:green"';
                    }
                    if(fixture.points != '')
                    {
                        htmlPoint = 'Points: ' + fixture.points;
                    }
                }
                html = 
                    '<div ' + styleWinner + '>' + fixture.name + '</div>\n\
                    <div ' + styleMethod + '>' + fixture.method + '&nbsp;</div>\n\
                    <div ' + styleRound + '>' + fixture.round + '&nbsp;</div>\n\
                    <div ' + styleMinute + '>' + fixture.minute + '&nbsp;</div>\n\
                    <div>' + htmlPoint + '</div>';
                jQuery("#" + body + fixture.fightID).empty().append(html);
                jQuery("#" + totalPoints).empty().append("Total points " + user.points);
            }
        }
    },
    
    inviteFriends: function()
    {
        var dialog = jQuery("#dlgInviteFriend").dialog({
            maxHeight: 600,
            width:800,
            minWidth:600,
            modal:true,
            open: function() {
                jQuery('.ui-widget-overlay').addClass('custom-overlay');
            }
        });
    },
    
    checkAll: function()
    {
        jQuery("input[name='val[friend_ids][]']").attr('checked', true);
    },

    checkNone: function()
    {
        jQuery("input[name='val[friend_ids][]']").removeAttr('checked');
    },
    
    sendInvite: function()
    {
        jQuery('#inviteForm').find('.inviting').show();
        var dataSring = jQuery('#inviteForm').serialize();
        jQuery.post(ajaxurl, 'action=sendInviteFriend&' + dataSring, function(result) {
            var data = JSON.parse(result);
            if(data.notice)
            {
                alert(data.notice);
            }
            else
            {
                alert(data.message);
                jQuery("#dlgInviteFriend").dialog('close');
                jQuery('#inviteForm').find('.inviting').hide();
            }
        })
        return false;
    }
}