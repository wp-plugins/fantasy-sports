<div class="wrap">
    <h2><?=__("Event Statistics");?></h2>
    <?=settings_errors();?>
    <form method="get">
        <input type="hidden" name="page" value="<?=$_REQUEST['page'];?>" />
        <?php $myListTable->search_box('search', 'search_id'); ?>
    </form>
    <?php $myListTable->display();$data = $myListTable->getData()?>
    <p>
        <b><?=__("Total Cash Processed");?>:</b>
        <?=$data['accumCash'];?>
    </p>
    <p>
        <b>Total Pay Out:</b>
        <?=$data['accumPayOut'];?>
    </p>
    <p>
        <b>Total Profit:</b>
        <?=$data['accumProfit'];?>
    </p>
</div>
<div id="dlgStatistic" style="display: none"><center><?=__("Loading...Please wait!");?></center></div>