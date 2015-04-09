jQuery.playerdraft =
{
    setData : function(aPlayers, salaryRemaining, playerIdPicks, league, aFights, aPool, aIndicators)
    {
        this.aPlayers = aPlayers;
        this.salaryRemaining = salaryRemaining;
        this.salaryCap = salaryRemaining;
        this.playerIdPicks = playerIdPicks;
        this.league = league;
        this.aFights = aFights;
        this.aPool = aPool;
        this.aIndicators = aIndicators;
        this.scoringCat = '';
    },
    
    loadPlayers: function()
    {
        var position_id = jQuery('.f-tabs li a.f-is-active').attr('data-id');
        var teamId1 = jQuery('.fixture-item.f-is-active').attr('data-team-id1');
        var teamId2 = jQuery('.fixture-item.f-is-active').attr('data-team-id2');
        var aPool = jQuery.parseJSON(this.aPool);
        var aPlayers = jQuery.parseJSON(this.aPlayers);
        var aIndicators = jQuery.parseJSON(this.aIndicators);
        var keyword = jQuery('#player-search').val().toString();

        if(aPlayers.length > 0)
        {
            var html = '';
            for(var i = 0; i < aPlayers.length; i++)
            {
                var aPlayer = aPlayers[i];
                if(keyword == '' || aPlayer.name.toString().search(new RegExp(keyword,'i')) > -1)
                {
                    if((typeof teamId1 == 'undefined' && typeof teamId2 == 'undefined') || 
                        (aPlayer.team_id == teamId1 || aPlayer.team_id == teamId2)
                       )
                    {
                        if((aPlayer.position_id == position_id) || 
                            position_id == '')
                        {
                            var match = '';
                            if(aPlayer.teamID1 == aPlayer.team_id)
                            {
                                match = '<b>' + aPlayer.team1 + '</b>@' + aPlayer.team2;
                            }
                            else 
                            {
                                match = aPlayer.team1 + '@<b>' + aPlayer.team2 + '</b>';
                            }
                            
                            //indicator
                            var htmlIndicator = '';
                            switch(aPlayer.indicator_alias)
                            {
                                case 'IR':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">IR</span>';
                                    break;
                                case 'O':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">O</span>';
                                    break;
                                case 'D':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">D</span>';
                                    break;
                                case 'Q':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">Q</span>';
                                    break;
                                case 'P':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-probable">P</span>';
                                    break;
                                case 'NA':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">NA</span>';
                                    break;
                            }
                            var positionName = aPlayer.position;
                            if(aPool.no_position == 1)
                            {
                                positionName = '&nbsp;';
                            }
                            html += '<tr class="f-pR" data-role="player">\n\
                                        <td class="f-player-position">' + positionName + '</td>\n\
                                        <td class="f-player-name">\n\
                                            <div onclick="jQuery.playerdraft.playerInfo(' + aPlayer.id + ')">' + aPlayer.name + htmlIndicator + '</div>\n\
                                        </td>';
                            if(aPool.only_playerdraft == 0)
                            {
                                html += 
                                        '<td class="f-player-played">' + aPlayer.myteam + '</td>\n\
                                        <td class="f-player-fixture">' + match + '</td>';
                            }
                            html +=
                                        '<td class="f-player-salary">$' + accounting.formatNumber(aPlayer.salary) + '</td>\n\
                                        <td class="f-player-add">\n\
                                            <a class="f-button f-tiny f-text f-player-add-button" id="buttonAdd' + aPlayer.id + '" onclick="jQuery.playerdraft.addPlayer(' + aPlayer.id + ')">\n\
                                                <i class="fa fa-plus-circle"></i>\n\
                                            </a>\n\
                                            <a class="f-button f-tiny f-text f-player-remove-button" id="buttonRemove' + aPlayer.id + '" onclick="jQuery.playerdraft.clearPlayer(' + aPlayer.id + ')">\n\
                                                <i class="fa fa-minus-circle"></i>\n\
                                            </a>\n\
                                        </td>\n\
                                    </tr>';
                        }
                    }
                }
            }
            if(html != '')
            {
                jQuery('#listPlayers tbody').empty().append(html);
                jQuery('#listPlayers .f-player-list-empty').hide();
                jQuery('th.f-player-salary').trigger('click');
                jQuery('th.f-player-salary').trigger('click');

                //check player in line
                jQuery('.f-roster-position').each(function(){
                    var id = jQuery(this).attr('data-id');
                    jQuery('#buttonAdd' + id).hide();
                    jQuery('#buttonAdd' + id).parents('tr').addClass('f-player-in-lineup');
                    jQuery('#buttonRemove' + id).css('display', 'block');
                })
            }
            else
            {
                jQuery('#listPlayers tbody').empty();
                jQuery('#listPlayers .f-player-list-empty').show();
            }
        }
        return false;
    },
    
    setNoImage: function(item)
    {
        item.parent().addClass('f-no-image').css('background-image', '');
        item.remove();
    },
    
    setActiveFixture: function(item)
    {
        jQuery('.fixture-item').removeClass('f-is-active');
        jQuery(item).addClass('f-is-active');
        jQuery(item).blur();
        return false;
    },
    
    setActivePosition: function(item)
    {
        jQuery('.f-tabs li a').removeClass('f-is-active');
        jQuery(item).addClass('f-is-active');
        jQuery(item).blur();
        return false;
    },
    
    doSort: function(item)
    {
        jQuery("#listPlayers table").tablesorter(); 
        jQuery("#listPlayers table").trigger("updateAll");
        var index = item.index() + 1;
        jQuery("#listPlayers table").trigger("sorton",[ [[index,"n"]] ]);
        if(this.sortIndex != index)
        {
            this.sortType = '';
            this.sortIndex = index;
        }
        item.parent().find('.f-icon').hide();
        if(this.sortType == 'asc')
        {
            item.find('.f-sorted-desc').show();
            this.sortType = 'desc';
        }
        else if(this.sortType == 'desc')
        {
            item.find('.f-sorted-asc').show();
            this.sortType = 'asc';
        }
        else 
        {
            this.sortType = 'asc';
            item.find('.f-sorted-asc').show();
        }
        return false;
    },
    
    editLineup: function()
    {
        if(this.playerIdPicks != '')
        {
            var playerIdPicks = jQuery.parseJSON(this.playerIdPicks);
            for(var i = 0; i < playerIdPicks.length; i++)
            {
                this.addPlayer(playerIdPicks[i]);
            }
        }
    },
    
    addPlayer: function(id)
    {
        var aPool = jQuery.parseJSON(this.aPool);
        var player = this.findPlayer(id);
        if(typeof player != 'undefined')
        {
            var position_id = player.position_id;
            if(aPool.no_position == 1)
            {
                position_id = 0;
            }

            var item = jQuery('.player-position-' + position_id + ':not(.f-has-player)').first();
            if(item.length == 1)
            {
                jQuery('#buttonAdd' + id).hide();
                jQuery('#buttonAdd' + id).parents('tr').addClass('f-player-in-lineup');
                jQuery('#buttonRemove' + id).css('display', 'block');
                var match = '';
                if(aPool.only_playerdraft == 0)
                {
                    if(player.teamID1 == player.team_id)
                    {
                        match = '<b>' + player.team1 + '</b>@' + player.team2;
                    }
                    else 
                    {
                        match = player.team1 + '@<b>' + player.team2 + '</b>';
                    }
                }
                item.addClass('f-has-player');
                item.attr('id', 'f-has-player' + id);
                item.attr('data-id', id);
                item.find('.f-empty-roster-slot-instruction').hide();
                item.find('.f-player-image').empty().append('<img src="' + player.full_image_path + '" onerror="jQuery.playerdraft.setNoImage(jQuery(this))" />');
                item.find('.f-player').empty().append(player.name).css('visibility', 'visible').attr("onclick" , "jQuery.playerdraft.playerInfo(" + player.id + ")");
                item.find('.f-salary').empty().append('$' + accounting.formatNumber(player.salary)).css('visibility', 'visible');
                item.find('.f-fixture').empty().append(match);
                item.find('.f-button').css('visibility', 'visible');
                item.find('.f-button').attr('onclick', 'jQuery.playerdraft.clearPlayer(' + id + ')');
                this.calculateSalary(id, 'add');
                this.calculateAvgPerPlayer();
            }
            else 
            {
                if(!jQuery('.f-errorMessage').is(':visible'))
                {
                    var positionName = "'" + player.position + "'";
                    if(aPool.no_position == 1)
                    {
                        positionName = '';
                    }
                    jQuery('.f-errorMessage').empty().append(wpfs['fullpositions1'] + positionName + " " + wpfs['fullpositions2']).slideToggle().delay(4000).fadeOut();
                }
            }
        }
    },
    
    clearPlayer: function(id)
    {
        jQuery('#buttonAdd' + id).css('display', 'block');
        jQuery('#buttonAdd' + id).parents('tr').removeClass('f-player-in-lineup');
        jQuery('#buttonRemove' + id).hide();
        this.resetLineup(id);
        this.calculateSalary(id, 'remove');
        this.calculateAvgPerPlayer();
    },
    
    clearAllPlayer: function()
    {
        if(confirm(wpfs['players_out_team']))
        {
            jQuery('.f-roster .f-roster-position').each(function(){
                if(typeof jQuery(this).attr('data-id') != typeof undefined)
                {
                    jQuery.playerdraft.clearPlayer(jQuery(this).attr('data-id'));
                }
            })
        }
    },
    
    resetLineup: function(id)
    {
        var item = jQuery('#f-has-player' + id);
        item.removeClass('f-has-player');
        item.removeAttr('id');
        item.removeAttr('data-id');
        item.find('.f-empty-roster-slot-instruction').show();
        item.find('.f-player-image').empty();
        item.find('.f-player').empty().css('visibility', 'hidden').removeAttr("onclick");
        item.find('.f-salary').empty().css('visibility', 'hidden');
        item.find('.f-button').css('visibility', 'hidden');
    },
    
    findPlayer: function(id)
    {
        var aPlayers = jQuery.parseJSON(this.aPlayers);
        for(var i = 0; i < aPlayers.length; i++)
        {
            if(aPlayers[i].id == id)
            {
                return aPlayers[i];
            }
        }
    },
    
    calculateSalary: function(player_id, task)
    {
        if(this.salaryCap > 0)
        {
            var player = this.findPlayer(player_id);
            switch (task)
            {
                case 'add':
                    this.salaryRemaining -= parseInt(player.salary);
                    if(this.salaryRemaining < 0)
                    {
                        jQuery('#salaryRemaining').addClass('f-error');
                    }
                    break;
                case 'remove':
                    this.salaryRemaining += parseInt(player.salary);
                    if(this.salaryRemaining > 0)
                    {
                        jQuery('#salaryRemaining').removeClass('f-error');
                    }
                    break;
            }
            jQuery('#salaryRemaining').empty().append('$' + accounting.formatNumber(this.salaryRemaining));
        }
    },
    
    calculateAvgPerPlayer: function()
    {
        var total = jQuery('.f-roster-position:not(.f-has-player)').length;
        if(total > 0)
        {
            total = this.salaryRemaining / total;
        }
        else 
        {
            total = 0;
        }
        jQuery('#AvgPlayer').empty().append('$' + accounting.formatNumber(Math.round(total)));
    },
    
    submitData: function()
    {
        if(jQuery('.f-roster-position:not(.f-has-player)').length > 0)
        {
            alert(wpfs['player_each_position']);
        }
        else if(this.salaryCap > 0 && this.salaryRemaining < 0)
        {
            alert(wpfs['team_exceed_salary_cap']);
        }
        else 
        {
            jQuery('.f-roster .f-roster-position').each(function(){
                if(typeof jQuery(this).attr('data-id') != typeof undefined)
                {
                    jQuery('#formLineup').append('<input type="hidden" value="' + jQuery(this).attr('data-id') + '" name="player_id[]">');
                }
            })
            jQuery('#formLineup').submit();
        }
    },
    
    userResult: function(leagueID, is_curent, userID, username, avatar, rank, totalScore, entry_number)
    {
        //load user info
        var html = 
            '<div class="f-user-score-summary f-clearfix">\n\
                <div>\n\
                    <div class="f-rank">\n\
                        <header>\n\
                            <h6>' + wpfs['position'] + '</h6>\n\
                        </header>\n\
                        <h1>' + rank + '</h1>\n\
                    </div>\n\
                    <div class="f-user-info">\n\
                        <div style="background-image: url(\'' + avatar + '\')" class="f-avatar f-left">' + username + '</div>\n\
                        <h2 class="f-truncate">\n\
                            ' + username + '\n\
                        </h2>\n\
                    </div>\n\
                    <div class="f-score right">\n\
                        <header>\n\
                            <h6>Score</h6>\n\
                        </header>\n\
                        <h1 class="f-user-score f-positive  ">' + totalScore + '</h1>\n\
                    </div>\n\
                </div>\n\
            </div>\n\
            <div class="f-roster">\n\
                <div class="f-loading">\n\
                </div>\n\
            </div>';
        if(is_curent == 1)
        {
            jQuery('#f-seat-1').empty().append(html);
        }
        else 
        {
            jQuery('#f-seat-2').empty().append(html);
        }
        
        //load result
        var leagueOptionType = jQuery('#leagueOptionType').val();
        var data = 'leagueID=' + leagueID + '&userID=' + userID + '&entry_number=' + entry_number;
        jQuery.post(ajaxurl, "action=loadUserResult&" + data, function(data) {
            data = jQuery.parseJSON(data);
            html = '';
            if(data)
            {
                for(var i = 0; i < data.length; i++)
                {
                    var aResult = data[i];
                    var resultPlayerDraft = '';
                    if(aResult.playerdraft != null)
                    {
                        for(var j in aResult.playerdraft)
                        {
                            var playerdraft = aResult.playerdraft[j];
                            var scroring_points = jQuery.playerdraft.getScoringPointById(playerdraft.scoring_category_id);
                            var score = scroring_points * playerdraft.points;
                            resultPlayerDraft += 
                                '<li class="f-player-card-item">' + playerdraft.points + '\n\
                                    <span title="' + playerdraft.scoring_name + '">\n\
                                        ' + playerdraft.scoring_name.substring(0, 3) + '\n\
                                         (' + score + ')\n\
                                    </span>\n\
                                </li>';
                        }
                    }
                    var resultFight = fights = '';
                    if(aResult.fights != null && typeof aResult.fights[0] != 'undefined')
                    {
                        fights = aResult.fights;
                        resultFight += '<div class="f-fixture-info">\n\
                                            <div> \n\
                                                <span class="f-away">\n\
                                                    ' + fights[0].nickName + '\n\
                                                </span> ' + fights[0].team1score + ' @\n\
                                                <span class="f-home f-player-team-highlight">\n\
                                                    ' + fights[1].nickName + '\n\
                                                </span> ' + fights[1].team2score + '\n\
                                                <span class="f-current-state started">\n\
                                                    FINAL\n\
                                                </span>\n\
                                            </div>\n\
                                        </div>';
                    }
                    
                    var htmlPosition = '';
                    if((typeof aResult.player_position != 'undefined') && (leagueOptionType != 'salary'))
                    {
                        htmlPosition = 
                            '<div class="f-pos">\n\
                                <span title="Point Guard">' + aResult.player_position + '</span>\n\
                            </div>';
                    }
                    var styleLeagueOptionType = '';
                    if(leagueOptionType == 'salary')
                    {
                        styleLeagueOptionType = 'style="padding-left:10px;"';
                    }
                    html += 
                        '<div class="f-roster-row f-finished" ' + styleLeagueOptionType + '>\n\
                            <div class="f-roster-row-summary">\n\
                                ' + htmlPosition + '\n\
                                <div class="f-name">' + aResult.player_name + '</div>\n\
                                ' + resultFight + '\n\
                                <div class="f-player-secondary-information">\n\
                                    <div class="f-player-salary">$' + accounting.formatNumber(aResult.player_salary) + '\n\
                                    </div>\n\
                                </div>\n\
                                <div class="f-player-score-breakdown">\n\
                                    <ul class="f-player-card">\n\
                                        ' + resultPlayerDraft + '\n\
                                    </ul>\n\
                                </div>\n\
                                <div class="f-score">\n\
                                    <div>\n\
                                        <div class="f-fixture-status f-positive f-finished">\n\
                                            ' + aResult.points + '\n\
                                        </div>\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                        </div>';
                }
            }
            if(is_curent == 1)
            {
                jQuery('#f-seat-1 .f-loading').removeClass('f-loading').empty().append(html);
            }
            else 
            {
                jQuery('#f-seat-2 .f-loading').removeClass('f-loading').empty().append(html);
            }
        })
        return false;
    },
    
    getScoringPointById: function(id)
    {
        var scoringCats = jQuery("#scoringCats").val();
        if(scoringCats != '')
        {
            scoringCats = jQuery.parseJSON(scoringCats);
            for(var i in scoringCats)
            {
                if(scoringCats[i].id == id)
                {
                    return scoringCats[i].points;
                }
            }
        }
        return 0;
    },
    
    searchPlayers: function()
    {
        jQuery.playerdraft.loadPlayers();
    },
    
    isPlayerInline: function(player_id)
    {
        var existed = false;
        jQuery('.f-roster .f-roster-position').each(function(){
            if(jQuery(this).attr('data-id') == player_id)
            {
                existed = true;
            }
        })
        if(existed)
        {
            return true;
        }
        return false;
    },
    
    sendInviteFriendEmail: function()
    {
        var warning = jQuery('.f-manual-email-form-button .f-warning');
        var dataSring = jQuery('#formInviteFriend').serialize();
        jQuery.post(ajaxurl, 'action=sendInviteFriend&' + dataSring, function(result) {
            var data = JSON.parse(result);
            if(data.notice)
            {
                warning.empty().append(data.notice).css('display','inline-block').delay(4000).fadeOut();
            }
            else
            {
                warning.empty().append(data.message).css('display','inline-block').delay(4000).fadeOut();
            }
        })
        return false;
    },
    
    loadContestScores: function(leagueID, entry_number)
    {
        var html = '';
        var aScore = '';
        var htmlCurrent = '';
        var htmlNoCurrent = '';
        var currentScores = '';
        var data = 'leagueID=' + leagueID + '&entry_number=' + entry_number;
        jQuery.post(ajaxurl, "action=loadContestScores&" + data, function(result) {
            var aScores = jQuery.parseJSON(result);
            if(aScores != null)
            {
                for(var i = 0; i < aScores.length; i++)
                {
                    aScore = aScores[i];
                    htmlCurrent = htmlNoCurrent = '';
                    if(aScore.current)
                    {
                        htmlCurrent = 'class="f-user-highlight"';
                        currentScores = aScore;
                    }
                    else
                    {
                        htmlNoCurrent = 'href="#" onclick="return jQuery.playerdraft.userResult(' + leagueID + ', 0, ' + aScore.userID + ', \'' + aScore.username + '\', \'' + aScore.avatar + '\', ' + aScore.rank + ', \'' + aScore.points + '\', ' + aScore.entry_number + ')"';
                    }
                    var htmlMultiEntry = '';
                    if(jQuery('#multiEntry').val() == 1)
                    {
                        htmlMultiEntry = '<td>\n\
                            ' + aScore.entry_number + '\n\
                        </td>';
                    }
                    html +=
                        '<tr ' + htmlCurrent + ' ' + htmlNoCurrent + ' >\n\
                            <td>\n\
                                ' + aScore.rank + '\n\
                            </td>\n\
                            <td>\n\
                                <div style="background-image: url(\'' + aScore.avatar + '\')" class="f-avatar">\n\
                                </div>\n\
                                <a class="f-truncate">\n\
                                    ' + aScore.username + '\n\
                                </a>\n\
                            </td>\n\
                            ' + htmlMultiEntry + '\n\
                            <td class="f-num">\n\
                                ' + aScore.points + '\n\
                            </td>\n\
                            <td class="f-num">\n\
                                ' + aScore.amount + '\n\
                            </td>\n\
                        </tr>';
                }
            }
            jQuery('#tableScores tbody').empty().append(html);
            if(currentScores != '')
            {
                jQuery.playerdraft.userResult(leagueID, 1, currentScores.userID, currentScores.username, currentScores.avatar, currentScores.rank, currentScores.points, currentScores.entry_number);
            }
        })
    },
    
    showIndicatorLegend: function()
    {
        var item = jQuery('.f-draft-legend-key-content');
        if(!item.is(':visible'))
        {
            item.slideDown();
        }
        else 
        {
            item.slideUp();
        }
    },
    
    ////////////////////////tab////////////////////////
    playerInfo: function(player_id)
    {
        var pool = jQuery.parseJSON(this.aPool);
        var player = this.findPlayer(player_id);
        
        //add player or remove player button
        var button = '';
        if(!this.isPlayerInline(player_id))
        {
            button =    '<div class="f-add-button">\n\
                            <input type="button" value="Add Player" class="f-button f-primary f-mini f-plbARB" onclick="jQuery.playerdraft.addPlayer(' + player_id + '); jQuery.playerdraft.closeDialog(\'#dlgInfo\');">\n\
                        </div>';
        }
        else 
        {
            button =    '<div class="f-add-button">\n\
                            <input type="button" value="' + wpfs['remove_player'] + '" class="f-button f-primary f-mini f-plbARB" onclick="jQuery.playerdraft.clearPlayer(' + player_id + '); jQuery.playerdraft.closeDialog(\'#dlgInfo\');">\n\
                        </div>';
        }
        
        //html
        var positionName = player.position;
        if(pool.no_position == 1)
        {
            positionName = '';
        }
        var html = '<div>\n\
                        <div class="f-player-stats-lightbox">\n\
                            <div class="f-player-chunk">\n\
                                <div class="f-player-image" style="background-image: none;">\n\
                                    <img alt="' + player.name + '" src="' + player.full_image_path_org + '" onerror="jQuery.playerdraft.setNoImage(jQuery(this))">\n\
                                </div>\n\
                                <div class="f-player-container">\n\
                                    <div class="f-player-info">\n\
                                        <span class="f-player-pos">' + positionName + '</span>\n\
                                        <h1 class="f-player-name">' + player.name + '</h1>';
        if(pool.only_playerdraft == 0)
        {
            html += 
                                        '<span class="f-player-team">' + player.myteam + '</span>';
        }
        html +=
                                    '</div>\n\
                                    <div class="f-player-stats f-brief">\n\
                                                   <div class="f-stat">\n\
                                            <b>' + player.played + '</b> ' + wpfs['played'] + ' </div>\n\
                                        <div class="f-stat">\n\
                                            <b>$' + accounting.formatNumber( player.salary) + '</b> ' + wpfs['salary'] + ' </div>\n\
                                    </div>\n\
                                </div>\n\
                                ' + button + '\n\
                                <ul class="f-tabs">\n\
                                    <li>\n\
                                        <a data-tabname="tab1" href="#tab1">' + wpfs['summary'] + '</a>\n\
                                    </li>\n\
                                    <li>\n\
                                        <a data-tabname="tab2" href="#tab2">' + wpfs['game_log'] + '</a>\n\
                                    </li>\n\
                                    <li>\n\
                                        <a data-tabname="tab3" href="#tab3">' + wpfs['player_news'] + '</a>\n\
                                    </li>\n\
                                </ul>\n\
                            </div>\n\
                            <div class="f-player-stats-lb-tab tab1" id="tab1">\n\
                                <div class="f-player-stats f-season">\n\
                                    <h1>' + wpfs['season_statistics'] + '</h1>\n\
                                    <div class="f-well f-clearfix" id="playerStatistic"></div>\n\
                                </div>\n\
                                <div class="f-player-news f-latest">\n\
                                    <div class="f-row">\n\
                                        <h1 class="f-left">' + wpfs['latest_player_news'] + '</h1>\n\
                                    </div>\n\
                                    <div data-role="scrollable-body" class="f-clear f-news-item" id="playerBrief"></div>\n\
                                </div>';
        if(pool.only_playerdraft == 0)
        {
            html +=             
                                '<div class="f-next-game">\n\
                                    <h1>' + wpfs['next_game'] + '</h1>\n\
                                    <div class="f-game">' + player.teamName1 + ' vs ' + player.teamName2 + '</div>\n\
                                </div>';
        }
        html += 
                            '</div>\n\
                            <div class="f-player-stats-lb-tab f-tab2" id="tab2">\n\
                                <div class="f-game-log">\n\
                                    <h1>' + wpfs['game_log'] + '</h1>\n\
                                    <div class="f-table-container" id="gameLog"></div>\n\
                                </div>\n\
                            </div>\n\
                            <div id="tab3" class="f-player-stats-lb-tab f-tab3">\n\
                                <div class="f-player-news">\n\
                                    <div class="f-row">\n\
                                        <h1 class="f-left">' + wpfs['player_news'] + '</h1>\n\
                                    </div>\n\
                                    <div class="f-clear f-news-item" data-role="scrollable-body" id="playerNews"></div>\n\
                                </div>\n\
                            </div>\n\
                        </div>\n\
                    </div>';
        this.showDialog('#dlgInfo', html)
        jQuery(".f-player-stats-lightbox").tabs({active : 0});
        
        //statistic
        var htmlStatistic = totalPlayed = '';
        var orgID = jQuery.parseJSON(this.aPool).organization;
        jQuery.post(ajaxurl, "action=loadPlayerStatistics&orgID=" + orgID + '&playerID=' + player_id, function(result) {
            result = jQuery.parseJSON(result);
            var aStatistics = result.scoring_category;
            totalPlayed = result.played;
            var aStatistic = '';
            htmlStatistic += '<div class="f-stat">\n\
                              <b>' + result.played + '</b>Games</div>';
            if(aStatistics != null)
            {
                for(var i =0; i < aStatistics.length; i++)
                {
                    aStatistic = aStatistics[i];
                    htmlStatistic += '<div class="f-stat">\n\
                            <b>' + aStatistic.points + '</b> ' + aStatistic.name + ' </div>';
                }
            }
            jQuery('#playerStatistic').empty().append(htmlStatistic);
            
            //full statistic
            var aStats = result.stats;
            var htmlPlayerStatistic = wpfs['player_no_match'];
            if(aStats.scoring != null)
            {
                htmlPlayerStatistic =   '<table class="f-game-log f-condensed f-text-align-right">\n\
                                                <thead>\n\
                                                <tr>';
                for(var i in aStats.cats)
                {
                    htmlPlayerStatistic += '<th>' + aStats.cats[i] + '</th>';
                }
                htmlPlayerStatistic += '</tr></thead><tbody class="f-text-align-right">';
                for(var i in aStats.scoring)
                {
                    htmlPlayerStatistic += '<tr>';
                    for(var j in aStats.scoring[i])
                    {
                        htmlPlayerStatistic += '<td>' + aStats.scoring[i][j] + '</td>';
                    }
                    htmlPlayerStatistic += '</tr>';
                }
                htmlPlayerStatistic += '</tbody></table>';
            }
            jQuery('#gameLog').empty().append(htmlPlayerStatistic);
        })
        
        //player news brief
        var htmlNewsBrief = '';
        var htmlNews = '';
        jQuery.post(ajaxurl, 'action=loadPlayerNews&playerID=' + player_id + '&brief=1', function(result) {
            result = jQuery.parseJSON(result);
            if(result != null)
            {
                var style = 'style="padding-bottom:5px;margin-bottom:5px;border-bottom:solid 1px #8b8b8b"';
                for(var i in result)
                {
                    if(result.length == (parseInt(i)+1))
                    {
                        style = '';
                    }
                    htmlNews += '<div ' + style + '>' + result[i].updated + '<br/>' + result[i].title + '<br/>' + result[i].content + '</div>';
                    if(i == 0)
                    {
                        htmlNewsBrief = result[i].title + '<br/>' + result[i].content;
                    }
                }
            }
            if(htmlNewsBrief == '')
            {
                htmlNewsBrief = wpfs['updating'] + '...'; 
            }
            if(htmlNews == '')
            {
                htmlNews = wpfs['updating'] + '...'; 
            }
            jQuery('#playerBrief').empty().append(htmlNewsBrief);
            jQuery('#playerNews').empty().append(htmlNews);
        })
    },
    
    ruleScoring: function(leagueID, name, entry_fee, salary_remaining, tab)
    {
        var html = '<div>\n\
                        <header>\n\
                            <h1 class="f-game-title">' + name + '</h1>\n\
                            <ul class="f-game-info">\n\
                                <li class="f-game-info-entry-fee">' + wpfs['Entry Fee'] + ': $' + entry_fee + '</li>\n\
                                <li class="f-game-info-salary-cap">' + wpfs['salary_cap'] + ': $' + accounting.formatNumber(salary_remaining) + '</li>\n\
                            </ul>\n\
                            <div id="tabRuleScoring">\n\
                                <ul class="f-tabs">\n\
                                    <li onclick="jQuery.playerdraft.loadTabScoringCategory(jQuery(this), ' + leagueID + ')">\n\
                                        <a data-tabname="tab-info" href="#tab1">' + wpfs['contest'] + '</a>\n\
                                    </li>\n\
                                    <li onclick="jQuery.playerdraft.loadTabLeagueEntries(jQuery(this), ' + leagueID + ')">\n\
                                        <a data-tabname="tab2" href="#tab2">' + wpfs['a_entries'] + '</a>\n\
                                    </li>\n\
                                    <li onclick="jQuery.playerdraft.loadTabLeaguePrizes(jQuery(this), ' + leagueID + ')">\n\
                                        <a data-tabname="tab3" href="#tab3">' + wpfs['Prizes'] + '</a>\n\
                                    </li>\n\
                                </ul>\n\
                            </div>\n\
                        </header>\n\
                        <div id="f-contest-lightbox-content">\n\
                            <div class="f-quickfire-tab" id="tab-info">\n\
                                <div class="f-tab-game-info"></div>\n\
                            </div>\n\
                        </div>\n\
                        <div class="f-quickfire-footer f-no-content"></div>\n\
                    </div>';
        jQuery('#dlgInfo').addClass('f-quickfire-lightbox');
        this.showDialog('#dlgInfo', html)
        
        switch(tab)
        {
            case 2:
                jQuery('#tabRuleScoring li:first').next().trigger('click');
                break;
            case 3:
                jQuery('#tabRuleScoring li:last').trigger('click');
                break;
            default :
                jQuery('#tabRuleScoring li:first').trigger('click');
        }
        return false;
    },
    
    dlgEntries: function(leagueID, name)
    {
        var html = '<div>\n\
                        <div class="f-lightbox-entries f-entries">\n\
                            <header>\n\
                                <h4>' + name + '</h4>\n\
                            </header>\n\
                            <div id="f-contest-lightbox-content">\n\
                                <div class="f-quickfire-tab" id="tab-info">\n\
                                    <div class="f-tab-game-info"></div>\n\
                                </div>\n\
                            </div>\n\
                            <div class="f-quickfire-footer f-no-content"></div>\n\
                        </div>\n\
                    </div>';
        this.showDialog('#dlgInfo', html)
        jQuery.playerdraft.loadTabLeagueEntries(jQuery(this), leagueID);
    },
    
    dlgPrize: function(leagueID, name)
    {
        var html = '<div>\n\
                        <div class="f-lightbox-prizes f-entries">\n\
                            <header>\n\
                                <h4>' + name + '</h4>\n\
                            </header>\n\
                            <div id="f-contest-lightbox-content">\n\
                                <div class="f-quickfire-tab" id="tab-info">\n\
                                    <div class="f-tab-game-info"></div>\n\
                                </div>\n\
                            </div>\n\
                            <div class="f-quickfire-footer f-no-content"></div>\n\
                        </div>\n\
                    </div>';
        this.showDialog('#dlgInfo', html)
        jQuery.playerdraft.loadTabLeaguePrizes(jQuery(this), leagueID);
    },
    
    loadTabScoringCategory: function(item, leagueID)
    {
        if(!item.find('a').hasClass('f-is-active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('f-is-active');
            item.find('a').addClass('f-is-active');

            var data = 'leagueID=' + leagueID;
            jQuery('.f-lightbox .f-tab-game-info').empty().append(this.loading());
            jQuery.post(ajaxurl, "action=loadPoolInfo&" + data, function(result) {
                result = jQuery.parseJSON(result);
                var aLeague = result.league;
                var aPlayerDraftScorings = result.scorings.playerdraft;
                var aNormalScorings = result.scorings.normal;
                var aFights = result.fights;
                var aRounds = result.rounds;

                //result fight
                var resultFight = '';
                if(aFights != null)
                {
                    for(var i = 0; i < aFights.length; i++)
                    {
                        resultFight += '<li><b>' + aFights[i].nickName1 + ' @ ' + aFights[i].nickName2 + '</b> ' + aFights[i].startDate + '</li>';
                    }
                }
                
                //result round
                var htmlRound = '';
                if(aRounds != null)
                {
                    for(var i = 0; i < aRounds.length; i++)
                    {
                        htmlRound += '<li><b>' + aRounds[i].name + '</b> ' + aRounds[i].startDate + '</li>';
                    }
                }
                

                //result scoring
                var resultScoring = scorings = bonusHtml = '';
                if(aLeague.bonus != null)
                {
                    bonusHtml = 
                        '<h5 class="f-game-info-scoring-title">Bonus</h5>\n\
                        <div class="f-game-info-scoring-categories">\n\
                            ' + aLeague.bonus + '\n\
                        </div>';
                }
                resultScoring = 
                        '<hr class="f-divider">\n\
                        <div class="f-row">\n\
                            <div class="f-column-12 game-info-scoring">\n\
                                <h5 class="f-game-info-scoring-title">' + wpfs['scoring_categories'] + '</h5>\n\
                                <div class="f-game-info-scoring-categories">';
                if(aNormalScorings != null)
                {
                    for(var i = 0; i < aNormalScorings.length; i++)
                    {
                        aScoring = aNormalScorings[i];
                        resultScoring += '<div style="margin-left:20px;">' + aScoring + '</div>';
                    }
                }
                if(aPlayerDraftScorings != null)
                {
                    if(aPlayerDraftScorings.length > 1)
                    {
                        for(var i = 0; i < aPlayerDraftScorings.length; i++)
                        {
                            resultScoring += '<div class="f-column-6">' + aPlayerDraftScorings[i].name + ':';
                            for(var j = 0; j < aPlayerDraftScorings[i].scoring_category.length; j++)
                            {
                                scorings = aPlayerDraftScorings[i].scoring_category[j];
                                resultScoring += '<br/>' + scorings.name + ' = ' + scorings.points;
                            }
                            resultScoring += '</div>';
                        }
                    }
                    else
                    {
                        aPlayerDraftScorings = aPlayerDraftScorings[0].scoring_category;
                        resultScoring += '<div style="margin-left:20px;">';
                        for(var i = 0; i < aPlayerDraftScorings.length; i++)
                        {
                            var aScoring = aPlayerDraftScorings[i];
                            resultScoring += aScoring.name + ' = ' + aScoring.points + '<br/> '; 
                        }
                        resultScoring += '</div>';
                    }
                    resultScoring +=
                                    '</div>\n\
                                    ' + bonusHtml + '\n\
                                </div>\n\
                            </div>';
                }

                var htmlPickPlayer = '';
                if(aLeague.is_playerdraft && aLeague.only_playerdraft == 0)
                {
                    htmlPickPlayer = '<p>' + wpfs['pick_a_team'] + '</p>';
                }
                else if(aLeague.is_playerdraft && aLeague.only_playerdraft == 1)
                {
                    htmlPickPlayer = '<p>' + wpfs['pick_player_from_list'] + '</p>';
                }
                var html = '<div class="f-row">\n\
                                <div class="f-game-info-fixtures">\n\
                                    ' + htmlPickPlayer + '\n\
                                    <ul class="f-game-info-fixture-list">\n\
                                        ' + resultFight + '\n\
                                        ' + htmlRound + '\n\
                                    </ul>\n\
                                </div>\n\
                                <div class="f-game-info-start-time">\n\
                                    <div class="f-stat">\n\
                                        <b> ' + result.startDate + '</b> ' + wpfs['Start'] + '\n\
                                    </div>\n\
                                </div>\n\
                            </div>\n\
                            ' + resultScoring;
                jQuery('.f-lightbox .f-tab-game-info').empty().append(html);
            })
        }
    },
    
    loadTabLeagueEntries: function(item, leagueID)
    {
        if(!item.find('a').hasClass('f-is-active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('f-is-active');
            item.find('a').addClass('f-is-active');

            var data = 'leagueID=' + leagueID;
            jQuery('.f-lightbox .f-tab-game-info').empty().append(this.loading());
            jQuery.post(ajaxurl, "action=loadLeagueEntries&" + data, function(result) {
                var aUsers = jQuery.parseJSON(result);
                var html = '<center>' + wpfs['no_contest_entry'] + '</center>';
                if(aUsers != null)
                {
                    html = '<ul class="f-contest-entrants-list">';
                    var user = '';
                    for(var i = 0; i < aUsers.length; i++)
                    {
                        user = aUsers[i];
                        html += 
                            '<li class="f-contest-entrant">\n\
                                <b class="f-number">' + (i + 1) + '.</b>\n\
                                ' + user.username + '\n\
                            </li>';
                    }
                    html += '</ul>';
                }
                jQuery('.f-tab-game-info').empty().append(html);
            })
        }
    },
    
    loadTabLeaguePrizes: function(item, leagueID)
    {
        if(!item.find('a').hasClass('f-is-active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('f-is-active');
            item.find('a').addClass('f-is-active');

            var data = 'leagueID=' + leagueID;
            jQuery('.f-lightbox .f-tab-game-info').empty().append(this.loading());
            jQuery.post(ajaxurl, "action=loadLeaguePrizes&" + data, function(result) {
                var json = jQuery.parseJSON(result);
                var aPrizes = json.prize;
                var note = json.note;
                var html = '<ul class="f-contest-prizes-list">';
                var aPrize = '';
                for(var i = 0; i < aPrizes.length; i++)
                {
                    aPrize = aPrizes[i];
                    html += 
                        '<li>\n\
                            <span class="f-number">' + aPrize.place + ': </span>\n\
                            $' + aPrize.prize + '\n\
                        </li>';
                }
                html += '</ul><div class="clear"></div>';
                if(note != null)
                {
                    html += '<div>' + note + '</div>';
                }
                jQuery('.f-lightbox .f-tab-game-info').empty().append(html);
            })
        }
    },
    
    loading: function()
    {
        return '<div class="f-loading-indicator">\n\
                    <div class="f-loading-circle f-loading-circle-1"></div>\n\
                    <div class="f-loading-circle f-loading-circle-2"></div>\n\
                    <div class="f-loading-circle f-loading-circle-3"></div>\n\
                </div>';
    },
    
    showDialog: function(dlg, data)
    {
        dlg = jQuery(dlg);
        if(typeof data !== 'undefined' && data != '')
        {
            dlg.find('.f-body').empty().append(data).show();
        }
        dlg.find('.f-body').show();
        dlg.fadeIn();
    },
    
    closeDialog: function(dlg)
    {
        dlg = jQuery(dlg);
        dlg.find('.f-body').hide();
        dlg.removeClass("f-quickfire-lightbox");
        dlg.fadeOut();
        return false;
    },
    
    copyLink: function(url)
    {
        Copied = jQuery('.f-refer-link input').createTextRange();
        Copied.execCommand("RemoveFormat");
        Copied.execCommand(url);
    }
}

jQuery(document).on('click', '.f-refer-prompt-tab-buttons a', function(){
    jQuery('.f-refer-prompt-tab-buttons a').removeClass('f-is-active');
    jQuery(this).blur().addClass('f-is-active');
    var tabName = jQuery(this).attr('data-tab-name');
    
    jQuery('.f-refer-prompt-tabs div').removeClass('f-is-active');
    jQuery('.f-refer-prompt-tabs div').each(function(){
        if(jQuery(this).attr('data-tab-name') == tabName)
        {
            jQuery(this).addClass('f-is-active').show();
        }
    })
})

jQuery('#formInviteFriend').submit(function(e){
    e.preventDefault();
    var dataSring = jQuery('#formInviteFriend').serialize();
    jQuery.post(ajaxurl, '=sendInviteFriend&' + dataSring, function(result) {
        var data = JSON.parse(result);
        if(data.notice)
        {
            alert(data.notice);
        }
        else
        {
            alert(data.message);
        }
    })
    return false;
})

function checkAll()
{
    jQuery("input[name='val[friend_ids][]']").attr('checked', true);
}

function checkNone()
{
    jQuery("input[name='val[friend_ids][]']").removeAttr('checked');
}