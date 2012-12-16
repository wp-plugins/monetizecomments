if(typeof jQuery == "function") {
	jQuery(document).ready(function($) {
		$("#wpmc_form_settings").submit( function( event ){
			var cwidth = $("#wpmc_cwidth").val();
			if( isNaN( cwidth ) || cwidth < 468 )
			{
				event.preventDefault();
				alert("Comment Section Width cannot be less than 468px");
				$("#wpmc_cwidth").focus().select();
				return false;
			}
		});

        
		function wpmc_adjustwidth()
		{
			
			var wid = ( ( document.getElementById("wpmc_form").offsetWidth ) - 28) +"px";
			$("#comment").css("width", wid );
			$("#cancel-comment-reply-link").focus();
		}

		$(".comment-reply-link").click(function() {
			
		//	$('html, body').animate({ scrollTop: 0 }, 'fast');
			commentValue=$(this).attr('onclick').split(",")[1];
			commentId=commentValue.split('"')[1];
			
			var depth=document.getElementById("depth-"+commentId).value;
			var width=document.getElementById("width-"+commentId).value;
			//alert(depth + "  " + width);
			if(parseInt(depth)==1)
			{
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-5)+"px";
			}
			if(parseInt(depth)==2)
			{
			    	
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-28)+"px";
			}
			if(parseInt(depth)==3)
			{
			    	
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-48)+"px";
			}
			if(parseInt(depth)==4)
			{
			    	
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-68)+"px";;
			}
			if(parseInt(depth)==5)
			{
			    	
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-93)+"px";
			}
			if(parseInt(depth)==6)
			{
			    	
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-113)+"px";
			}
			if(parseInt(depth)==7)
			{
			    	
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-138)+"px";
			}
			if(parseInt(depth)==8)
			{
			    	
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-158)+"px";
			}
			if(parseInt(depth)==9)
			{
			    	
				document.getElementById("wpmc_fields_reply").style.width=parseInt(width-178)+"px";
			}
			//alert(document.getElementById("wpmc_fields_reply").style.width);
			$("#wpmc-respond_reply").addClass("wpmc-reply");
			wpmc_adjustwidth();

			//$('html, body').animate({ scrollTop: parseInt(($('#wpmc-respond_reply').offset().top)-105) }, 'fast');
		});

		$(".wpmc_commentlike").click(function() {
			comment_id = $(this).attr('id').split("-")[1];
			if(typeof(comment_id) != "undefined") {
				handleajaxcall( comment_id, 'like' );
			}
		});
		$(".wpmc_commentdislike").click(function() {
			comment_id = $(this).attr('id').split("-")[1];
			if(typeof(comment_id) != "undefined") {
				handleajaxcall( comment_id, 'dislike' );
			}
		});

		function handleajaxcall( comment_id, likedislike )
		{
			$('#wpmc_loader-'+comment_id).css("display", "inline");
			$('#twpmc_loader-'+comment_id).css("display", "inline");
			var data = {
				action: 'wpmc_like',
				like: likedislike,
				comment_id: comment_id,
				_ajax_nonce: ajax_nonce
			};

			var myajax = jQuery.post(ajaxurl, data, function(response) {
				if( response != '-1' )
				{
					if( response.v ) alert( response.v );
					$('#wpmc_commentlike'+ '-' + response.cid ).html(response.like);
					$('#wpmc_commentdislike'+ '-' + response.cid ).html(response.dislike);
					$('#twpmc_commentlike'+ '-' + response.cid ).html(response.like);
					$('#twpmc_commentdislike'+ '-' + response.cid ).html(response.dislike);
					$('#wpmc_loader-'+comment_id).css("display", "none");
					$('#twpmc_loader-'+comment_id).css("display", "none");
				}
			});
			$(window).unload( function () { myajax.abort(); } );
		}
	});
}