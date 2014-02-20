<?php
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<div id="%1$s" class="dbx-box %2$s">',
        'after_widget' => '</div></div>',
        'before_title' => '<h2 class="dbx-handle">',
        'after_title' => '</h2><div class="dbx-content">',
    ));
?>
<?php function widget_meta() {
?>
      <div id="meta" class="dbx-box">
        <h2 class="dbx-handle">Meta</h2>
        <div class="dbx-content">
          <ul>
              <li class="rss"><a href="<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a></li>
              <li class="rss"><a href="<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a></li>
              <li class="wordpress"><a href="http://www.wordpress.org" title="Powered by WordPress">WordPress</a></li>
              <li class="login"><?php wp_loginout(); ?></li>
          </ul>
        </div>
      </div>

<?php
}

?>
