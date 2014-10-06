<?php get_header(); ?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<script type="text/javascript">
				updateLiveContests();
				setInterval(function() { updateLiveContests() }, 60000);
			</script>

			<h3 class="widget-title">
				<?=$sHeader;?>
			</h3>
			<div class="content">
				<div id="popup_leagues_details" class="popup_block"></div>
				<form action="<?=FANVICTOR_URL_RANKINGS;?>" method="POST">
					<input type="hidden" name="live" value="1" />
					<input type="hidden" class="leagueID" name="leagueID" />
					<input type="hidden" class="poolID" name="poolID" />
					<div id="leagues_live_games_grid"></div>
				</form>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>