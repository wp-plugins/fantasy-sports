<h1><?=$sHeader;?></h1>
<?php if($futureEvents != null):?>
    <form action="{url link='fanvictor.submitpicks'}" method="POST">
        <input type="hidden" class="leagueID" name="leagueID" />
        <input type="hidden" class="poolID" name="poolID" />
        <div id="leagues_future_events">

            <table class="table table-striped table-bordered table-responsive table-condensed">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Sport</th>
                    <th>Start Date</th>
                    <th>Cut Date</th>
                    <th>Fixture</th>
                </tr>
                <?php foreach($futureEvents as $item):?>
                <tr>
                    <td><?=$item['poolID']?></td>
                    <td><?=$item['poolName']?></td>
                    <td><?=$item['organization']?></td>
                    <td><?=$item['startDate']?></td>
                    <td><?=$item['cutDate']?></td>
                    <td>
                        <?php if($item['only_playerdraft'] == 0):?>
                        <a href="#" onclick="return viewPoolFixture(<?=$item['poolID'];?>, '<?=__("fixtures");?>')">
                            <?=__("View fixtures");?>
                        </a>
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach;?>
            </table>
        </div>
    </form>
    <div id="dlgFixture" style="display: none"></div>
<?php else:?>
    <?=__("There are no future events");?>
<?php endif;?>