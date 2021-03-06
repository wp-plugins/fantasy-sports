<?php
class MyFunds
{
    private static $payment;
    private static $user;
    private static $fanvictor;
    private static $coupon;
    public function __construct() 
    {
        self::$payment = new Payment();
        self::$user = new User();
        self::$fanvictor = new Fanvictor();
        self::$coupon = new FV_CouponModel();
    }

	public static function process()
	{
        add_action( 'wp_enqueue_scripts', array('MyFunds', 'theme_name_scripts') );
        add_filter('the_content', array('MyFunds', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('payment.js', FANVICTOR__PLUGIN_URL_JS.'payment.js');
        wp_enqueue_script('ui.js', FANVICTOR__PLUGIN_URL_JS.'ui.js');
        wp_enqueue_style('payment.css', FANVICTOR__PLUGIN_URL_CSS.'payment.css');
        wp_enqueue_style('ui.css', FANVICTOR__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        unset($_SESSION['is_transaction']);
        unset($_SESSION['iFundHitoryId']);
        unset($_SESSION['totalMoney']);
        
        $aGateways = self::$payment->viewGateway();
        $aUserPayment = self::$payment->getUserPaymentInfo(PAYPAL, get_current_user_id());
        $aUser = self::$payment->getUserData();
        $withdrawPending = self::$user->getWithdrawlsTotal(get_current_user_id());
        $isHasCoupon = self::$coupon->isHasCoupon(CP_ACTION_ADD_MONEY);
        include FANVICTOR__PLUGIN_DIR_VIEW.'myfunds.php';
    }
}
?>