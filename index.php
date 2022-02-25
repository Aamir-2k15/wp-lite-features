<?php
/* *
 * Plugin Name: WpLite2020 Features
 * Plugin URI: n/a
 * Description: Wp-lite theme additional features
 * Plugin Author: Anonymous
 * Version: 1.1.1
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
// add_shortcode('wpl_signup', 'wpl_signup_cb');

function wpl_signup_cb()
{
    ob_start();?>
<div class="signup-form">
    <form action="" method="post" id="signup" name="signup">
		<h2>Register</h2>
		<p class="hint-text">Create your account. It's free and only takes a minute.</p>
        <div class="form-group">
			<div class="row">
				<div class="col"><input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name"></div>
				<div class="col"><input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name"></div>
			</div>
        </div>
        <div class="form-group">
        	<input type="email" class="form-control" name="email" placeholder="Email">
        </div>
		<div class="form-group">
            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
        </div>
		<div class="form-group">
            <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Confirm Password">
        </div>
        <div class="form-group">
			<label class="form-check-label"><input type="checkbox" name="terms" id="terms"> I accept the <a href="#">Terms of Use</a> &amp; <a href="#">Privacy Policy</a></label>
		</div>
		<div class="form-group">
            <button type="submit" class="btn btn-success btn-lg btn-block" id="signup_btn">Register Now</button>
        </div>
    </form>
	<div class="text-center">Already have an account? <a href="/signin">Sign in</a></div>
</div>
<?php add_action('wp_footer', 'signup_script', 24);
    function signup_script() {
     ob_start();   
        ?>
<style>.error{color: #dc3545;}.form-group {    margin-bottom: 1rem;    position: relative;}
.custom-control-inline label.error {    color: #dc3545;    position: absolute;    top: 2px;    clear: both;    display: block;    width: 202px;    left: 230px;    height: 27px;   margin-bottom:10px;    line-height: 20px;}</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
  (function ($) {
 $(document).ready(function() {

    $("form[name='signup']").validate({

// Specify validation rules
rules: {
    // The key name on the left side is the name attribute
    // of an input field. Validation rules are defined
    // on the right side

    first_name: {
        required: true,
        minlength: 3
    },
    last_name: {
        required: true,
        minlength: 3
    },
    email: {
        required: true,
        // Specify that email should be validated
        // by the built-in "email" rule
        email: true
    },
    password: {
        required: true,
        minlength: 5
    },
    password_confirm: {
        required: true,
        minlength: 5,
        equalTo: "#password"
    },
    terms: "required",
},
// Specify validation error messages
messages: {

    first_name: {
        required: "Please provide a username",
        minlength: "Your username must be at least 5 characters long"
    },
    last_name: {
        required: "Please provide a username",
        minlength: "Your username must be at least 5 characters long"
    },
    password: {
        required: "Please provide a password",
        minlength: "Your password must be at least 5 characters long"
    },
    email: "Please enter a valid email address"
},
// Make sure the form is submitted to the destination defined
// in the "action" attribute of the form when valid
submitHandler: function (form) {
    // form.submit();
    signup_user(event);
}
});
//validation end
	console.log('noConf added!');

function signup_user(event){
    event.preventDefault();
    noenter();
    let form_data = $('form#signup').serialize();
    let the_url = "<?php echo admin_url('admin-ajax.php') ?>?action=register_user";
    // console.log(form_data);
    $.ajax({
            //debugger;
            url: the_url,
            type: "post",
            // dataType: "json",
            //                    async: false
            data: form_data,
            //                    beforeSend: ez_loading_func()
        }).done(function (response) {
            // debugger;
            console.log(response);
            // if (response.status == 1) {
                $('form#signup')[0].reset();
                // $("#response").html(response.response);
            // }
        }); //ajax done
}

 });
})(jQuery);

function noenter() {
  return !(window.event && window.event.keyCode == 13); }
</script>
<?php }?>
<?php $reg_form = ob_get_clean();
    return $reg_form;
}
/////////////////////////////
function mailtrap($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = '8974f4e3d756f0';
    $phpmailer->Password = 'cb34ad3f8d2cad';
  }  
  add_action('phpmailer_init', 'mailtrap');
////////////////////////////
add_action('wp_ajax_register_user', 'register_user');
add_action('wp_ajax_nopriv_register_user', 'register_user');
function register_user()
{
    $id_check = false;
    $user_id = wp_create_user($_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['email']);
    if ($user_id) {
        $id_check = true;
    }

    wp_update_user(
        array(
            'ID' => $user_id,
            'nickname' => $_REQUEST['first_name'],
        )
    );
    update_user_meta($user_id, 'name', $_REQUEST['first_name'] . ' ' . $_REQUEST['last_name']);
    update_user_meta($user_id, 'display_name', $_REQUEST['first_name']);

    $firstname = $_REQUEST['first_name'];
    $lastname = $_REQUEST['last_name'];
    update_user_meta($user_id, 'first_name', $firstname);
    update_user_meta($user_id, 'last_name', $lastname);
    wp_set_password($user_password, $user_id);

    update_user_meta($user_id, 'wp_user_level', 0);
    $user = new WP_User($user_id);
    $user->add_role('subscriber');//CHANGE THIS VIA $OPTIONS

    $status = 1;
    $message = "Thanks for registring with us, please check your inbox.";
    $email_msg = "Thank you for registering with Us. Your Password is: $user_password.<br/><br/>For further queries please contact us @ ".get_bloginfo( 'admin_email' ).". <br/><br/>Thanks.";
    wp_mail($_REQUEST['email'], $message, $email_msg, '','');

    $return = json_encode(array('request' => $_REQUEST, 'status' => $status, 'message' => $message));
    echo $return;
    exit;
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