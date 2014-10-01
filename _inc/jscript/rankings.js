function cancelBubble(e)
{
    if (!e)
      e = window.event;

    if (e.cancelBubble)
      e.cancelBubble = true;
    else
      e.stopPropagation();
}
function setValueN(id,x,value,compare)
{
        if (x.value == compare)
        {
                x.value = value;
                if(compare=="")
                        jQuery('#' + id + ' input[name="'+x.name + '"]').css("color", "#666");
                else
                        jQuery('#' + id + ' input[name="'+x.name + '"]').css("color", "black");
        }
}
function sendInvite()
{
    jQuery('#inviteForm').find('.inviting').show();
    var dataSring = jQuery('#inviteForm').serialize();
    jQuery.post(ajaxurl, 'action=sendInviteFriend&' + dataSring, function(result) {
        var data = JSON.parse(result);
        if(data.notice)
        {
            alert(data.notice);
        }
        else
        {
            alert(data.message);
            jQuery("#dlgInviteFriend").dialog('close');
            jQuery('#inviteForm').find('.inviting').hide();
        }
    })
    return false;
}

jQuery(document).on('submit', "#inviteForm", function(e){
    e.preventDefault();
});

function checkAll()
{
    jQuery("input[name='val[friend_ids][]']").attr('checked', true);
}

function checkNone()
{
    jQuery("input[name='val[friend_ids][]']").removeAttr('checked');
}

function toggleAll(element)
{
	//var form = document.forms.openinviter, z = 0;
	var z = 0;
	var form = jQuery('#popup_block_show form[name="openinviter"]')[0];
	for(z=0; z<form.length;z++)
	{
		if(form[z].type == 'checkbox')
			form[z].checked = element.checked;
	}
}
function toggleAllFriends(element)
{
	jQuery('#popup_block_show .list_of_users_to_invite input[name="username"]').each(function(){
	//	jQuery(this).attr('checked', element.checked);
		jQuery(this).iCheck('check');
	});
}
function importFriends()
{
	showPopup('inviterForm', 500);
}
function inviteFriends()
{
	//jQuery("#myModal").show();
    var dialog = jQuery("#dlgInviteFriend").dialog({
        maxHeight: 600,
        width:800,
        minWidth:600,
        modal:true,
        open: function() {
            jQuery('.ui-widget-overlay').addClass('custom-overlay');
        }
    });
}
function selectRankingsRowCheck(userID)
{
	var leagues_ranking = new leaguesClass();
	leagues_ranking.lastSelectedUserID = userID;
	jQuery("#leagues_history_ranking_grid table tr").removeClass('trSelected');
	jQuery("#rowuser_" + userID).addClass('trSelected');
	leagues_ranking.putCompetitorIntoRankingGrid(userID, 'click');
}

function selectHistoryRankingGridRow(celDiv, rowID)
{
	jQuery(celDiv).click
	(
		function ()
		{
			var userID = rowID.substring(5);	// id="rowID_<id>";
			leagues_ranking.lastSelectedUserID = userID;
			jQuery("#league_history .radio_rank_flexigrid_row_" + userID).attr("checked", "checked");
			leagues_ranking.putCompetitorIntoRankingGrid(userID, 'click');
		}
	);
}
function setprovider_box(provider)
{
	jQuery('#popup_block_show select[name="provider_box"]').val(provider);
}
function getOIAuth()
{
	//jQuery('a.close_popup, #fade').click();
	var username = encodeURIComponent(jQuery('#popup_block_show input[name="emailauth_username"]').val());
	var password = encodeURIComponent(jQuery('#popup_block_show input[name="emailauth_password"]').val());
	var provider = encodeURIComponent(jQuery('#popup_block_show select[name="provider_box"]').val());
	var importleagueID = jQuery('#importleagueID').val();
	if ( importleagueID == "" )
	{
		alert("Sorry, the system detected a spam attempt. Please contact support");
		return false;
	}
	if ( username == "" || password == "" )
	{
		alert("Please fill out all fields");
		//jQuery('#emailauth_username').focus();
		//document.getElementById('emailauth_username').focus();
		return false;
	}

	//alert(provider);

	jQuery.ajax({
		type: "GET",
		url: "/api/authOI/" + username + "/" + password,
		dataType: 'json',
		data: "provider_box="+provider+"&importleagueID="+importleagueID + "&phpfoxsess=1",
		cache: false,
		success: function(msg){
			//Login failed
			if ( msg )
			{
				//if ( msg.message == "Login failed" )
				if ( msg.message )
				{
					alert(msg.message);
				}
				else
				{
					//jQuery('a.close_popup, #fade').click();
					closePopup(true);
					var html = jQuery('#contactDiv form[name="openinviter"]').html();
					jQuery('#contactDiv form[name="openinviter"]').html(html + '<div style="height:500px;overflow-y:auto;overflow-x:none">' + msg.contacts + '<\/div>');
					showPopup('contactDiv', 600);
				}
			}
			else
			{
				alert('Error occured.');
			}
		}
	});

	return false;
}

function updateLiveGamesDynamic_ranking()
{
	//jQuery("#leagues_history_ranking_grid").flexReload();
	leagues_ranking.enterLeagueHistory(leagues_ranking.currentLeagueID, leagues_ranking.currentPoolID, true);
}
