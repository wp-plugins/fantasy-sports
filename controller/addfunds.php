<?php
class Addfunds
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
        add_action('wp_enqueue_scripts', array('Addfunds', 'theme_name_scripts'));
        add_filter('template_include', array('Addfunds', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('payment.js', FANVICTOR__PLUGIN_URL_JS.'payment.js');
        wp_enqueue_style('payment.css', FANVICTOR__PLUGIN_URL_CSS.'payment.css');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
        unset($_SESSION['is_transaction']);
        unset($_SESSION['iFundHitoryId']);
        unset($_SESSION['totalMoney']);
        
        $sUrlSubmit = FANVICTOR_URL_ADD_FUNDS;
        $aGateways = self::$payment->viewGateway();
        include FANVICTOR__PLUGIN_DIR_VIEW.'addfunds.php';
    }
}
?>