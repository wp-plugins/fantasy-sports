<h1><?=$sHeader;?></h1>
<form action="{url link='fanvictor.submitpicks'}" method="POST">
    <input type="hidden" class="leagueID" name="leagueID" />
    <input type="hidden" class="poolID" name="poolID" />
    <div id="leagues_future_events">
        <?php if(is_array($futureEvents) && $futureEvents != null):?>
        <table class="table table-striped table-bordered table-responsive table-condensed">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Sport</th>
                <th>Organization</th>
                <th>Start Date</th>
                <th>Cut Date</th>
                <th>Fixture</th>
            </tr>
            <?php foreach($futureEvents as $item):?>
            <tr>
                <td><?=$item['poolID']?></td>
                <td><?=$item['poolName']?></td>
                <td><?=$item['type']?></td>
                <td><?=$item['organization']?></td>
                <td><?=$item['startDate']?></td>
                <td><?=$item['cutDate']?></td>
                <td><a href="#" onclick="return viewPoolFixture(<?=$item['poolID'];?>, '<?=__("fixtures");?>')"><?=__("View fixtures");?></a></td>
            </tr>
            <?php endforeach;?>
        </table>
        <?php else:?>
            <?=__("No future events");?>
        <?php endif;?>
    </div>
</form>
<div id="dlgFixture" style="display: none"></div>
