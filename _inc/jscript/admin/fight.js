jQuery.fight =
{
    setData : function(aSports, aPositions, lineup)
    {
        this.aSports = aSports;
        this.aPositions = aPositions;
        this.lineup = lineup;
        this.only_playerdraft = 0;
    },
    
    loadSport: function(sel)
    {
        var aSports = jQuery.parseJSON(this.aSports);
        var result = '<select id="poolSport" class="sport" name="val[type]" onchange="jQuery.fight.loadOrgsBySport();jQuery.fight.displayType();">';
        for(var i = 0; i < aSports.length; i++)
        {
            var aSport = aSports[i];
            var select = '';
            if(aSport.name == sel)
            {
                select = 'selected="true"';
            }
            result += '<option ' + select + ' value="' + aSport.name + '">' + aSport.name + '</option>';
        }
        result += '</select>';
        jQuery('#sportResult').empty().append(result);
    },
    
    loadPosition: function()
    {
        var aPositions = jQuery.parseJSON(this.aPositions);
        var data = '';
        if(this.lineup != '')
        {
            data = jQuery.parseJSON(this.lineup);
        }
        var org_id = jQuery('#poolOrgs').val();
        var result = '<table>';
        var hasPosition = false;
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
                                <td><input type="text" name="val[lineup][' + aPosition.id + '][total]" value="' + total + '" /></td>\n\
                                <td><input type="checkbox" name="val[lineup][' + aPosition.id + '][enable]" ' + checked + ' value="1" /></td>\n\
                            </tr>';
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
    
    addFight : function(oObj)
    {
        var fightItem = jQuery(oObj).parents('.fight_container');
        var cloneItem = fightItem.clone();
        cloneItem.find('select option').removeAttr('selected');
        cloneItem.find('input[type=text]').val('');
        cloneItem.find('input[type=checkbox]').removeAttr('checked');
        cloneItem.find('input[data-name=fightID]').val('');
        fightItem.after(cloneItem);
        cloneItem.find('.fightDatePicker').removeClass('hasDatepicker').removeAttr('id');
        cloneItem.find(".fightDatePicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });
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
        //check only playerdraft
        this.only_playerdraft = jQuery('option:selected', "#poolOrgs").attr('only_playerdraft');
        if(this.only_playerdraft == 1)
        {
            jQuery('.exclude_fixture').hide();
            jQuery('.fight_container input').attr('disabled', 'true');
            jQuery('.fight_container select').attr('disabled', 'true');
        }
        else 
        {
            jQuery('.exclude_fixture').show();
            jQuery('.fight_container input').removeAttr('disabled');
            jQuery('.fight_container select').removeAttr('disabled');
        }
        
        var is_team = jQuery('option:selected', "#poolOrgs").attr('is_team');
        if(is_team == 0)
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
        
        var is_round = jQuery('option:selected', "#poolOrgs").attr('is_round');
        if(is_round == 1)
        {
            jQuery('.for_round').show();
        }
        else
        {
            jQuery('.for_round').hide();
        }
        
        return false;
    },
    
    loadOrgsBySport: function(sel){
        var sport = jQuery('#poolSport').val();
        var sel = jQuery('#selOrgs').val();
        
        var aSports = jQuery.parseJSON(this.aSports);
        var result = '<select id="poolOrgs" onchange="jQuery.fight.loadFightersOrTeams()" name="val[organization]">';
        for(var i = 0; i < aSports.length; i++)
        {
            var aSport = aSports[i];
            if(aSport.name == sport)
            {
                for(var j = 0; j < aSport.child.length; j++)
                {
                    var org = aSport.child[j];
                    var select = '';
                    if(org.id == sel)
                    {
                        select = 'selected="true"';
                    }
                    result += '<option ' + select + ' value="' + org.organizationID + '">' + org.description + '</option>';
                }
            }
        }
        result += '</select>';
        jQuery('#orgResult').empty().append(result);
        this.loadFightersOrTeams();
        this.loadPosition();
    },
    
    loadFightersOrTeams: function(){
        if(this.only_playerdraft == 0)
        {
            var orgs = jQuery('#poolOrgs').val();
            var is_team = jQuery('option:selected', "#poolOrgs").attr('is_team');
            if(is_team == 0)
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
        if(confirm('Are you sure?'))
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
                        jQuery(oObj).parents('tr').find('.column-result a').hide();
                        jQuery(oObj).parents('tr').find('.column-playerdraft_result a').hide();
                        jQuery(oObj).parents('tr').find('.column-edit a').hide();
                        jQuery(oObj).parents('tr').find('.btn-reverse').show();
                    }
                }
            })
        }
        else 
        {
            jQuery(oObj).val(curValue);
        }
    },
    
    ////////////////////////v2////////////////////////
    viewPlayerDraftResult: function(iPoolID, sTitle){
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
            action: 'viewPlayerDraftResult',
            iPoolID: iPoolID,
        };
        jQuery.post(ajaxurl, data, function(result){
            result = jQuery.parseJSON(result);
            jQuery.fight.loadPlayerDraftResult(result.pool, result.fights, result.rounds);
            jQuery.fight.loadPlayerPoints(result.scoring_cat, 1);
            jQuery("#resultDialog").dialog({
                buttons: {
                    "Add": function() {
                        jQuery.fight.updatePlayerDraftResult();
                    },
                    "Close": function() {
                        dialog.dialog( "close" );
                    }
                }
            });
        });
        return false;
    },
    
    loadPlayerDraftResult: function(aPool, aFights, aRounds)
    {
        this.aFights = aFights;
        this.aRounds = aRounds;
        this.aPool = aPool;
        var html = '';
        
        //fight
        var htmlCbFight = '<select name="fightID" id="cbFight" onchange="jQuery.fight.loadPlayerPoints(null, 1)">';
        if(aFights != null && aFights.length > 0)
        {
            for(var i in aFights)
            {
                var aFight = aFights[i];
                htmlCbFight += '<option value="' + aFight.fightID + '">' + aFight.name + '</option>';
            }
        }
        htmlCbFight += '</select>';
        var htmlFight = '';
        if(aPool.only_playerdraft == 0)
        {
            htmlFight  = 
                '<div class="table">\n\
                    <div class="table_left">Fight: </div>\n\
                    <div class="table_right">\n\
                        ' + htmlCbFight + '\n\
                    </div>\n\
                    <div class="clear"></div>\n\
                </div>';
            html = '<div id="resultMessage"></div>\n\
                        <form id="formResult">\n\
                        <input type="hidden" name="poolID" value="' + aPool.poolID + '" />\n\
                        ' + htmlFight + '\n\
                        <div class="table">\n\
                            <div class="table_left" style="width:100px;">Scoring: </div>\n\
                            <div class="table_right" id="resultScoring" style="margin-left:100px;">\n\
                            </div>\n\
                            <div class="clear"></div>\n\
                        </div>\n\
                    </div>';
        }
        
        //round
        var htmlCbRound = '<select name="roundID" id="cbRound" onchange="jQuery.fight.loadPlayerPoints(null, 1)">';
        if(aRounds != null && aRounds.length > 0)
        {
            for(var i in aRounds)
            {
                var aRound = aRounds[i];
                htmlCbRound += '<option value="' + aRound.id + '">' + aRound.name + '</option>';
            }
        }
        htmlCbRound += '</select>';
        var htmlRound = '';
        if(aPool.is_round == 1)
        {
            htmlRound  = 
                '<div class="table">\n\
                    <div class="table_left">Round: </div>\n\
                    <div class="table_right">\n\
                        ' + htmlCbRound + '\n\
                    </div>\n\
                    <div class="clear"></div>\n\
                </div>';
            html = '<div id="resultMessage"></div>\n\
                        <form id="formResult">\n\
                        <input type="hidden" name="poolID" value="' + aPool.poolID + '" />\n\
                        ' + htmlRound + '\n\
                        <div class="table">\n\
                            <div class="table_left" style="width:100px;">Scoring: </div>\n\
                            <div class="table_right" id="resultScoring" style="margin-left:100px;">\n\
                            </div>\n\
                            <div class="clear"></div>\n\
                        </div>\n\
                    </div>';
        }
       
        
        jQuery("#resultDialog").empty().append(html);
    },
    
    loadPlayersResult: function()
    {
        var aPlayers = this.aPlayers;
        var aFights = this.aFights;
        var aPool = this.aPool;
        var fightID = jQuery('#cbFight').val();
        var teamID1 = ''; 
        var teamID2 = '';
        if(aFights != null && aFights.length > 0)
        {
            for(var i in aFights)
            {
                if(aFights[i].fightID == fightID)
                {
                    teamID1 = aFights[i].fighterID1;
                    teamID2 = aFights[i].fighterID2;
                }
            }
        }
        
        var result = '<select name="playerID" id="cbPlayers" onchange="jQuery.fight.loadPlayerPoints()">';
        for(var i in aPlayers)
        {
            var aPlayer = aPlayers[i];
            if(aPool.only_playerdraft == 0 && (aPlayer.team_id == teamID1 || aPlayer.team_id == teamID2))
            {
                result += '<option value="' + aPlayer.id + '">' + aPlayer.name + '</option>';
            }
            else if(aPool.only_playerdraft == 1)
            {
                result += '<option value="' + aPlayer.id + '">' + aPlayer.name + '</option>';
            }
        }
        result += '</select>';
        jQuery('#resultPlayer').empty().append(result);
    },
    
    loadPlayerPoints: function(resultScoringCat, page)
    {
        if(typeof resultScoringCat != 'undefined' && resultScoringCat != null)
        {
            this.resultScoringCat = resultScoringCat;
        }
        var fightID = jQuery('#cbFight').val();
        var roundID = jQuery('#cbRound').val();
        var playerID = jQuery('#cbPlayers').val();
        var aPool = this.aPool;
        var aScoringCats = this.resultScoringCat;
        var data = 'action=loadPlayerPoints&poolID=' + aPool.poolID + '&fightID=' + fightID + '&roundID=' + roundID + '&playerID=' + playerID + '&page=' + page;
        jQuery.post(ajaxurl, data, function(result){
            result = jQuery.parseJSON(result);
            var aPlayers = result.players;
            var paging = result.paging;
            var i, j, aPlayer, aScoringCat, aPlayerScoring, point;
            var html = '<table>\n\
                <tr>\n\
                    <td style="width:200px"></td>';
            if(aScoringCats != null)
            {
                for(i in aScoringCats)
                {
                    aScoringCat = aScoringCats[i];
                    html += '<td style="width:35px;text-align:center">' + aScoringCat.name + '</td>';
                }
            }
            html +=  '<tr>';
            
            if(aPlayers != null)
            {
                for(i in aPlayers)
                {
                    aPlayer = aPlayers[i];
                    aPlayerScoring = aPlayer.scorings;
                    html += 
                        '<tr>\n\
                            <td>\n\
                                ' + aPlayer.name + '\n\
                                <input type="hidden" name="playerID[]" value="' + aPlayer.id + '" />\n\
                            </td>';
                    for(j in aScoringCats)
                    {
                        aScoringCat = aScoringCats[j];
                        point = jQuery.fight.parsePlayerScoring(aScoringCat.id, aPlayerScoring);
                        html += '<td><input type="text" style="width:100%" name="scoring_category_id[' + aPlayer.id + '][' + aScoringCat.id + ']" value="' + point + '" /></td>';
                    }
                    html += '</tr>';
                }
            }
            html += '</table>';
            if(paging != null)
            {
                html += '<div class="tablenav bottom">\n\
                            <div class="tablenav-pages">\n\
                                <span class="pagination-links">' + paging + '</span>\n\
                            </div>\n\
                        </div>';
            }
            jQuery('#resultScoring').empty().append(html);
        })
    },
    
    parsePlayerScoring: function(scoring_category_id, aPlayerScorings)
    {
        if(aPlayerScorings != null)
        {
            var i;
            for(i in aPlayerScorings)
            {
                if(aPlayerScorings[i].scoring_category_id == scoring_category_id)
                {
                    return aPlayerScorings[i].points;
                }
            }
        }
        return 0;
    },
    
    updatePlayerDraftResult: function()
    {
        var data = 'action=updatePlayerDraftResult&' + jQuery('#formResult').serialize();
        jQuery.post(ajaxurl, data, function(result){
            alert(result);
        })
    },
    
    reverseResult: function(poolID, oObj)
    {
        if(confirm("Are you sure?"))
        {
            jQuery(oObj).parents('tr').find('.btn-reverse').attr('disabled', 'true');
            var data = 'action=reverseResult&poolID=' + poolID;
            jQuery.post(ajaxurl, data, function(result){
                var data = JSON.parse(result);
                if(data.notice)
                {
                    alert(data.notice);
                }
                else
                {
                    alert(data.result);
                    jQuery(oObj).parents('tr').find('.btn-reverse').removeAttr('disabled');
                    jQuery(oObj).parents('tr').find('.btn-reverse').hide();
                    jQuery(oObj).parents('tr').find('select').removeAttr('disabled').val('NEW');
                    jQuery(oObj).parents('tr').find('.column-result a').show();
                    jQuery(oObj).parents('tr').find('.column-playerdraft_result a').show();
                    jQuery(oObj).parents('tr').find('.column-edit a').show();
                }
            })
        }
    }
}


jQuery(document).on('click', '#resultScoring .page-numbers:not(current)', function(e){
    e.preventDefault();
    var href = jQuery(this).attr('href');
    var page = href.split('?paged=');
    jQuery.fight.loadPlayerPoints(null, page[1]);
})