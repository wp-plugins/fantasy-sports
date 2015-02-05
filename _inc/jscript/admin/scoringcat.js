jQuery.scoringcat = 
{
    setData: function()
    {
        this.aScoringTypes = jQuery('#scoringTypes').val();
    },
    
    loadScoringType: function()
    {
        var aScoringTypes = jQuery.parseJSON(this.aScoringTypes);
        var org = jQuery('#org :selected').text();
        var selectType = jQuery('#selectType').val();
        var html = '';
        if(aScoringTypes != null)
        {
            var aScoringType = '';
            var select = '';
            for(var i in aScoringTypes)
            {
                if(i.trim() == org.trim())
                {
                    if(aScoringTypes[i].length > 0)
                    {
                        for(var j in aScoringTypes[i])
                        {
                            aScoringType = aScoringTypes[i][j];
                            select = '';
                            if(selectType == aScoringType)
                            {
                                select = 'selected="true"';
                            }
                            html += '<option ' + select + ' value="' + aScoringType + '">' + aScoringType + '</option>';
                        }
                    }
                    else 
                    {
                        html += '<option value="">--None--</option>';
                    }
                }
            }
        }
        if(html == '')
        {
            html += '<option value="">--Please select organization first--</option>';
        }
        jQuery('#htmlScoringTypes').empty().append(html);
    },
}

jQuery(window).load(function(){
    jQuery.scoringcat.setData();
    jQuery.scoringcat.loadScoringType();
})