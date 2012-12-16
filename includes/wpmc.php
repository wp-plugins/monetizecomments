<?php
session_start();
$maxNoActivity= 3600 ;
$Overlaymax=100;
if($_SESSION['googleAdDisplay']=="") {$_SESSION['googleAdDisplay']="none";}
if($_SESSION['activeTime']>0)
{
     $difference = (time() - $_SESSION['activeTime']);
	if($difference > $maxNoActivity) {$_SESSION['googleAdDisplay']="none";}
}
if($_SESSION['overlayAdDisplay']=="") {$_SESSION['overlayAdDisplay']="block";}
/*if($_SESSION['OverlayactiveTime']>0)
{
     $difference = (time() - $_SESSION['OverlayactiveTime']);
	if($difference > $Overlaymax) {$_SESSION['overlayAdDisplay']="block";}
}*/
$count=1;
global $googleAdValue;
include "getactive.php";
if( ! class_exists('wp_mc_comments')):

class wp_mc_comments

{

	var $date_format;

	var $wpmc_title;

	var $wpmc_commentformoverride;
   
    var $count;
    var $j;
    var $commentsCount;
	function __construct()

	{

		global $wpdb;

		$this->date_format 				= 'j M, Y h:i A';

		$this->wpmc_commentformoverride		= false;

         $this->count=1;
         $this->j=1;
         $this->commentsCount=0;


		if ( basename(dirname( WPMC_FILE )) == 'plugins' ){

			define( "WPMC_DIR"			, ABSPATH 			. 'wp-content/plugins/'		);
			


			define( "WPMC_URL"			, get_option("siteurl")	. '/wp-content/plugins/'	);

		} else {

			define( "WPMC_DIR"			, ABSPATH 			. 'wp-content/plugins/'.basename(dirname( WPMC_FILE )) . '/');

			define( "WPMC_URL"			, get_option("siteurl")	. '/wp-content/plugins/'.basename(dirname( WPMC_FILE )) . '/');

		}

		define( "WPMC_ADMIN_PER_PAGE"			, 20								);

		define( "WPMC_VER"				, "1.0.0" 							);



		add_action( 'init'				, array( &$this, 'wpmc_reload_login'		));

		add_action( 'admin_menu'			, array( &$this, 'wpmc_options_page'		));



		add_action( 'wp_print_styles'			, array( &$this, 'wpmc_style'				));

		add_action( 'wp_print_scripts'		, array( &$this, 'wpmc_script'			));

		add_action( 'comments_template'		, array( &$this, 'wpmc_comments_template'		));	

		add_action( 'comment_post'			, array( &$this, 'wpmc_add_commentmeta'		));



		add_action( 'wp_ajax_wpmc_like'		, array( &$this, 'wpmc_likedislike'			));

		add_action( 'wp_ajax_nopriv_wpmc_like'	, array( &$this, 'wpmc_likedislike'			));



		add_filter( 'plugin_action_links'		, array( &$this, 'wpmc_plugin_actions'		), 10, 2 );

		add_filter( 'preprocess_comment'		, array( &$this, 'wpmc_process_comment'		));

		add_filter( 'cancel_comment_reply_link'	, array( &$this, 'wpmc_cancelcommentlink'), 20, 3);





		register_activation_hook( WPMC_FILE	, array( &$this, 'wpmc_activate'				)); 

		register_deactivation_hook ( WPMC_FILE	, array( &$this, 'wpmc_deactivate'			));



		if( ! $wpmc_ver = get_option ("wpmc_ver") )

			update_option ("wpmc_ver", WPMC_VER);

	}



	/*

	 * // activate

	 */

	function wpmc_activate()

	{

		if( ! $wpmc_ver = get_option ("wpmc_ver") )

			update_option ("wpmc_ver", WPMC_VER);



		$wpmc_options = $this->wpmc_buildarray();

		update_option( 'wpmc_options', $wpmc_options );

	}
//time function
function time_stamp($time_ago)
{ 
	$ago = "";
	$cur_time=time();
	$time_elapsed = $cur_time - $time_ago; 
	$seconds = $time_elapsed ; 
	$minutes = round($time_elapsed / 60 );
	$hours = round($time_elapsed / 3600); 
	$days = round($time_elapsed / 86400 ); 
	$weeks = round($time_elapsed / 604800); 
	$months = round($time_elapsed / 2600640 ); 
	$years = round($time_elapsed / 31207680 ); 
	// Seconds
	if($seconds <= 60)
	{
		$ago = "$seconds seconds ago"; 
	}
	 //Minutes
	else if($minutes <=60)
	{
		if($minutes==1)
		{
			$ago = "a minute ago"; 
		}
		else
		{
			$ago = "$minutes minutes ago"; 
		}
 
	}
	//Hours
	else if($hours <=24)
	{
		if($hours==1)
		{
			$ago = "an hour ago";
		}
		else
		{
			$ago = "$hours hours ago";
		}
	
	}
	//Days
	else if($days <= 7)
	{
		
		if($days==1)
		{
			$ago = "yesterday";
		}
		else
		{
			$ago = "$days days ago";
		}
 	}
	//Weeks
	else if($weeks <= 4.3)
	{
		if($weeks==1)
		{
			$ago = "a week ago";
		}
		else
		{
			$ago = "$weeks weeks ago";
		}
	}
	//Months
	else if($months <=12)
	{
		if($months==1)
		{
			$ago = "a month ago";
		}
		else
		{
			$ago = "$months months ago";
		}
	}
	//Years
	else
	{
		if($years==1)
		{
			$ago = "one year ago";
		}
		else
		{
			$ago = "$years years ago";
		}
	}
	return $ago;
}


	/*

	 * // deactivate

	 */

	function wpmc_deactivate()

	{
		$wpmc_options2 = get_option('wpmc_options');
		$val="api_key=".$wpmc_options2['wpmc_api']."&e=2";
    	$url = 'http://www.monetizecomments.com/api/getstatus.php?'.$val;
		list( $ret, $res ) = fetchURL( $url );

	
		   if( $ret )
			{
				//echo $res;
			}
			else
			{
				$err[] = $res;
				echo "error".print_r($res);
			}
	//	delete_option('wpmc_options');

	
	}



	function wpmc_buildarray()

	{

		$wpmc_options = array();

		$wpmc_options['wpmc_enabled'] = 1;

		$wpmc_options['wpmc_template'] = 1;

		$wpmc_options['wpmc_cwidth'] = 468;

		return $wpmc_options;

	}



	/*

	 * // adding CSS file to the front end.

	 */

	function wpmc_style()

	{

		wp_enqueue_style ( 'wpmc_style1', 'http://www.monetizecomments.com/css/plugin.css' );

	}



	/*

	 * // adding JS file to the front end.

	 */

	function wpmc_script()

	{

		wp_enqueue_script ( 'wpmc_script1', WPMC_URL . 'js/common.js', array( 'jquery' ) );

		?>

		<script type='text/javascript'>

			ajax_nonce 	= '<?php echo wp_create_nonce( 'wpmc_ajax' ); ?>';

			ajaxurl 	= '<?php echo admin_url( 'admin-ajax.php' );  ?>';
			
			
			
		</script>
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo WPMC_URL."/includes/";?>animatedcollapse.js"></script>
	<script type="text/javascript">
	
	animatedcollapse.addDiv('googleAd', 'fade=1')
	animatedcollapse.ontoggle=function($, divobj, state){ //fires each time a DIV is expanded/contracted
		//$: Access to jQuery
		//divobj: DOM reference to DIV being expanded/ collapsed. Use "divobj.id" to get its ID
		//state: "block" or "none", depending on state
	}
	
	animatedcollapse.init()
	
	</script>
	<script type="text/javascript">
	
	animatedcollapse.addDiv('fortest', 'fade=1')
	animatedcollapse.ontoggle=function($, divobj, state){ //fires each time a DIV is expanded/contracted
		//$: Access to jQuery
		//divobj: DOM reference to DIV being expanded/ collapsed. Use "divobj.id" to get its ID
		//state: "block" or "none", depending on state
	}
	
	animatedcollapse.init()
	
	</script>	
	
	
			<script type='text/javascript'>
			
		function trim(stringToTrim) 
		{
			return stringToTrim.replace(/^\s+|\s+$/g,"");
		}
		
		function updateText(act,val,id)
		{
			if(act=='click')
			{
				if(trim(document.getElementById(id).value)==val)
				{
					document.getElementById(id).value = "";
					document.getElementById(id).style.color="black";
				}
			}
			if(act=='blur')
			{
				if(trim(document.getElementById(id).value)=="")
				{
					document.getElementById(id).value =val;
					document.getElementById(id).style.color="#666666";
				}
			}
		}
		
		
		
		$(window).resize(function() {
		//	alert("test");
  		if(document.getElementById("wpmc-respond"))
  		{
  			var windowLeft4rd=document.getElementById("wpmc-respond").offsetLeft;
   			var windowTop4rd=document.getElementById("wpmc-respond").offsetTop;
   			windowWidth4rd=document.getElementById("wpmc-respond").offsetWidth;
   			document.getElementById("fade").style.left = windowLeft4rd+"px";
  			 document.getElementById("fade").style.top = parseInt(windowTop4rd)+"px";
		}
		
});
			
		function closeit()
{
	animatedcollapse.hide('fortest');
	//alert($args['wpmc_url']);
	document.getElementById("wpmc_fields").style.opacity="0.8";
   document.getElementById("wpmc_fields").style.filter="alpha(opacity=80)";
	var strURL="<?php echo WPMC_URL."/includes/ajax.php?f=2&overlayad=block"?>";
	//alert(strURL);
		req = getXMLHTTP();
		if (req) 
		{	 
			req.onreadystatechange = function() 
			{
				//alert("state"+ req.readyState+ " : "+req.status);
				if (req.readyState == 4) 
				{
					if (req.status == 200) 
					{	
						var res = req.responseText;
						//alert(res);
						
					}		
				}
			}
		}
		if(strURL)
		{
			req.open("GET", strURL, true);
			req.send(null);	
		}			
}
function fetchGoogleAd()
{
	var googlead,googlead2;
	var keywords=trim(document.getElementById("keywords").value);
	var pid="";
	var siteid="";
	var val="";
	var DetechFormAd=document.getElementById("DetechFormAd").value;
	if(DetechFormAd=="On")
	{
		siteid=document.getElementById("siteid").value;
		pid=document.getElementById("pid").value;
		val=document.getElementById("val").value;
		if(trim(keywords)!="")
		{
			var Keyarray=keywords.split(",");
			var matchval=(document.getElementById("comment_text").value).toLowerCase();
			for(var j=0;j<Keyarray.length;j=j+1)
			{
			   if(matchval.indexOf(trim(Keyarray[j]).toLowerCase())!=-1)
			   {
				   $match=1;
				   break;
			    }
			    else
			    {
					$match=0;
				}
			}
			if($match==1)
			{
					animatedcollapse.show('googleAd');
					googlead="block";
					
			}
			else
			{
				googlead="none";
			}
		}
		else
		{
					animatedcollapse.show('googleAd');
					googlead="block";
				
		}
		
	}
	else
	{
		googlead="none";
	}
		
	var strURL="<?php echo  WPMC_URL."/includes/ajax.php?f=1&googlead="?>"+googlead+"&siteid="+siteid+"&pid="+pid+"&var="+val+"";
	//alert(strURL);
		req = getXMLHTTP();
		if (req) 
		{	 
			req.onreadystatechange = function() 
			{
				//alert("state"+ req.readyState+ " : "+req.status);
				if (req.readyState == 4) 
				{
					if (req.status == 200) 
					{	
						var res = req.responseText;
						//alert(res);
						
					}		
				}
			}
		}
		if(strURL)
		{
			req.open("GET", strURL, true);
			req.send(null);	
		}			
	
}
function getXMLHTTP() 
{ 
	//fuction to return the xml http object
	xmlhttp=false;	
	try
	{
		xmlhttp=new XMLHttpRequest();
	}
	catch(e)	
	{		
		try
		{			
			xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch(e1)
			{
				xmlhttp=false;
			}
		}
	}
	return xmlhttp;
}
</script>

		<?php

	}



	function wpmc_plugin_actions($links, $file)

	{

		if( strpos( $file, basename(WPMC_FILE)) !== false )

		{

			$link = '<a href="'.get_option('siteurl').'/wp-admin/options-general.php?page=wpmc_main">'.__('Settings', 'wpmc_lang').'</a>';

			array_unshift( $links, $link );

		}

		return $links;

	}



	function wpmc_footer() {

		$plugin_data = get_plugin_data( WPMC_FILE );

		printf('%1$s | Provided by %3$s | Version %2$s <br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']); 

	}



	function wpmc_page_footer() {

		echo '<br/><div id="page_footer" style="text-align:center"><em>';

		self::wpmc_footer(); 

		echo '</em><br/>';

	}



	function wpmc_options_page()

	{

		add_options_page( 'MonetizeComments - Settings', 'MonetizeComments', 8, 'wpmc_main', array( &$this, 'wpmc_main' ) );

	}



	function wpmc_reload_login()

	{

		if( isset( $_GET['wpmc_ireload'] ) && $_GET['wpmc_ireload'] == '1' )

		{

			?>

			<script type="text/javascript">
			

				function getQueryVariable(variable) {
                   
					var query = window.top.location.search.substring(1);

					var vars = query.split("&");

					for (var i = 0; i < vars.length; i++) {

						var pair = vars[i].split("=");

						if (pair[0] == variable) {

							return unescape(pair[1]);

						}

					}

				}

				var reply_to = window.top.document.getElementById('comment_parent').value;

				replytocom = getQueryVariable('replytocom');

				var current = window.top.location.href;

				if( window.top.location.href.indexOf("#") > 0 )

					current = window.top.location.href.split("#")[0];



				if( replytocom > 0 || reply_to == 0 )

					window.top.location.reload();

				else if( window.top.location.href.indexOf("?") > 0 )

					window.top.location.href = current+'&replytocom='+reply_to+'#wpmc-respond_reply';

				else

					window.top.location.href = current+'/?replytocom='+reply_to+'#wpmc-respond_reply';



			</script>

			<?php

			die();

		}

		if( isset( $_GET['wpmc_preload'] ) && $_GET['wpmc_preload'] == '1' )

		{

		?>

			<script type="text/javascript">

				function getQueryVariable(variable) {
					var query = window.opener.location.search.substring(1);

					var vars = query.split("&");

					for (var i = 0; i < vars.length; i++) {

						var pair = vars[i].split("=");

						if (pair[0] == variable) {

							return unescape(pair[1]);

						}

					}

				}

				var reply_to = window.opener.document.getElementById('comment_parent').value;

				replytocom = getQueryVariable('replytocom');



				var current = window.opener.location.href;

				if( window.opener.location.href.indexOf("#") > 0 )

					current = window.opener.location.href.split("#")[0];



				if( replytocom > 0 || reply_to == 0 )

					window.opener.location.reload();

				else if( window.opener.location.href.indexOf("?") > 0 )

					window.opener.location.href = current+'&replytocom='+reply_to+'#wpmc-respond_reply';

				else

					window.opener.location.href = current+'/?replytocom='+reply_to+'#wpmc-respond_reply';

				setTimeout( function(){ self.close();}, 1000);

			</script>

			<?php

			die();

		}

	}



	function wpmc_process_comment( $author_data )

	{

		if( isset( $_REQUEST['wpmc_plugn'] ) && $_REQUEST['wpmc_plugn'] == '1' )

		{

			if( isset( $author_data['user_id'] ) ) unset( $author_data['user_id'] );

			if( isset( $author_data['user_ID'] ) ) unset( $author_data['user_ID'] );



			$author_data['comment_author'] 	= ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) 	: $author_data['comment_author'];

			$author_data['comment_author_url']  = ( isset($_POST['url']) )     ? trim(strip_tags($_POST['url']))		: $author_data['comment_author_url'];

			$author_data['comment_author_email']= ( isset($_POST['email']) )   ? ''				 			: $author_data['comment_author_email'];

		}

		return $author_data;

	}



	function fetchURL( $url )

	{

		$url = trim($url);



		if ( function_exists('curl_init') ) 

		{

			$ch = curl_init();



			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

			curl_setopt($ch, CURLOPT_TIMEOUT, 60);

			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

			curl_setopt($ch, CURLOPT_MAXREDIRS, 4);

			curl_setopt($ch, CURLOPT_HEADER, false);

			curl_setopt($ch, CURLOPT_FAILONERROR, true);

			curl_setopt($ch, CURLOPT_AUTOREFERER, true);



			$ret = curl_exec($ch);



			$rhead['status'] 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);

			$rhead['type'] 	= curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

			$rhead['header_size'] = curl_getinfo($ch, CURLINFO_HEADER_SIZE);



			if (curl_errno($ch)) {

				return array(false, sprintf(__("Unable to contact server - %1s: %2s; %3s", 'mof_lang'), $url, curl_errno($ch), curl_error($ch)));

			}



			if( empty($ret)) {

				return array(false, sprintf(__("cURL Error - Status: %1s; ContentType: %2s; for url: %3s", 'mof_lang'), $rhead['status'], $rhead['type'], $url));

			}

			curl_close($ch);

		} 

		else 

		{

			$old_ua = @ ini_get('user_agent');

			@ ini_set('user_agent', "Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");

			@ ini_set( 'allow_url_fopen', '1');



			$opts = array(

			  	'http'=>array(

					'method'	=>"GET"

				)

			);

			$context = stream_context_create($opts);



			$ret =  file_get_contents($url, false, $context);

			@ ini_set('user_agent', $old_ua);



			//API sends 0

			if( empty($ret) )

			{

				return array(false, __('Unable to contact server. Please check if fopen is enabled', 'mof_lang'));

			}

		}

		return array(true, $ret);

	}



	function call_wpmc( $action, $args = array() )

	{

		$url = 'http://www.monetizecomments.com/api/?'.http_build_query( array_merge( $args, array('wpmc_do'=>$action, 'wpmc_domain'=>get_option('siteurl')) ) );

		return $this->fetchURL( $url );

	}
     
     function call_data($api)
    {
    	$url = 'http://www.monetizecomments.com/api/getdata.php?'.$api;
         
		return $this->fetchURL( $url );
		
	}
	function active_deactive($api)
    {
    	$url = 'http://www.monetizecomments.com/api/getstatus.php?'.$api;
         
		return $this->fetchURL( $url );
		
	}


	function wpmc_likedislike()

	{

		global $wpdb;



		check_ajax_referer( "wpmc_ajax" );



		if(!defined('DOING_AJAX')) define('DOING_AJAX', 1);



		set_time_limit(60);



		if( ! isset( $_POST['like'] ) || ! isset( $_POST['comment_id'] ) )

			die('-1');



		$comment_ID = isset( $_POST['comment_id'] ) ? (int) $_POST['comment_id'] : 0;



		if( empty( $comment_ID ) )

			die('-1');



		$newlike = ( isset( $_POST['like'] )? trim( $_POST['like'] ): '' );



		if( ! empty( $newlike ) && in_array( $newlike, array( 'like', 'dislike' ) ) )

		{

			$like_count 	= get_comment_meta( $comment_ID, "wpmc_like", true );

			$dislike_count 	= get_comment_meta( $comment_ID, "wpmc_dislike", true );

			$like_count 	= ( $like_count ? $like_count: 0 );

			$dislike_count 	= ( $dislike_count ? $dislike_count: 0 );



			$wpmc_comment = array();

			if( isset( $_COOKIE['wpmc_comments'] ) )

			{

				$comments = explode( "|", $_COOKIE['wpmc_comments'] );

				foreach( $comments as $comment )

				{

					list( $com, $like ) = explode(":", $comment );

					$wpmc_comment[$com] = $like;

				}



				// if liked/ disliked already, increase one and decrease the other.

				if( isset( $wpmc_comment[$comment_ID] ) && $wpmc_comment[$comment_ID] != $newlike )

				{

					if( $newlike == 'like' )

					{

						$like_count++;

						$dislike_count--;

						$dislike_count = max(0, $dislike_count);

					}

					else

					{

						$dislike_count++;

						$like_count--;

						$like_count = max(0, $like_count);

					}

					$wpmc_comment[$comment_ID] = $newlike;

				}

				// if not previously liked/ disliked//

				else if( ! isset( $wpmc_comment[$comment_ID] ) )

				{

					if( $newlike == 'like' )

						$like_count++;

					else

						$dislike_count++;



					$wpmc_comment[$comment_ID] = $newlike;

				}

			}

			else

			{

				if( $newlike == 'like' )

					$like_count++;

				else

					$dislike_count++;



				$wpmc_comment[$comment_ID] = $newlike;

			}



			update_comment_meta( $comment_ID, "wpmc_like", $like_count );

			update_comment_meta( $comment_ID, "wpmc_dislike", $dislike_count );



			$site = parse_url( get_option( 'siteurl' ) );

			$site = str_replace( array( 'https://', 'http://', 'www.' ), '', $site['host'] );



			$cookiestr = array();

			foreach( $wpmc_comment as $id => $val )

				$cookiestr[] = $id.":".$val;

			$cookiestr = implode("|", $cookiestr );



			setcookie("wpmc_comments", $cookiestr, time()+( 60 * 60 * 24 * 365 ), "/", ".".$site , 0 , 1 );

		}

		else

			die('-1');



		$out = array();

		$out['cid'] = $comment_ID;

		$out['like'] = sprintf( __('Like (%d)', 'wpmc_lang'), $like_count );

		$out['dislike'] = sprintf( __('Dislike (%d)', 'wpmc_lang'), $dislike_count );



		header('Content-Type: Application/json');

		echo json_encode( $out );

		exit();

	}



	function wpmc_loginform()

	{

		if (!current_user_can('manage_options')) wp_die(__('Sorry, but you have no permissions to change settings.a'));



		$wpmc_options = get_option('wpmc_options');



		if( isset( $_REQUEST['call'] ) && trim( $_REQUEST['call'] ) == 'save' )

		{

			check_admin_referer('wpmc-settings');



			$wpmc_options 			= $this->wpmc_buildarray();

			$wpmc_options['wpmc_email'] 	= ( isset( $_REQUEST['wpmc_email'] )? 	esc_attr( trim( strip_tags( stripslashes( $_REQUEST['wpmc_email'] ) ) ) ): '' );

			$wpmc_options['wpmc_pwd'] 	= ( isset( $_REQUEST['wpmc_pwd'] )?		esc_attr( trim( strip_tags( stripslashes( $_REQUEST['wpmc_pwd'] ) ) ) ): '' );



			foreach( $wpmc_options as $t )

				if( empty( $t ) && $t != 0 )

					$err[] = sprintf( __('% can not be blank','wpmc_lang'), ucfirst( $t ) );



			if( empty( $err ) )

			{

				list( $ret, $res ) = $this->call_wpmc( 'pub_login', $wpmc_options );

				unset( $wpmc_options['wpmc_pwd'] );



				if( $ret )

				{

					$res = json_decode( $res, true );

					if( $res['msg'] == 'logged' )

					{

						$wpmc_options['wpmc_pub'] 	= $res['wpmc_pub'];

						$wpmc_options['wpmc_api'] 	= $res['wpmc_api'];

						update_option( 'wpmc_options', $wpmc_options );

						$result = true;

					}

					else

						$err[] = stripslashes( $res['msg'] );

				}

				else

					$err[] = $res;

			}

		}

		?>

	<div class="wrap">

	<h2><?php _e('MonetizeComments', 'wpmc_lang')?></h2>

	<h3><?php _e('Settings', 'wpmc_lang');?></h3>

<?php

if($result)

{

?>

<div id="message" class="updated fade"><p><?php echo sprintf( __( 'Plugin has been enabled. Please goto <a href="%s">settings page</a>', 'wpmc_lang'), get_option('siteurl').'/wp-admin/options-general.php?page=wpmc_main' ); ?></p></div>

<?php

}



if($err)

{

?>

<div class="error fade"><p><b><?php _e('Error: ', 'wpmc_lang')?></b><?php echo implode( "<br/>", $err );?></p></div>

<?php

}



if( empty( $wpmc_options['wpmc_api'] ) )

{

?>

	<form method="post" action="">

	<?php wp_nonce_field('wpmc-settings'); ?>

	<input type="hidden" name="call" value="save"/>

	<table border="0" cellpadding="2" cellspacing="2" class="wp-list-table widefat" >

	<thead>

	 <tr><th scope="row" colspan="2" style="font-family: arial;"><?php _e('You must log in with your <a target="_blank" href="http://www.monetizecomments.com">MonetizeComments</a> account in order to use this plugin. Make sure your site has already been added to your Publisher panel and is set to "Active". Don\'t have an account yet? Register <a target="_blank" href="http://www.monetizecomments.com">here</a>. Need help? Click <a target="_blank" href="http://www.monetizecomments.com/help">here</a>.', 'wpfaqg_lang'); ?></th></tr>

	</thead>

		<tbody>

		<tr valign="top">

		<th scope="row" width="25%" style="font-family: arial;"><label for="wpmc_email"><?php _e('Email/ Username', 'wpmc_lang')?></label></th>

		<td width="75%"><input type="text" name="wpmc_email" id="wpmc_email" value="<?php echo $wpmc_options['wpmc_email']?>" class="regular-text"/>

		<br/><span class="description"><?php _e('Log in with your MonetizeComments user name or e-mail.', 'wpfaqg_lang');?></span>

		</td></tr>

		<tr valign="top">

		<th scope="row" width="25%" style="font-family: arial;"><label for="wpmc_pwd"><?php _e('Password', 'wpmc_lang')?></label></th>

		<td width="75%"><input type="password" name="wpmc_pwd" id="wpmc_pwd" value="" class="regular-text"/>

		<br/><span class="description"><?php _e('Your MonetizeComments password.', 'wpfaqg_lang');?></span>

		</td></tr>

		</tbody>

	</table>

	<p class="submit">

		<input type="submit" class="button-primary" value="<?php _e('Activate', 'wpmc_lang') ?>" />

	</p>

	</form>

<?php 

}

?>

</div><!-- /wrap -->

	<?php

		$this->wpmc_page_footer();

	}



	/*

	 * // main function to show theatres.

	 */

	function wpmc_main()

	{

		global $wpdb;



		if( isset( $_REQUEST['call'] ) && trim( $_REQUEST['call'] ) == 'wpmc_reset' )

		{

			check_admin_referer( 'wpmc-settings' );

			$wpmc_options = $this->wpmc_buildarray();

			update_option( 'wpmc_options', $wpmc_options );

		}



		$wpmc_options = get_option('wpmc_options');



		if( empty( $wpmc_options['wpmc_email'] ) ||  empty( $wpmc_options['wpmc_api'] ) )

		{

			$this->wpmc_loginform();

			return;

		}



		if( isset( $_REQUEST['call'] ) && trim( $_REQUEST['call'] ) == 'save' )

		{

			check_admin_referer('wpmc-settings');

			$wpmc_options['wpmc_cwidth'] 	= ( isset( $_REQUEST['wpmc_cwidth'] )? 	(int) esc_attr( trim( strip_tags( stripslashes( $_REQUEST['wpmc_cwidth'] ) ) ) ): 468 );

			$wpmc_options['wpmc_cwidth'] 	= max( $wpmc_options['wpmc_cwidth'], 468 );

			$wpmc_options['wpmc_enabled'] = ( isset( $_REQUEST['wpmc_enabled'] )? 1: 0 );

			$val="api_key=".$wpmc_options['wpmc_api']."&e=1&enabled=".$_REQUEST['wpmc_enabled'];
    		$url = 'http://www.monetizecomments.com/api/getstatus.php?'.$val;
			
			list( $ret, $res ) = fetchURL( $url );

		
			   if( $ret )
				{
					//echo $res;
				}
				else
				{
					$err[] = $res;
					echo "error".print_r($res);
				}
			$wpmc_options['wpmc_template'] = ( isset( $_REQUEST['wpmc_template'] ) && $_REQUEST['wpmc_template'] == '1' ? 1: 0 );



			$wpmc_options['wpmc_opt1'] 	= ( isset( $_REQUEST['wpmc_opt1'] )? 1: 0 );

			$wpmc_options['wpmc_opt2'] 	= ( isset( $_REQUEST['wpmc_opt2'] )? 1: 0 );

			$wpmc_options['wpmc_opt3'] 	= ( isset( $_REQUEST['wpmc_opt3'] )? 1: 0 );

			$wpmc_options['wpmc_opt4'] 	= ( isset( $_REQUEST['wpmc_opt4'] )? 1: 0 );

			$wpmc_options['wpmc_opt5'] 	= ( isset( $_REQUEST['wpmc_opt5'] )? 1: 0 );

			update_option( 'wpmc_options', $wpmc_options );

			$result = 1;

		}

		?>

	<div class="wrap">

	<h2><?php _e('MonetizeComments', 'wpmc_lang')?></h2>

<?php

if($result)

{

?>

<div id="message" class="updated fade"><p><?php echo __( 'Settings have been updated.', 'wpmc_lang'); ?></p></div>

<?php

}



if($err)

{

?>

<div class="error fade"><p><b><?php _e('Error: ', 'wpmc_lang')?></b><?php echo implode( "<br/>", $err );?></p></div>

<?php

}

?>

	<form method="post" action="" name="wpmc_form_settings" id="wpmc_form_settings">

	<?php wp_nonce_field('wpmc-settings'); ?>

	<input type="hidden" name="call" value="save"/>



	<table border="0" cellpadding="2" cellspacing="2" class="wp-list-table widefat">

	<thead>

	 <tr><th scope="row" colspan="2"><?php _e('General Options:', 'wpmc_lang'); ?></th></tr>

	</thead>

		<tbody>

		<tr valign="top">

		<th scope="row" width="20%"><label for="wpmc_pub"><?php _e('Publisher', 'wpmc_lang')?></label></th>

		<td width="80%"><input type="text" name="wpmc_pub" id="wpmc_pub" value="<?php echo $wpmc_options['wpmc_pub']?>" class="regular-text" readonly="readonly"/>

		<br /><span class="description"><?php _e('<div style="margin: 5px 0 5px 0;">Publisher User Name</div>', 'wpmc_lang');?></span>

		</td></tr>



		<tr valign="top">

		<th scope="row" width="20%"><label for="wpmc_api"><?php _e('API key', 'wpmc_lang')?></label></th>

		<td width="80%"><input type="text" name="wpmc_api" id="wpmc_api" value="<?php echo $wpmc_options['wpmc_api']?>" class="regular-text" readonly="readonly"/>

		<br /><span class="description"><?php _e('<div style="margin: 5px 0 5px 0;">Site API Key</div>', 'wpmc_lang');?></span>

		</td></tr>



		<tr valign="top">

		<th scope="row" width="20%"><label for="wpmc_cwidth"><?php _e('Comment Section Width', 'wpmc_lang')?></label></th>

		<td width="75%"><input type="text" name="wpmc_cwidth" id="wpmc_cwidth" value="<?php echo $wpmc_options['wpmc_cwidth']?>" style="width:50px;"/>px

		<br /><span class="description"><?php _e('<div style="margin: 5px 0 5px 0;">Comment section width in pixels. ( Minimum: 468px )</div>', 'wpmc_lang');?></span>

		</td></tr>



		<tr valign="top">

			<th scope="row" style="white-space: nowrap;"><?php _e('Comment Template', 'wpmc_lang')?></label></th>

			<td>

			<input name="wpmc_template" value="1" <?php checked( $wpmc_options['wpmc_template'], "1" ) ?> id="wpmc_template_1" type="radio"> <label for="wpmc_template_1"><?php _e('MonetizeComments Template (Recommended)', 'wpmc_lang' );?></label> <br>

			<input name="wpmc_template" value="0" <?php checked( $wpmc_options['wpmc_template'], "0" ) ?> id="wpmc_template_0" type="radio"> <label for="wpmc_template_0"><?php _e('WordPress Comment Template', 'wpmc_lang' );?></label>

			<br />

			<span class="description"><?php _e('<div style="margin: 5px 0 5px 0;">Selecting MonetizeComments template will also give your users the ability to <em>Like</em>, <em>Dislike</em>, and <em>Reply to</em> individual comments.</div>', 'wpmc_lang');?></p>

		</td></tr>




		<tr valign="top">

		<th scope="row" width="20%"><label for="wpmc_enabled"><?php _e('Enabled', 'wpmc_lang')?></label></th>

		<td width="80%"><input type="checkbox" name="wpmc_enabled" id="wpmc_enabled" value="1" <?php checked( $wpmc_options['wpmc_enabled'], "1" ) ?> />

		<br /><span class="description"><?php _e('<div style="margin: 5px 0 5px 0;">This checkbox MUST be checked in order to use MonetizeComments.</div>', 'wpmc_lang');?></span>

		</td></tr>
    
    <tr valign="top">

		<th scope="row" width="20%"><label for="wpmc_enabled">Ad Network Setup</label></th>

		<td width="80%"><a href="http://www.monetizecomments.com/publishers" target="_blank">Login to your Publisher's panel</a> and click on the <b>Manager Ads</b> link for this site to set up all your ads. Once they're set up, your ads will immediately begin showing up on your comments, no further configuration is needed here. 

		<br /><span class="description"><?php _e('<div style="margin: 5px 0 5px 0;">Check the <a href="http://www.monetizecomments.com/help" target="_blank">Help</a> section for complete instructions.</div>', 'wpmc_lang');?></span>

		</td></tr>

		</tbody>

	</table>

	<p class="submit">

		<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wpmc_lang') ?>" />

	</p>

	</form>

	<hr noshade="noshade" size="1" align="center" width="80%"/>

	<form id="wpmc_plugin_reset" action="" method="POST">

			<input name="call" value="wpmc_reset" type="hidden">

			<?php wp_nonce_field('wpmc-settings'); ?>

			<p>Click the button below to log out of MonetizeComments. Your comments section will revert back to the one of your theme and will no longer be serving any ads. </p>

			<p class="submit" style="border: 0pt none; padding: 0pt 0pt 10px;">

				<input name="Submit" value="Log out" type="submit">

			</p>

		</form>

	</div><!-- /wrap -->

	<?php

		$this->wpmc_page_footer();

	}



	/*

	 * // main function to intercept the template for wpmc.

	 */

	function wpmc_comments_template( $value ) 

	{

		global $post;



		if( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) )

			return $value;



		$wpmc_options = get_option('wpmc_options');



		if( empty( $wpmc_options['wpmc_email'] ) ||  empty( $wpmc_options['wpmc_api'] ) )

			return $value;



		if( $wpmc_options['wpmc_enabled'] == 0 )

			return $value;



		if ( comments_open($post->ID) )

		{
		
			$this->wpmc_comment_form();
			

			if ( $wpmc_options['wpmc_template'] == 1 )

			{

				//the comment list;

				$value = WPMC_DIR.'/includes/wpmc_comments.php';

			}

			else

			{

				// the regular comment list according to the theme; without the comment form.//

				$this->wpmc_commentformoverride = true;

				add_filter( 'comments_open', array( &$this, 'wpmc_comments_open' ) );

				remove_all_actions( 'comment_form_comments_closed' );

			}
			?>
			<div id="reply" style="position:relative;display:none;">
			<?php
			$this->wpmc_comment_form_reply();
			
			?>
			</div>
			<?php

		}

		return $value;

	}



	//as close to the original as possible..//

	function wpmc_comment_form() 

	{

		global $id, $post;

	

		if ( null === $post_id )

			$post_id = $id;

		else

			$id = $post_id;

	

		$wpmc_options = get_option('wpmc_options');

		$req = get_option( 'require_name_email' );

		$aria_req = ( $req ? " aria-required='true'" : '' );

        

		do_action( 'comment_form_before' );

		?><a name="respond"></a>
		<?php
		if($wpmc_options['wpmc_cwidth']=="468") $w="468";
		else
		 $w=$wpmc_options['wpmc_cwidth']-5;
		?>

		<style type="text/css">
.wpmc-comments .depth-1 {width: <?php echo $w;?>px;}
.wpmc-comments .depth-2 {width: <?php echo ($wpmc_options['wpmc_cwidth']-28);?>px;}
.wpmc-comments .depth-3 {width: <?php echo ($wpmc_options['wpmc_cwidth']-48);?>px;}
.wpmc-comments .depth-4 {width: <?php echo ($wpmc_options['wpmc_cwidth']-68);?>px;}
.wpmc-comments .depth-5 {width: <?php echo ($wpmc_options['wpmc_cwidth']-93);?>px;}
.wpmc-comments .depth-6 {width: <?php echo ($wpmc_options['wpmc_cwidth']-113);?>px;}
.wpmc-comments .depth-7 {width: <?php echo ($wpmc_options['wpmc_cwidth']-138);?>px;}
.wpmc-comments .depth-8 {width: <?php echo ($wpmc_options['wpmc_cwidth']-158);?>px;}
.wpmc-comments .depth-9 {width: <?php echo ($wpmc_options['wpmc_cwidth']-178);?>px;}
.wpmc-comments .depth-10 {width: <?php echo ($wpmc_options['wpmc_cwidth']-203);?>px;}
.black_overlay{
	display: block;
	position: absolute;
	background-color: black;
	z-index:1001;
	background: rgba(10, 10, 10, 0.5);
}
.white_content {
	display: block;
	position: absolute;
	z-index:1008;
	overflow: hidden;
	background-color: white;

	
}
.operation {
	float: right;
}
.children{
	margin: 0 0 -10px 1.5em !important;
  padding: 0em !important;
}
.comment
{
padding-left: 0px !important;
}
			div#wpmc-respond.wpmc-reply, #wpmc-respond.wpmc-reply{width:75% !important; float:left !important;}

			#respond { display: none; } 

			#wpmc-comments-wrap { width: <?php echo $wpmc_options['wpmc_cwidth'];?>px !important;}

			#wpmc-respond  { width: <?php echo $wpmc_options['wpmc_cwidth'];?>px !important;}

			#wpmclogo { width: <?php echo $wpmc_options['wpmc_cwidth'];?>px !important;}

		</style>
<?php
		$val="api_key=".$wpmc_options['wpmc_api']."&source=keyword&v=".$_SESSION['googleAdDisplay'];
		list( $ret, $res ) = $this->call_data($val);
		
		if( $ret )

				{
					echo $res;
					//$googleAdValue=explode(":",$res);
					

				}

				else
{
					$err[] = $res;
					echo "error".print_r($res);
		}
		  ?>

				<div id="respond"></div>

				<div id="wpmc-respond-wrap">

				<div id="wpmc-respond">

					<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="commentform">

					<?php 

						do_action( 'comment_form_top' );
						
						do_action( 'comment_form_before_fields' ); 

					?>

					<div id="wpmc_form_loading" style="display:none; font-size:12px;"><img src='<?php echo WPMC_URL?>/img/loading.gif' alt='Loading' border='0' align='absmiddle' />  Loading...</div>

					<div id="wpmc_form_holder" style="display:none;">

						<p class="comment-form-author"><label for="author"><?php _e( 'Name' ) ?></label><?php echo ( $req ? '<span class="required">*</span>' : '' )?>

						<input id="author" name="author" type="text" value="" size="30" <?php echo $aria_req ?> /></p>

						<p class="comment-form-email"><label for="email"><?php _e( 'Email' ) ?></label> <?php echo ( $req ? '<span class="required">*</span>' : '' )?>

						<input id="email" name="email" type="text" value="" size="30" <?php echo $aria_req ?> /></p>

						<p class="comment-form-url"><label for="url"><?php _e( 'Website' ) ?></label>

						<input id="url" name="url" type="text" value="" size="30" /></p>

						<p class="comment-form-comment"><label for="comment"><?php _x( 'Comment', 'noun' ) ?></label>

							<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>

						<p class="form-submit">

							<input name="submit" type="submit" id="submit" value="<?php _e('Comment'); ?>" />

						</p>

					</div>

					<?php 

						do_action( 'comment_form_after_fields' );
						
						
					   //comment_id_fields( $post_id );
					   $rep_str =str_replace("id='comment_parent'","id='comment_parent1'",get_comment_id_fields( $post_id ));
						echo $rep_str;

						do_action( 'comment_form', $post_id ); 

					?>

					</form>

				</div><!-- #wpmc-respond -->

				</div><!-- #wpmc-respond-wrap -->
				<?php
				if($_SESSION['overlayAdDisplay']=="block")
				{
				?>
				<div id="fortest" style="display:none;" >
                  <div id="fade"  class="black_overlay" style="margin:0px 0px 15px 0px;" >
						<div style="margin:5px 5px 0px 0px;"  class="operation"><a href="javascript:void(0)"><img id="im1" src="<?php echo  WPMC_URL."/includes/close.png";?>" onclick="closeit()"></a></div>
						<div id="light" style="margin:10px 0px 10px 0px;" class="white_content" >
						<?php
						$wpmc_options1 = get_option('wpmc_options');
		
        				$val="api_key=".$wpmc_options1['wpmc_api']."&source=overlay";
						list( $ret, $res ) =$this->call_data($val);
						
						if( $ret )

						{
							
								echo $res;
						}
						else
						{
							$err[] = $res;
							echo "error".print_r($res);
						}
		
						?>
						
						</div>
						
						</div>
				  </div>
				  <?php
				  }
				  else
				  {?>
				  <div id="fortest" style="display:none;">
					<input type="hidden" name="overlay_detect" id="overlay_detect" value="Off">	
					</div>
			<?php
				}
				  ?>
	<?php

		$mc_vars 			= array();

		$mc_vars['wpmc_do']	= 'load_form';

		$mc_vars['wpmc_host']	= 'monetizecomments.com';

		$mc_vars['wpmc_pub'] 	= $wpmc_options['wpmc_pub'];

		$mc_vars['wpmc_api'] 	= $wpmc_options['wpmc_api'];

		$mc_vars['wpmc_url']	= urlencode( get_permalink() );

		$mc_vars['wpmc_domain'] = urlencode( get_option( 'siteurl' ) );

		$mc_vars['wpmc_title'] 	= urlencode( $post->post_title );

		$mc_vars['wpmc_postid'] = $post_id;

		$mc_vars['wpmc_com_no']	= get_comments_number( $post->post_ID );

		$mc_vars['wpmc_swname']	= 'wordpress';

		$mc_vars['wpmc_ver']	= WPMC_VER;

	

		$q1 = array();

		$q2 = array();

		foreach( $mc_vars as $id => $val )

		{

			$q1[] = $id . "=" . $val;

			$q2[] = $id . " : '" . $val ."'";

		}

		$q1 = implode( "&", $q1 );

		$q2 = implode( ",\n", $q2 );

	?>

	<script type="text/javascript">

	/* <![CDATA[ */

		var wpmc_array = {

			<?php	echo $q2; ?>

		}

		document.getElementById('wpmc_form_loading').style.display = "block";

		document.getElementById('wpmc_form_holder').style.display = "none";



	/* ]]> */

	</script>

	

	<script type="text/javascript">

	/* <![CDATA[ */

	(function() {

		var wpmc = document.createElement('script'); wpmc.type = 'text/javascript';

		wpmc.async = true;

		<?php

		if (is_ssl())

			$ssl = "https";

		else

			$ssl = "http";

		?>
		wpmc.src = '<?php echo $ssl; ?>' + '://' + wpmc_array.wpmc_host + '/api/?<?php echo $q1; ?>';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(wpmc);

	})();

	/* ]]> */

	</script>

		<?php 

			do_action( 'comment_form_after' ); 

	}

function wpmc_comment_form_reply() 

	{

		global $id, $post;
	

		if ( null === $post_id )

			$post_id = $post->ID;

		else

			$post->ID = $post_id;


		$wpmc_options = get_option('wpmc_options');

		$req = get_option( 'require_name_email' );

		$aria_req = ( $req ? " aria-required='true'" : '' );

        

		do_action( 'comment_form_before' );

		?><a name="respond_reply"></a>

		<style type="text/css">

			
			#respond_reply { display: none; } 

			#wpmc-comments-wrap_reply { width: <?php echo $wpmc_options['wpmc_cwidth'];?>px !important;}

			#wpmc-respond_reply  { width: <?php echo $wpmc_options['wpmc_cwidth'];?>px !important;}

			#wpmclogo_reply { width: <?php echo $wpmc_options['wpmc_cwidth'];?>px !important;}

		</style>

				<div id="respond_reply"></div>

				<div id="wpmc-respond-wrap_reply">

				<div id="wpmc-respond_reply">

					<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="commentform_reply">

					<?php 

						do_action( 'comment_form_top' );
						
						do_action( 'comment_form_before_fields' ); 

					?>

					<div id="wpmc_form1_loading_reply" style="display:none; font-size:12px;"><img src='<?php echo WPMC_URL?>/img/loading.gif' alt='Loading' border='0' align='absmiddle' />  Loading...</div>

					<div id="wpmc_form1_holder_reply">

						<p class="comment-form-author"><label for="author"><?php _e( 'Name' ).$wpmc_options['wpmc_domain'] ?></label><?php echo ( $req ? '<span class="required">*</span>' : '' )?>

						<input id="author" name="author" type="text" value="" size="30" <?php echo $aria_req ?> /></p>

						<p class="comment-form-email"><label for="email"><?php _e( 'Email' ) ?></label> <?php echo ( $req ? '<span class="required">*</span>' : '' )?>

						<input id="email" name="email" type="text" value="" size="30" <?php echo $aria_req ?> /></p>

						<p class="comment-form-url"><label for="url"><?php _e( 'Website' ) ?></label>

						<input id="url" name="url" type="text" value="" size="30" /></p>

						<p class="comment-form-comment"><label for="comment"><?php _x( 'Comment', 'noun' ) ?></label>

							<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>

						<p class="form-submit">

							<input name="submit" type="submit" id="submit" value="<?php _e('Comment'); ?>" />

						</p>

					</div>

					<?php 

						do_action( 'comment_form_after_fields' );

						comment_id_fields( $post_id );
						

						do_action( 'comment_form', $post_id ); 

					?>

					</form>

				</div><!-- #wpmc-respond-reply -->

				</div><!-- #wpmc-respond-wrap-reply -->
			<?php

		$mc_vars 			= array();

		$mc_vars['wpmc_do']	= 'load_form1';

		$mc_vars['wpmc_host']	= 'monetizecomments.com';

		$mc_vars['wpmc_pub'] 	= $wpmc_options['wpmc_pub'];

		$mc_vars['wpmc_api'] 	= $wpmc_options['wpmc_api'];

		$mc_vars['wpmc_url']	= urlencode( get_permalink() );

		$mc_vars['wpmc_domain'] = urlencode( get_option( 'siteurl' ) );

		$mc_vars['wpmc_title'] 	= urlencode( $post->post_title );

		$mc_vars['wpmc_postid'] = $post->post_ID;

		$mc_vars['wpmc_com_no']	= get_comments_number( $post->post_ID );

		$mc_vars['wpmc_swname']	= 'wordpress';

		$mc_vars['wpmc_ver']	= WPMC_VER;

	

		$q1 = array();

		$q2 = array();

		foreach( $mc_vars as $id => $val )

		{

			$q1[] = $id . "=" . $val;

			$q2[] = $id . " : '" . $val ."'";

		}

		$q1 = implode( "&", $q1 );

		$q2 = implode( ",\n", $q2 );

	?>

	<script type="text/javascript">

	/* <![CDATA[ */

		var wpmc_array = {

			<?php	echo $q2; ?>

		}

		document.getElementById('wpmc_form1_loading_reply').style.display = "block";

		document.getElementById('wpmc_form1_holder_reply').style.display = "none";



	/* ]]> */

	</script>
	<script type="text/javascript">

	/* <![CDATA[ */

	(function() {

		var wpmc = document.createElement('script'); wpmc.type = 'text/javascript';

		wpmc.async = true;

		<?php

		if (is_ssl())

			$ssl = "https";

		else

			$ssl = "http";

		?>

		wpmc.src = '<?php echo $ssl; ?>' + '://' + wpmc_array.wpmc_host + '/api/?<?php echo $q1; ?>';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(wpmc);

	})();

	/* ]]> */

	</script>

		<?php 

			do_action( 'comment_form_after' ); 

	}

	function wpmc_add_commentmeta( $comment_ID )

	{

		add_comment_meta($comment_ID, 'wpmc_sns', trim( strip_tags( stripslashes( $_POST['wpmc_sns'] ) ) ), true);

		add_comment_meta($comment_ID, 'wpmc_userid', trim( strip_tags( stripslashes( $_POST['wpmc_userid'] ) ) ), true);

		add_comment_meta($comment_ID, 'wpmc_profilepic', trim( strip_tags( stripslashes( $_POST['wpmc_profilepic'] ) ) ), true);

	}



	function top_comments( $post_ID )

	{

		global $wpdb;



		$topcomment = $wpdb->get_results("

						SELECT 

							C.comment_ID, comment_post_ID, comment_author, comment_author_url, comment_date, comment_content, meta_key, meta_value 

						FROM 

							`".$wpdb->comments."` C, `".$wpdb->commentmeta."` M 

						WHERE

							C.comment_ID = M.comment_ID AND

							C.comment_post_ID = ".$post_ID." AND

							M.meta_key = 'wpmc_like' AND

							C.comment_approved = 1

						ORDER BY 

							M.meta_value DESC

						LIMIT 2

					");

		$html = '';



		if( ! empty( $topcomment ) )

		{

		remove_filter( 'comments_open', array( &$this, 'wpmc_comments_open' ) );



		ob_start();

		foreach( $topcomment as $comment )

		{

			$comment_meta 			= array();

			$comment_meta['sns'] 		= get_comment_meta($comment->comment_ID,"wpmc_sns", true);

			$comment_meta['userid'] 	= get_comment_meta($comment->comment_ID,"wpmc_userid", true);

			$comment_meta['profilepic'] 	= get_comment_meta($comment->comment_ID,"wpmc_profilepic", true);

			if( $comment_meta['profilepic'] )

				$comment_meta['profilepic'] 	= '<img src="'.$comment_meta['profilepic'].'" alt="'. $comment->comment_author.'&#39;s avatar - Go to Profile" width="68"/>';



			if( ! $comment_meta['profilepic'] && $email = get_comment_author_email( $comment->comment_ID ) )

				$comment_meta['profilepic'] 	= get_avatar( $email, 60 );



			if( ! $comment_meta['profilepic'] )

				$comment_meta['profilepic'] 	= '<img src="http://www.monetizecomments.com/api/img/avatar.png" alt="'. $comment->comment_author.'&#39;s avatar - Go to Profile" width="68"/>';



			$comment_meta['like'] 		= $comment->meta_value		? $comment->meta_value: '0';

			$comment_meta['dislike']	=  get_comment_meta( $comment->comment_ID,"wpmc_dislike", true);

			$comment_meta['dislike']	= $comment_meta['dislike']	? $comment_meta['dislike']: '0';



			$commenttxt = apply_filters( 'get_comment_text', $comment->comment_content, $comment );

			$commenttxt = apply_filters( 'comment_text', $commenttxt, $comment );



			$editlink = '';

			if (current_user_can('moderate_comments'))

				$editlink = '<a class="comment-edit-link" href="'.get_edit_comment_link($comment->comment_ID).'" title="'.__( 'Edit comment' ).'">'.__( '(Edit)' ).'</a>';



			$date = mysql2date(get_option('date_format'), $comment->comment_date);

			$time = mysql2date(get_option('time_format'), $comment->comment_date);



			$t = ( $t == 'even'? 'odd':'even' );

		?>

		<li class="comment <?php echo $t;?> thread-<?php echo $t;?> depth-1" id="li-comment-<?php echo $comment->comment_ID; ?>">

			<div id="tcomment-<?php echo $comment->comment_ID; ?>" class="wpmc_comment">

				<div class="wpmc_avatar" id="wpmc_avatar-<?php echo $comment_meta['userid']; ?>">

					<?php echo $comment_meta['profilepic']?>

				</div>



				<div class="wpmc_comment_body" id="wpmc_comment_body-<?php echo $comment->comment_ID; ?>">

				      <cite id="wpmc_cite-<?php echo $comment->comment_ID; ?>" class="wpmc_cite wpmc-comment-author">

					<?php if( $comment->comment_author_url ) : ?>

  		      		    <a id="wpmc_author-<?php echo $comment_meta['userid']; ?>" href="<?php echo $comment->comment_author_url; ?>" target="_blank" rel="nofollow"><?php echo $comment->comment_author; ?></a> <span class="ago"><?php $agoText = $this->time_stamp(strtotime($comment->comment_date)); echo '&#32;&#183;&#32;'.$agoText ?> <?php edit_comment_link( __( '(Edit)', 'wpmc_lang' ), ' ' ); ?></span> 

					<?php else : ?>

			            	<span id="wpmc_author-<?php echo $comment_meta['userid']; ?>"><?php echo $comment->comment_author; ?></span>  <span class="ago"><?php $agoText = $this->time_stamp(strtotime($comment->comment_date)); echo '&#32;&#183;&#32;'.$agoText ?> <?php edit_comment_link( __( '(Edit)', 'wpmc_lang' ), ' ' ); ?></span> 

					<?php endif; ?>

		      		</cite>



					<div class="wpmc_comment_content" id="wpmc_comment_content-<?php echo $comment->comment_ID; ?>"><?php echo wp_filter_kses($commenttxt); ?></div>

					<div class="wpmc_commentbottom" id="wpmc_commentbottom-<?php echo $comment->comment_ID; ?>">

						<a href="javascript:;" id="twpmc_commentlike-<?php echo $comment->comment_ID?>" class="wpmc_commentlike"><?php echo sprintf( __('Like (%s)', 'wpmc_lang'), $comment_meta['like'] )?></a>

						<a href="javascript:;" id="twpmc_commentdislike-<?php echo $comment->comment_ID?>" class="wpmc_commentdislike"><?php echo sprintf( __('Dislike (%s)', 'wpmc_lang'), $comment_meta['dislike'] )?></a>

						<?php comment_reply_link( array( 'add_below' => 'tcomment', 'depth' => 1, 'max_depth' => 5, 'respond_id' => 'wpmc-respond_reply' ), $comment->comment_ID,  $post_ID ); ?>
						
					
						
						<input type="hidden" name="depth-<?php echo comment_ID(); ?>" id="depth-<?php echo comment_ID(); ?>" value="1">
						
						<?php
						
						$wpmc_options1 = get_option('wpmc_options');
						?>
						<input type="hidden" name="width-<?php echo comment_ID(); ?>" id="width-<?php echo comment_ID(); ?>" value="<?php echo $wpmc_options1['wpmc_cwidth'];?>">
						
						<img src="<?php echo WPMC_URL?>/img/loading.gif" style="display:none" id="twpmc_loader-<?php echo $comment->comment_ID?>" alt="">

					</div><!-- .wpmc_commentbottom -->

				</div>

			</div>

		</li>

		<?php

		}

		$this->wpmc_commentformoverride = true;

		add_filter( 'comments_open', array( &$this, 'wpmc_comments_open' ) );



		$html = ob_get_contents();

		ob_end_clean();

		}



		return $html;

	}



	function wpmc_single_comment( $comment, $args, $depth )

	{
		
		global $topcomments;
      $wpmc_options1 = get_option('wpmc_options');
   
		$GLOBALS['comment'] = $comment;
		remove_filter( 'comments_open', array( &$this, 'wpmc_comments_open' ) );



		$comment_meta 			= array();

		$comment_meta['sns'] 		= get_comment_meta(get_comment_ID(),"wpmc_sns", true);

		$comment_meta['userid'] 	= get_comment_meta(get_comment_ID(),"wpmc_userid", true);

		$comment_meta['profilepic'] 	= get_comment_meta(get_comment_ID(),"wpmc_profilepic", true);

		if( $comment_meta['profilepic'] )

			$comment_meta['profilepic'] 	= '<img src="'.$comment_meta['profilepic'].'" alt="'. get_comment_author().'&#39;s avatar - Go to Profile" width="68"/>';



		if( ! $comment_meta['profilepic'] && $email = get_comment_author_email( get_comment_ID() ) )

			$comment_meta['profilepic'] 	= get_avatar( $email, 60 );



		if( ! $comment_meta['profilepic'] )

			$comment_meta['profilepic'] 	= '<img src="http://www.monetizecomments.com/api/img/avatar.png" alt="'. get_comment_author().'&#39;s avatar - Go to Profile" width="68"/>';



		$comment_meta['like']		=  get_comment_meta( get_comment_ID(),"wpmc_like", true);

		$comment_meta['dislike']	=  get_comment_meta( get_comment_ID(),"wpmc_dislike", true);



		$comment_meta['like'] 		= $comment_meta['like']		? $comment_meta['like']: '0';

		$comment_meta['dislike']	= $comment_meta['dislike']	? $comment_meta['dislike']: '0';



		switch ($comment->comment_type):

		    case '' :
            
		// To check if there is "depth-1" or not
		?>

		<li <?php comment_class(); ?>  id="li-comment-<?php echo comment_ID(); ?>">

			<div id="comment-<?php echo comment_ID(); ?>" class="wpmc_comment">

				<div class="wpmc_avatar" id="wpmc_avatar-<?php echo $comment_meta['userid']; ?>">

					<?php echo $comment_meta['profilepic']?>

				</div>



				<div class="wpmc_comment_body" id="wpmc_comment_body-<?php echo comment_ID(); ?>">

					<?php if ( $comment->comment_approved == '0' ){  ?>

						<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>

					<br/>

					<?php } ?>
					


				      <cite id="wpmc_cite-<?php echo comment_ID(); ?>" class="wpmc_cite wpmc-comment-author">

					<?php if( $url = get_comment_author_url() ) : ?>

  		      		    <a id="wpmc_author-<?php echo $comment_meta['userid']; ?>" href="<?php echo $url; ?>" target="_blank" rel="nofollow"><?php echo comment_author(); ?></a> <span class="ago"><?php $agoText = $this->time_stamp(strtotime($comment->comment_date)); echo '&#32;&#183;&#32;'.$agoText ?> <?php edit_comment_link( __( '(Edit)', 'wpmc_lang' ), ' ' ); ?></span> 

					<?php else : ?>

			            	<span id="wpmc_author-<?php echo $comment_meta['userid']; ?>"><?php echo comment_author(); ?></span> <span class="ago"><?php $agoText = $this->time_stamp(strtotime($comment->comment_date)); echo '&#32;&#183;&#32;'.$agoText ?> <?php edit_comment_link( __( '(Edit)', 'wpmc_lang' ), ' ' ); ?></span> 

					<?php endif; ?>

		      		</cite>



					<div class="wpmc_comment_content" id="wpmc_comment_content-<?php echo comment_ID(); ?>"><?php echo wp_filter_kses(comment_text()); ?>
					
					</div>



					<div class="wpmc_commentbottom" id="wpmc_commentbottom-<?php echo comment_ID(); ?>">

						<a href="javascript:;" id="wpmc_commentlike-<?php echo comment_id()?>" class="wpmc_commentlike"><?php echo sprintf( __('Like (%s)', 'wpmc_lang'), $comment_meta['like'] )?></a>

						<a href="javascript:;" id="wpmc_commentdislike-<?php echo comment_id(); ?>" class="wpmc_commentdislike"><?php echo sprintf( __('Dislike (%s)', 'wpmc_lang'), $comment_meta['dislike'] )?></a>

						<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'respond_id' => 'wpmc-respond_reply' ) ) ); ?>
						<input type="hidden" name="depth-<?php echo comment_ID(); ?>" id="depth-<?php echo comment_ID(); ?>" value="<?php echo $depth;?>">
						<input type="hidden" name="width-<?php echo comment_ID(); ?>" id="width-<?php echo comment_ID(); ?>" value="<?php echo $wpmc_options1['wpmc_cwidth'];?>">

						<img src="<?php echo WPMC_URL?>/img/loading.gif" style="display:none" id="wpmc_loader-<?php echo comment_id()?>" alt="">

					</div><!-- .wpmc_commentbottom -->

				</div>

			</div>
			<?php
			$commentID = get_comment_ID();
			$checkSql = "select * from wp_comments where comment_ID='".$commentID."'";
			$checkRs = mysql_query($checkSql) or die("Cannot Execute Query: <P>".$checkSql."<P>".mysql_error());
			$checkRow = mysql_fetch_array($checkRs);
			
			
		
        	$val="api_key=".$wpmc_options1['wpmc_api']."&source=instream&comment_count=".$this->commentsCount."&count=".$this->count."&comment_parent=".$checkRow['comment_parent'];
			list( $ret, $res ) = fetchGoogleAds($val);
			
			if($ret)
				{
					if((strpos(trim($res),'googleAdmany')!=false) and ($checkRow['comment_parent']<=0))
					{
						echo $res;
						$parentSql = "select * from wp_comments where comment_parent='".$commentID."'";
						$parentRs = mysql_query($parentSql) or die("Cannot Execute Query: <P>".$parentSql."<P>".mysql_error());
						if(mysql_num_rows($parentRs)>0) echo "<div style='height:10px;'></div>";
						$this->count=1;
						$this->commentsCount=$this->commentsCount+1;
					}
					else
					{
						if(($checkRow['comment_parent']<=0)) $this->count=$this->count+1;
					}
					//echo $res;
				}

				else
				{
					$err[] = $res;
					echo "error".print_r($res);
				}
?>
	
			<?php
		  
	        break;

	    case 'pingback'  :

	    case 'trackback' :
         
		?>

		<li class="post pingback">

			<p><?php echo __('Pingback:'); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('(Edit)'), ' '); ?></p>

		</li>

		<?php

	        break;

		endswitch;


		$this->wpmc_commentformoverride = true;

		add_filter( 'comments_open', array( &$this, 'wpmc_comments_open' ) );

	}



	function wpmc_comments_open( $wpmc_open = false, $post_id=null ) 

	{

		if( $this->wpmc_commentformoverride )

			return false;

		return $wpmc_open;

	}



	function wpmc_cancelcommentlink( $alink, $link, $text )

	{

		if( strpos( $alink, '#respond_reply' ) !== false )

			$alink = str_replace( '#respond', '#wpmc-respond_reply', $alink );

		return $alink;

	}

}

endif;



global $wp_mc_comments;

if( ! $wp_mc_comments ) $wp_mc_comments = & new wp_mc_comments();

?>