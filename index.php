<?php
/* *
 * Plugin Name: WpLite2020 Features
 * Plugin URI: n/a
 * Description: Wp-lite theme additional features
 * Plugin Author: Anonymous
 * Version: 1
 * Text Domain: wpl_f
 * */

/***
 * ACTIVATE
 **/
function wplite_plugin_activate()
{
    add_option('WpLitePlugin_activated', time());
    // Some other code to run on plugin activation.
    // Can register Custom Post Types here, etc.

}
/********************************************************************/
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * THE FUNCTIONS *******
 * * * * ** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/***
 * img alt tag from img name
 * ****/
function get_img_alt($img_url)
{
    $URL = $img_url;
    $image_name = (stristr($URL, '?', true)) ? stristr($URL, '?', true) : $URL;
    $pos = strrpos($image_name, '/');
    $image_name = substr($image_name, $pos + 1);

    $without_extension = pathinfo($image_name, PATHINFO_FILENAME);
    echo ucfirst(str_replace('-', ' ', $without_extension));
}

/* *
 * loop posts (ALL POST TYPES)
 * * */
function loop_posts($atts)
{
    global $post;
    $row_class = $atts['row_class'];
    $col_class = $atts['col_class'];
    $post_type = $atts['pt'];
    $cate = $atts['cate'];
    $col_width = $atts['col_width'];
    $per_page = $atts['per_page'];
    $excerpt = $atts['excerpt'];
    $content = $atts['content'];
    $feat_img = $atts['feat_img'];
    $author = $atts['author'];
    $readmore_title = $atts['readmore_title'];
    $order = $atts['order'];
    $date = $atts['date'];
    $date_format = $atts['date_format'];
    $custom_field_1_group = $atts['custom_field_1_group'];
    $custom_field_2_group = $atts['custom_field_2_group'];
    $custom_field_1 = $atts['custom_field_1'];
    $custom_field_2 = $atts['custom_field_2'];

    $n = 1;
    $the_args = array('row_class' => $row_class, 'col_class' => $col_class, 'post_type' => $post_type, 'category_name' => $cate, 'posts_per_page' => $per_page, 'orderby' => 'ID', 'order' => $order, 'post__not_in' => array(get_the_ID()));

    $query = new WP_Query($the_args);
    $output = '<div class="' . $row_class . ' row pb-5 looped-pt pt-' . $post_type . '">';
    while ($query->have_posts()): $query->the_post();
        $output .= '<div class="' . $col_class . 'col-md-' . $col_width . ' p-3" id="position_' . $n . '">';
        $output .= '<div class="post-' . get_the_ID() . ' ' . $post_type . ' ' . $post_type . '-' . $n . '">';
        if ($feat_img != "") {
            if (has_post_thumbnail()) {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
                $output .= '<div class="loop-' . $post_type . '-img-container"><a href="' . get_the_permalink(get_the_ID()) . '"><img src="' . $image[0] . '" class="img-fluid ml-auto mr-auto"/></a></div>';
            }
        }
        $output .= '<h4 class="loop-' . $post_type . '-title"><a href="' . get_the_permalink(get_the_ID()) . '">' . get_the_title() . '</a></h4>';
        if ($excerpt != "") {
            $output .= '<p class="excerpt ' . $post_type . '-excerpt">' . get_the_excerpt() . '</p>';
        }
        if ($content != "") {
            $output .= '<p class="content ' . $post_type . '-content">' . get_the_content() . '</p>';
        }
        if ($author != "") {
            $output .= '<p class="author ' . $post_type . '-author">' . get_the_author() . '</p>';
        }
        if ($date != "") {
            $output .= '<p class="date ' . $post_type . '-date">' . get_the_date($date_format) . '</p>';
        }

        if ($custom_field_1 != "" && $custom_field_1_group != "") {
            $output .= '<p class=" ' . $post_type . '-custom_field_1">' . get_field($custom_field_1, $custom_field_1_group) . '</p>';
        }
        if ($custom_field_2 != "" && $custom_field_2_group != "") {
            $output .= '<p class=" ' . $post_type . '-custom_field_2">' . get_field($custom_field_2, $custom_field_2_group) . '</p>';
        }

        if ($readmore_title != "") {
            $output .= '<a class="more-link" href="' . get_the_permalink(get_the_ID()) . '">' . $readmore_title . '</a>';
        }

        $output .= '</div>';
        $output .= '</div>';

        $n++;
    endwhile;
    wp_reset_postdata();
    $output .= '</div>';
    return $output;
}

//loop posts

add_shortcode('loop_pt', 'loop_posts');

//EXAMPLE: [loop_pt row_class='the_posts' col_clss='the_post' pt='posts' feat_img='' cate='popular' order='ASC' col_width='4' per_page='8' excerpt='yes' readmore_title='readmore' date='yes']

/* * **
 * //RELATED POSTS
 * ** */
function related_posts($atts)
{
    global $post;
    $category = $atts['category'];
    $per_page = $atts['per_page'];

    $category != "" ? $category : 'uncategorized';
    $per_page != "" ? $per_page : 3;

    $related_args = array('post_type' => 'post', 'category_name' => $category, 'post__not_in' => array(get_the_ID()), 'posts_per_page' => $per_page);
    $related = new WP_Query($related_args);
    ob_start();
    ?>
  <div class="row mt-2 pb-5 related <?php echo $category ?>">
      <?php while ($related->have_posts()): $related->the_post();?>
	          <div class="col-md-4 p-3">
	              <div class="article post-<?php echo get_the_ID(); ?>"> <a href="<?php echo get_the_permalink(get_the_ID()); ?>">
	                      <?php if (has_post_thumbnail()) {?>
	                          <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');?>
	                          <img src="<?php echo $image[0]; ?>" class="img-fluid ml-auto mr-auto" width=""/>
	                      <?php }?>
	                      <p class="text-center"><?php echo get_the_title(); ?></p>
	                  </a> </div>
	          </div>
	          <?php
endwhile;
    wp_reset_postdata();
    ?>
  </div>
  <?php
$html = ob_get_clean();
    return $html;
}

//related posts
add_shortcode('related_posts', 'related_posts');
//[related_posts category="" per_page="3"] // NOTE: NEEDS TO BE AUTOMATED WITHOUT ANY PARAMS

/* * ***
 * * BREADCRUMBS
 * * */

function the_breadcrumb($class)
{
    echo "<ol class='breadcrumb '.$class>";
    if (!is_home()) {
        echo '<li><a href="' . get_option('home') . '"> Home ' . "</a><li>";
        if (is_category() || is_single()) {
            echo '<li>' . the_category('title_li=') . '</li>';
            if (is_single()) {
                the_title();
            }
        } elseif (is_page()) {
            the_title();
        }
    } //is_home
    echo "</ol>";
}

/****
 *
 *  MISC SHORTCODEs
 ****/
function wp_lite_button_func($atts)
{
    $title = $atts['title'];
    $class = $atts['class'];
    $link = $atts['link'];

    $target = $atts['target'];
    $target = $target = !"" ? $target : '';
    return "<a href='$link' class='btn $class' target='" . $target . "'>$title</a>";
}
add_shortcode('btn', 'wp_lite_button_func');
//[btn title='' class='' link='']

function row_shortcode_func()
{
    $output = '<div class="row">';
    return $output;
}add_shortcode('row', 'row_shortcode_func');
function rowend_shortcode_func()
{
    $output = '</div>';
    return $output;
}add_shortcode('row_end', 'rowend_shortcode_func');

function col_shortcode_func()
{
    $output = '<div class="col col-xs-12">';
    return $output;
}add_shortcode('col', 'col_shortcode_func');

function colend_shortcode_func()
{
    $output = '</div>';
    return $output;
}add_shortcode('col_end', 'colend_shortcode_func');

/****
 *
 *  User Registration Process
 ****/

/** 1. Registration
 * **/
//add_shortcode('wpl_signup', 'wpl_signup_cb');

function wpl_signup_cb()
{
    ob_start();?>
<div class="signup-form">
    <form action="" method="post" id="">
		<h2>Register</h2>
		<p class="hint-text">Create your account. It's free and only takes a minute.</p>
        <div class="form-group">
			<div class="row">
				<div class="col"><input type="text" class="form-control" name="first_name" placeholder="First Name" required="required"></div>
				<div class="col"><input type="text" class="form-control" name="last_name" placeholder="Last Name" required="required"></div>
			</div>
        </div>
        <div class="form-group">
        	<input type="email" class="form-control" name="email" placeholder="Email" required="required">
        </div>
		<div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" required="required">
        </div>
		<div class="form-group">
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required="required">
        </div>
        <div class="form-group">
			<label class="form-check-label"><input type="checkbox" required="required"> I accept the <a href="#">Terms of Use</a> &amp; <a href="#">Privacy Policy</a></label>
		</div>
		<div class="form-group">
            <button type="submit" class="btn btn-success btn-lg btn-block">Register Now</button>
        </div>
    </form>
	<div class="text-center">Already have an account? <a href="/signin">Sign in</a></div>
</div>
<?php add_action( 'wp_footer', 'signup_script', 24 );
    function signup_script()
    {
        ?>
<script>
  (function ($) {
 $(document).ready(function() {
	console.log('noConf added!');
 });
})(jQuery);
</script>
<?php }?>
<?php $reg_form = ob_get_clean();
    return $reg_form;
}

/** 2. Login
 * **/
/** 3. Email Verification
 * **/
/** 4. Forgot Password
 * **/

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * PLUGING SETTINGS PAGE *******
 * * * * ** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function settings_page()
{
    //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_menu_page('WpLP Feataures', 'WpLP Feataures', 'manage_options', 'plugin_settings', 'the_features_page', 'dashicons-list-view', 41);
}
add_action('admin_menu', 'settings_page');
//THE PAGE::
function the_features_page()
{?>
  <?php if (is_admin()) {?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<?php }?>
<style>  .page_title {    color: #FFF;    background-color: #000;    text-align: center;    width: 100%;    padding: 15px 10px;    border: 1px solid #000;}.wrap {margin: 10px 20px 0 2px;}</style>
<div class="wrap">
<div class="row">
    <div class="col-md-12">
      <h3 class="page_title">Welcome to WP Lite Additional Features</h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
        <p>Below are some features that can be invoked with shortcodes and/or functions:</p>
        <div>
          <ol>
            <li> <strong> get_img_alt($img_url) </strong><br> This will get image url and return a name with dashes(-) that can be used for image alt tag.</li>
            <li> <strong>EXAMPLE: [loop_pt row_class='the_posts' col_clss='the_post' pt='posts' feat_img='' cate='popular' order='ASC' col_width='4' per_page='8' excerpt='yes' readmore_title='readmore' date='yes']</strong> <br>
                This is beta version but it should work though needs some/many improvements
          </li>
          <li> <strong>[related_posts category="test" per_page="3"]</strong> <br>
                Shows related posts based on category, it is beta version but it should work though needs some/many improvements.
          </li>
            <li> <strong>the_breadcrumb($class) </strong> <br> </li>
            <li> <strong>[btn title='' class='' link='']</strong> <br> </li>
            <li> <strong>[row][row_end]</strong> <br> </li>
            <li> <strong>[col][col_end]</strong> <br> </li>
          </ol>
        </div>
  </div>
</div>
</div>
<?php
}
/***************************************************************************************************************************************
 * *************************************************************************************************************************************/
register_activation_hook(__FILE__, 'wplite_plugin_activate');
/***
 * DEACTIVATE
 **/
function wplite_plugin_deactivate()
{
    add_option('WpLitePlugin_deactivated', time());
    // Some other code to run on plugin activation.
    // Can register Custom Post Types here, etc.

}
register_deactivation_hook(__FILE__, 'wplite_plugin_deactivate');
/***************************************************************************************************************************************
 * *************************************************************************************************************************************/
/***
 * UNINSTALL
 **/
function wplite_plugin_uninstall()
{
    delete_option('WpLitePlugin_activated');
    delete_option('WpLitePlugin_deactivated');
    // Some other code to run on plugin uninstall/deletion.
    // If your plugin file contains arbitrary code,
    // you must create an uninstall.php file.
}
register_uninstall_hook(__FILE__, 'wplite_plugin_uninstall');
//
/********************************
 * *  COMING UP IN NEXT VERSIONS:
1.1. USER ROLE SETTINGS
1.2. USER LOGIN
1.3. REGISTRATION
1.4. EMAIL VARIFICATION
1.5. FORGOT PASSWORD

2. FILE UPLOAD AND CREATE POSTS

3. CALL AN API AND FETCH DATA

4. INTEGRATE PAYMENT METHODS

5. A PLUGIN THAT SEARCHES A STRING IN
5.1. PAGES
5.2. POSTS
5.3. CUSTOM POST TYPES
5.4. USERS
5.5. META TABLES
5.6. OPTIONS
6. A CONTACT FORM
1. CAN DISABLE DEFAULT FIELDS
2. CAN ADD NEW FIELDS
3. SAVES & DISPLAYS MESSAGAES INSIDE WP ADMIN
4. REPLAY TO THOSE CONTACTORS
 * *
 * ***/
//