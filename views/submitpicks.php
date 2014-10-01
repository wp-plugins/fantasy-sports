<?php get_header(); ?>
    <?php if($errorMessage):?><div id="error_message" class="error_message"><?=$errorMessage;?></div><?php endif;?>
    <form action="<?=FANVICTOR_URL_RANKINGS;?>" method="POST" id="submitPicksForm" name="submitPicksForm">
    <?=$htmlData;?>
    </form>
<?php get_footer(); ?>
