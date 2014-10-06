<?php get_header(); ?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<?php if($errorMessage):?><div id="error_message" class="error_message"><?=$errorMessage;?></div><?php endif;?>
			<form action="<?=FANVICTOR_URL_RANKINGS;?>" method="POST" id="submitPicksForm" name="submitPicksForm">
			<?=$htmlData;?>
			</form>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>
