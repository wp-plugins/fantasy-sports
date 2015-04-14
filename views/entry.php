<div class="contentPlugin">    <h3 id="gametitle_current"><?=$league['name'];?></h3>    <div class="f-column-12 f-row">        <div class="f-clearfix" id="league-info">            <div class="f-column-4" id="league-info-size"><?=$league['size'];?> <?=__('Player League', FV_DOMAIN);?>                <span class="f-entries f-link" href="#" onclick="return jQuery.playerdraft.dlgEntries(<?=$league['leagueID'];?>, '<?=$league['name'];?>');">                    (<?=$league['entries'];?> <?=__('entries so far', FV_DOMAIN);?>)                </span>            </div>            <div class="f-column-2" id="league-info-stake"><?=__('Entry fee:', FV_DOMAIN);?>                $<?=$league['entry_fee'];?>					            </div>            <div class="f-column-6 f-text-align-right" id="league-info-prizes">                <?=__('Prize:');?>  <span class="f-showAllPrizes clickable f-link" href="#" onclick="return jQuery.playerdraft.dlgPrize(<?=$league['leagueID'];?>, '<?=$league['name'];?>', '<?=$league['entry_fee'];?>', '<?=$aPool['salary_remaining'];?>');">                    <?=__('Show All', FV_DOMAIN);?>                </span>            </div>        </div>        <!--<div class="f-gamestatus f-gamestatus_open"><?=__('Game starts in', FV_DOMAIN);?>            <div id="countdown_1" class="f-countdown"></div>        </div>-->    </div>    <div class="f-rostertop row">        <div class="f-player">            <div class="f-opponent">                <div class="f-seatroster" id="thisroster">                    <table cellspacing="2" class="f-roster">                        <thead>                            <tr>                                <th style="font-size:12px" class="f-username" colspan="2">                                    <div class="f-button-container">                                        <a href="<?=FANVICTOR_URL_GAME.$leagueID."/?num=".$entry_number;?>" style="font-weight:normal;" class="f-button f-mini f-text-grey1"><?=__('Edit', FV_DOMAIN)?></a>                                     </div>                                    <a>                                        <div class="f-avatar f-small" style="background-image:url(<?=$user_avatar;?>)"></div>                                        <?=$current_user->display_name;?>                                    </a>                                </th>                                <th class="f-username"></th>                            </tr>                        </thead>                        <tbody>                            <?php if(!empty($aPlayers)):?>                            <?php foreach($aPlayers as $aPlayer):?>                            <tr class="f-pregame f-no-scoring">                                                                <td class="f-position">                                    <?php if($league['option_type'] != 'salary'):?>                                    <?=$aPlayer['position'];?>                                    <?php endif;?>                                </td>                                                                <td class="f-player" style="vertical-align: middle;">                                    <div class="f-name"><?=$aPlayer['name'];?></div>                                    <?php if(!$aPool['only_playerdraft']):?>                                    <div class="f-fixture">                                        <?php if($aPlayer['teamID1'] == $aPlayer['team_id']):?>                                            <strong><?=$aPlayer['team1'];?></strong> @ <?=$aPlayer['team2'];?>                                        <?php else:?>                                            <?=$aPlayer['team1'];?> @ <strong><?=$aPlayer['team2'];?></strong>                                        <?php endif;?>                                    </div>                                    <?php endif;?>                                </td>                                <td class="f-score"></td>                            </tr>                            <?php endforeach;?>                            <?php endif;?>                        </tbody>                    </table>                </div>                <div class="f-column-4 f-text-align-center" id="f-whatnext">                    <h2><?=__('What next?', FV_DOMAIN);?></h2>                    <div id="invitePane">                        <input type="button" style="width:100%" value="<?=__('Challenge friends', FV_DOMAIN);?>" class="f-button f-primary f-fullwidth" onclick="jQuery.playerdraft.showDialog('#dlgFriends')">                    </div>                    <a class="f-showMultipleEntryLB f-button f-fullwidth" href="<?=FANVICTOR_URL_LOBBY;?>"><?=__('Enter other contests', FV_DOMAIN);?></a>		                </div>                <div class="clear"></div>            </div>        </div>    </div></div><?php require_once('dlg_info.php');?><?php require_once('dlg_info_friends.php');?><script type="text/javascript">    var showInviteFriends = '<?=$showInviteFriends;?>';    if(showInviteFriends)    {        jQuery.playerdraft.showDialog('#dlgFriends')    }</script>