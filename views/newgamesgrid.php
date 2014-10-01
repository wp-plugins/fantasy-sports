<form action="<?=FANVICTOR_URL_SUBMIT_PICKS;?>" method="POST">
    <input type="hidden" class="leagueID" name="leagueID" />
    <input type="hidden" class="poolID" name="poolID" />
    <div id="leagues_list">
        <div id="leagues_list">
            <table class="table table-striped table-bordered table-condensed table-responsiv">
                <tr>
                    <th><?=__('Contest');?></th>
                    <th><?=__('Type');?></th>
                    <th><?=__('Size');?></th>
                    <th><?=__('Entry/Prizes');?></th>
                    <th><?=__('Starts(ET)');?></th>
                    <th> </th>
                </tr>
            </table>
        </div>
    </div>
</form>
<div id="dlgLeagueDetail" style="display: none"><center><?=__('Loading...Please wait!');?></center></div>
<a href="<?=FANVICTOR_URL_CREATE_CONTEST;?>">Create Contest</a>