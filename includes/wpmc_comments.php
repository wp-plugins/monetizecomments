<?php
if( ! defined( 'ABSPATH' ) || ! defined('WPMC_FILE') )
	die('no dice');

global $wp_mc_comments;
function fetchGoogleAds($val)
{
$url = 'http://www.monetizecomments.com/api/getdata.php?'.$val;
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
?><!-- WP Mc Comments -->
	<div id="wpmc_thread">
        <div id="wpmc-comments-wrap" class="wpmc-comments">
		<?php if (get_comment_pages_count() > 1 && get_option('page_comments')): // Are there comments to navigate through? ?>
	            <div class="navigation">
	                <div class="nav-previous"><?php previous_comments_link( '<span class="meta-nav">&larr;</span> Older Comments'); ?></div>
	                <div class="nav-next"><?php next_comments_link('Newer Comments <span class="meta-nav">&rarr;</span>'); ?></div>
	            </div> <!-- .navigation -->
		<?php endif; // check for comment navigation ?>
			<?php

				$top_comments = $wp_mc_comments->top_comments( $post->ID );
				if( ! empty( $top_comments ) ):
			?> 
			<div class="wpmc_headline"><?php _e('Top Comments')?></div>
			<ol id="twpmc-comments">
				<?php echo $top_comments; ?>
			</ol>
			<?php
				endif;

			?>
			<div class="wpmc_headline"><?php _e('All Comments')?></div>
			<?php
			$commentSql = "select * from wp_comments where comment_post_ID='".$_REQUEST['p']."' and comment_approved!=0";
			$commentRs = mysql_query($commentSql) or die("Cannot Execute Query: <P>".$commentSql."<P>".mysql_error());
			if(mysql_num_rows($commentRs)==0)
			{
				echo "<div class='no-comments'>No comments yet for this post. Be the first and comment above!</div>";
			}
			
			?>
			
			<ol id="wpmc-comments">
	                <?php wp_list_comments(array('callback' => array( $wp_mc_comments, 'wpmc_single_comment' ) ) ); ?>
			</ol>

		<?php if (get_comment_pages_count() > 1 && get_option('page_comments')): // Are there comments to navigate through? ?>
	            <div class="navigation">
	                <div class="nav-previous"><?php previous_comments_link( '<span class="meta-nav">&larr;</span> Older Comments'); ?></div>
	                <div class="nav-next"><?php next_comments_link('Newer Comments <span class="meta-nav">&rarr;</span>'); ?></div>
	            </div><!-- .navigation -->
		<?php endif; // check for comment navigation ?>

	        </div>
		</div>
<!-- /WP Mc Comments -->