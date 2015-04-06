<table width="100%" border="0" class="table table-condensed table-responsive">
    <tr>
        <td valign="top">
            <h3>Contest ID: <?=$aLeague['leagueID'];?> - <?=$aLeague['name'];?></h3>
        </td>
        <td valign="top" align="right"></td>
    </tr>
    <tr class="info">
        <td colspan="2">
            <table width="100%" border="0" class="table table-responsive">
                <tbody>
                    <tr class="info">
                        <td colspan="2">
                            <br>&nbsp;&nbsp;<b><?=__('Prize structure', FV_DOMAIN)?>:</b> <?=$aLeague['prize_structure'];?>
                            <br>&nbsp;&nbsp;<b><?=__('Sport', FV_DOMAIN)?>:</b> <?=$aPool['sport_name'];?>
                            <br>&nbsp;&nbsp;<b><?=__('Game Type', FV_DOMAIN)?>:</b> <?=$aLeague['gameType'];?>
                            <br>&nbsp;&nbsp;<b><?=__('Start', FV_DOMAIN)?>:</b> <?=$aLeague['startDate'];?>
                            <br>&nbsp;&nbsp;<b><?=__('Ends', FV_DOMAIN)?>:</b> Prizes paid next day
                            <br>&nbsp;&nbsp;<b><?=__('Creator', FV_DOMAIN)?>:</b> <?=$creator->data->user_login;?>
                            <br>&nbsp;&nbsp;<b><?=__('Players', FV_DOMAIN)?>:</b> <?=$aLeague['size'];?> player game, <?=$aLeague['entries'];?> entries</td>
                        <td width="170" align="center">
                            <br>
                            <div style="height:40px;-moz-border-radius: 5px;-webkit-border-radius: 5px;border: 1px solid #000;padding: 10px;background-color: #E6E6E6;">
                                <font size="4"><b>Entry</b> $<?=$aLeague['entry_fee'];?></font>
                            </div>
                        </td>
                        <td style="width:15px">&nbsp;</td>
                        <td width="170" align="center">
                            <br>
                            <div style=" height:40px;-moz-border-radius: 5px;-webkit-border-radius: 5px;border: 1px solid #000;padding: 10px;background-color: #E6E6E6;">
                                <font size="4"><b>Prizes</b> $<?=$aLeague['prizes'];?></font>
                            </div>
                        </td>
                        <td style="width:15px">&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<input type="hidden" id="dataResult" />
<div id="league_history" >
    <input type="hidden" id="importleagueID" value="<?=$leagueID;?>" />
    <div class="leaguesHeader"></div>
    <br>
    <div id="listPlayers"></div>
    <br>
    <div class="results_caption"><?=__('Results for')?>:  <span class="competitor_name competitor_name_2"><?=__('Please select a user', FV_DOMAIN);?></span></div>
    <br>
    <div id="listFixtures"></div>
</div>
<br><br>

<!-- Modal -->
<div id="dlgInviteFriend" style="display: none;z-index: 99" title="Invite your friends to play against you">
    <form name="inviteForm" id="inviteForm">
        <input type="hidden" name="val[importleagueID]" value="<?=$leagueID;?>" />
        <div>
            <label><?=__('Attach a message', FV_DOMAIN);?></label>
            <br>
            <textarea rows='3' cols='58' name='val[message_boxinvite]'></textarea>
        </div>
        <br>
        <table class="table table-responsive">
            <tr>
                <td>
                    <div>
                        <label><?=__('Who would you like to invite?', FV_DOMAIN);?></label>
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
                        <input type='submit' class='button' value='Send Invites' onclick='jQuery.ranking.sendInvite(); return false;'>
                        <span class="inviting" style="display: none"><?=__('Sending...', FV_DOMAIN);?></span>
                    </div>
                </td>
                <td>
                    <label><?=__('Select your Friends', FV_DOMAIN);?></label>
                    <div class="Content list_of_users_to_invite" style="width:450px;height:250px;overflow:auto;">
                        <input type="button" onclick="jQuery.ranking.checkAll()" value="Select All" class="button">
                        <input type="button" onclick="jQuery.ranking.checkNone()" value="Select None" class="button">
                        <br/>
                        <?php if($aFriends != null):?>
                        <?php foreach($aFriends as $buddy):?>
                            <label>
                                <input type="checkbox" checked name="val[friend_ids][]" value="<?=$buddy["ID"];?>">
                                <?=htmlspecialchars($buddy["full_name"]);?>
                            </label><br>
                        <?php endforeach;?>
                        <?php endif;?>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>
<script type="text/javascript">
    var isLive = <?=$aLeague['is_live'];?>;
    var status = '<?=$aPool['status'];?>';
    var showInviteFriends = '<?=$showInviteFriends;?>';
    if (isLive == 1 && status == 'NEW')
    {
        jQuery.ranking.enterLeagueHistory();
        setInterval(function(){ 
            jQuery.ranking.enterLeagueHistory()
        },10000);
    }
    else 
    {
        jQuery.ranking.enterLeagueHistory();
    }
    if(showInviteFriends)
    {
        jQuery.ranking.inviteFriends();
    }
</script>