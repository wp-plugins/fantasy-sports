<input type="hidden" value='<?=json_encode($league);?>' id="leagueInfo" />
<?php if($users != null):?>
    <input type="hidden" value='<?=json_encode($users);?>' id="pickData" />
    <label><?=__("Game type", FV_DOMAIN);?></label>:<?=$league['gameType'];?>
    <br/>
    <label><?=__("User", FV_DOMAIN);?></label>
    <select id="cbUsers" onchange="jQuery.admin.showPicksDetail();">
        <?php foreach($users as $user):?> 
            <option value="<?=$user['userID'];?>"><?=$user['user_login'];?></option>
        <?php endforeach;?>
    </select>
    <?php if($league['multi_entry']):?>
        <br/>
        <label><?=__("Entry number", FV_DOMAIN);?></label>
        <?php foreach($users as $user):?> 
        <select id="cbEntry<?=$user['userID'];?>" class="cbEntry" style="display: none"  onchange="jQuery.admin.showPicksDetail();">
            <?php foreach($user['entries'] as $entry):?> 
                <option value="<?=$entry['entry_number'];?>"><?=$entry['entry_number'];?></option>
            <?php endforeach;?>
        </select>
        <?php endforeach;?>
    <?php endif;?>
    <table id="tbPickDetail" class="wp-list-table widefat books">
        <thead>
            <tr>
                <th style="width: 40px"><?=__("ID", FV_DOMAIN);?></th>
                <?php if($league['gameType'] == 'PLAYERDRAFT' && $league['is_team'] == 1):?>
                    <th style="width: 200px"><?=__("Team", FV_DOMAIN);?></th>
                <?php elseif($league['gameType'] != 'PLAYERDRAFT'):?> 
                    <th style="width: 200px"><?=__("Fixture", FV_DOMAIN);?></th>
                <?php endif;?>
                <th><?=__("Pick Name", FV_DOMAIN);?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
<?php else:?> 
    <center><?=__('No picks', FV_DOMAIN);?></center>
<?php endif;?>