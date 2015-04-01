jQuery(window).load(function(){
    jQuery.fight.setData(jQuery('#sportData').val(), jQuery('#positionData').val(), jQuery('#lineupData').val());
    //jQuery.fight.loadSport(jQuery('#selType').val());
    //jQuery.fight.loadOrgsBySport(jQuery('#selOrg').val());
    jQuery.fight.displayType();
    jQuery.fight.loadPosition();
    jQuery.fight.loadFightersOrTeams()
    jQuery.fight.fixFightIndexs();
    
})

jQuery(function() {
    jQuery("#startDate").datepicker({
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected) {
            jQuery("#cutDate").datepicker("option","minDate", selected)
        }
    });
    jQuery("#cutDate").datepicker({ 
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected) {
           jQuery("#startDate").datepicker("option","maxDate", selected)
        }
    }); 
    jQuery(".fightDatePicker").datepicker({
        dateFormat: 'yy-mm-dd'
    });
});