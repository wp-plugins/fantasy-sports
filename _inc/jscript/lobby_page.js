jQuery.lobby = 
{
    loadLobbyPage: function()
    {
        jQuery.ajaxSetup({async:false});
        var aLeagues = '';
        jQuery.post(ajaxurl, "action=loadLeagueLobby", function(result) {
            aLeagues = result;
        })
        jQuery.ajaxSetup({async:true});
        this.aLeagues = aLeagues;
        this.showLobbyPage();
        jQuery('#lobbyContent th.f-title').trigger('click');
        jQuery('#lobbyContent th.f-title').trigger('click');
        
        //disable sports have no leauges
        aLeagues = jQuery.parseJSON(aLeagues);
        jQuery('.f-sport ul li input').each(function(){
            var org_id = jQuery(this).val();
            if(org_id != '')
            {
                var hasLeague = false;
                if(aLeagues != null)
                {
                    for(var i in aLeagues)
                    {
                        if(aLeagues[i].organization == org_id)
                        {
                            hasLeague = true;
                            break;
                        }
                    }
                }
                if(!hasLeague)
                {
                    jQuery(this).attr('disabled', 'true');
                    jQuery(this).closest('label').addClass('f-disabled');
                }
                else 
                {
                    jQuery(this).removeAttr('disabled', 'true');
                    jQuery(this).closest('label').removeClass('f-disabled');
                }
            }
        });
    },
    
    showLobbyPage: function()
    {
        var org = jQuery('.f-sport .f-checked input').val();
        var keyword = jQuery('.f-text-search .f-search-input').val().toString();
        var contestType = jQuery('.f-type .f-checked input').val();
        var leagueSize = jQuery('.f-size .f-checked input').val();
        leagueSize = leagueSize.split("-");
        var entryFeeStart = parseInt(jQuery('.f-entryfee .ui-rangeSlider-leftLabel .ui-rangeSlider-label-inner').text());
        var entryFeeEnd = parseInt(jQuery('.f-entryfee .ui-rangeSlider-rightLabel .ui-rangeSlider-label-inner').text());
        var startTime = jQuery('.f-startTime .f-checked input').val();
        var html = aLeague = '';
        if(this.aLeagues != 'null')
        {
            var aLeagues = jQuery.parseJSON(this.aLeagues);
            var minTime = 0;
            for(var i = 0; i < aLeagues.length; i++)
            {
                aLeague = aLeagues[i];
                //filter keyword
                if(keyword == '' || aLeague.name.search(new RegExp(keyword,'i')) > -1)
                {
                    //filter sport
                    if((typeof org == typeof undefined) || org == '' || (org == aLeague.organization))
                    {
                        //filter starttime
                        if((startTime == 'today' && aLeague.today == true) || 
                           ((startTime == 'next' && aLeague.today == false)) || 
                            startTime == 'all')
                        {
                            //filter type
                            if((contestType == 'headtohead' && aLeague.size == 2) || 
                               (contestType == 'league' && aLeague.size > 2) ||
                                contestType == 'all')
                            {
                                //filter size
                                if((aLeague.size == parseInt(leagueSize[0])) ||
                                   (aLeague.size >= parseInt(leagueSize[0]) && aLeague.size <= parseInt(leagueSize[1])) ||
                                   (leagueSize[0].indexOf('+') !== -1 && aLeague.size >= parseInt(leagueSize[0]))||
                                   leagueSize[0] == 'all')
                                {
                                    //filter entry fee
                                    if(aLeague.entry_fee >= entryFeeStart && 
                                       aLeague.entry_fee <= entryFeeEnd)
                                    {
                                        var enterLable = 'Enter';
                                        if(aLeague.enter)
                                        {
                                            enterLable = 'Edit'
                                        }
                                        
                                        //find min time
                                        if(minTime == 0 || minTime > aLeague.startTimeStamp)
                                        {
                                            minTime = aLeague.startTimeStamp;
                                        }
                                        
                                        //image
                                        var htmlImage = '';
                                        if(aLeague.icon != '')
                                        {
                                            htmlImage = '<img src="' + aLeague.icon + '" style="width:16px" class="f-nhl">';
                                        }
                    
                                        html +=   
                                            '<tr class="f-lobbyitem">\n\
                                                <td class="f-title">\n\
                                                    ' + htmlImage + '\n\
                                                    <a href="#" class="f-title-link" onclick="return jQuery.playerdraft.ruleScoring(' + aLeague.leagueID + ', \'' + quoteEncoding(aLeague.name) + '\', \'' + aLeague.entry_fee + '\', \'' + aLeague.salary_remaining + '\')">' + aLeague.name + '\n\
                                                        <span class="f-icon1"></span><span class="f-icon2"></span>\n\
                                                        <span class="f-icon3"></span>\n\
                                                    </a>\n\
                                                </td>\n\
                                                <td class="f-entries">' + aLeague.gameType + '</td>\n\
                                                <td class="f-entries">\n\
                                                    <a href="#" onclick="return jQuery.playerdraft.ruleScoring(' + aLeague.leagueID + ', \'' + quoteEncoding(aLeague.name) + '\', \'' + aLeague.entry_fee + '\', \'' + aLeague.salary_remaining + '\', 2)">' + aLeague.entries + '</a>\n\
													/' + aLeague.size + '\n\
                                                </td>\n\
                                                <td class="f-entryfee"><div>$' + aLeague.entry_fee + '</div></td>\n\
                                                <td class="f-prizes breakdown">\n\
                                                    <div onclick="return jQuery.playerdraft.ruleScoring(' + aLeague.leagueID + ', \'' + quoteEncoding(aLeague.name) + '\', \'' + aLeague.entry_fee + '\', \'' + aLeague.salary_remaining + '\', 3)">\n\
                                                        $' + aLeague.prizes + '\n\
                                                    </div>\n\
                                                </td>\n\
                                                <td class="f-startTime"><div>' + aLeague.startDate + '</div></td>\n\
                                                <td class="f-entry">\n\
                                                    <div>\n\
                                                        <a href="' + jQuery('#submitUrl').val() + aLeague.leagueID + '" class="f-button f-primary f-tiny">' + enterLable + '</a>\n\
                                                    </div>\n\
                                                </td>\n\
                                            </tr>';
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //set countdown
            if(minTime > 0)
            {
                clearCountDown();
                getCountdown("lobbyCountdown", true, minTime);
                jQuery('#contestCountdown').show();
            }
            else 
            {
                jQuery('#contestCountdown').hide();
            }
        }
        if(html == '')
        {
            jQuery('.f-empty-view').show();
            jQuery('#wrapContest').hide();
            jQuery("#contestCountdown").hide();
        }
        else 
        {
            jQuery('.f-empty-view').hide();
            jQuery('#wrapContest').show();
            jQuery("#contestCountdown").show();
        }
        jQuery('#lobbyContent tbody').empty().append(html);
        jQuery("#lobbyContent").tablesorter();
        jQuery("#lobbyContent").trigger("updateAll");
    },
    
    search: function()
    {
        jQuery.lobby.showLobbyPage();
        var item = jQuery('#f-foo');
        if(item.find('.f-search-input').val() != '')
        {
            item.find('.f-search-reset').css('display', 'inline');
        }
        else 
        {
            item.find('.f-search-reset').hide();
        }
    }
}

jQuery(window).load(function(){
    jQuery('#f-foo')[0].reset();
    jQuery.lobby.loadLobbyPage();
})

setInterval(function() { jQuery.lobby.loadLobbyPage() }, 60000);

jQuery(document).on('click', '.f-filter ul li input', function(){
    switch(jQuery(this).attr('data-filter-type'))
    {
        case 'sport':
            jQuery('.f-sport label').removeClass('f-checked');
            jQuery(this).parents('label').addClass('f-checked');
            break;
        case 'type':
            jQuery('.f-type label').removeClass('f-checked');
            jQuery(this).parents('label').addClass('f-checked');
            if(jQuery(this).val() == 'league')
            {
                jQuery('.f-filter .f-size').show();
            }
            else 
            {
                jQuery('.f-filter .f-size').hide();
                jQuery('.f-filter .f-size label:first li input').trigger('click');
            }
            break;
        case 'size':
            jQuery('.f-filter .f-size label').removeClass('f-checked');
            jQuery(this).parents('label').addClass('f-checked');
            break;
        case 'start':
            jQuery('.f-startTime label').removeClass('f-checked');
            jQuery(this).parents('label').addClass('f-checked');
            break;
    }
    jQuery.lobby.showLobbyPage();
})

jQuery(document).on('click', '.f-headers th', function(){
    jQuery.lobby.doSort(jQuery(this));
})

jQuery(document).on('keyup', '#f-foo .f-search-input', function(){
    jQuery.lobby.search();
})

jQuery(document).on('click', '#f-foo .f-search-reset', function(){
    jQuery('#f-foo .f-search-input').val('');
    jQuery.lobby.search();
})

jQuery(function() {
    var prices_array = [0, 1, 2, 5, 10, 25, 50, 100, 250, 500, 1000, 10000];
    jQuery(".ui-rangeSlider-leftLabel .ui-rangeSlider-label-value").empty().append('Free');
    jQuery(".ui-rangeSlider-leftLabel .ui-rangeSlider-label-inner").empty().append(prices_array[0]);
    jQuery(".ui-rangeSlider-rightLabel .ui-rangeSlider-label-value").empty().append('$' + parsePrize(prices_array[prices_array.length - 1]));
    jQuery(".ui-rangeSlider-rightLabel .ui-rangeSlider-label-inner").empty().append(prices_array[prices_array.length - 1]);
    jQuery('#rangeSlider').slider({ 
        min:0, 
        max:prices_array.length - 1, 
        step: 1,
        values: [0, prices_array.length - 1],
        slide: function( event, ui ) {
            var from = 'Free';
            var to = '$' + parsePrize(prices_array[ui.values[1]]);
            if(prices_array[ui.values[0]] > 0)
            {
                from = '$' + parsePrize(prices_array[ui.values[0]]);
            }

            jQuery(".ui-rangeSlider-leftLabel .ui-rangeSlider-label-value").empty().append(from);
            jQuery(".ui-rangeSlider-leftLabel .ui-rangeSlider-label-inner").empty().append(prices_array[ui.values[0]]);
            jQuery(".ui-rangeSlider-rightLabel .ui-rangeSlider-label-value").empty().append(to);
            jQuery(".ui-rangeSlider-rightLabel .ui-rangeSlider-label-inner").empty().append(prices_array[ui.values[1]]);
            jQuery.lobby.showLobbyPage();
        }
    });
    function parsePrize(prize)
    {
        if(prize >= 1000)
        {
            prize = (prize / 1000) + 'K';
        }
        return prize;
    }
});

function quoteEncoding(str)
{
    str = str.replace("&#39;", "'");
    str = str.replace(/'/g, "\\'");
    return str;
}