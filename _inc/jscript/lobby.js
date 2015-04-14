jQuery(window).load(function(){
    jQuery.league.lobby();
})
setInterval(function() { jQuery.league.lobby() }, 60000);