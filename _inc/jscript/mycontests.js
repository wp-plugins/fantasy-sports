function resultPicks(leagueID)
{
    jQuery(".leagueID").val(leagueID);
    return true;
}
function updateLiveContests()
{
    var data = {
        action: 'LiveLeagues'
    };
    jQuery.post(ajaxurl, data, function(result) {
        var data = JSON.parse(result)
        if ( data.success )
        {
            var mobile = false;
            if(window.location.href.indexOf("mobile") > -1) {
                      mobile = true;
            }

            var bootstrap_html = '<table class="table table-striped table-bordered table-responsive table-condensed"><tr>';
            bootstrap_html  +=      '<th>ID</th>';
            bootstrap_html  +=      '<th>Date</th>';
            bootstrap_html  +=      '<th>Name</th>';

            if ( ! mobile ) 
            {
                    bootstrap_html  +=      '<th>Size</th>';
                    bootstrap_html  +=      '<th>Entries</th>';
                    bootstrap_html  +=      '<th>Entry Fee</th>';
                    bootstrap_html  +=      '<th>Prizes</th>';
                    bootstrap_html  +=      '<th>Rank</th>';
            }
            bootstrap_html  +=      '<th>&nbsp;</th></tr>';

            for ( var id in data.rows )
            {
                    bootstrap_html+='<tr>';
            bootstrap_html+='<td>'+data.rows[id]["cell"][0]+'</td>';
            bootstrap_html+='<td>'+data.rows[id]["cell"][9]+'</td>';
            bootstrap_html+='<td>'+data.rows[id]["cell"][1]+'</td>';

            if ( ! mobile )
            {
                    bootstrap_html+='<td>'+data.rows[id]["cell"][5]+'</td>';
                    bootstrap_html+='<td>'+data.rows[id]["cell"][4]+'</td>';
                    bootstrap_html+='<td>'+data.rows[id]["cell"][6]+'</td>';
                    bootstrap_html+='<td>'+data.rows[id]["cell"][7]+'</td>';
                    bootstrap_html+='<td>'+data.rows[id]["cell"][8]+'</td>';
            }

            bootstrap_html+='<td>'+data.rows[id]["cell"][10]+'</td>';
                    bootstrap_html+='</tr>';
            }
            bootstrap_html+='</table>';
            jQuery("#leagues_live_games_grid").html(bootstrap_html);	
        }
    })
}
