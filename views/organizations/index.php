<div class="wrap">
    <h2><?=__("Organization Settings");?></h2>
    <?=settings_errors();?>
    <form name="adminForm" action="<?=self::$url;?>" method="post">
        <input id="submitTask" type="hidden" name="task">
        <?php $myListTable->display();?>
    </form>
</div>
<div id="resultDialog" title="" style="display: none"></div>