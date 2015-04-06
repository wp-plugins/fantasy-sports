jQuery.statistic =
{
    setData : function(aLeagues)
    {
        this.aLeague = aLeagues;
    },
    
    showPoolStatisticDetail : function(leagueID, sTitle)
    {
        jQuery("#dlgStatistic").empty().append("<center>Loading...Please wait!</center>");
        var dialog = jQuery("#dlgStatistic").dialog({
            height: 'auto',
            width:'800',
            modal:true,
            title:sTitle,
            buttons: {
                'Close': function() {
                    dialog.dialog( "close" );
                }
            }
        });
        var data = {
            action: 'showPoolStatisticDetail',
            leagueID : leagueID
        };
        var aLeague = '';
        jQuery.ajaxSetup({async:false});
        jQuery.post(ajaxurl, data, function(result) {
            aLeague = result;
            //jQuery("#dlgStatistic").empty().append(data.result);
        })
        jQuery.ajaxSetup({async:true});
        this.aLeague = aLeague;
        jQuery.statistic.loadLeague();
        return false;
    },
    
    loadLeague: function()
    {
        var aLeague = jQuery.parseJSON(this.aLeague);
        var html = '';
        var htmlUser = '';
        
        //users
        if(aLeague.entries > 0)
        {
            htmlUser += '<select id="cbUsers" onchange="jQuery.statistic.pickDetail();">';
            for(var j in aLeague.pick)
            {
                var pick = aLeague.pick[j];
                htmlUser += '<option value="' + pick.userID + '">' + pick.user_login + '</option>';
            }
            htmlUser += '</select>';
        }
        if(htmlUser != '')
        {
            htmlUser += 
                '<table class="wp-list-table widefat books" id="tbPickDetail">\n\
                    <thead>\n\
                        <tr>\n\
                            <th style="width: 30px">Id</th>\n\
                            <th style="width: 60px">' + wpfs['a_name'] + '</th>\n\
                            <th style="width: 60px">' + wpfs['a_points'] + '</th>\n\
                        </tr>\n\
                    </thead>\n\
                    <tbody>\n\
                    </tbody>\n\
                </table>';
        }
        
        //league
        html += '<div id="leagueDetail">\n\
                    <table class="wp-list-table widefat books" id="tbLeagueDetail">\n\
                        <thead>\n\
                            <tr>\n\
                                <th style="width: 30px"></th>\n\
                                <th style="width: 60px">' + wpfs['a_prizes'] + '</th>\n\
                                <th style="width: 60px">' + wpfs['a_awarded'] + '</th>\n\
                                <th style="width: 60px">Entry Fee' + wpfs['a_fee'] + '</th>\n\
                                <th style="width: 40px">Size' + wpfs['a_size'] + '</th>\n\
                                <th style="width: 60px">Entries' + wpfs['a_entries'] + '</th>\n\
                                <th style="width: 70px">' + wpfs['a_total'] + '</th>\n\
                            </tr>\n\
                        </thead>\n\
                        <tbody>\n\
                            <tr>\n\
                                <th>' + aLeague.leagueID + '</th>\n\
                                <th>' + aLeague.prize_structure + '</th>\n\
                                <th>' + aLeague.awarded + '</th>\n\
                                <th>' + aLeague.entry_fee + '</th>\n\
                                <th>' + aLeague.size + '</th>\n\
                                <th>' + aLeague.entries + '</th>\n\
                                <th>' + aLeague.total_cash + '</th>\n\
                            </tr>\n\
                        </tbody>\n\
                    </table>\n\
                    <div id="divUsers">' + htmlUser + '</div>\n\
                </div>'
        jQuery('#dlgStatistic').empty().append(html);
        if(htmlUser != '')
        {
            this.pickDetail();
        }
    },
    
    leagueDetail: function(leagueID)
    {
        var leagueID = jQuery('#cbLeague').val();
        var aLeagues = jQuery.parseJSON(this.aLeague);
        var html = '';
        var htmlUser = '';
        for(var i in aLeagues)
        {
            var aLeague = aLeagues[i];
            if(aLeague.leagueID == leagueID)
            {
                html += 
                    '<tr>\n\
                        <th>' + aLeague.leagueID + '</th>\n\
                        <th>' + aLeague.prize_structure + '</th>\n\
                        <th>' + aLeague.awarded + '</th>\n\
                        <th>' + aLeague.entry_fee + '</th>\n\
                        <th>' + aLeague.size + '</th>\n\
                        <th>' + aLeague.entries + '</th>\n\
                        <th>' + aLeague.total_cash + '</th>\n\
                    </tr>';
                //users
                if(aLeague.entries > 0)
                {
                    htmlUser += '<select id="cbUsers" onchange="jQuery.statistic.pickDetail();">';
                    for(var j in aLeague.pick)
                    {
                        var pick = aLeague.pick[j];
                        htmlUser += '<option value="' + pick.userID + '">' + pick.user_login + '</option>';
                    }
                    htmlUser += '</select>';
                }
            }
        }
        if(htmlUser != '')
        {
            htmlUser += 
                '<table class="wp-list-table widefat books" id="tbPickDetail">\n\
                    <thead>\n\
                        <tr>\n\
                            <th style="width: 30px">Id</th>\n\
                            <th style="width: 60px">Name</th>\n\
                            <th style="width: 60px">Points</th>\n\
                        </tr>\n\
                    </thead>\n\
                    <tbody>\n\
                    </tbody>\n\
                </table>';
        }
        jQuery('#tbLeagueDetail tbody').empty().append(html);
        jQuery('#divUsers').empty().append(htmlUser);
        if(htmlUser != '')
        {
            this.pickDetail();
        }
    },
    
    pickDetail: function()
    {
        var user_id = jQuery('#cbUsers').val();
        var aLeague = jQuery.parseJSON(this.aLeague);
        var html = '';
        for(var j in aLeague.pick)
        {
            var pick = aLeague.pick[j];
            if(pick.userID == user_id)
            {
                for(var k in pick.players)
                {
                    var player = pick.players[k];
                    html += 
                        '<tr>\n\
                            <th>' + player.id + '</th>\n\
                            <th>' + player.name +'</th>\n\
                            <th>' + player.points +'</th>\n\
                        </tr>';
                }
            }
        }
        jQuery('#tbPickDetail tbody').empty().append(html);
    },
    
    loadLeagueDetail: function(leagueID, sTitle)
    {
        jQuery("#dlgStatistic").empty();
        var dialog = jQuery("#dlgStatistic").dialog({
            height: 'auto',
            width:'800',
            modal:true,
            title:sTitle,
            buttons: {
                'Close': function() {
                    dialog.dialog( "close" );
                }
            }
        });
        var data = {
            action: 'loadLeagueDetail',
            leagueID : leagueID
        };
        jQuery.post(ajaxurl, data, function(result) {
            var aLeague = jQuery.parseJSON(result);
            var html = "League does not exist";
            if(aLeague != null)
            {
                html = '<div id="leagueDetail">\n\
                        <table class="wp-list-table widefat books" id="tbLeagueDetail">\n\
                            <thead>\n\
                                <tr>\n\
                                    <th style="width: 30px">Id</th>\n\
                                    <th style="width: 30px">Name</th>\n\
                                    <th style="width: 60px">Prizes</th>\n\
                                    <th style="width: 60px">Awarded</th>\n\
                                    <th style="width: 60px">Entry Fee</th>\n\
                                    <th style="width: 40px">Size</th>\n\
                                    <th style="width: 60px">Entries</th>\n\
                                </tr>\n\
                            </thead>\n\
                            <tbody>\n\
                                <tr>\n\
                                    <th>' + aLeague.leagueID + '</th>\n\
                                    <th>' + aLeague.name + '</th>\n\
                                    <th>' + aLeague.prize_structure + '</th>\n\
                                    <th>' + aLeague.awarded + '</th>\n\
                                    <th>' + aLeague.entry_fee + '</th>\n\
                                    <th>' + aLeague.size + '</th>\n\
                                    <th>' + aLeague.entries + '</th>\n\
                                </tr>\n\
                            </tbody>\n\
                        </table>\n\
                    </div>'
            }
            jQuery("#dlgStatistic").empty().append(html);
        })
        return false
    }
}