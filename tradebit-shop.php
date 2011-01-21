<?php
/*
   Plugin Name: Tradebit Download Shop and Affiliate Tool
   Plugin URI: http://www.tradebit.info/downloads/wordpress.php
   Description: Tradebit is the leading platform to publish and sell digital goods like photos and music. This wordpress plugin offers a seamless download shop integration and easy access to the powerful affiliate functionality <a href="http://www.tradebit.com/indexNew.php/affiliate">on tradebit</a>!
   Author: puzzler
   Version: 3.0.0
   Author URI: http://www.tradebit.com/channels/
*/

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', '1');

// create tradebit administration menu
function tb_adminoptions() {
	add_menu_page(__('Tradebit Configuration','tradebit-menu'), __('Tradebit Shop','tradebit-menu'), 'manage_options', 'tradebit-top-level-handle', 'tradebit_edit_settings' );
	add_submenu_page('tradebit-top-level-handle', __('Memberarea','tradebit-menu'), __('Tradebit Memberarea','tradebit-menu'), 'manage_options', 'sub-page', 'tradebit_loadmember');
}

// -------------------------------------------------------------------------
// Settings menu in the top level
function tradebit_edit_settings()
{
	if($_REQUEST["tbitaction"]=="tbclear")
	{
		delete_option("tbbitlogin");
		delete_option("tbitpw");
		delete_option("tbituid");
		delete_option("tbitppal");
		delete_option("tbitactive");
	}

	$tbitactive=get_option("tbitactive");

	if($tbitactive!="true")
	{
		if($_REQUEST["tbitaction"]!="tbitcreate")
			tbitcreateuserform();
		else
			tbitcreateuserremote();
	}
	else
	{
		$tbitlogin=get_option("tbbitlogin");
		$tbitpw=get_option("tbitpw");
		$tbituid=get_option("tbituid");
		$tbitppal=get_option("tbitppal");
		print "
			<div style='margin:15px; padding:5px; border: 2px dotted #AFAFAF; width:480px; background-color: #FFFFFF;'>
				<img src='http://www.tradebit.com/layout-graphics/logo11.gif' alt='Tradebit Logo' border='0' align='right'>
				<div style='font-size: 14px;'>
				Your Tradebit settings:<BR>
				</div>
				<BR clear='all'>
				<B>Login:</B> $tbitlogin<BR>\n
				<B>Paypal eMail:</B> $tbitppal<BR>\n
				<B>User / affiliate ID:</B> $tbituid<BR>\n
				<BR clear='all'>
				<BR clear='all'>
				You may edit your settings in the Tradebit member area directly and pick up
				additional javascript boxes for promotions:<BR><BR>
				<UL>
					<LI>Tutorials: Learn to bulk upload, manage affiliates, edit your subdomain</LI>
					<LI>Promotions: Get your Tradebit power blogs promoted for free!</LI>
				</UL>
				<BR><BR>
				Clear wordpress options:
				<form action='".$_SERVER["REQUEST_URI"]."' METHOD='POST' style='margin: 0px;'>
				<input type='hidden' name='tbitaction' value='tbclear'>
				<input type='submit' value='clear'> (No undo! This does not clear your tradebit account.)
				</form>
			</div>
		";
	}
	return;
}
// tradebit user creation form
function tbitcreateuserform()
{
	print "
		<div style='margin:15px; padding:5px; border: 2px dotted #AFAFAF; width:480px; background-color: #FFFFFF;'>
			<img src='http://www.tradebit.com/layout-graphics/logo11.gif' alt='Tradebit Logo' border='0' align='right'>
			<div style='font-size: 14px;'>
			".__("Create your own <b>FREE</b> seller account<BR>with a few clicks:","tradebit-pages")."</div>
			<div style='font-size: 11px;'>
			".__("If you already have a Tradebit seller account, enter the values below matching your details on Tradebit.","tradebit-pages")."
			</div>
			<BR><BR>
			<form action='".$_SERVER["REQUEST_URI"]."' METHOD='POST' style='margin: 0px;'>
			<input type='hidden' name='tbitaction' value='tbitcreate'>
			<div style='float: left; width: 150px;'>".__("Desired username:","tradebit-pages")."</div>
			<div><input type='text' name='tbitlogin' value='".$_REQUEST["tbitlogin"]."'></div><BR clear='all'>
			<div style='float: left; width: 150px;'>".__("Password:","tradebit-pages")."</div>
			<div><input type='password' name='tbitpw' value='".$_REQUEST["tbitpw"]."'></div><BR clear='all'>
			<div style='float: left; width: 150px;'>".__("Password confirm:","tradebit-pages")."</div>
			<div><input type='password' name='tbitpw2' value='".$_REQUEST["tbitpw2"]."'></div><BR clear='all'>
			<div style='float: left; width: 150px;'>".__("Paypal payout eMail:","tradebit-pages")."</div>
			<div><input type='text' name='tbitpayoutmail' value='".$_REQUEST["tbitpayoutmail"]."'></div><BR clear='all'>
			<div style='float: left; width: 150px;'><a href='http://www.tradebit.com/terms.php' target='_blank'>".__("Accept terms:","tradebit-pages")."</a></div>
			<div><input type='checkbox' name='tbitterms' value='yes'> ".__("30% commission on successful sales","tradebit-pages")."</div><BR clear='all'>
			<BR>
			<div style='font-size: 11px;'>
			".__("Submitting this form creates a free seller account on Tradebit.com.<BR>Your login is also your free subdomain on the site!","tradebit-pages")."
			".__("Tradebit offers an integrated affiliate network and pays 9.5% to affiliates from their commissions. Standard payout rates are at 70%.","tradebit-pages")."
			".__("No monthly fees, no additional charges. Sales are paid once a week to the paypal email on file!","tradebit-pages")."
			</div>
			<BR>
			<input type='submit' name='tbsubmit' value='".__("Great, create the account now!","tradebit-pages")."' style='float: right; background-color: #EEFFEE;'>
			<BR clear='all'>
		</div>
	";
}
// contact tradebit.com to create the user
function tbitcreateuserremote()
{
	$mytbitlogin=$_REQUEST["tbitlogin"];
	$mytbitpw=$_REQUEST["tbitpw"];
	$mytbitpw2=$_REQUEST["tbitpw2"];
	$mytbitpayoutmail=$_REQUEST["tbitpayoutmail"];
	$mytbitterms=$_REQUEST["tbitterms"];
	$mytbitpluginurl = WP_PLUGIN_URL."/tradebit-shop/";

	$mytbiterror="";

	if(strlen($mytbitlogin)<4) $mytbiterror="Login too short! Please use at least 4 characters...";
	if(strlen($mytbitpw)<4) $mytbiterror="Password too short! Please use at least 4 characters...";
	if($mytbitpw!=$mytbitpw2) $mytbiterror="Password confirmation entry wrong: you entered 2 different passwords!";
	if($mytbitterms!="yes") $mytbiterror="Please accept the terms!";
	if(substr_count($mytbitpayoutmail,"@")<1 OR substr_count($mytbitpayoutmail,".")<1 ) $mytbiterror="Payout email for paypal seems to be wrong!";

	if(strlen($mytbiterror)>2)
	{
		print "<B style='font-size: 14px;'>".$mytbiterror."</B><BR><BR>\n";
		tbitcreateuserform();
	}
	else
	{
		$myopenurl="http://www.tradebit.com/indexNew.php/sty_txt/guestarea/createConfirmAccountAPI?l=".urlencode($mytbitlogin)."&p=".urlencode($mytbitpw)."&pp=".urlencode($mytbitpayoutmail)."&caller=".urlencode($mytbitpluginurl);
		$mytbitresult=file_get_contents($myopenurl);

		$myresparts = explode(",",$mytbitresult);
		print "<div style='margin:15px; padding:5px; border: 2px dotted #AFAFAF; width:480px; background-color: #FFFFFF;'>
				<img src='http://www.tradebit.com/layout-graphics/logo11.gif' alt='Tradebit Logo' border='0' align='right'>
				<div style='font-size: 14px;'>";
		print 	$myresparts[0]."<BR><BR>\n";
		print 	$myresparts[1]."<BR><BR>\n";
		print "<!-- $myopenurl : $mytbitresult -->\n";

		if($myresparts[0]=="FOUND" OR $myresparts[0]=="CREATED")
			$tbitsuccess=true;
		else
			$tbitsuccess=false;

		//$tbitsuccess=true;
		//$myresparts[1]=1;

		if($tbitsuccess)
		{
			if(0+$myresparts[1]>0)
			{
				add_option("tbbitlogin",$mytbitlogin,"","no");
				add_option("tbitpw",md5($mytbitpw),"","no");
				add_option("tbitppal",$mytbitpayoutmail,"","no");
				add_option("tbituid",(0+$myresparts[1]),"","no");

				add_option("tbitactive","true","","no");
				echo "<b>User created!</b><BR><BR>\n";
				echo "Do not forget to activate your WIDGET in the Settings page for the sidebar...<BR>\n";
			}
			else
			{
				print "Unknown user id!";
			}
		}
		else
		{

		}
		print "	</div>\n
			   </div>\n";
	}
}
// remove settings on deactivation!
function tbitremoveplugin()
{
	echo "removing local settings";
}

// menu for the page
function tradebit_loadmember()
{
	$mytbiturl="http://www.tradebit.com/";
	$tbitactive=get_option("tbitactive");

	if($tbitactive!="true")
	{
		if($_REQUEST["tbitaction"]!="tbitcreate")
			tbitcreateuserform();
		else
			tbitcreateuserremote();
	}
	else
	{
		$tbitlogin=get_option("tbbitlogin");
		$tbitpw=get_option("tbitpw");
		$tbituid=get_option("tbituid");
		$tbitppal=get_option("tbitppal");

		echo "<h2>" . __( 'Launching Tradebit', 'tradebit-menu' ) . "</h2>";
		echo __( 'If Tradebit does not open here, please edit your products in a new window via this link:', 'tradebit-menu' );
		echo "<BR><BR>\n";
		echo '
		<div class="dhtmlgoodies_window">
			<div class="dhtmlgoodies_window_top">
				<img src="'.WP_PLUGIN_URL."/tradebit-shop/".'images/top_left.gif" align="left">
				<img src="'.WP_PLUGIN_URL."/tradebit-shop/".'images/top_center.gif" class="topCenterImage">
				<div class="top_buttons">
					<img class="closeButton" src="'.WP_PLUGIN_URL."/tradebit-shop/".'images/close.gif">
					<img src="'.WP_PLUGIN_URL."/tradebit-shop/".'images/top_right.gif">
				</div>
			</div>
			<div class="dhtmlgoodies_windowMiddle">
				<div class="dhtmlgoodies_windowContent">
				<iframe src="'.$mytbiturl.'" width="100%" height="100%" id="tbitmember" name="tbitmember"></iframe>
				</div>
			</div>
			<div class="dhtmlgoodies_window_bottom">
				<img class="resizeImage" src="'.WP_PLUGIN_URL."/tradebit-shop/".'images/bottom_right.gif">
			</div>
		</div>
		<form action="'.$mytbiturl.'/mytradebit/" id="tbdologin" method="POST" target="tbitmember">
		<input type="hidden" name="action" value="login">
		<input type="hidden" name="login" value="'.$tbitlogin.'">
		<input type="hidden" name="pw" value="'.$tbitpw.'">
		<input type="hidden" name="md5" value="y">
		<input type="hidden" value="Open member area">
		</form>
		<a href="'.$mytbiturl.'" target="_blank">Go to tradebit.com</a>
		<script type="text/javascript">
		// Setting initial size of windows
		// These values could be overridden by cookies.
		windowSizeArray[1] = [996,500];	// Size of first window
		windowPositionArray[1] = [20,20]; // X and Y position of first window
		document.forms["tbdologin"].submit();
		</script>
		';
	}
}

// enhance the editor with a button
function tradebit_edit_plug($initcontext)
{
	$mytbdir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$mybutton="<a href='http://www.tradebit.com/G/shop/".get_option("tbituid")."' ";
	$mybutton.="onClick='window.open(\"http://www.tradebit.com/sty_simple/G/shop/".get_option("tbituid")."\",\"Tradebit\",\"width=640,height=500,scrollbars=yes,toolbar=no,location=yes,directorys=no\"); return false;' ";
	$mybutton.="target='_blank'><img src='".$mytbdir."images/edit_button.gif' title='add tradebit file'></a>";
	return $initcontext.$mybutton;
}

// ---------------------------------------------------------------------------------- Widget part
// display the sidebar widget
function tbitWidget_display()
{
	if(get_option("tbituid")>0)
	{
		echo "<h3 class='widget-title'>".__("Download Shop","tradebit-pages")."</h3>\n";
		$mybutton="<a href='http://www.tradebit.com/G/shop/".get_option("tbituid")."' ";
		$mybutton.="onClick='window.open(\"http://www.tradebit.com/sty_simple/G/shop/".get_option("tbituid")."\",\"Tradebit\",\"width=640,height=500,scrollbars=yes,toolbar=no,location=yes,directorys=no\"); return false;' ";
		$mybutton.="target='_blank' style=\"font-weight: bold;\">Show Downloads</a>";
		echo "<UL><LI>";
		echo $mybutton;
		echo "</LI></UL>\n";
		echo "<BR><BR>\n";
	}
}
function tbitWidget_install()
{
	register_sidebar_widget(__('Download Shop',"tradebit-pages"), 'tbitWidget_display');
}
// --------------------------------------------------------------------------------------------
// Allow direct VERIFICATION call from tradebit.com to avoid spammers
if($_REQUEST["verification"]!="tradebit")
{
	wp_register_script("myfloating",plugins_url()."/tradebit-shop/js/floating-window.js");
	wp_register_style("myfloatingstyle",plugins_url()."/tradebit-shop/css/floating-window.css");
	wp_enqueue_script("myfloating");
	wp_enqueue_style("myfloatingstyle");

	// admin
	add_action('admin_menu', 'tb_adminoptions');
	add_filter('media_buttons_context','tradebit_edit_plug');

	// widget
	add_action("plugins_loaded", "tbitWidget_install");
}
else
{
	print "Tradebit Wordpress Shop Plugin - see <a href='http://www.tradebit.info/downloads/wordpress.php'></a>.";
}
?>