jQuery(window).load(function(){
    jQuery.playerdraft.loadPlayers();
    jQuery.playerdraft.calculateAvgPerPlayer();
    jQuery.playerdraft.editLineup();
})

jQuery(document).ready(function(){
    jQuery(".table-sorting").click(function() {
         jQuery.playerdraft.doSort(jQuery(this));
    });
})

jQuery(document).on('keyup', '#player-search', function(){
    jQuery.playerdraft.searchPlayers();
})