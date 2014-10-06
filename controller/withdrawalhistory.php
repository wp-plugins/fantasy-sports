<?php
class WithdrawalHistory
{
    private static $payment;
    private static $fanvictor;
    public function __construct() 
    {
        self::$payment = new Payment();
        self::$fanvictor = new Fanvictor();
    }
    
	public static function process()
	{
        add_action('wp_enqueue_scripts', array('WithdrawalHistory', 'theme_name_scripts'));
        add_filter('the_content', array('WithdrawalHistory', 'addContent'));
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('payment.js', FANVICTOR__PLUGIN_URL_JS.'payment.js');
        wp_enqueue_style('payment.css', FANVICTOR__PLUGIN_URL_CSS.'payment.css');
    }

    public static function addContent()
    {
        list($total_items, $aWithdraws) = self::$payment->getListWithdraw(null, 'withdrawlID DESC', (1 - 1) * 10, 10);
        
        $sUrlSubmit = FANVICTOR_URL_REQUEST_HISTORY;
        $aGateways = self::$payment->viewGateway();
        include FANVICTOR__PLUGIN_DIR_VIEW.'withdrawalhistory.php';
    }
}
?>