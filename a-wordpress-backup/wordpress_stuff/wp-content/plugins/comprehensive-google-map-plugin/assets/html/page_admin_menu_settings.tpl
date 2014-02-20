<h2>Comprehensive Google Map by Alexander Zagniotov</h2>
<div style="border: 1px solid #FFCC99; width: 70%; padding: 5px 15px; -webkit-border-radius: 6px; -moz-border-radius: 6px; border-radius: 6px;">
<p>This plugin is continuing to evolve because of contributions from users like you. Thank you. If you found this plugin useful, especially if you use it for commercial purposes, please consider making a <a href="http://goo.gl/yI5j6O" target="_blank">donation</a>. Your support helps me to spend more time on development and provide suberb customer service. Please check my other plugins at <a href="http://initbinder.com/plugins" target="_blank">InitBinder.com</a>.</p>
<p>Alternatively, you can write on your own blog about the plugin, join the <a href="http://www.facebook.com/pages/Comprehensive-Google-Map-Plugin/180316032076503" target="_blank">Comprehensive Google Map Fan Page</a> on Facebook, <a href="http://twitter.com/InitBinder" target="_blank">spread the word</a> about it on Twitter, <a href="http://wordpress.org/extend/plugins/comprehensive-google-map-plugin/" target="_blank">rate it</a> on Wordpress.org or <a href="http://goo.gl/yI5j6O" target="_blank">donate</a>, thanks!</p>
</div>
<div class="tools-tabs">
	<ul class="tools-tabs-nav hide-if-no-js">
		<li class="">
			<a title="Shortcode Builder" href="#settings">Settings</a>
		</li>
        <li class="">
            <a title="Support" href="#support">Support</a>
        </li>
		<li class="">
			<a title="Contribute" href="#contribute">Contribute</a>
		</li>
	</ul>

	<div class="tools-tab-body" id="settings" style="">
		<div class="tools-tab-content settings">
				<form action='' name='' id='' method='post'>
				<div id='google-map-container-settings' style='margin-top: 20px'>
				<table cellspacing='0' cellpadding='0' border='0'>
					<tbody>
						<tr>
							<td><b>Shortcode builder under default post/page HTML WYSIWYG editor?</b></td>
						</tr>
						<tr>
							<td>
								<label id='yes-display-label' for='yes-display'>Visible</label>
								<input type='radio' id='yes-display' name='builder-under-post' value='true' YES_DISPLAY_SHORTCODE_BUILDER_INPOST_TOKEN />&nbsp;
								<label id='no-display-label' for='no-display'>Hidden</label>
								<input type='radio' id='no-display' name='builder-under-post' value='false' NO_DISPLAY_SHORTCODE_BUILDER_INPOST_TOKEN /></td>
						</tr>
					</tbody>
				</table>
				<table cellspacing='0' cellpadding='0' border='0'>
					<tbody>
						<tr>
                            <td><br />&nbsp;<br /></td>
						</tr>
					</tbody>
				</table>
				<table cellspacing='0' cellpadding='0' border='0'>
					<tbody>
						<tr>
							<td><b>The following custom post/page types:</b><br />
                                <span style="color: green; font-weight: bold;">[a]</span>&nbsp;Will be included in Geo Mashup maps<br />
                                <span style="color: green; font-weight: bold;">[b]</span>&nbsp;Will have shortcode builder visible under HTML WYSIWYG editor</td>
						</tr>
						<tr>
							<td>
								<label id='custom-post-types' for='custom-post-types'>Enter <span style="color: green; font-weight: bold;">comma</span>-separated values:</label>
								<input type='text' id='custom-post-types' name='custom-post-types' maxlength="40" size="50" value='CUSTOM_POST_TYPES_TOKEN' />
							</td>
						</tr>
					</tbody>
				</table>
                <table cellspacing='0' cellpadding='0' border='0'>
                    <tbody>
                    <tr>
                        <td><br />&nbsp;<br /></td>
                    </tr>
                    </tbody>
                </table>
                <table cellspacing='0' cellpadding='0' border='0'>
                    <tbody>
                    <tr>
                        <td><b>HTML WYSIWYG TinyMCE button to load saved shortcodes</b></td>
                    </tr>
                    <tr>
                        <td>
                            <label id='yes-enabled-label' for='yes-enabled'>Enabled</label>
                            <input type='radio' id='yes-enabled' name='tinymce-button-in-editor' value='true' YES_ENABLED_TINYMCE_BUTTON_TOKEN />&nbsp;
                            <label id='no-enabled-label' for='no-enabled'>Disabled</label>
                            <input type='radio' id='no-enabled' name='tinymce-button-in-editor' value='false' NO_ENABLED_TINYMCE_BUTTON_TOKEN /></td>
                    </tr>
                    </tbody>
                </table>
                <table cellspacing='0' cellpadding='0' border='0'>
                    <tbody>
                    <tr>
                        <td><br />&nbsp;<br /></td>
                    </tr>
                    </tbody>
                </table>
                <table cellspacing='0' cellpadding='0' border='0'>
                    <tbody>
                    <tr>
                        <td><b>Plugin admin bar menu</b></td>
                    </tr>
                    <tr>
                        <td>
                            <label id='yes-menu-enabled-label' for='yes-menu-enabled'>Enabled</label>
                            <input type='radio' id='yes-menu-enabled' name='plugin-admin-bar-menu' value='true' YES_ENABLED_PLUGIN_ADMIN_BAR_MENU_TOKEN />&nbsp;
                            <label id='no-menu-enabled-label' for='no-menu-enabled'>Disabled</label>
                            <input type='radio' id='no-menu-enabled' name='plugin-admin-bar-menu' value='false' NO_ENABLED_PLUGIN_ADMIN_BAR_MENU_TOKEN /></td>
                    </tr>
                    </tbody>
                </table>
                <table cellspacing='0' cellpadding='0' border='0'>
                    <tbody>
                    <tr>
                        <td><br />&nbsp;<br /></td>
                    </tr>
                    </tbody>
                </table>
                <table cellspacing='0' cellpadding='0' border='0'>
                    <tbody>
                    <tr>
                        <td><b>When viewing map on mobile devices, map should ignore user-set width & height,<br />instead the map should expand to the device's screen width & height</b></td>
                    </tr>
                    <tr>
                        <td>
                            <label id='yes-map-fill-viewport-enabled-label' for='map-fill-viewport-enabled'>Enabled</label>
                            <input type='radio' id='map-fill-viewport-enabled' name='map-fill-viewport' value='true' YES_ENABLED_MAP_FILL_VIEWPORT_TOKEN />&nbsp;
                            <label id='no-map-fill-viewport-enabled-label' for='no-map-fill-viewport-enabled'>Disabled</label>
                            <input type='radio' id='no-map-fill-viewport-enabled' name='map-fill-viewport' value='false' NO_ENABLED_MAP_FILL_VIEWPORT_TOKEN /></td>
                    </tr>
                    </tbody>
                </table>
			</div><br /><br />
			<input type='submit' onclick='' class='button-primary' tabindex='4' value=' Save Settings ' id='cgmp-save-settings' name='cgmp-save-settings' />
		</form>
		</div>
	</div>

    <div class="tools-tab-body" id="support" style="">
        <div class="tools-tab-content">
            <h3 class="hide-if-js">Support</h3>
            <p>Please report the following to azagniotov@gmail.com if you have problems with the plugin, Thank you</p>
            SUPPORT_DATA
        </div>
    </div>

	<div class="tools-tab-body" id="contribute" style="">
		<div class="tools-tab-content">
			<h3 class="hide-if-js">Contribute</h3>
			<h4>Supporting This Plugin</h4>
			<p>If you’ve found some value in the features which Comprehensive Google Map provides for enhancing your site, and you'd like to say thanks, a direct contribution toward the development effort via PayPal is always welcome. Alternatively, you can write on your own blog about the plugin, like it on Facebook and/or spread the word about it on Twitter, thanks!</p>
			<a target="_blank" href="http://goo.gl/yI5j6O"><img src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online." /></a>
		</div>
	</div>
</div>
