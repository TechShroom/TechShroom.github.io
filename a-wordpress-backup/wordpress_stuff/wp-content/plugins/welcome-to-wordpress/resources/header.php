<?php

/**
 * Copyright 2012 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH') )
	die( 'Forbidden' );

$options = get_option( 'wtwp_options' );

?>
<style type="text/css">

	/** Copyright notice **/
	#wtwp-copyright {
		margin: 60px auto 20px auto;
		text-align: center;
	}
	.wrap-h3 {
		background: gradient(center top , #F9F9F9, #F5F5F5);
		background: -moz-linear-gradient(center top , #F9F9F9, #F5F5F5);
		background: -ms-gradient(linear, left top, left bottom, from(#F9F9F9), to(#F5F5F5));
		background: -o-gradient(linear, left top, left bottom, from(#F9F9F9), to(#F5F5F5));
		background: -webkit-gradient(linear, left top, left bottom, from(#F9F9F9), to(#F5F5F5));
		background-color: #F5F5F5;
		border: 1px solid #DFDFDF;
		border-bottom-color: #DFDFDF;
		border-radius: 3px 3px 3px 3px;
		border-top-left-radius: 3px;
		border-top-right-radius: 3px;
		box-shadow: 0 1px 0 #FFFFFF;
		color: #464646;
		cursor: move;
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#F9F9F9', endColorstr='#F5F5F5');
		font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
		font-size: 18px;
		font-weight: normal;
		line-height: 1;
		margin: 8px 18px;
		padding: 7px 10px;
		text-shadow: 0 1px 0 #FFFFFF;
	}
	.wtwp-article > h3 {
		background-color: transparent;
		background-image: none;
		font-family: georgia;
		font-size: 1.4em;
		font-weight: normal;
		margin: 0px;
		padding: 10px 0 4px;
	}
	h3.display {
		background-color: transparent;
		background-image: none;
		color: #464646;
		font-family: georgia;
		font-size: 1.4em;
		font-weight: normal;
		margin: 0px;
		padding: 10px 0 4px;
		text-decoration: none;
	}
	h3.display a {
		color: #464646;
		text-decoration: none;
	}
	h3.display a:hover {
		color: #D54E21;
		text-decoration: none;
	}
	.postbox > strong {
		color: #464646 !important;
		cursor: pointer;
		display: block;
		font-family: georgia;
		font-size: 1.5em !important;
		font-weight: normal !important;
	}
	li, dd, .widget, .postbox, .stuffbox {
		line-height: 15px;
	}
	#icon-index {
		margin: 8px;
	}
	div.wrap > div > div.wtwp-article {
		margin: 8px 0px;
	}
	p + h3 {
		margin: 8px 0px;
	}
	.postbox {
		display: none;
		padding: 10px;
	}
	.widget .widget-top, .postbox h3, .stuffbox h3 {
		cursor: auto;
	}
	div.wtwp-article ul {
		list-style-type: disc;
		list-style-position: outside;
		margin-left: 24px;
		line-height: 15px;
		display: block;
		margin-bottom: 12px;
		margin-top: 12px;
	}
	div#hcc-div {
		margin: 50px auto 0 auto;
		width: 500px;
		text-align: center;
	}
</style>

<script type="text/javascript">

	function wtwp_show_help(article_id) {
		url = '<?php echo $options['help_url']; ?>/helpme/article/' + article_id + '?plabel=<?php echo $options['plid']; ?>&plabelfo=YVlO1BnP&locale=<?php echo substr( get_locale(), 0, 2 ); ?>';
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = url;
		jQuery("body").append(script);
	}

	function define(o) {
		if (undefined === o.content || null === o.content || "" === o.content) {
			jQuery("div#article-" + o.id).parent().remove();
		} else {
			jQuery("div#article-" + o.id).html(o.content).parent().show();
			jQuery("div#article-" + o.id + " a").each(function(i, v) {
				if (undefined !== jQuery(v).attr("href") && jQuery(v).attr("href").indexOf("://") < 0) {
					jQuery(v).attr("href", '<?php echo $options['help_url']; ?>' + jQuery(v).attr("href") + ((jQuery(v).attr("href").indexOf('?') > -1) ? '&' : '?') + 'pl_id=<?php echo $options['plid']; ?>&isc=<?php echo $options['isc']; ?>');
				} else if (undefined !== jQuery(v).attr("href") && jQuery(v).attr("href").match(/[&?]prog_id=/)) {
					jQuery(v).attr("href", jQuery(v).attr("href") + ((jQuery(v).attr("href").indexOf('?') > -1) ? '&' : '?') + 'isc=<?php echo $options['isc']; ?>');
				}
				jQuery(v).attr("target", "_blank");
			});
			
			if ( jQuery("div.wtwp-article:first").attr("id") ===  jQuery("div#article-" + o.id).attr("id")) {
				jQuery("div#article-" + o.id).show();
			} else {
				jQuery("div#article-" + o.id).hide();
			}
		}
	}

	jQuery(document).on("click", "div.postbox", function(evt) {
		if (jQuery("div.wtwp-article", jQuery(this)).is(":hidden")) {
			jQuery("div.wtwp-article").slideUp();
			jQuery("div.wtwp-article", jQuery(this)).slideDown();
		}
	});

	jQuery(document).ready(function($) {
		$("div.wtwp-article").each(function(i, v) {
			wtwp_show_help($(v).attr("id").replace(/article-/, ""));
		});
	});

</script>
<div class="wrap">
	<div id="icon-index" class="icon32"><br/></div>
	<h2 class="page-title"><?php echo $label; ?></h2>
