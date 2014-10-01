<?php
add_action( 'init', array('Transactions', 'process'));
class Transactions
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
        add_action('wp_enqueue_scripts', array('Transactions', 'theme_name_scripts'));
        add_filter('template_include', array('Transactions', 'addContent'));
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('payment.js', FANVICTOR__PLUGIN_URL_JS.'payment.js');
        wp_enqueue_style('payment.css', FANVICTOR__PLUGIN_URL_CSS.'payment.css');
    }

    public static function addContent()
    {
        list($total_items, $aFundHistorys) = self::$payment->getFundhistory(null, 'fundshistoryID DESC', (1 - 1) * 10, 10);
        $aFundHistorys = self::$payment->parseFunhistoryData($aFundHistorys);
        
        $sUrlSubmit = FANVICTOR_URL_ADD_FUNDS;
        $aGateways = self::$payment->viewGateway();
        include FANVICTOR__PLUGIN_DIR_VIEW.'transactions.php';
    }
}

?> 
