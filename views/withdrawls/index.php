<div class="wrap">
    <h2><?=__("Manage Withdrawls");?></h2>
    <?=settings_errors();?>
    <form method="get">
        <input type="hidden" name="page" value="<?=$_REQUEST['page'];?>" />
        <?php $myListTable->search_box('search', 'search_id'); ?>
    </form>
    <?php $myListTable->display();?>
</div>
<?php require_once(FANVICTOR__PLUGIN_DIR_VIEW.'withdrawls/dlg_user_withdrawls.php');?>