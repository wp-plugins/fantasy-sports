<div class="wrap">
    <h2>
        <?=__("Manage Scoring Category");?>
        <a class="add-new-h2" href="<?=self::$urladdnew;?>"><?=__("Add New");?></a>
    </h2>
    <?=settings_errors();?>
    <form method="get">
        <input type="hidden" name="page" value="<?=$_REQUEST['page'];?>" />
        <?php $myListTable->search_box('search', 'search_id'); ?>
    </form>
    <form name="adminForm" action="<?=self::$url;?>" method="post">
        <input id="submitTask" type="hidden" name="task">
        <?php $myListTable->display();?>
        <input type="button" value="<?=__("Delete Selected");?>" class="button button-primary"  onclick="return jQuery.admin.action('', 'delete');">
    </form>
</div>
<div id="resultDialog" title="" style="display: none"></div>