<div class="wrap">
    <h2><?=__("Manage Credits", FV_DOMAIN);?></h2>
    <?=settings_errors();?>
    <form method="get">
        <input type="hidden" name="page" value="<?=$_REQUEST['page'];?>" />
        <?php $myListTable->search_box('search', 'search_id'); ?>
    </form>
    <?php $myListTable->display();?>
</div>
<?php require_once(FANVICTOR__PLUGIN_DIR_VIEW.'credits/dlg_user_credits.php');?>