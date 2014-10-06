<?php get_header(); ?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<h1><?=__('Withdrawal History');?></h1>
			<table>
				<tr>
					<th style="width:90px"><?=__('Request date');?></th>
					<th style="width:80px"><?=__('Credits');?></th>
					<th style="width:100px"><?=__('Rate');?></th>
					<th style="width:80px"><?=__('Real money');?></th>
					<th style="width:200px"><?=__('Reason');?></th>
					<th style="width:80px"><?=__('Status');?></th>
					<th style="width:100px"><?=__('Response date');?></th>
					<th><?=__('Response message');?></th>
				</tr>
				<?php foreach($aWithdraws as $aWithdraw):?>
				<tr>
					<td><?=$aWithdraw['requestDate'];?></td>
					<td><?=$aWithdraw['amount'];?></td>
					<td><?=get_option('fanvictor_credit_to_cash');?> <?=__('credits equals');?> $1</td>
					<td><?=$aWithdraw['real_amount'];?></td>
					<td><?=$aWithdraw['reason'];?></td>
					<td><?=$aWithdraw['status'];?></td>
					<td><?=$aWithdraw['processedDate'];?></td>
					<td><?=$aWithdraw['response_message'];?></td>
				</tr>
				<?php endforeach;?>
			</table>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>
