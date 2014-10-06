<?php if($errorMessage):?>
    <br><br><br><div id="error_message" class="error_message"><?=$errorMessage;?></div>
<?php else:?>
    <?=$leagueheader;?>
    <div id="league_history" >
        <input type="hidden" id="importleagueID" value="<?=$leagueID;?>" />
        <div class="leaguesHeader"></div>
        <br>
        <div class="bootstrap_grid"></div>
        <br>
        <div class="results_caption">Results for:  <span class="competitor_name competitor_name_2"><?=__('Please select a user');?></span></div>
        <br>
        <div class="results_grid"></div>
    </div>
    <br><br>

    <!-- Modal -->
    <div id="dlgInviteFriend" style="display: none;z-index: 99" title="Invite your friends to play against you">
        <form name="inviteForm" id="inviteForm">
            <input type="hidden" name="val[importleagueID]" value="<?=$leagueID;?>" />
            <div>
                <label><?=__('Attach a message');?></label>
                <br>
                <textarea rows='3' cols='58' name='val[message_boxinvite]'></textarea>
            </div>
            <br>
            <table class="table table-responsive">
                <tr>
                    <td>
                        <div>
                            <label><?=__('Who would you like to invite?');?></label>
                            <div>
                                <input type="text" name="val[emails][]" placeholder="Enter email address" style="width:200px">
                            </div>
                            <div>
                                <input type="text" name="val[emails][]" placeholder="Enter email address" style="width:200px">
                            </div>
                            <div>
                                <input type="text" name="val[emails][]" placeholder="Enter email address" style="width:200px">
                            </div>
                            <div>
                                <input type="text" name="val[emails][]" placeholder="Enter email address" style="width:200px">
                            </div>
                            <div>
                                <input type="text" name="val[emails][]" placeholder="Enter email address" style="width:200px">
                            </div>
                        </div>
                        <br>
                        <div>
                            <input type='submit' class='button' value='Send Invites' onclick='sendInvite(); return false;'>
                            <span class="inviting" style="display: none"><?=__('Sending...');?></span>
                        </div>
                    </td>
                    <td>
                        <label><?=__('Select your Friends');?></label>
                        <div class="Content list_of_users_to_invite" style="width:450px;height:250px;overflow:auto;">
                            <input type="button" onclick="checkAll()" value="Select All" class="button">
                            <input type="button" onclick="checkNone()" value="Select None" class="button">
                            <br/>
                            <?=$myString;?>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <script type="text/javascript">
        var leagues_ranking = new leaguesClass();
        var elemToShow = "<?=$elem_to_show;?>";
        var leagueID = "<?=$leagueID;?>";
        var showInvite = "<?=$showInvite;?>";
        var isLive = "<?=$isLive;?>";
        var myData = "";
        jQuery("#" + elemToShow).show();

        if (isLive ==1 &&  leagueID )
        {
            enterLeagueHistory(leagueID,showInvite,1);
            setInterval(function() { enterLeagueHistory(leagueID,showInvite,1)},60000);
        }

        <?php if($allowMinutes == 1):?>
        leagues_ranking.allowMinutes = true;
        <?php else:;?>
        leagues_ranking.allowMinutes = false;
        <?php endif;?>

        if ( leagueID && isLive != 1)
        {
            if ( showInvite)
            {
                inviteFriends();
            }
            enterLeagueHistory(leagueID, showInvite);
        }
    </script>
<?php endif;?>