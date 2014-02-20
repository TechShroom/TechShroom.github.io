<?php
/* 
    This file contains a version of Feed Key which has been adapted to work with WP-Sentry.

    Feed Key (v0.2) <http://code.andrewhamilton.net/wordpress/plugins/feed-key/>

        Adds a 32bit (or 40bit) key for each of your users, creating a unique feed url for every registered on user the site. 
        This allows you to restrict you feeds to registered users only.

        Author: Andrew Hamilton <http://andrewhamilton.net>
        Licensed under the The GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
*/ 


if (function_exists('feedkey')) {
    $sentryerror_feedkey_exists = true;
} else {
    $sentryerror_feedkey_exists = false;

    //----------------------------------------------------------------------------
    //		SETUP FUNCTIONS & GLOBAL VARIABLES
    //----------------------------------------------------------------------------

    //Feed Key Options
    $feedkey_opt = get_option('feedkey_options');

    //The page that was originally requested by the user
    $feedkey_reqpage = $_SERVER["REQUEST_URI"];

    //Setup Feedkey Variables
    $feedkey_valid = FALSE;
    $feed_redirected = FALSE;
    $feedkey_userid = 0;

    //Get WordPress URLs and Title
    $blogurl = get_bloginfo('url');
    $wpurl = get_bloginfo('wpurl');
    $blogtitle = get_bloginfo('title');

    //----------------------------------------------------------------------------
    //	Error Messages
    //----------------------------------------------------------------------------

    $errormsg = array(
        'feedkey_invalid' => 'The Feed Key you used is invalid. It is either incorrect or has been revoked. Please login to obtain a valid Feed Key',
        'feedkey_missing' => 'You need to use a Feed Key to access feeds on this site. Please login to obtain yours.',
        'feedkey_notgen' => 'Feed Key not found.',
        'feedurl_notgen' => 'URL is available once a Feed Key has been generated'
        );

    //---------------------------------------------------------------------------
    //	Setup Default Settings
    //---------------------------------------------------------------------------

    function feedkey_setup_options()
    {
        global $feedkey_opt;
        
        $feedkey_version = get_option('feedkey_version'); //Feed Key Version Number
        $feedkey_this_version = '0.2';
        
        // Check the version of Feed Key
        if (empty($feedkey_version))
        {
            add_option('feedkey_version', $feedkey_this_version);
        } 
        elseif ($feedkey_version != $feedkey_this_version)
        {
            update_option('feedkey_version', $feedkey_this_version);
        }
        
        // Setup Default Options Array
        $optionarray_def = array(
            'char_set' => 'alpha-numeric-mixed',
            'key_length' => '32',
            'salt' => 'username',
            'hash_using' => 'md5',
            'user_reset' => TRUE,
            'enable_public' => FALSE
        );
            
        if (empty($feedkey_opt)){ //If there aren't already options for Feed Key
            add_option('feedkey_options', $optionarray_def, 'Feed Key Wordpress Plugin Options');
        }	
    }

    //Detect WordPress version to add compatibility with 2.3 or higher
    $wpversion_full = get_bloginfo('version');
    $wpversion = preg_replace('/([0-9].[0-9])(.*)/', '$1', $wpversion_full); //Boil down version number to X.X


    //--------------------------------------------------------------------------
    //	Add Admin Page
    //--------------------------------------------------------------------------

    function feedkey_add_options_page()
    {
    
    /* REPLACED BY WP-SENTRY
        if (function_exists('add_options_page'))
        {
            // add_options_page('Feed Key', 'Feed Key', 8, basename(__FILE__), 'feedkey_options_page');
        }
    */
    }

    //---------------------------------------------------------------------------
    //	Add Feed Key to Profile Page
    //---------------------------------------------------------------------------

    function feedkey_display()
    {	
        global $blogurl, $feedkey_opt, $profileuser, $current_user, $errormsg;
        
        // Setup Feed Key Reset Options
        $feedkey_reset_types = array(
        'Feed Key Options...' => NULL,
        'Reset Feed Key' => 'feedkey-reset',
        'Remove Feed Key' => 'feedkey-remove'
        );
        
        foreach ($feedkey_reset_types as $option => $value) {
            if ($value == $optionarray_def['login_redirect_to']) {
                    $selected = 'selected="selected"';
            } else {
                    $selected = '';
            }
            
            $feedkey_reset_options .= "\n\t<option value='$value' $selected>$option</option>";
        }
        
        $yourprofile = $profileuser->ID == $current_user->ID;
        $feedkey = get_usermeta($profileuser->ID,'feed_key');
        $permalink_structure = get_option(permalink_structure);
        
        //Check if Permalinks are being used
        empty($permalink_structure) ? $feedjoin = '?feed=rss2&feedkey=' : $feedjoin = '/feed/?feedkey=';
        
        $feedurl = $blogurl.$feedjoin.$feedkey;
        $feedurl = '<a href="'.$feedurl.'">'.$feedurl.'</a>';

        ?>
        <table class="form-table">
            <h3><?php echo $yourprofile ? _e("Your Feed Key", 'feed-key') : _e("User's Feed Key", 'feed-key') ?></h3>
            <tr>
                <th><label for="feedkey">Feed Key</label></th>
                <td width="250px"><?php echo empty($feedkey) ? _e($errormsg['feedkey_notgen']) : _e($feedkey); ?></td>
                <td>
                <?php if ($feedkey_opt ['feedkey_reset'] == TRUE && !$current_user->has_cap('level_9')) : ?>
                    <input name="feedkey-reset" type="checkbox" id="feedkey-reset_inp" value="0" /> Reset Key
                <?php elseif ($current_user->has_cap('level_9')) : ?>
                    <?php if (empty($feedkey)) : ?>
                        <input name="feedkey-generate" type="checkbox" id="feedkey-generate_inp" value="0" /> Generate Key
                    <?php else : ?>
                        <select name="feedkey-reset-admin" id="feedkey-reset-admin"><?php echo $feedkey_reset_options ?></select>
                    <?php endif; ?>
                <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="feedkey">Your Feed URL</label></th>
                <td colspan="2"><?php echo empty($feedkey) ? _e($errormsg['feedurl_notgen']) : _e($feedurl); ?></td>
            </tr>
        </table>
        <?php
    }

    //----------------------------------------------------------------------------
    //		PLUGIN FUNCTIONS
    //----------------------------------------------------------------------------

    //----------------------------------------------------------------------------
    //	Generate Feed Key Function
    //----------------------------------------------------------------------------

    function feedkey_gen()
    {
        global $userdata, $feedkey_opt;
        
        
        //Construct Character Set
        $charset = "0123456789"; //Numeric Character Set
        
        //Add rest of character set based on settings
        switch ($feedkey_opt['char_set'])
        {
            case 'alpha-numeric-lower':
                $charset .= 'abcdefghijklmnopqrstuvwxyz';
                break;
            case 'alpha-numeric-upper':
                $charset .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alpha-numeric-mix':
                $charset .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alpha-lower':
                $charset = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case 'alpha-upper':
                $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'alpha-mixed':
                $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'numeric':
                break;
        }
        
        $keylength = $feedkey_opt['key_length']; //Key Length

        for ($i=0; $i<$keylength; $i++) 
        {
            $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
        }
        
        //Choose salt being used to hash key against
        switch ($feedkey_opt['salt'])
        {
            case 'username':
                $salt = $userdata->user_login;
                break;
            case 'email':
                $salt = $userdata->user_email;
                break;
        }
        
        switch ($feedkey_opt['hash_using'])
        {
            case 'md5':
                $hashedkey = md5($salt.$key);
                break;
            case 'sha1':
                $hashedkey = sha1($salt.$key);
                break;
            case 'sha1_md5':
                $hashedkey = sha1(md5($salt.$key));
                break;
            case 'md5_sha1':
                $hashedkey = md5(sha1($salt.$key));
                break;
        }
        
        return $hashedkey;
    }

    //----------------------------------------------------------------------------
    //	Reset Feed Key Function
    //----------------------------------------------------------------------------

    function feedkey_reset()
    {	
        $id = $_POST['user_id'];
        
        if ($_POST['feedkey-reset'] != NULL || $_POST['feedkey-generate'] != NULL || $_POST['feedkey-reset-admin'] == 'feedkey-reset') //If the reset or generate check box is checked
        {
            $feedkey = feedkey_gen();
            update_usermeta($id, 'feed_key', $feedkey);
        }
        
        if ($_POST['feedkey-reset-admin'] == 'feedkey-remove')
        {
            $feedkey = NULL;
            update_usermeta($id, 'feed_key', $feedkey);
        }
    }

    //----------------------------------------------------------------------------
    //	Create RSS Feed Function
    //----------------------------------------------------------------------------

    function feedkey_create_feed($item_title, $item_description)
    {	
        global $blogtitle, $blogurl;
        
        $today = date('F j, Y G:i:s T');
        
        $feed_content = '<?xml version="1.0" encoding="ISO-8859-1" ?> 
                        <rss version="2.0"> 
                            <channel> 
                                <title>'.$blogtitle.'</title>
                                <link>'.$blogurl.'</link>
                                <item>
                                    <title>'.$item_title.'</title>
                                    <link>'.$blogurl.'</link>
                                    <description>'.$item_description.'</description>
                                    <pubDate>'.$today.'</pubDate>
                                </item>
                            </channel>
                        </rss>';
                        
        return $feed_content;
    }

    //----------------------------------------------------------------------------
    //	Main Function
    //----------------------------------------------------------------------------

    function feedkey() {
		if (!is_feed()) { return; }
		
        global $feedkey_valid, $feedkey_userid, $feedkey_opt, $errormsg;

        if (is_feed()) //Check if URL is a Feed
        {
            if (empty($_GET['feedkey'])) {
                if (!$feedkey_opt['enable_public']) {
                    $feed = feedkey_create_feed('No Feed Key Found', $errormsg['feedkey_missing']);
                    header("Content-Type: application/xml; charset=ISO-8859-1");
                    echo $feed;
                    exit;
                }
            } elseif ($feedkey_valid == FALSE) {
                if (!$feedkey_opt['enable_public']) {
                    $feed = feedkey_create_feed('Feed Key is Invalid', $errormsg['feedkey_invalid']);
                    header("Content-Type: application/xml; charset=ISO-8859-1");
                    echo $feed;
                    exit;
                }
            } elseif ($feedkey_valid == TRUE) {
                // Let the user access private feeds
                wp_set_current_user($feedkey_userid);
            }
        } 
    }

    //----------------------------------------------------------------------------
    //	Init Function
    //----------------------------------------------------------------------------

    function feedkey_init()
    {   
        global $userdata, $feedkey_valid, $feedkey_userid, $errormsg, $feedkey_opt, $wpdb;
       
        if (!empty($userdata->ID)) // If user is logged in
        {
            //Get User's Feed key
            $users_feedkey = get_usermeta($userdata->ID,'feed_key');
            
            //If there isn't one then generate one
            if (empty($users_feedkey))
            {
                $feedkey = feedkey_gen();
                update_usermeta($userdata->ID, 'feed_key', $feedkey);
            }
        }
        
        $submitted_feedkey = $_GET['feedkey'];
        
        if (!empty($submitted_feedkey))
        {
            // Check if Feed Key is in the Database
            $find_feedkey = $wpdb->get_var("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_value = '$submitted_feedkey'");
            
            if (!empty($find_feedkey)) //If Feed Key is found
            {
                $feedkey_valid = TRUE;
                $feedkey_userid = $find_feedkey;
            }
        }
            
        //WordPress Feed Files
        switch (basename($_SERVER['PHP_SELF'])) 
        {
            case 'wp-rss.php':
            case 'wp-rss2.php':
            case 'wp-atom.php':
            case 'wp-rdf.php':
            case 'wp-commentsrss2.php':
            case 'wp-feed.php':
                if (empty($submitted_feedkey)) {
                    if (!$feedkey_opt['enable_public']) {
                        $feed = feedkey_create_feed('No Feed Key Found', $errormsg['feedkey_missing']);
                        header("Content-Type: application/xml; charset=ISO-8859-1");
                        echo $feed;
                        exit;
                    }
                } elseif ($feedkey_valid == FALSE) {
                    if (!$feedkey_opt['enable_public']) {
                        $feed = feedkey_create_feed('Feed Key is Invalid', $errormsg['feedkey_invalid']);
                        header("Content-Type: application/xml; charset=ISO-8859-1");
                        echo $feed;
                        exit;
                    }
                }
                break;
        }
        
        //WordPress Feed Queries
        switch ($_GET['feed'])
        {
            case 'rss':
            case 'rss2':
            case 'atom':
            case 'rdf':
                if (empty($submitted_feedkey)) {
                    if (!$feedkey_opt['enable_public']) {                
                        $feed = feedkey_create_feed('No Feed Key Found', $errormsg['feedkey_missing']);
                        header("Content-Type: application/xml; charset=ISO-8859-1");
                        echo $feed;
                        exit;
                    }
                } elseif ($feedkey_valid == FALSE) {
                    if (!$feedkey_opt['enable_public']) {
                        $feed = feedkey_create_feed('Feed Key is Invalid', $errormsg['feedkey_invalid']);
                        header("Content-Type: application/xml; charset=ISO-8859-1");
                        echo $feed;
                        exit;
                    }
                }
                break;
        }
    }


    //----------------------------------------------------------------------------
    //		ADMIN OPTION PAGE FUNCTIONS
    //----------------------------------------------------------------------------

    function feedkey_options_page()
    {
        global $wpdb, $wpversion;

        if (isset($_POST['submit']) ) {
            
        // Options Array Update
        $optionarray_update = array (
            'char_set' => $_POST['char_set'],
            'key_length' => $_POST['key_length'],
            'salt' => $_POST['salt'],
            'hash_using' => $_POST['hash_using'],
            'user_reset' => $_POST['user_reset'],
            'enable_public' => $_POST['enable_public']
        );
        
        update_option('feedkey_options', $optionarray_update);
        }
        
        // Get Options
        $optionarray_def = get_option('feedkey_options');
        
        // Setup Character Set Options
        $charset_types = array(
            'Alpha-Numeric (Lowercase)' => 'alpha-numeric-lower',
            'Alpha-Numeric (Uppercase)' => 'alpha-numeric-upper',
            'Alpha-Numeric (Mixed Case)' => 'alpha-numeric-mixed',
            'Alpha (Lowercase)' => 'alpha-lower',
            'Alpha (Uppercase)' => 'alpha-upper',
            'Alpha (Mixed)' => 'alpha-mixed',
            'Numeric' => 'numeric'
        );
        
        foreach ($charset_types as $option => $value) {
            if ($value == $optionarray_def['char_set']) {
                    $selected = 'selected="selected"';
            } else {
                    $selected = '';
            }
            
            $charset_options .= "\n\t<option value='$value' $selected>$option</option>";
        }
        
        // Setup Key Length Options
        $keylength_types = array(
            '8bit' => '8',
            '16bit' => '16',
            '32bit' => '32',
            '64bit' => '64',
            '128bit' => '128',
            '256bit' => '256'
        );
        
        foreach ($keylength_types as $option => $value) {
            if ($value == $optionarray_def['key_length']) {
                    $selected = 'selected="selected"';
            } else {
                    $selected = '';
            }
            
            $keylength_options .= "\n\t<option value='$value' $selected>$option</option>";
        }
        
        // Setup Salt Options
        $salt_types = array(
            'Username' => 'username',
            "eMail" => 'email'
        );
        
        foreach ($salt_types as $option => $value) {
            if ($value == $optionarray_def['salt']) {
                    $selected = 'selected="selected"';
            } else {
                    $selected = '';
            }
            
            $salt_options .= "\n\t<option value='$value' $selected>$option</option>";
        }
        
        // Setup Hash Options
        $hash_types = array(
            'md5' => 'md5',
            'sha1' => 'sha1',
            'sha1 then md5' => 'sha1_md5',
            'md5 then sha1' => 'md5_sha1'
        );
        
        foreach ($hash_types as $option => $value) {
            if ($value == $optionarray_def['hash_using']) {
                    $selected = 'selected="selected"';
            } else {
                    $selected = '';
            }
            
            $hash_options .= "\n\t<option value='$value' $selected>$option</option>";
        }
        
        ?>
        <div class="wrap">
        <h2>Feed Key Options</h2>
        <form method="post">
        <fieldset class="options" style="border: none">
        <p>
        <em>Feed Key</em> creates unique feed URLs for each of your users on your site by adding <em>Feed Keys</em> to the end of the exsisting feed url. <em>Feed Keys</em> are made unique by hashing the user's email or username against a random key, of which you can choose the length. You can also choose which algorithm to use to hash the salt and the key together by choosing either md5, sha1 or both (in either order).
        </p>
        <h3>Key Generation</h3>
        <table width="100%" <?php $wpversion >= 2.5 ? _e('class="form-table"') : _e('cellspacing="2" cellpadding="5" class="editform"'); ?> >
            <tr valign="top">
                <th width="200px" scope="row">Character Set</th>
                <td width="100px"><select name="char_set" id="char_set_inp"><?php echo $charset_options ?></select></td>
                <td><span style="color: #555; font-size: .85em;">Choose which character set you want to use to make the input key <em>(Feed Keys are always alpha-numeric)</em></span></td>
            </tr>
            <tr valign="top">
                <th width="200px" scope="row">Input Key Length</th>
                <td width="100px"><select name="key_length" id="key_length_inp"><?php echo $keylength_options ?></select></td>
                <td><span style="color: #555; font-size: .85em;">Choose the length of the input key, before it gets hashed <em>(Feed Keys are either 32 or 40bit in length)</em></span></td>
            </tr>
            <tr valign="top">
                <th width="200px" scope="row">Salt</th>
                <td width="100px"><select name="salt" id="salt_inp"><?php echo $salt_options ?></select></td>
                <td><span style="color: #555; font-size: .85em;">To ensure the key is unique, choose what user info to use as the 'salt' when hashing the input key</span></td>
            </tr>
            <tr valign="top">
                <th width="200px" scope="row">Algorithm</th>
                <td width="100px"><select name="hash_using" id="hash_using_inp"><?php echo $hash_options ?></select></td>
                <td><span style="color: #555; font-size: .85em;">Choose how which algorithm to hash the 'salt' and key with. md5 <em>(and sha1 followed by md5)</em> will result in a 32bit <em>Feed Key</em>, sha1 <em>(and md5 followed by sha1)</em> will result in a 40bit <em>Feed Key</em>.</span></td>
            </tr>
        </table>
        <h3>Public Feeds</h3>
        <table width="100%" <?php $wpversion >= 2.5 ? _e('class="form-table"') : _e('cellspacing="2" cellpadding="5" class="editform"'); ?> >
            <tr valign="top">
                <th scope="row">Enable?</th>
                <td><input name="enable_public" type="checkbox" id="enable_public_inp" value="1" <?php checked('1', $optionarray_def['enable_public']); ?> /></td>
                <td><span style="color: #555; font-size: .85em;">Choose whether users who are not logged in can see any feeds at all.</span></td>
            </tr>
        </table>
        
        <h3>Feed Key Reset</h3>
        <table width="100%" <?php $wpversion >= 2.5 ? _e('class="form-table"') : _e('cellspacing="2" cellpadding="5" class="editform"'); ?> >
            <tr valign="top">
                <th scope="row">User Reset</th>
                <td><input name="user_reset" type="checkbox" id="user_reset_inp" value="1" <?php checked('1', $optionarray_def['user_reset']); ?> /></td>
                <td><span style="color: #555; font-size: .85em;">Choose whether users can reset their own <em>Feed Key</em>, otherwise only admins can reset <em>Feed Keys</em></span></td>
            </tr>
        </table>
        </fieldset>
        <p />
        <div class="submit">
            <input type="submit" name="submit" value="<?php _e('Update Options') ?> &raquo;" />
        </div>
        </form>
        <?php

    }

    //----------------------------------------------------------------------------
    //		WORDPRESS FILTERS AND ACTIONS
    //----------------------------------------------------------------------------
    
    // Moved to wp-sentry.php
}
?>