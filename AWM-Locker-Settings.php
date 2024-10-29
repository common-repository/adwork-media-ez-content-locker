<?php
/*
	Plugin Name: AdWork Media EZ Content Locker
	Plugin URI: http://www.adworkmedia.com/
	Description: Easily integrate your AdWork Media Content Lockers & Link Lockers into your WordPress site.
	Author: AdWorkMedia.com
	Version: 3.0
	License: GPLv2
*/

global $wpdb; 
define("AWM_TABLE",$wpdb->prefix."AWM_EZ_CL_options");
register_activation_hook( __FILE__, "install_AWM_EZ_CL");
//error_reporting(0);
$VERSION=2.0;
$table_name=$wpdb->prefix."AWM_EZ_CL_options";
function install_AWM_EZ_CL()
{
    global $wpdb;
    $collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}
    $table_name=$wpdb->prefix."AWM_EZ_CL_options";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		//mysql_query("DROP TABLE ".AWM_TABLE);	
      $sql="CREATE TABLE IF NOT EXISTS $table_name (
	  ID int(8) NOT NULL AUTO_INCREMENT,
	  locker_code text NOT NULL,
	  status int(1) NOT NULL,
	  posts text NOT NULL,
	  pages text NOT NULL,
	  display int(1) NOT NULL,
	  hideShow int(1) NOT NULL,
	  totalLeads int(10) NOT NULL,
	  name varchar(270) NOT NULL,
	  lockType int(1) NOT NULL,
	  profileID int(11) NOT NULL,
	  API_key varchar(35) NOT NULL,
	  disallowDomains varchar(250) NOT NULL,
	  allowDomains varchar(250) NOT NULL,
	  PRIMARY KEY (ID),
	  KEY lockType (lockType),
	  KEY status (status),
	  KEY hideShow (hideShow)	 
	) $collate";		
	dbDelta( $sql );
}



function AWM_EZ_CL_LOAD_CSS() {
    echo '<style type="text/css" media="screen">
        #AWM_sidebar_icon{
			width:15px;height:15px;margin-right:2px;border:0;display:inline-block;background:url('.plugins_url("images/adwork_media_WP_tiny_icon.png", __FILE__).') no-repeat; }
   </style>';
}

function AWM_EZ_CL_plugin_menu() {
	add_submenu_page('options-general.php', 'AdWork Media EZ Content Locker Settings', '<span id="AWM_sidebar_icon"></span>AWM EZ Locker', 10, __FILE__, 'AWM_EZ_CL_plugin_options');
	add_menu_page('AdWork Media EZ Content Locker Settings', 'AdWork Media EZ Content Locker', 'administrator',  'AWM-Locker-Settings', 'AWM_EZ_CL_plugin_options', plugins_url("images/adwork_media_WP_icon.png", __FILE__));	
	remove_submenu_page('AWM-Locker-Settings', 'AWM-Locker-Settings');
	add_submenu_page('AWM-Locker-Settings', 'Settings Overview', 'Settings Overview', 0, 'AWM-Locker-Settings', 'AWM_EZ_CL_plugin_options');
	add_submenu_page('AWM-Locker-Settings', 'Content Locker Settings', 'Content Locker Settings', 0, 'AWM-CL-Locker-Settings', 'AWM_EZ_CLS_plugin_options');
	add_submenu_page('AWM-Locker-Settings', 'Link Locker Settings', 'Link Locker Settings', 0, 'AWM-LL-Locker-Settings', 'AWM_EZ_LLS_plugin_options');	
	//add_submenu_page("my-menu-slug", "My Submenu", "My Submenu", 0, "my-submenu-slug", "mySubmenuPageFunction");
}

function AWM_EZ_CL_plugin_options() {
    global $wpdb;
	include('AWM-admin-settings.php');
}

function AWM_EZ_CLS_plugin_options() {
    global $wpdb;
	include('AWM-admin-CL-settings.php');
}

function AWM_EZ_LLS_plugin_options() {
    global $wpdb;
	include('AWM-admin-LL-settings.php');
}

function updateDatabase () {
global $wpdb;
	//index update
        
		$countIndex=$wpdb->query("SHOW INDEX FROM ".AWM_TABLE." WHERE Key_name='status'");	
		if($countIndex==0) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD INDEX (status)");	
		}
		$countIndex=$wpdb->query("SHOW INDEX FROM ".AWM_TABLE." WHERE Key_name='hideShow'");
    
		if($countIndex==0) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD INDEX (hideShow)");	
		}
		$countIndex=$wpdb->query("SHOW INDEX FROM ".AWM_TABLE." WHERE Key_name='lockType'");	
		if($countIndex==0) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD INDEX (lockType)");	
		}		
		$findColumn=$wpdb->get_results("SHOW COLUMNS FROM ".AWM_TABLE, ARRAY_A);//$wpdb->query("SHOW COLUMNS FROM ".AWM_TABLE);
    
		$totalLeads=false;
		$hideShow=false;
        foreach($findColumn as $rowColumn){
            if($rowColumn['Field']=='totalLeads') {
				$totalLeads=true;
			}	
			if($rowColumn['Field']=='hideShow') {
				$hideShow=true;
			}
			if($rowColumn['Field']=='API_key') {
				$hideAPI=true;
			}
			if($rowColumn['Field']=='profileID') {
				$hideprofileID=true;
			}
			if($rowColumn['Field']=='disallowDomains') {
				$hidedisallowDomains=true;
			}
			if($rowColumn['Field']=='allowDomains') {
				$hideallowDomains=true;
			}
			if($rowColumn['Field']=='lockType') {
				$hidelockType=true;
			}
        }
		/*while($rowColumn=mysql_fetch_array($findColumn)) {			
			if($rowColumn['Field']=='totalLeads') {
				$totalLeads=true;
			}	
			if($rowColumn['Field']=='hideShow') {
				$hideShow=true;
			}
			if($rowColumn['Field']=='API_key') {
				$hideAPI=true;
			}
			if($rowColumn['Field']=='profileID') {
				$hideprofileID=true;
			}
			if($rowColumn['Field']=='disallowDomains') {
				$hidedisallowDomains=true;
			}
			if($rowColumn['Field']=='allowDomains') {
				$hideallowDomains=true;
			}
			if($rowColumn['Field']=='lockType') {
				$hidelockType=true;
			}										
		}*/
		if($hideShow==false) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD `hideShow` INT( 1 ) NOT NULL");
		}
		if($totalLeads==false) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD `totalLeads` INT( 10 ) NOT NULL");						
		}
		if($hideAPI==false) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD `API_key` varchar(35) NOT NULL");						
		}
		if($hideprofileID==false) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD `profileID` int(11) NOT NULL");						
		}
		if($hidedisallowDomains==false) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD `disallowDomains` varchar(250) NOT NULL");						
		}
		if($hideallowDomains==false) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD `allowDomains` varchar(250) NOT NULL");						
		}
		if($hidelockType==false) {
			$wpdb->query("ALTER TABLE ".AWM_TABLE." ADD `lockType` int(1) NOT NULL");						
		}			
	// -----------
	
}

/**
 * Add Settings link to plugins - code from GD Star Ratings
 */
function AWM_add_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
 
	if ($file == $this_plugin){
		$settings_link = '<a href="admin.php?page=' . $this_plugin . '">'.__("Settings", "AWM-Locker-Settings").'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}

function AWM_LOCK_PAGE() {
    global $wpdb;
    $table_name=$wpdb->prefix."AWM_EZ_CL_options";
	updateDatabase();
		
	$searchProfiles=$wpdb->get_results("SELECT * FROM ".AWM_TABLE." WHERE status=1",ARRAY_A);

	if($wpdb->num_rows>0) {	
		$lockPage=false;
		$trigger=false;
        
        /*
		while($row=mysql_fetch_array($searchProfiles)) {*/
        foreach($searchProfiles as $row){
            
			$pages=explode(',', $row['pages']);
			$posts=explode(',', $row['posts']);			
			if($lockPage==true) { continue; }
			if(in_array('HP', $pages) && is_home()) {
				$trigger=true;	
			} 
			foreach($pages as $pg) {
				if(is_page($pg)) {
					$trigger=true;	
					break;
				}
			}			
			//if($trigger==false) {
			foreach($posts as $pg) {
				if(is_single($pg)) {
					$trigger=true;	
					break;
				}
			}					
			//}		
			$hideLockPF=false;
			if($row['hideShow']==1) {
				if(is_user_logged_in()) {
					$trigger=false;
					$hideLockPF=true;
				}
			}

			if($row['hideShow']==2) {
				if (current_user_can('administrator')) {
					$trigger=false;
					$hideLockPF=true;
				}
			}	
			
			if($row['hideShow']==3) {
				if (current_user_can('administrator') || is_user_logged_in()) {
					$trigger=false;
					$hideLockPF=true;
				}
			}
			
			if(stripos(EZAWM_curPageURL(), '/wp-admin/')!==false) {
				$trigger=false;	
				$hideLockPF=true;
			}																		
						
			if($row['display']==0 && $hideLockPF==false) {
				if($trigger==true) {
					if($row['lockType']==0) {
						if(!empty($row['locker_code'])) {
							$wpdb->query("UPDATE $table_name SET totalLeads=totalLeads+1 WHERE ID=".$row['ID']." LIMIT 1");
							echo stripslashes($row['locker_code']);
							$lockPage=true;								
						}						
					} else {
						if(!empty($row['API_key'])) {
							$wpdb->query("UPDATE $table_name SET totalLeads=totalLeads+1 WHERE ID=".$row['ID']." LIMIT 1");
							
							echo '<script type="text/javascript" src="http://swftfile.com/api/auto_link.php"></script> 
							<script type="text/javascript">
								var API_key = "'.$row['API_key'].'";
								var profileID = "'.$row['profileID'].'";
								';
								if(!empty($row['disallowDomains'])) {
									$exD=explode(',',$row['disallowDomains']);								
									if(sizeof($exD)<=1) {
										echo 'var disallowDomains = ["'.str_replace(' ' , '', $exD[0]).'"];';		
									} else {
										echo 'var disallowDomains = [';
										foreach($exD as $exDD) {
											if($exDD==$exD[0]) {
												echo '"';
												echo str_replace(' ' , '', $exDD);
												echo '"';				
											} else {
												echo ', "';
												echo str_replace(' ' , '', $exDD);
												echo '"';
											}
										}
										echo '];';
									}
								} elseif(!empty($row['allowDomains'])) {
									$incD=explode(',',$row['allowDomains']);								
									if(sizeof($incD)<=1) {
										echo 'var allowDomains = ["'.str_replace(' ' , '', $incD[0]).'"];';		
									} else {
										echo 'var allowDomains = [';
										foreach($incD as $incDD) {
											if($incDD==$incD[0]) {
												echo '"';
												echo str_replace(' ' , '', $incDD);
												echo '"';				
											} else {
												echo ', "';
												echo str_replace(' ' , '', $incDD);
												echo '"';
											}
										}
										echo '];';
									}	
								}
								echo'</script>';
														
							$lockPage=true;	
						}
					}
				}
			} else {
				if($trigger==false && $hideLockPF==false) {
					if($row['lockType']==0) {
						if(!empty($row['locker_code'])) {
							$wpdb->query("UPDATE $table_name SET totalLeads=totalLeads+1 WHERE ID=".$row['ID']." LIMIT 1");
							echo stripslashes($row['locker_code']);
							$lockPage=true;								
						}						
					} else {
						if(!empty($row['API_key'])) {
							$wpdb->query("UPDATE $table_name SET totalLeads=totalLeads+1 WHERE ID=".$row['ID']." LIMIT 1");
							
							echo '<script type="text/javascript" src="http://swftfile.com/api/auto_link.php"></script> 
							<script type="text/javascript">
								var API_key = "'.$row['API_key'].'";
								var profileID = "'.$row['profileID'].'";
								';
								if(!empty($row['disallowDomains'])) {
									$exD=explode(',',$row['disallowDomains']);								
									if(sizeof($exD)<=1) {
										echo 'var disallowDomains = ["'.str_replace(' ' , '', $exD[0]).'"];';		
									} else {
										echo 'var disallowDomains = [';
										foreach($exD as $exDD) {
											if($exDD==$exD[0]) {
												echo '"';
												echo str_replace(' ' , '', $exDD);
												echo '"';				
											} else {
												echo ', "';
												echo str_replace(' ' , '', $exDD);
												echo '"';
											}
										}
										echo '];';
									}
								} elseif(!empty($row['allowDomains'])) {
									$incD=explode(',',$row['allowDomains']);								
									if(sizeof($incD)<=1) {
										echo 'var allowDomains = ["'.str_replace(' ' , '', $incD[0]).'"];';		
									} else {
										echo 'var allowDomains = [';
										foreach($incD as $incDD) {
											if($incDD==$incD[0]) {
												echo '"';
												echo str_replace(' ' , '', $incDD);
												echo '"';				
											} else {
												echo ', "';
												echo str_replace(' ' , '', $incDD);
												echo '"';
											}
										}
										echo '];';
									}	
								}
								echo'</script>';
														
							$lockPage=true;	
						}
					}
				}
			}			
		}
		/*}*/		
	}
}

function createLocker($name, $code, $pages, $posts, $display, $hideShow) {
        global $wpdb;
        $table_name=$wpdb->prefix."AWM_EZ_CL_options";
        $wpdb->query($wpdb->prepare("INSERT INTO $table_name ( name, locker_code, status, pages, posts, display, hideShow) VALUES( %s, %s, %d, %s, %s, %s, %s)",$name,$code,1,$pages,$posts,$display,$hideShow));
		/*mysql_query("INSERT INTO ".AWM_TABLE." SET name='".mysql_real_escape_string($name)."', locker_code='".mysql_real_escape_string($code)."', status=1, pages='".mysql_real_escape_string($pages)."', posts='".mysql_real_escape_string($posts)."', display='".mysql_real_escape_string($display)."', hideShow='".mysql_real_escape_string($hideShow)."'") or die(mysql_error());	*/	
		echo '<div class="updated"><p><strong>Success, your profile has been created.</strong></p></div>';
		return true;
}

function createLLLocker($name, $code, $pages, $posts, $display, $hideShow, $profileID, $exD, $incD) {
    global $wpdb;
    $table_name=$wpdb->prefix."AWM_EZ_CL_options";
    $wpdb->query($wpdb->prepare("INSERT INTO $table_name (name,API_key,profileID,disallowDomains,allowDomains,status,pages,posts,display,hideShow,lockType) VALUES (%s,%s,%d,%s,%s,%d,%s,%s,%s,%s,%d)",$name,$code,$profileID,$exD,$incD,1,$pages,$posts,$display,$hideShow,1));
    
		/*mysql_query("INSERT INTO ".AWM_TABLE." SET name='".mysql_real_escape_string($name)."', API_key='".mysql_real_escape_string($code)."', profileID='".mysql_real_escape_string($profileID)."', disallowDomains='".mysql_real_escape_string($exD)."', allowDomains='".mysql_real_escape_string($incD)."', status=1, pages='".mysql_real_escape_string($pages)."', posts='".mysql_real_escape_string($posts)."', display='".mysql_real_escape_string($display)."', hideShow='".mysql_real_escape_string($hideShow)."', lockType=1") or die(mysql_error());*/		    
		echo '<div class="updated"><p><strong>Success, your profile has been created.</strong></p></div>';
		return true;
}


function updateLocker($ID, $name, $code, $status, $pages, $posts, $display, $hideShow) {
    global $wpdb;
    $table_name=$wpdb->prefix."AWM_EZ_CL_options";
    $wpdb->query($wpdb->prepare("UPDATE $table_name SET name='%s', locker_code='%s', status='%d', pages='%s', posts='%s', display='%s', hideShow='%s' WHERE ID='%s' LIMIT 1",$name,$code,$status,$pages,$posts,$display,$hideShow,$ID));
    
		/*mysql_query("UPDATE ".AWM_TABLE." SET name='".mysql_real_escape_string($name)."', locker_code='".mysql_real_escape_string($code)."', status='".mysql_real_escape_string($status)."', pages='".mysql_real_escape_string($pages)."', posts='".mysql_real_escape_string($posts)."', display='".mysql_real_escape_string($display)."', hideShow='".mysql_real_escape_string($hideShow)."' WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");	*/	
		echo '<div class="updated"><p><strong>Success, your profile has been updated.</strong></p></div>';
		return true;
}

function updateLLLocker($ID, $name, $code, $status, $pages, $posts, $display, $hideShow, $profileID, $exD, $incD) {	
    global $wpdb;
    $table_name=$wpdb->prefix."AWM_EZ_CL_options";
    $wpdb->query($wpdb->prepare("UPDATE $table_name SET name='%s', API_key='%s', profileID='%s', disallowDomains='%s', allowDomains='%s', status='%d', pages='%s', posts='%s', display='%s', hideShow='%s' WHERE ID='%s' LIMIT 1",$name,$code,$profileID,$exD,$incD,$status,$pages,$posts,$display,$hideShow,$ID));
    
		/*mysql_query("UPDATE ".AWM_TABLE." SET name='".mysql_real_escape_string($name)."', API_key='".mysql_real_escape_string($code)."', profileID='".mysql_real_escape_string($profileID)."', disallowDomains='".mysql_real_escape_string($exD)."', allowDomains='".mysql_real_escape_string($incD)."', status='".mysql_real_escape_string($status)."', pages='".mysql_real_escape_string($pages)."', posts='".mysql_real_escape_string($posts)."', display='".mysql_real_escape_string($display)."', hideShow='".mysql_real_escape_string($hideShow)."' WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");*/		
		echo '<div class="updated"><p><strong>Success, your profile has been updated.</strong></p></div>';
		return true;
}


function deleteLocker($ID) {
    global $wpdb;
    $table_name=$wpdb->prefix."AWM_EZ_CL_options";
		$wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE ID='%s' LIMIT 1",$ID));		
		echo '<div class="updated"><p><strong>Success, your profile has been deleted.</strong></p></div>';
		return true;
}

function EZAWM_curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

function choosePosts($showing, $lookup)
{
	if(!is_array($showing))	{ $showing=array();	}	
	$get_all_posts = get_posts(array('numberposts' => -1));	
	if($lookup=='new') {
		$name='new_posts[]';
	} elseif($lookup>0) {
		$name='posts'.$lookup.'[]';
	} else {
		$name='posts[]';	
	}
	$out='<select multiple="multiple" size="10" name="'.$name.'" style="height:160px;width:350px;">';
	
	foreach($get_all_posts as $this_post) {
		$ID=$this_post->ID;		
		
		if(in_array($ID, $showing)) {
			$special=' selected="selected"';
		} else {
			$special='';
		}
		$out.='<option value="'.$ID.'"'.$special.'>'.$this_post->post_title.'</option>';
	}
	$out.='</select>';	
	return $out;	
}

function choosePages($showing, $lookup)
{
	if(!is_array($showing))	{ $showing=array();	}	
	$get_all_pages = get_pages(array('numberposts' => -1));		
	if($lookup=='new') {
		$name='new_pages[]';
	} elseif($lookup>0) {
		$name='pages'.$lookup.'[]';
	} else {
		$name='pages[]';	
	}	
	$out='<select multiple="multiple" size="10" name="'.$name.'" style="height:160px;width:250px;">';
	if(in_array('HP', $showing)) {
		$specialHome=' selected="selected"';
	} else {
		$specialHome='';
	}
		$out.='<option value="HP"'.$specialHome.'>Homepage</option>';		
	foreach($get_all_pages as $this_page) {
		$ID=$this_page->ID;		
		if(in_array($ID, $showing)) {
			$special=' selected="selected"';
		} else {
			$special='';
		}
		$out.='<option value="'.$ID.'"'.$special.'>'.$this_page->post_title.'</option>';
	}
	$out.='</select>';	
	return $out;	
}


//ADD HOOKS
add_filter('plugin_action_links', 'AWM_add_settings_link', 10, 2);
add_action('wp_print_scripts', 'AWM_LOCK_PAGE',0);
add_action('admin_menu', 'AWM_EZ_CL_plugin_menu');
add_action( 'admin_head', 'AWM_EZ_CL_LOAD_CSS' );
?>