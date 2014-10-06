<h1><?=__('Transaction History');?></h1>
<?php getMessage();?>
<table>
    <tr>
        <th style="width: 80px"><?=__('Date');?></th>
        <th style="width: 80px"><?=__('Operation');?></th>
        <th style="width: 80px"><?=__('Type');?></th>
        <th style="width: 80px"><?=__('Gateway');?></th>
        <th style="width: 80px"><?=__('Leagueid');?></th>
        <th style="width: 100px"><?=__('Transactionid');?></th>
        <th style="width: 80px"><?=__('Amount');?></th>
        <th style="width: 90px"><?=__('New balance');?></th>
        <th>Reason<?=__('Request date');?></th>
    </tr>
    <?php foreach($aFundHistorys as $aFundHistory):?>
    <tr>
        <td><?=$aFundHistory['date'];?></td>
        <td><?=$aFundHistory['operation'];?></td>
        <td><?=$aFundHistory['type'];?></td>
        <td><?=$aFundHistory['gateway'];?></td>
        <td><?php if($aFundHistory['leagueID'] > 0):?><?=$aFundHistory['leagueID'];?><?php endif;?></td>
        <td><?=$aFundHistory['transactionID'];?></td>
        <td><?=$aFundHistory['amount'];?></td>
        <td><?=$aFundHistory['new_balance'];?></td>
        <td><?=$aFundHistory['reason'];?></td>
    </tr>
    <?php endforeach;?>
</table>