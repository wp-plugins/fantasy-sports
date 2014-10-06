<?php get_header(); ?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<h3 class="widget-title">
				<?=$sHeader;?>
			</h3>
			<div class="content">
				<form action="<?=FANVICTOR_URL_RANKINGS;?>" method="POST">
					<input type="hidden" class="leagueID" name="leagueID" />
					<div id="leagues_history_grid"><?=$historyContests;?></div>
				</form>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>