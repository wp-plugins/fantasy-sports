jQuery(window).load(function(){
    jQuery.fight.loadOrgsBySport();
    jQuery.fight.loadFightersOrTeams();
    jQuery.fight.fixFightIndexs();
    jQuery.fight.displayType();
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
});