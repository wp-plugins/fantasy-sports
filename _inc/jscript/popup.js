function closePopup(atOnce)
{
	if ( atOnce )
	{
		$('#fade, a.close_popup, #popup_block_show').remove();
	}
	else
	{
		$('#fade, #popup_block_show').fadeOut(function() {
			$('#fade, a.close_popup, #popup_block_show').remove();
		});
	}
}
function showPopup(popID, popWidth)
{
	if ( !popWidth )
	{
		popWidth = 700;
	}
	$('body').append('<div id="popup_block_show">' + $('#' + popID).html() + '</div>');
	$('#popup_block_show').fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#" class="close_popup"><img src="http://mmavictor.com/module/mmavictor/static/image/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');
	//Define margin for center alignment (vertical   horizontal) - we add 80px to the height/width to accomodate for the padding  and border width defined in the css
	var popMargTop = ($('#popup_block_show').height() + 80) / 2;
	var popMargLeft = ($('#popup_block_show').width() + 80) / 2;
	//Apply Margin to Popup
	$('#popup_block_show').css({
		'margin-top' : -popMargTop,
		'margin-left' : -popMargLeft
	});
	//Fade in Background
	$('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies
}


$Behavior.Popup = function(){
	//When you click on a link with class of poplight and the href starts with a #
	//        //Close Popups and Fade Layer
	$('a.close_popup, #fade').live('click', function() { //When clicking on the close or fade layer...
                $('#fade, #popup_block_show').fadeOut(function() {
                        $('#fade, a.close_popup, #popup_block_show').remove();  //fade them both out
                });
                return false;
        });
};
