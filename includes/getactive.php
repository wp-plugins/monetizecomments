<?php

//check Site is Active or not


$wpmc_options = get_option('wpmc_options');
$val="api_key=".$wpmc_options['wpmc_api'];
    	$url = 'http://www.monetizecomments.com/api/getstatus.php?'.$val;
         
		 
		
	
	
list( $ret, $res ) = fetchURL( $url );
		
		if( $ret )

				{
				//	echo $res;
					if($res!="Active")
					{
						//echo "here";
						$wpmc_options['wpmc_enabled']=0;
						update_option( 'wpmc_options', $wpmc_options );
					}
					else
					{
						if(!empty($wpmc_options['wpmc_api']))
						{
							$wpmc_options['wpmc_enabled']=1;
						update_option( 'wpmc_options', $wpmc_options );
						}
					}
					

				}

				else
				{
					$err[] = $res;
					echo "error".print_r($res);
				}
 //$wpmc_options['wpmc_enabled']=0;
//update_option( 'wpmc_options', $wpmc_options );
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
?>