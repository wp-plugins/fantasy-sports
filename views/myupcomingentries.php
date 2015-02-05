<h3 class="widget-title">
    <?=__("My Upcoming Entries");?>
</h3>
<?php if($aLeagues != null):?>
    <div class="content">
        <div class="wrap_content">
            <table class="table table-striped table-bordered table-responsive table-condensed">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Entries</th>
                        <th>Size</th>
                        <th>Entry Fee</th>
                        <th>Prizes</th><th>&nbsp;</th>
                    </tr>
                </thead>
                <?php if($aLeagues != null):?>
                <tbody>
                    <?php foreach($aLeagues as $aLeague):?>
                    <tr>
                        <td><?=$aLeague['leagueID'];?></td>
                        <td><?=$aLeague['startDate'];?></td>
                        <td>
                            <span ><?=$aLeague['name'];?></span>
                        </td>
                        <td><?=$aLeague['gameType'];?></td>
                        <td><?=$aLeague['entries'];?></td>
                        <td><?=$aLeague['size'];?></td>
                        <td>$<?=$aLeague['entry_fee'];?></td>
                        <td>$<?=$aLeague['prizes'];?></td>
                        <td style="text-align: center">
                            <input type="button" class="btn btn-success btn-xs" value="EDIT" onclick="window.location = '<?=FANVICTOR_URL_SUBMIT_PICKS.$aLeague['leagueID'];?>'">
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
                <?php endif;?>
            </table>
        </div>
    </div>
<?php else:?>
    <?=__("There are no upcoming entries");?>
<?php endif; ?>
