<div class="wrap">
    <h2><?=__("Organization Settings", FV_DOMAIN);?></h2>
    <?=settings_errors();?>
    <form name="adminForm" action="<?=self::$url;?>" method="post">
        <input id="submitTask" type="hidden" name="task">
        <?php $myListTable->display();?>
        <input type="button" value="<?=__("Delete Selected");?>" class="button button-primary"  onclick="return jQuery.admin.action('', 'delete');">
    </form>
</div>
<div id="resultDialog" title="" style="display: none"></div>