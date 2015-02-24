<?php
class FanvictorInit
{
    static function active()
    {
        if(!get_option('fanvictor_done_installed',false))
        {
            self::installDb();
            self::installOptions();
            self::installPages();

            self::install_widget();
            add_option('fanvictor_version', FANVICTOR_VERSION);
            update_option('fanvictor_done_installed',true);
			self::sendUserInfo();
        }
        else 
        {
            $file = FANVICTOR__PLUGIN_DIR.'install/install.xml';
            $xml = '';
            if(file_exists($file))
            {
                $xml = simplexml_load_file($file);
            }

            if($xml != null && isset($xml->pages))
            {
                $curPage = get_page_by_title("Fantasy");
                wp_update_post(array(
                    'ID' => $curPage->ID,
                    'post_status' => 'publish',
                    'ping_status' => 'open'
                ));
                foreach($xml->pages->page as $page)
                {
                    $curPage = get_page_by_title((string)$page->name);
                    wp_update_post(array(
                        'ID' => $curPage->ID,
                        'post_status' => 'publish',
                        'ping_status' => 'open'
                    ));
                }
            }
        }
    }
    
    static function deactivate()
    {
        $file = FANVICTOR__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->pages))
        {
            $curPage = get_page_by_title("Fantasy");
            wp_update_post(array(
                'ID' => $curPage->ID,
                'post_status' => 'draft',
                'ping_status' => 'closed'
            ));
            foreach($xml->pages->page as $page)
            {
                $curPage = get_page_by_title((string)$page->name);
                wp_update_post(array(
                    'ID' => $curPage->ID,
                    'post_status' => 'draft',
                    'ping_status' => 'open'
                ));
            }
        }
    }

    static function upgrade()
    {
        $file = FANVICTOR__PLUGIN_DIR.'install/upgrade.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }
        
        if($xml != null && isset($xml->version))
        {
            $curVersion = get_option('fanvictor_version',false);
            foreach($xml->version as $version)
            {
                $version = (string)$version->number;
                if($version > $curVersion)
                {
					if(file_exists(FANVICTOR__PLUGIN_DIR.'class.table-credits.php'))
					{
						unlink(FANVICTOR__PLUGIN_DIR.'class.table-credits.php');
						unlink(FANVICTOR__PLUGIN_DIR.'class.table-fighters.php');
						unlink(FANVICTOR__PLUGIN_DIR.'class.table-organizations.php');
						unlink(FANVICTOR__PLUGIN_DIR.'class.table-pools.php');
						unlink(FANVICTOR__PLUGIN_DIR.'class.table-statistic.php');
						unlink(FANVICTOR__PLUGIN_DIR.'class.table-teams.php');
						unlink(FANVICTOR__PLUGIN_DIR.'class.table-withdrawls.php');
					}
                    self::upgradeDb($version);
                    self::upgradeOptions($version);
                    self::upgradePages($version);
                    update_option('fanvictor_version', $version);
                }
            }
        }
    }

    static function uninstall()
    {
        self::uninstallDb();
        self::uninstallOptions();
        self::uninstallPages();
        self::uninstall_widget();
        delete_option('fanvictor_version');
        delete_option('fanvictor_done_installed');
    }
	
	static function xml_attribute($object, $attribute)
	{
		if(isset($object[$attribute]))
		{
			return (string) $object[$attribute];
		}
		return null;	
	}
    
    static function sendUserInfo()
    {
        $user = wp_get_current_user();
        $website = "http://".$_SERVER['SERVER_NAME'];
        $to      = FANVICTOR_EMAIL_SUPPORT;
        $subject = "FanVictor's Client Email";
        $message = 
"Website: ".$website."
Email: ".$user->user_email."
User login: ".$user->user_login;
        $headers = "From: ".$website;
        try 
        {
            mail($to, $subject, $message, $headers);
        } 
        catch (Exception $ex) 
        {
        }
    }

    ////////////////////////////widget////////////////////////////
    static function install_widget()
    {
        $add_to_sidebar = 'fanvictor_home_sidebar';
        $widget_name = 'lobby_widget';
        $sidebar_options = get_option('sidebars_widgets');
        if(!isset($sidebar_options[$add_to_sidebar]))
        {
            $sidebar_options[$add_to_sidebar] = array('_multiwidget'=>1);
        }

        $homepagewidget = array();
        $count = count($homepagewidget)+1;
        // add first widget to sidebar:
        $sidebar_options[$add_to_sidebar][] = $widget_name.'-'.$count;
        $homepagewidget[$count] = array();
        $homepagewidget['_multiwidget'] = 1;
        $count++;

        update_option('sidebars_widgets',$sidebar_options);
        update_option('widget_'.$widget_name,$homepagewidget);
    }

    static function uninstall_widget()
    {
        delete_option('widget_lobby_widget');
        $add_to_sidebar = 'fanvictor_home_sidebar';
        $widget_name = 'lobby_widget';
        $sidebar_options = get_option('sidebars_widgets');
        if(isset($sidebar_options[$add_to_sidebar]))
        {
            unset($sidebar_options[$add_to_sidebar]);
            update_option('sidebars_widgets',$sidebar_options);
        }
    }

    static function home_sidebar()
    {
        echo '<div id="fanvictor_home_sidebar">';
        dynamic_sidebar('fanvictor_home_sidebar');;
        echo "</div>";
    }

    static function init_home_sidebar_area()
    {
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'model.php');
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/fighters.php');
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/teams.php');
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'payment.php');
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/pools.php');
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/sports.php');
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/scoringcategory.php');
        require_once(FANVICTOR__PLUGIN_DIR_MODEL.'fanvictor.php');
        require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER."lobby.php");
        add_action( 'widgets_init', function(){
            register_sidebar( array(
                'name' => __('Fan Victor Home Sidebar'),
                'id' => 'fanvictor_home_sidebar',
            ));
            register_widget( 'Lobby' );
        });
    }

    ////////////////////////////data tables////////////////////////////
    static function installDb()
    {
        global $wpdb;
        $file = FANVICTOR__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->queries))
        {
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            foreach($xml->queries->query as $query)
            {
                $query = str_replace('{PREFIX}', $wpdb->prefix, (string)$query).";";
                $wpdb->query($query);
            }
        }
    }

    static function upgradeDb($version)
    {
        global $wpdb;
        $file = FANVICTOR__PLUGIN_DIR.'install/upgrade.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null)
        {
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            foreach($xml->version as $ver)
            {
                if((string)$ver->number == $version && isset($ver->queries))
                {
                    foreach($ver->queries->query as $query)
                    {
                        $query = str_replace('{PREFIX}', $wpdb->prefix, (string)$query).";";
                        $wpdb->query($query);
                    }
                }
            }
        }
    }

    static function uninstallDb()
    {
        global $wpdb;
        $file = FANVICTOR__PLUGIN_DIR.'install/uninstall.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->queries))
        {
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            foreach($xml->queries->query as $query)
            {
                $query = str_replace('{PREFIX}', $wpdb->prefix, (string)$query);
                $wpdb->query($query);
            }
        }
    }

    ////////////////////////////data options////////////////////////////
    static function installOptions()
    {
        $file = FANVICTOR__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->options))
        {
            foreach($xml->options->option as $option)
            {
				$attr = $option->value->attributes();
				$attr = self::xml_attribute($attr, 'type');
				$value = '';
				switch($attr)
				{
					case 'array':
						$value = explode(',', (string)$option->value);
						break;
					default:
						$value = (string)$option->value;
				}
                add_option((string)$option->name, $value);
            }
        }
    }
    
    static function upgradeOptions($version)
    {
        $file = FANVICTOR__PLUGIN_DIR.'install/upgrade.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->version->options))
        {
            foreach($xml->version as $ver)
            {
                if((string)$ver->number == $version && isset($ver->options))
                {
                    foreach($ver->options->option as $option)
                    {
                        add_option((string)$option->name, (string)$option->value);
                    }
                }
            }
        }
    }

    static function uninstallOptions()
    {
        $file = FANVICTOR__PLUGIN_DIR.'install/uninstall.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->options))
        {
            foreach($xml->options->option as $option)
            {
                delete_option((string)$option->name);
            }
        }
    }

    ////////////////////////////data pages////////////////////////////
    static function installPages($upgrade = false)
    {
        $file = FANVICTOR__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->pages))
        {
            $parent_id = self::insert_page("Fantasy", 'publish', 20);
            $count = 0;
            foreach($xml->pages->page as $page)
            {
                $count += 1;
                $id = self::insert_page((string)$page->name, 'publish', $count, $parent_id);
                /*if((string)$page->menu == 1)
                {
                    $post_id = insert_primary_menu(1, $parent_id);
                    add_term_relationship($post_id, $menu_id);
                    add_menu_post_meta($post_id, $p1, $item_parent);
                }*/
            }
        }
    }
    
    static function upgradePages($version)
    {
		global $wpdb;
        $file = FANVICTOR__PLUGIN_DIR.'install/upgrade.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null)
        {
            foreach($xml->version as $ver)
            {
                if((string)$ver->number == $version && isset($ver->pages))
                {
                    $page = get_page_by_title('Fantasy');
                    $parent_id = $page->ID;
                    $count = 0;
                    foreach($ver->pages->page as $page)
                    {
                        $count += 1;
						$name = (string)$page->name;
						$post_name = strtolower($name);
						$query = "INSERT INTO ".$wpdb->prefix."posts(post_title,post_status, post_type, post_parent,post_author, menu_order, post_name)
							VALUES('$name', 'publish', 'page', '$parent_id', '".get_current_user_id()."', '$count', '$post_name')";
						$wpdb->query($query);
                    }
                    break;
                }
            }
        }
    }

    static function uninstallPages()
    {
        self::delete_menu("Fantasy");
        self::delete_page("Fantasy");
        $file = FANVICTOR__PLUGIN_DIR.'install/uninstall.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->pages))
        {
            foreach($xml->pages->page as $page)
            {
                self::delete_menu((string)$page->name);
                self::delete_page((string)$page->name);
            }
        }
    }

    ////////////////////////////frontend pages////////////////////////////
    static function showMenu()
    {
        $exclude = array();
        
        //read from xml
        $file = FANVICTOR__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->pages))
        {
            foreach($xml->pages->page as $page)
            {
                $curPage = get_page_by_title((string)$page->name);
                if((get_current_user_id() > 0 && (string)$page->menu_loggedin == 0) ||
                   (get_current_user_id() == 0 && (string)$page->menu == 0))
                {
                    $exclude[] = $curPage->ID;
                }
                if(!get_option('fanvictor_create_contest') && $curPage->post_title == "Create Contest")
                {
                    $exclude[] = $curPage->ID;
                }
            }
        }
        
        //select menu to show
        if($exclude != null)
        {
            $exclude = implode(',', $exclude);
        }

        $args = array(
        'exclude'      => $exclude,
        'echo'         => 1,
        'menu_class' => 'nav-menu nav');
        return $args;
    }
    
    static function initPage()
    {
		//check jquery loadded
        if(!wp_script_is('jquery', 'registered')) 
        {
            add_action( 'wp_enqueue_scripts', array('FanvictorInit', 'theme_name_scripts'));
        }
		
        if(pageSegment(1) == 'fantasy' || isset($_GET['page_id']))
        {
            if(pageSegment(2) == '')
            {
                wp_redirect(FANVICTOR_URL_CREATE_CONTEST);exit;
            }
        
            //require login 
            if(get_current_user_id() == 0)
            {
                $file = FANVICTOR__PLUGIN_DIR.'install/install.xml';
                $xml = '';
                if(file_exists($file))
                {
                    $xml = simplexml_load_file($file);
                }
                
                if($xml != null && isset($xml->pages))
                {
                    if(isset($_GET['page_id']))
                    {
                        $curPage = get_page($_GET['page_id']);
                    }
                    else 
                    {
                        $curPage = get_page_by_path(pageSegment(1).'/'.pageSegment(2));
                    }
                    
                    foreach($xml->pages->page as $page)
                    {
                        if((string)$page->menu == 0 && (string)$page->public != 1 &&
                           (string)$page->name == $curPage->post_title)
                        {
                            wp_redirect(wp_login_url());exit;
                        }
                    }
                }
            }

            //request page
            if(isset($_GET['page_id']))
            {
                $curPage = get_page($_GET['page_id']);
            }
            else 
            {
                $curPage = get_page_by_path(pageSegment(1).'/'.pageSegment(2));
            }
            if(!get_option('fanvictor_create_contest') && $curPage->post_title == "Create Contest")
            {
                wp_redirect(home_url());exit;
            }
            if($curPage != null)
            {
                //model
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'fanvictor.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/pools.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'payment.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/organizations.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/user.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'paypal.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'mypaypal.php');
                
                //v2 model
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/playerposition.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/scoringcategory.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/players.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/teams.php');
                require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/fighters.php');
                self::call_page($curPage->post_name);
                
            }
        }

        if(pageSegment(1) == "")
        {
            add_filter('the_content', array('FanvictorInit', 'addlobby'));
        }
        
        //set top menu no link
        add_action('wp_enqueue_scripts',array('FanvictorInit', 'top_menu_no_link'));
    }
	
	static function theme_name_scripts()
    {
        wp_enqueue_script('jquery.js', FANVICTOR__PLUGIN_URL_JS.'jquery.js', array(), '1.11.1');
    }
    
    static function top_menu_no_link()
    {
        $page = get_page_by_path('/fantasy/');
        ?>
        <script type="text/javascript">
            var topmenuid = '<?=$page->ID;?>';
        </script>
        <?php
        wp_enqueue_script('nolink.js', FANVICTOR__PLUGIN_URL_JS.'nolink.js');
    }
    
    static function call_page($name)
    {
        $name = trim($name);
        $name = str_replace('-', '', $name);
        if(file_exists(FANVICTOR__PLUGIN_DIR_CONTROLLER."$name.php"))
        {
            require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER."$name.php");
            if(class_exists($name))
            {
                $$name = new $name();
                if(method_exists($name, 'process'))
                {
                    add_action( 'wp_loaded', array($name, 'process'));
                }
            }
        }
    }
    
    static function addlobby($content)
    {
        return self::home_sidebar().$content;
    }

    static function insert_page($name, $status, $menu_order = 0, $parent = null)
    {
        if(!get_page_by_title($name))
        {
            $my_post = array(
                'post_title'    => $name,
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_parent'   => $parent,
                'post_author'   => get_current_user_id(),
                'menu_order'    => $menu_order,
            );
            $id = wp_insert_post( $my_post );
            if($status == 'pending')
            {
                $my_post = array(
                    'ID'            => $id,
                    'post_status'   => 'pending',
                );
                wp_update_post($my_post);
            }
            return $id;
        }
        return null;
    }

    static function delete_page($name, $status, $menu_order = 0, $parent = null)
    {
        if(get_page_by_title($name))
        {
            global $wpdb;
            $wpdb->delete($wpdb->prefix."posts", array('post_title' => $name));
        }
    }

    static function delete_menu($name)
    {
        global $wpdb;
        $id = get_page_by_title($name)->ID;
        $table = $wpdb->prefix.'postmeta';
        $sql = "SELECT post_id FROM $table WHERE meta_key = '_menu_item_object_id' AND meta_value = $id";
        $post_id = $wpdb->get_var($sql);

        $wpdb->delete($wpdb->prefix."postmeta", array('post_id' => $post_id));
        $wpdb->delete($wpdb->prefix."term_relationships", array('object_id' => $post_id));
        $wpdb->delete($wpdb->prefix."posts", array('ID' => $post_id));
    }
}

function pluginname_ajaxurl() 
{
    ?>
    <script type="text/javascript">
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
}

function pageSegment($pos = 0)
{
    $siteUrl = explode('/', get_site_url().'/');
	$siteUrl = array_filter($siteUrl);
	$siteUrl = array_values($siteUrl);
	$offset = count($siteUrl) - 2;
    $url =  $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    if(isset($url[$pos + $offset]))
    {
        return $url[$pos + $offset];
    }
    return null;
}

function redirect($url, $msg = null, $blank = false)
{
    if($msg != null && function_exists('add_settings_error'))
    {
        add_settings_error('general', 'settings-updated', __($msg), 'updated');
        set_transient('settings_errors', get_settings_errors(), 30);
    }
    else if($msg != null)
    {
        $_SESSION['msg'] = $msg;
    }
    if(!$blank)
    {
        $url = add_query_arg( 'settings-updated', 'true', $url);
    }
    wp_redirect($url);
    exit;
}

function getMessage()
{
    if(isset($_SESSION['msg']))
    {
        $msg = $_SESSION['msg'];
        unset($_SESSION['msg']);
        echo '<div class="public_message" style="display: block;">'.$msg.'</div>';
    }
}

require_once(FANVICTOR__PLUGIN_DIR_MODEL.'model.php');
if (is_admin()) 
{
    //model
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/pools.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/fighters.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/teams.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/organizations.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/user.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/statistic.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'payment.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'fanvictor.php');
	require_once(FANVICTOR__PLUGIN_DIR_MODEL.'mypaypal.php');
    
    //v2 model
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/sports.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/playerposition.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/scoringcategory.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/players.php');
    require_once(FANVICTOR__PLUGIN_DIR_MODEL.'admin/leagues.php');
    
    //controller
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-pools.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-contests.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-fighters.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-teams.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-credits.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-withdrawls.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-statistic.php');
    
    //v2 controller
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-sports.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-scoringcategory.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-players.php');
    require_once(FANVICTOR__PLUGIN_DIR_CONTROLLER.'admin/fanvictor-playerposition.php');
    
    //admin page
    require_once(FANVICTOR__PLUGIN_DIR.'class.fanvictor-admin.php');
	$fanvictor = new Fanvictor_Admin();
    $fanvictor->init();
    
    //ajax page
    require_once(FANVICTOR__PLUGIN_DIR.'class.ajax.php');
}
else
{    
    add_action('wp_enqueue_scripts','pluginname_ajaxurl'); 
    
    //request page
    add_action('init', array('FanvictorInit', 'initPage'));
    
    //select menu to show
    add_filter( 'wp_page_menu_args', array('FanvictorInit', 'showMenu'));
}
?>