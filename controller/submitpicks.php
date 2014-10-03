<?php 
class Submitpicks
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
        add_action( 'wp_enqueue_scripts', array('Submitpicks', 'theme_name_scripts') );
        add_filter('template_include', array('Submitpicks', 'addContent'));
	} 
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('submitpicks.js', FANVICTOR__PLUGIN_URL_JS.'submitpicks.js');
        wp_enqueue_style('style.css', FANVICTOR__PLUGIN_URL_CSS.'style.css');
    }

    public static function addContent()
    {
        $error = false;
        $htmlData = '';
        $errorMessage = '';

		# We can come to this page in 3 ways:
		# 1 - When a user makes a new contest
		# 2 - When a user wants to edit his picks.   
		# 3 - When a user wants to enter the contest
		# We need to know the difference.  We can tell by checking what
		# POST variables were posted to us.  submitContest is the submit 
		# button on the createContest page.  This is how we will tell.

		# 1 - If submitContest is present then we need to postLeague
		if ( isset($_POST) && isset($_POST["submitContest"]))
		{
            //check valid
            self::validData();
            
			$ret = self::$fanvictor->postLeague($_POST);
			$ret = json_decode($ret);

			if(!isset($ret->success))
			{
                $errorMessage = __('There was a problem adding your contest');
			}
            else if(isset($ret->message) && "No userID" == $ret->message)
			{
                $errorMessage = __('Sorry! You must be logged into to perform that action');
			}

			if(isset($ret->success) && $ret->success)
			{
				$poolID = $ret->poolID;	
				$leagueID = $ret->leagueID;
			}
		}
		# 2/3 IF we have the pool and league ID's then we just get fights and maybe try to return userpicks if exist
		else if (isset($_POST["poolID"]) && isset($_POST["leagueID"]))
		{
			$poolID = $_POST["poolID"]; 
            $leagueID = $_POST["leagueID"];
		}
		else
		{
            $errorMessage = __('There was a problem adding your contest');
            $error = true;
		}	

		if (!$error ) 
		{
			$htmlData = self::$fanvictor->getFights($poolID,$leagueID);

			if ( $htmlData == 'pool_expired' )
                $errorMessage = __('Sorry the pool expired');
			else
                $htmlData = $htmlData;
		}
        include FANVICTOR__PLUGIN_DIR_VIEW.'submitpicks.php';
    }
    
    private static function validData()
    {
        if(empty($_POST['leaguename']))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('Please enter league name'));
        }
        else if(!in_array($_POST['leagueSize'], get_option('fanvictor_league_size')))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('League size does not exist'));
        }
        else if($_POST['entry_fee'] > 0 && !in_array($_POST['entry_fee'], get_option('fanvictor_entry_fee')))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('Entry fee does not exist'));
        }
        else if(!self::$payment->isUserEnoughMoneyToJoin($_POST['entry_fee']))
        {
            redirect(FANVICTOR_URL_CREATE_CONTEST, __('You do not have enough funds to enter. Please click <a href="'.FANVICTOR_URL_ADD_FUNDS.'">here</a> to add funds'));
        }
    }
} 
?>