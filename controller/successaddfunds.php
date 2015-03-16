<?php
class Successaddfunds
{
	public static function process()
	{
        redirect(FANVICTOR_URL_TRANSACTIONS, __('Transaction Complete'), true);
	}
}
?>