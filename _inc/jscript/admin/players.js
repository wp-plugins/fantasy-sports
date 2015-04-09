jQuery.players = 
{
    setData: function()
    {
        this.aTeams = jQuery('#teamsData').val();
        this.aPositions = jQuery('#positionsData').val();
    },
    
    loadTeams: function()
    {
        var aTeams = jQuery.parseJSON(this.aTeams);
        var orgID = jQuery('#org').val();
        var selectTeam = jQuery('#selectTeam').val();
        var html = 
            '<select name="val[team_id]">\n\
                <option value="0">None</option>';
        if(aTeams != null)
        {
            var aTeam = '';
            var select = ''
            for(var i = 0; i < aTeams.length; i++)
            {
                aTeam = aTeams[i];
                select = '';
                if(selectTeam == aTeam.teamID)
                {
                    select = 'selected="true"';
                }
                if(aTeam.organization_id == orgID)
                {
                    html += '<option ' + select + ' value="' + aTeam.teamID + '">' + aTeam.name + '</option>';
                }
            }
        }
        html += '</select>';
        jQuery('#htmlTeams').empty().append(html);
    },
    
    loadPositions: function()
    {
        var aPositions = jQuery.parseJSON(this.aPositions);
        var orgID = jQuery('#org').val();
        var selectPosition = jQuery('#selectPosition').val();
        var html = '<select name="val[position_id]">';
        if(aPositions != null)
        {
            var aPosition = '';
            var select = ''
            for(var i = 0; i < aPositions.length; i++)
            {
                aPosition = aPositions[i];
                select = '';
                if(selectPosition == aPosition.id)
                {
                    select = 'selected="true"';
                }
                if(aPosition.org_id == orgID)
                {
                    html += '<option ' + select + ' value="' + aPosition.id + '">' + aPosition.name + '</option>';
                }
            }
        }
        html += '</select>';
        jQuery('#htmlPositions').empty().append(html);
    },
}