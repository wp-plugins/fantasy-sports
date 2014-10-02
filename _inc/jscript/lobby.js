var item = jQuery('#fanvictor_home_sidebar');
jQuery('#content').prepend(item.clone());
item.remove();

updateNewContests();
setInterval(function() { updateNewContests() }, 60000);
