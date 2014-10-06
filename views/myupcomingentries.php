<?php get_header(); ?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<h3 class="widget-title">
				<?=$sHeader;?>
			</h3>
			<div class="content">
				<form action="<?=FANVICTOR_URL_SUBMIT_PICKS;?>" method="POST">
					<input type="hidden" class="leagueID" name="leagueID" />
					<input type="hidden" class="poolID" name="poolID" />
					<div id="leagues_upcoming_games_grid"><?=$upcomingContests;?></div>
				</form>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>