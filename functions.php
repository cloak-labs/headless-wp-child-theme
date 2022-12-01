<?php

add_action('enqueue_block_editor_assets', 'gutenberg_editor_assets');
function gutenberg_editor_assets()
{
  // Load the theme styles within Gutenberg.
  wp_enqueue_style('my-gutenberg-editor-styles', get_theme_file_uri('/blocks/styles.css'), array(), filemtime(), 'all');
  wp_enqueue_script('gutenberg-editor-js', get_theme_file_uri('/blocks/scripts.js'), array(), filemtime(), true);
}

add_theme_support('post-thumbnails'); // enable featured images
add_post_type_support('page', 'excerpt'); // enable page excerpts

add_action('init', 'register_menus');
function register_menus()
{
  register_nav_menus(
    array(
      'nav-1' => __('Nav Dropdown #1'),
      'nav-2' => __('Nav Dropdown #2'),
      'nav-3' => __('Nav Dropdown #3'),
      'nav-4' => __('Nav Dropdown #4'),
      'footer-menu' => __('Footer')
    )
  );
}

add_filter('acf/settings/rest_api_format', function () {
  return 'standard';
});

add_action('init', 'register_blocks');
function register_blocks()
{
  register_block_type(get_template_directory() . '/blocks/boxesBenefits/block.json');
  register_block_type(get_template_directory() . '/blocks/postList/block.json');
  register_block_type(get_template_directory() . '/blocks/pageHeader/block.json');
  register_block_type(get_template_directory() . '/blocks/testimonial/block.json');
  register_block_type(get_template_directory() . '/blocks/cardFeature/block.json');
  register_block_type(get_template_directory() . '/blocks/faq/block.json');
  register_block_type(get_template_directory() . '/blocks/cta/block.json');
}

// Brought in from lionheart
add_filter('jwt_auth_iss', function () {
  // Default value is get_bloginfo( 'url' );
  return 'http://wordpress-admin-url';
});

// Brought in from lionheart
add_action('rest_api_init', function () {
  register_rest_route('wp/v2', 'menu', array(
    'methods' => 'GET',
    'callback' => 'custom_wp_menu',
  ));
});

// create custom function to return nav menu
function custom_wp_menu() {
	// Replace your menu name, slug or ID carefully
	return wp_get_nav_menu_items('Navbar');
}

// brought in from lionheart
add_filter('register_post_type_args', 'wpd_change_post_type_args', 10, 2);
function wpd_change_post_type_args($args, $post_type)
{
  if ('testimonials' == $post_type) {
    $args['rewrite']['with_front'] = false;
    $args['rewrite']['slug'] = 'testimonials';
  } else if ('faqs' == $post_type) {
    $args['rewrite']['with_front'] = false;
    $args['rewrite']['slug'] = 'faqs';
  }
  return $args;
}

// Helper function to str_replace multi-dimensional array
function str_replace_json($search, $replace, $subject)
{
  return json_decode(str_replace($search, $replace,  json_encode($subject)), true);
}

// Begin Yoast SEO Filters
add_filter('wpseo_schema_webpage', 'webpage_filter');
/**
 * Changes @type of Webpage Schema data.
 *
 * @param array $data Schema.org Webpage data array.
 *
 * @return array Schema.org Webpage data array.
 */
function webpage_filter($data)
{

  $data['isPartOf']['@id'] = get_frontend_url();

  return $data;
}

add_filter('wpseo_schema_website', 'website_filter');
/**
 * Changes Website Schema data output, overwriting the name and alternateName.
 *
 * @param array $data Schema.org Website data array.
 *
 * @return array Schema.org Website data array.
 */
function website_filter($data)
{
  $site_url = get_frontend_url();

  $new_data = str_replace('http://wordpress-admin-url', $site_url, $data);
  $new_data['publisher'] = str_replace('http://wordpress-admin-url', $site_url, $data['publisher']);
  $new_data['potentialAction'][0]['target']['urlTemplate'] = $site_url . '?query={search_term_string}';
  return $new_data;
}

add_filter('wpseo_schema_organization', 'change_organization_schema', 11, 2);

/**
 * Add extra properties to the Yoast SEO Organization
 *
 * @param array             $data    The Schema Organization data.
 * @param Meta_Tags_Context $context Context value object.
 *
 * @return array $data The Schema Organization data.
 */
function change_organization_schema($data, $context)
{
  $site_url = get_frontend_url();

  $new_data = str_replace('http://wordpress-admin-url', $site_url, $data);

  return $new_data;
}

add_filter('wpseo_schema_person', 'schema_change_person', 11, 2);
/**
 * Changes the Yoast SEO Person schema.
 *
 * @param array             $data    The Schema Person data.
 * @param Meta_Tags_Context $context Context value object.
 *
 * @return array $data The Schema Person data.
 */
function schema_change_person($data, $context)
{
  return NULL;
}
