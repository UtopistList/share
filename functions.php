    <?php







    function line_break_shortcode() {

        return '<br />';

    }

    add_shortcode( 'br', 'line_break_shortcode' );




    vc_disable_frontend();

    function my_load_child_theme_styles() {

        if ( ! defined( 'WPEX_THEME_STYLE_HANDLE' ) ) {
            wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array(), '1.0' );
        }

        // First de-register the main stylesheet (which is now your child theme style.css)
        wp_dequeue_style( WPEX_THEME_STYLE_HANDLE );
        wp_deregister_style( WPEX_THEME_STYLE_HANDLE );

        // Add the parent style.css with the main style handle
        wp_enqueue_style( WPEX_THEME_STYLE_HANDLE, get_template_directory_uri() . '/style.css', array(), WPEX_THEME_VERSION );

        // Re-add child CSS with parent as dependency
        wp_enqueue_style( 'child-css', get_stylesheet_directory_uri() . '/style.css', array( WPEX_THEME_STYLE_HANDLE ), '1.0' );

        wp_enqueue_style(
        'child_media-css', // stylesheet handle
        get_stylesheet_directory_uri() . '/child_media_1300_max.css', // stylesheet file URL
        array( WPEX_THEME_STYLE_HANDLE ), // dependencies
        '1.0', // stylesheet version
        'only screen and (max-width:1300px)' // media query
    );


    wp_enqueue_style(
        'child_media-css', // stylesheet handle
        get_stylesheet_directory_uri() . '/child_media_1300_min.css', // stylesheet file URL
        array( WPEX_THEME_STYLE_HANDLE ), // dependencies
        '1.0', // stylesheet version
        'only screen and (max-width:1300px)' // media query
    );


        

    }
    add_action( 'wp_enqueue_scripts', 'my_load_child_theme_styles' );



    function sc_get_related( $item_id ) {
        $terms = wp_get_post_terms( $item_id, 'relations' );
        $query = new WP_Query([
            'post_type' => 'post',
            'tax_query' => [[
                'taxonomy'  => 'relations',
                'field'     => 'term_id',
                'terms' => $terms[0]->term_id,

            ],],
        ]);
        $html = [];
        if ( 0 < $query->post_count ) {
            foreach ( $query->posts as $post ) {
                $href = get_the_permalink( $post );
                $html[] = "<a href=\"$href\">". esc_html( $post->post_title ) .'</a>';
            }
        }
        return implode('◦', $html );
    }









    function donate_shortcode(){
        return '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_s-xclick" />
    <input type="hidden" name="hosted_button_id" value="RTXY54HJ4MC8C" />
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="Donate with PayPal button" />
    </form>';
    }

    add_shortcode('donate', 'donate_shortcode');






    /*
    // Shortcode to output custom PHP in Visual Composer
    function wpc_vc_shortcode( $atts ) {
        echo "<?php dynamic_sidebar( 'counter' ); ?>";
    }
    add_shortcode( 'my_vc_php_output', 'wpc_vc_shortcode');

    add_filter( 'tribe_events_kill_responsive', '__return_true');


    */


    function exclude_pages_from_search($query) {
        if ( ! is_admin() && $query->is_main_query() && is_search() ) {
            $query->set( 'post_type', 'post' );
        }
        return $query;
    }
    add_filter( 'pre_get_posts','exclude_pages_from_search' );  


    /*

    add_filter( 'wp_get_object_terms', function( $terms ) {
        if ( $terms ) {
            $excluded_terms = array( 21 ); // array of term ID's to exclude
            foreach( $terms as $key => $term ) {
                if ( in_array( $term->term_id, $excluded_terms ) ) {
                    unset($terms[$key]);
                }
            }
        }
        return $terms;
    }, 10, 3 );

    function wpb_total_posts() {
    $total = wp_count_posts()->publish;
    return $total;
    }
    add_shortcode('total_posts','wpb_total_posts');






    // Add Shortcode to show posts count inside a category
    function category_post_count( $atts ) {

        $atts = shortcode_atts( array(
            'category' => null
        ), $atts );

        // get the category by slug.
        $term = get_term_by( 'slug', $atts['category'], 'category');

        return ( isset( $term->count ) ) ? $term->count : 0;
    }
    add_shortcode( 'category_post_count', 'category_post_count' );  */






    add_shortcode('category_post_count', function ($atts, $content) {
        $atts = shortcode_atts([
            'category' => 0
        ], $atts);
        $cat_id = is_numeric($atts['category']) ?
            intval($atts['category']) :
            get_category_by_slug($atts['category'])->term_id;
        return count(get_posts([
            'nopaging' => true,
            'fields' => 'ids',
            'cat' => $cat_id
        ]));
    });



    add_filter( 'wpex_title', function( $title ) {
        if ( is_search() ) {
            return 'lists found';
        }
        return $title;
    }, 50 );



    add_action( 'vc_after_init_base', 'add_more_custom_layouts' );
    function add_more_custom_layouts() {
      global $vc_row_layouts;
      array_push( $vc_row_layouts, array(
      'cells' => '616_616_216_216',
      'mask' => '480',
      'title' => 'Custom 6/16 + 6/16 + 2/16 + 2/16',
      'icon_class' => 'l_616_616_216_216' )
      );
    }



    function my_custom_header_text() { ?>
        <div class="logosubheading clr">For The Coffee Table<img class="coffee_table" src="https://utopistlist.com/coffee_table.svg" ></div>
    <?php }
    add_action( 'wpex_hook_site_logo_inner', 'my_custom_header_text', 40 );



    // Add related items setting for standard posts
    add_filter( 'wpex_metabox_array', function( $array, $post ) {

        if ( 'post' == $post->post_type ) {

            $array['main']['settings']['related_post_ids'] = array(
                'title'         => __( 'Related Posts', 'total' ),
                'description'   => __( 'Comma seperated ID\'s for related items', 'total' ),
                'id'            => 'related_post_ids',
                'type'          => 'text',
            );

        }

        // Return fields
        return $array;

    }, 40, 2 );

    add_filter(
        'tg_wp_query_args',
        function( $query_args, $grid_name ) {

            $related = get_post_meta( get_the_ID(), 'related_post_ids', true );

             if ( 'related' === $grid_name && ! empty( $related ) ) {

                $query_args['post__in'] = explode( ',', $related );
                unset( $query_args['category__in'] );

            }

            return $query_args;

        },
        10,
        2
    );





    add_filter( 'bbp_get_the_content', 'amend_reply', 10, 3);

    Function amend_reply ($output, $args, $post_content) {
    if ($args['context'] == 'reply' && $post_content == '') $output=str_replace('></textarea>', 'placeholder="Submit comment" ></textarea>',$output) ;
    return $output ;
    }






    add_shortcode( 'bbpresscomments', function() {
        $output     = '';
        $current_id = function_exists( 'vcex_get_the_ID' ) ? vcex_get_the_ID() : get_the_ID();
        $topics = new WP_Query( array(
            'post_type' => 'topic',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ) );
        if ( $topics->have_posts() ) {

            foreach( $topics->posts as $topic ) {
                if ( get_the_title( $topic ) == get_the_title( $current_id ) ) {
                    $output .= do_shortcode( '[bbp-single-topic id="' . intval( $topic ) . '"]' );
                }
            }
        }
        return $output;
    } );





    /*
    add_action( 'wpex_hook_header_inner', function() {
            if (is_singular()) {
                    $template = 7027; // Change this to the templatera post ID you want to grab content from
                    echo do_shortcode( '[templatera id="' . $template . '"]' );
            }
    } );

    add_filter( 'excerpt_length', function($length) {
        return 700;
    }, 9999 );
    */

    /*

    function items_query_args($query_args, $grid_name ) {
        if ($grid_name == 'items_grid') {
            $terms = wp_get_post_terms( get_the_ID(), 'relations' );
            $query_args['tax_query'] = array(
            array(
                'taxonomy'  => 'relations',
                'field'     => 'term_id',
                'terms'     =>  $terms[0],
            ),
        );
        $query_args['post_type'] = 'items';
        
        }

        return $query_args;

    }
    add_filter('tg_wp_query_args', 'items_query_args', 10, 2);


        
    */


    function items_query_args_wpgridbuilder( $query_args, $grid_id ) {

        // If it matches grid id 1, we exclude post IDs 1,2,3,4.
        if ( 2 === $grid_id ) {
            $terms = wp_get_post_terms( get_the_ID(), 'relations' );
            $query_args['tax_query'] = array(
            array(
                'taxonomy'  => 'relations',
                'field'     => 'term_id',
                'terms'     =>  $terms[0],
            ),
        );
        $query_args['post_type'] = 'items';
        }

        return $query_args;

    }

    add_filter( 'wp_grid_builder/grid/query_args', 'items_query_args_wpgridbuilder', 10, 2 );




    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10);

    add_action('woocommerce_shop_loop_item_title','fun',10);
    function fun()
    {
       echo '<img class="ads_arrow_svg" src="https://utopistlist.com/ads_arrow.svg"/>rent this AD space';
    }

    /*
    add_action('init','change_author_permalinks');  
    function change_author_permalinks()  
    {  
        global $wp_rewrite;  
        $wp_rewrite->author_base = 'profile'; // Change 'member' to be the base URL you wish to use  
        $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base. '/%author%';  
    }  */

    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'You can manage the content of your ads on the <a href="%s">ad setup page</a>.' :
                $translated_text = __( '<a href="%s">Go to your AD setup page</a></br> ', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );



    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Setup page for order #%1$d, %2$s' :
                $translated_text = __( 'Setup page for order #%1$d', 'total' );
                break;
        }
        return $translated_text;
    }, PHP_INT_MAX, 3 );



    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Return to shop' :
                $translated_text = __( 'Return to home page', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );


    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Please complete the ad details so that we can process your order.' :
                $translated_text = __( 'We have sent you an order confirmation e-mail, and the link to this setup page.
    [br][br]Please submit your AD image and target URL so that we can publish your AD.', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );

    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Image Upload' :
                $translated_text = __( 'Aspect Ratio<span class="aspectratio">1 - 1,5 &nbsp; &nbsp;width - height</span>', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );








    function myprefix_move_topbar() {

        // Remove from wrap top
        remove_action( 'wpex_hook_wrap_top', 'wpex_top_bar', 5 );

        // Re-add to header after hook
        add_action( 'wpex_hook_header_after', 'wpex_top_bar' );

    }
    add_action( 'init', 'myprefix_move_topbar' );   


    function my_theme_modify_stripe_fields_styles( $styles ) {
        return array(
            'base' => array(
                'iconColor'     => '#0a0e0f',
                'color' => '#0a0e0f',
                'fontSize' => '5vw',
                'font-weight' => '300',
                '::placeholder' => array(
                    'color' => '#0a0e0f',
                    
                ),
            ),
            'invalid' => array(
                'color' => '#0a0e0f',
                'iconColor' => '#0a0e0f ',
            ),
            'complete' => array(
                'color' => '#0a0e0f',
                'iconColor' => '#0a0e0f ',
            ),
        );
    }

    add_filter( 'wc_stripe_elements_styling', 'my_theme_modify_stripe_fields_styles' );



    // Return the post ID that a topic is linked to by having the same name
    function utopistlist_get_topic_post_id( $topic_id = '' ) {

            // Get topic name based on the passed topic_id
            $topic_title = get_the_title( $topic_id );

            // Get all posts
            $posts = new WP_Query( array(
            'post_type'   => 'post',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );

        // Loop through posts to find the one which title matches our topic title
        if ( $posts->have_posts() ) {
            foreach( $posts->posts as $post ) {
                if ( get_the_title( $post ) == $topic_title ) {
                    return $post;
                }
            }
        }
    }



    function myprefix_custom_reply_shortcode( $atts ) {

        // Parse your shortcode settings with it's defaults
        $atts = shortcode_atts( array(
        
            'posts_per_page' => '-1',
            'term'           => ''
        ), $atts, 'myprefix_custom_reply' );
    $user_id = userpro_get_view_user( get_query_var('up_username') );
        // Extract shortcode atributes
        extract( $atts );

        // Define output var
        $output = '';



        

        // Define query
        $query_args = array(
        'author'=> $user_id,
            'post_type'      => 'reply', // Change this to the type of post you want to show
            'posts_per_page' => $posts_per_page,
        );

        // Query by term if defined
        if ( $term ) {

            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'ID',
                    'terms'    => $term,
                    
                ),
            );

        }

        // Query posts
        $custom_query = new WP_Query( $query_args );

        // Add content if we found posts via our query
        if ( $custom_query->have_posts() ) {

            // Open div wrapper around loop
            $output .= '<div>';

            // Loop through posts
            while ( $custom_query->have_posts() ) {

                // Sets up post data so you can use functions like get_the_title(), get_permalink(), etc
                $custom_query->the_post();

                // This is the output for your entry so what you want to do for each post.
                


    $topic_id = get_post_meta( get_the_ID(), '_bbp_topic_id', true );
    $output .= '<div class="replytitle"><a href="' . esc_url( get_permalink( utopistlist_get_topic_post_id( $topic_id ) ) ) . '">' . get_the_title()  . '</a></div>';
    $output .= '<div>' . get_the_content() . '</div> <br />';

            }

            // Close div wrapper around loop
            $output .= '</div>';

            // Restore data
            wp_reset_postdata();

        }

        // Return your shortcode output
        return $output;

    }
    add_shortcode( 'myprefix_custom_reply', 'myprefix_custom_reply_shortcode' );

    function myprefix_custom_grid_shortcode( $atts, $post_id ) {

        // Parse your shortcode settings with it's defaults
        $atts = shortcode_atts( array(
        
            'posts_per_page' => '-1',
            'term'           => ''
        ), $atts, 'myprefix_custom_grid' );
    $user_id = userpro_get_view_user( get_query_var('up_username') );
        // Extract shortcode atributes
        extract( $atts );

        // Define output var
        $output = '';



        

        // Define query
        $query_args = array(
        'author'=> $user_id,
            'post_type'      => 'items', // Change this to the type of post you want to show
            'posts_per_page' => $posts_per_page,
        );

        // Query by term if defined
        if ( $term ) {

            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'ID',
                    'terms'    => $term,
                    
                ),
            );

        }

        // Query posts
        $custom_query = new WP_Query( $query_args );

        // Add content if we found posts via our query
        if ( $custom_query->have_posts() ) {

            // Open div wrapper around loop
            $output .= '<div>';

            // Loop through posts

            while ( $custom_query->have_posts() ) {
    $parent_post = get_the_ID();
                // Sets up post data so you can use functions like get_the_title(), get_permalink(), etc
                $custom_query->the_post();

                // This is the output for your entry so what you want to do for each post.
               // This is the output for your entry so what you want to do for each post.

    $output .= '<div class="sc-post">' . esc_html( get_the_title());
    $output .= ' <span class="sc-related"><br> ' . ( sc_get_related( get_the_ID())) . '</span></div><br>';
            }
            // Close div wrapper around loop
            
            $output .= '</div>';

            // Restore data
            wp_reset_postdata();

        }

        // Return your shortcode output
        return $output;

    }
    add_shortcode( 'myprefix_custom_grid', 'myprefix_custom_grid_shortcode' );



    /*

    add_action( 'init', function() {
        remove_action( 'wpex_hook_footer_before', 'wpex_footer_callout' );
    } );

    add_shortcode( 'footer_callout', function() {
        ob_start();
        if ( function_exists( 'wpex_footer_callout' ) ) {
            wpex_footer_callout();
        }
        return ob_get_clean();
    } );

    */



    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Register an Account' :
                $translated_text = __( 'Register an Account (scroll down)', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );

    add_action( 'wpex_hook_footer_bottom_before', 'wpex_top_bar' );

    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Popular post' :
                $translated_text = __( 'Likes', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );

    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'None' :
                $translated_text = __( 'Updated', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );

    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'You will be able to submit the ad content after the purchase.' :
                $translated_text = __( 'Manage the content of your AD space after the purchase.', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );


    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'submit this ad' :
                $translated_text = __( 'Submit my AD', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );



    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Place order' :
                $translated_text = __( 'Rent this ad space', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );

    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'User is online :)' :
                $translated_text = __( 'User is online', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );

    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'User is offline :(' :
                $translated_text = __( 'User is offline', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );


    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'This ad is currently in review.' :
                $translated_text = __( 'Your AD is currently in review.[br][br]Once it is approved, it will be published and you will be notified by email.', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );


    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Mycred Rank' :
                $translated_text = __( 'Utopist Rank', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );


    /*
     * Filter for bbpress reply post_title, without "Reply To: " prefix
     * - reference: https://bbpress.trac.wordpress.org/browser/trunk/src/includes/replies/template.php
     */
    /*

    function custom_bbp_get_reply_title_fallback( $post_title = '', $post_id = 0 ){
      // Bail if title not empty, or post is not a reply
      if ( ! empty( $post_title ) || ! bbp_is_reply( $post_id ) ) {
        return $post_title;
      }
      
      // Get reply topic title.
      $topic_title = bbp_get_reply_topic_title( $post_id );
      // Get empty reply title fallback.
      $reply_title = sprintf( __( '%s', 'bbpress' ), $topic_title );  
      
      return apply_filters( 'bbp_get_reply_title_fallback', $reply_title, $post_id, $topic_title );  
    }
    // add custom filter
    add_filter( 'the_title', 'custom_bbp_get_reply_title_fallback', 2, 2);
    // remove bbpress default filter
    remove_filter( 'the_title', 'bbp_get_reply_title_fallback', 2);

    */

    function myprefix_entry_single_meta_sections( $sections ) {
        $sections['date'] = function() {
            $icon = '<span class="ticon ticon-clock-o" aria-hidden="true"></span>';
            echo  $icon . 'Updated on ' . esc_html( get_the_modified_date( get_option( 'date_format' ) ) );
        };
        return $sections;
    }
    add_filter( 'wpex_blog_entry_meta_sections', 'myprefix_entry_single_meta_sections' );
    add_filter( 'wpex_blog_single_meta_sections', 'myprefix_entry_single_meta_sections' );

    // Enable fixed footer on products
    add_filter( 'body_class', function( $class) {

        if ( is_singular( 'product' ) ) {
            $class[] = 'wpex-has-fixed-footer';
        }

        return $class;
    } );

    // Enable fixed footer on products
    add_filter( 'body_class', function( $class) {

        if(get_query_var('pagename')==='checkout') {
            $class[] = 'wpex-has-fixed-footer';
        }

        return $class;
    } );

    // Enable fixed footer on products
    add_filter( 'body_class', function( $class) {

        if( is_page( 7178 ) ) {
            $class[] = 'wpex-has-fixed-footer';
        }

        return $class;
    } );


    // Enable fixed footer on products
    add_filter( 'body_class', function( $class) {

        if( is_page( 8631 ) ) {
            $class[] = 'wpex-has-fixed-footer';
        }

        return $class;
    } );


    add_filter( 'body_class', function( $classes ) {
            if ( is_search() ) {
                    $classes[] = 'wpex-has-fixed-footer';
            }
            return $classes;
    } );


    /**
     * Remove related products output
     */
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    // Disable WordPress Lazy Load
    add_filter( 'wp_lazy_loading_enabled', '__return_false' );



    function myprefix_search_filter( $query ) {
        if ( ! is_admin() && $query->is_main_query() ) {
            if ( $query->is_search ) {
                $query->set('post_type', 'post');
            }
        }
    }
    add_action( 'pre_get_posts','myprefix_search_filter' );





    add_action( 'wpex_hook_page_header_inner', function() {

        if ( function_exists( 'wpex_page_header_style' )
            && function_exists( 'wpex_page_header_background_image' )
            && 'background-image' == wpex_page_header_style() ) {

            $bg_image = wpex_page_header_background_image();

            if ( $bg_image ) {
                 echo '<div class="utopistlist-page-header-bg"><img src="' . esc_url( $bg_image ) . '" /></div>';
            }

        }

    } );




    add_filter(
        'wp_grid_builder/blocks',
        function( $blocks ) {

            

            $blocks['date_block'] = [
                'name'            => 'Date Block',
                'render_callback' => function() {

                    $date = get_post_timestamp( wpgb_get_the_id() );
                    $diff = (int) abs( time() - $date );

                    if ( $diff < DAY_IN_SECONDS ) {
                        echo '<div class="todaylabel">Today</div>';
                    } elseif ( $diff < DAY_IN_SECONDS * 2 ) {
                        echo '<div class="yesterdaylabel">Yesterday</div>';
                    }
                },
            ];

            return $blocks;

           

        }
    );




    function prefix_query_args( $query_args, $grid_id ) {

        
        if ( 2 === $grid_id ) {

    global $post;

    $referer = wp_get_referer();
    $post_id = wp_doing_ajax() ? url_to_postid( $referer ) : $post->ID;


            $terms = wp_get_post_terms( $post->ID, 'relations' );
            $query_args['tax_query'] = array(
            array(
                'taxonomy'  => 'relations',
                'field'     => 'term_id',
                'terms'     =>  $terms[0],
            ),
        );
        $query_args['post_type'] = 'items';
        
        }

        return $query_args;

    }

    add_filter( 'wp_grid_builder/grid/query_args', 'prefix_query_args', 10, 2 );










```php

    add_filter(
        'wp_grid_builder/grid/query_args',
        function( $query_args, $grid_id ) {

            global $post;

            if ( 3 === $grid_id ) {

                $referer = wp_get_referer();
                $post_id = wp_doing_ajax() ? url_to_postid( $referer ) : $post->ID;
                $related = get_post_meta( $post_id, 'related_post_ids', true );

                if ( ! empty( $related ) ) {

                    $query_args['post__in'] = explode( ',', $related );
                    unset( $query_args['category__in'] );

                }
            }

            return $query_args;

        },
        10,
        2
    );


```









    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'reason</span>';
            $replace_with = '<img class="brain_svg" src="https://utopistlist.com/brain.svg" /> reason';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );


    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'food</span>';
            $replace_with = '<img class="food_svg" src="https://utopistlist.com/food.svg" /> food';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );


    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'technology</span>';
            $replace_with = '<img class="tech_svg" src="https://utopistlist.com/tech.svg" /> technology';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );



    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'vagabond</span>';
            $replace_with = '<img class="vagabond_svg" src="https://utopistlist.com/vagabond.svg" /> vagabond';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );


    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'people</span>';
            $replace_with = '<img class="people_svg" src="https://utopistlist.com/people.svg" /> people';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );


    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'spiritual</span>';
            $replace_with = '<img class="spiritual_svg" src="https://utopistlist.com/spiritual.svg" /> spiritual';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );



    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'psyche</span>';
            $replace_with = '<img class="psyche_svg" src="https://utopistlist.com/psyche.svg" /> psyche';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );


    add_shortcode( 'utopistlist_post_likes', function() {
        if ( function_exists( 'wpex_get_current_post_id' ) ) {
            $post_id = wpex_get_current_post_id();
        } else {
            $post_id = get_queried_object_id();
        }
        return do_shortcode('[wp_ulike for="post" id="'. $post_id .'"]');
    } );



    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'The Arts</span>';
            $replace_with = '<img class="arts_svg" src="https://utopistlist.com/arts.svg" /> The Arts';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );



    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 1 === (int) $facet_id ) {
            
            $string_to_replace = 'political</span>';
            $replace_with = '<img class="arts_svg" src="https://utopistlist.com/political.svg" /> political';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );



    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 7 === (int) $facet_id ) {
            
            $string_to_replace = 'essential general knowledge</span>';
            $replace_with = '<img class="general_svg" src="https://utopistlist.com/general.svg" /> essential general knowledge';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );


    add_shortcode( 'utopistlist_items_likes', function() {
       
            $post_id = wpgb_get_the_id();
        return do_shortcode('[wp_ulike for="items" id="'. $post_id .'"]');
    } );











    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 7 === (int) $facet_id ) {
            
            $string_to_replace = 'peculiar niche</span>';
            $replace_with = '<img class="general_svg" src="https://utopistlist.com/niche.svg" /> peculiar niche';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );



    add_filter('wp_ulike_format_number','wp_ulike_new_format_number',10,3);
    function wp_ulike_new_format_number($value, $num, $plus){
        if ($num >= 1000 && get_option('wp_ulike_format_number') == '1'):
        $value = round($num/1000, 2) . 'K';
        else:
        $value = $num;
        endif;
        return $value;
    }


    add_shortcode( 'subscribe-to-list', function() {
       global $wpdb, $post;
       $id = $wpdb->get_var("SELECT ID FROM wptq_mailster_forms WHERE name = '{$post->post_title}';");
       return do_shortcode( '[newsletter_signup_form id="' . intval( $id ) . '"]' );
    } );


    add_action( 'wp_head', 'add_viewport_meta_tag' , '1' );

    function add_viewport_meta_tag() {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
    }


    /**
     * Changes the redirect URL for the Return To Shop button in the cart.
     *
     * @return string
     */
    function wc_empty_cart_redirect_url() {
        return 'https://utopistlist.com';
    }
    add_filter( 'woocommerce_return_to_shop_redirect', 'wc_empty_cart_redirect_url' );





    add_action( 'set_object_terms', 'bcw_set_date', 10, 6 );
    function bcw_set_date( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
        $item = get_post( $object_id );
        if ('items' == $item->post_type && 'relations' == $taxonomy && 0 < count( $tt_ids)){
            // Form an array of data to be updated
            $latest_cpt = get_posts([
                'posts_per_page'=> 1,
                'post_type'=>'post',
                'tax_query'=>[[
                   'taxonomy'=>'relations',
                   'field'=>'term_taxonomy_id',
                   'terms'=> $tt_ids[0],
                ],],
            ]);
            // Get the current time
            $time = current_time('mysql');
            // Form an array of data to be updated
            $post_data = array(
                'ID'           => $latest_cpt[0]->ID, 
                'post_modified'   => $time,
                'post_modified_gmt' =>  get_gmt_from_date( $time )
            );
            // Update the latest post with the same term
            wp_update_post( $post_data );
        }
    }





    add_shortcode( 'subscribe-to-cat1', function() {
       global $wpdb, $post;
     
    $the_cat = get_the_category( $post->ID );
    $category_name = $the_cat[0]->cat_name;
       $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM wptq_mailster_forms WHERE name = '{$category_name}';"));
       if (is_null($id)) { return ''; }
       return do_shortcode( '[newsletter_signup_form id="' . intval( $id ) . '"]' );
    } );




    add_shortcode( 'subscribe-to-cat2', function() {
       global $wpdb, $post;
     
    $the_cat = get_the_category( $post->ID );
    $category_name = $the_cat[1]->cat_name;
       $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM wptq_mailster_forms WHERE name = '{$category_name}';"));
       if (is_null($id)) { return ''; }
       return do_shortcode( '[newsletter_signup_form id="' . intval( $id ) . '"]' );
    } );




    add_shortcode( 'subscribe-to-cat3', function() {
       global $wpdb, $post;
     
    $the_cat = get_the_category( $post->ID );
    $category_name = $the_cat[2]->cat_name;
       $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM wptq_mailster_forms WHERE name = '{$category_name}';"));
       if (is_null($id)) { return ''; }
       return do_shortcode( '[newsletter_signup_form id="' . intval( $id ) . '"]' );
    } );





    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Sorry, no results match your search criteria.' :
                $translated_text = __( 'No lists were found.', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );



    add_action( 'get_the_modified_date', 'my_project_filter_publish_dates', 10, 3 );
     
    function my_project_filter_publish_dates( $the_modified_date, $d, $post ) {
        if ( is_int( $post) ) {
            $post_id = $post;
        } else {
            $post_id = $post->ID;
        }
     
        if ( 'post' != get_post_type( $post_id ) )
            return $the_modified_date;
     
        return date( 'F j, Y', strtotime( $the_modified_date ) );
    }



    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Sorry, no content found.' :
                $translated_text = __( 'Sorry, no list items found.', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );



    /*


    function my_exclude_next_prev_terms( $excluded_terms ) {

        if ( is_singular( 'post' ) ) {

       

            // Get current post categories.
            $post_categories = get_the_terms( get_the_ID(), 'category' );
            $post_categories = wp_list_pluck( $post_categories, 'term_id' ); // get ID's only
            
            // If there are more then 1 category assined to the post, exclude all other terms
            if ( $post_categories && ! is_wp_error( $post_categories ) && count( $post_categories ) > 1 ) {

                // Define new excluded terms array.
                $excluded_terms = array();

                // Get all categories
                $all_categories = get_terms( array(
                        'taxonomy'   => 'category',
                        'hide_empty' => false,
                    ) );
                    $all_categories = wp_list_pluck( $all_categories, 'term_id' ); // get ID's only

                // Loop through all categories and if they aren't part of the current post add them to the excluded list.
                foreach ( $all_categories as $category ) {

                    // Category isn't a part of the current post so add it to the excluded terms.
                    if ( ! in_array( $category, $post_categories ) ) {
                        $excluded_terms[] = $category;
                    }


                }

            }

        }

        return $excluded_terms;
    }
    add_filter( 'get_previous_post_excluded_terms', 'my_exclude_next_prev_terms' );
    add_filter( 'get_next_post_excluded_terms', 'my_exclude_next_prev_terms' ); 

    */


    add_filter( 'advanced-ads-selling-upload-file-size', function(){
       return 10000000;
    } );



    add_shortcode( 'in-same-term-prev-next-post', 'in_same_term_prev_next_post' );
    function in_same_term_prev_next_post() {

    $post_object = get_queried_object();
    $terms = wp_get_post_terms( $post_object->ID, 'category', array( 'fields' => 'ids' ) );


    $next_args = array(

        'post_type' => 'post',
        'ignore_sticky_posts'=>'false',
        'posts_per_page' => 1,
        'order' => 'ASC',
        'category__and' => $terms,
        'date_query' => array(
            'operator'  => 'AND',
            array(
                'after' => $post_object->post_date,  // Get the post after the current post, use current post post_date
                'inclusive' => false, // Don't include the current post in the query
            )
        ) 
    );

    $prev_args = array(

        'post_type' => 'post',
        'ignore_sticky_posts'=>'false',
        'posts_per_page' => 1,
        'order' => 'DESC',
        'category__and' => $terms,
        'date_query' => array(
            'operator'  => 'AND',
            array(
                'before' => $post_object->post_date,  // Get the post after the current post, use current post post_date
                'inclusive' => false, // Don't include the current post in the query
            )
        ) 
    );



    $nextpost = new WP_Query( $next_args );
    $prevpost = new WP_Query( $prev_args );


    /*
    echo '<li><a href="'.get_permalink( $nextpost->posts ).'">'.get_the_title( $nextpost->posts ).'</a></li>';

    */
    $output = '<div class="vcex-post-next-prev textright categorynavigation" style="font-size:1.4em;">';

    if ( ! empty( $nextpost->posts )) :

    $output .= '<div class="vcex-col wpex-inline-block wpex-mr-0">
        <a href="'. esc_url( get_permalink( $nextpost->posts[0])) .
       '" class="theme-button graphical blue wpex-text-center wpex-max-w-100">

            <span class="ticon ticon-chevron-left wpex-mr-10">
            </span>

              ' .get_the_title( $nextpost->posts[0]) . '

      </a>
      </div>';
      
    endif;


    if ( ! empty( $prevpost->posts )) :
    $output .=  '<div class="vcex-col wpex-inline-block wpex-ml-0">
        <a href="'. esc_url( get_permalink( $prevpost->posts[0])) .
       '" class="theme-button graphical blue wpex-text-center wpex-max-w-100">

               ' .get_the_title( $prevpost->posts[0]) . '
      

            <span class="ticon ticon-chevron-right wpex-ml-10">
                
            </span>
            </a>
            </div>';

        endif;

    $output .= '</div>';



    return $output;


    }



    add_shortcode( 'subscribe-to-tag', function() {
       global $wpdb, $post;
     
    $the_tag = get_the_tags( $post->ID );
    $tag_name = $the_tag[0]->name;
       $id = $wpdb->get_var($wpdb->prepare( "SELECT ID FROM wptq_mailster_forms WHERE name = %s", $tag_name ));
       if (is_null($id)) { return ''; }
       return do_shortcode( '[newsletter_signup_form id="' . intval( $id ) . '"]' );
    } ); 


    /**
     * Registers an editor stylesheet for the theme.
     */
    function wpdocs_theme_add_editor_styles() {
        add_editor_style( 'https://utopistlist.com/custom-editor-style.css' );
    }
    add_action( 'admin_init', 'wpdocs_theme_add_editor_styles' );



    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Subscribe to Politics' :
                $translated_text = __( 'Subscribe to <span class="cat_style_button">Politics</span>', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );


    function change_button( $translation, $text ) {

        if ( $text == 'Subscribe to new lists in - Politics' )     // Your button text
            $text = 'Subscribe to new lists in - <span class="cat_style_button">Politics</span>';

        return $text;
    }

    add_filter( 'gettext', 'change_button', 10, 2 );





    add_filter( 'wp_grid_builder/facet/html', function( $html, $facet_id ) {

        if ( 7 === (int) $facet_id ) {
            
            $string_to_replace = 'peculiar niche</span>';
            $replace_with = '<img class="general_svg" src="https://utopistlist.com/niche.svg" /> peculiar niche';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;

    }, 10, 2 );




    add_filter( 'mailster_text', function( $string, $option, $fallback ) {

        switch ( $option ) {
            case '<input name="submit" type="submit" value="Subscribe to new lists in - Politics" class="submit-button button" aria-label="Subscribe to new lists in - Politics">':
                $string = '<input name="submit" type="submit" value="Subscribe to new lists in - <span class="cat_style_button">Politics</span>" class="submit-button button" aria-label="Subscribe to new lists in - <span class="cat_style_button">Politics</span>">';
            break;
        }

        return $string;

    }, 10, 3 );


    add_filter( 'mailster_form', function( $html, $form_id, $form ) {

    if ( 6 === (int) $form_id ) {

    $string_to_replace = '<input name="submit" type="submit" value="Subscribe to new lists in - Politics" class="submit-button button" aria-label="Subscribe to new lists in - Politics">';
            $replace_with = '<input name="submit" type="submit" value="Subscribe to new lists in" class="submit-button button cat_style_button" aria-label="Subscribe to new lists in"><input name="submit" type="submit" value="Politics" class="submit-button button cat_style_button" aria-label="Politics">';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;



    }, 10, 3);


    add_filter( 'mailster_form', function( $html, $form_id, $form ) {

    if ( 8 === (int) $form_id ) {

    $string_to_replace = '<input name="submit" type="submit" value="Subscribe to new lists in Peculiar Niche" class="submit-button button" aria-label="Subscribe to new lists in Peculiar Niche">';
            $replace_with = '<input name="submit" type="submit" value="Subscribe to new lists in" class="submit-button button cat_style_button" aria-label="Subscribe to new lists in"><input name="submit" type="submit" value="Peculiar Niche" class="submit-button button cat_style_button" aria-label="Peculiar Niche">';

            $html = str_replace( $string_to_replace, $replace_with, $html );
        }

        return $html;



    }, 10, 3);


    // change post link to display published posts only
    function wcs_change_admin_post_link() {
        global $submenu;
        $submenu['edit.php'][5][2] = 'edit.php?post_status=publish';
    }
    add_action( 'admin_menu', 'wcs_change_admin_post_link' );



    // change page link to display published items only
    function wcs_change_admin_page_link() {
        global $submenu;
        $submenu['edit.php?post_type=items'][5][2] = 'edit.php?post_type=items&post_status=publish';
    }
    add_action( 'admin_menu', 'wcs_change_admin_page_link' );



    /**
     * Search SQL filter for matching against post title only.
     *
     * @link    http://wordpress.stackexchange.com/a/11826/1685
     *
     * @param   string      $search
     * @param   WP_Query    $wp_query
     */
    function wpse_11826_search_by_title( $search, $wp_query ) {
        if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
            global $wpdb;

            $q = $wp_query->query_vars;
            $n = ! empty( $q['exact'] ) ? '' : '%';

            $search = array();

            foreach ( ( array ) $q['search_terms'] as $term )
                $search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );

            if ( ! is_user_logged_in() )
                $search[] = "$wpdb->posts.post_password = ''";

            $search = ' AND ' . implode( ' AND ', $search );
        }

        return $search;
    }

    add_filter( 'posts_search', 'wpse_11826_search_by_title', 10, 2 );











    /*
    // show all postmeta on the frontent for all posts

    add_action('wp_head', 'output_all_postmeta' );
    function output_all_postmeta() {

        $postmetas = get_post_meta(get_the_ID());

        foreach($postmetas as $meta_key=>$meta_value) {
            echo $meta_key . ' : ' . $meta_value[0] . '<br/>';
        }
    }
    */


    // Insert custom content above blog posts.
    add_action( 'wpex_hook_content_top', function() {

        if ( ! is_singular( 'post' ) ) {
            return; //insert on blog posts only.
        }

        // Get current post id.
        $post_id = function_exists( 'wpex_get_current_post_id' ) ? wpex_get_current_post_id() : get_the_ID();

        // Check custom field for custom content.
        if ( $meta = get_post_meta( $post_id, 'post_intro_text', true ) ) {
            echo '<div class="parent_div"><div class="erikalleman-post-top-content">' . wp_kses_post( $meta ) . '</div></div>';
        }

    } );




    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case '%s&quot; added to your order. Complete your order below.' :
                $translated_text = __( '%s&quot; added to your order.</br></br>Complete your order below.', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );



    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case '%s&quot; added to your order. Complete your order below.' :
                $translated_text = __( '%s&quot; added to your order.</br></br>Complete your order below.', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );



    add_filter( 'woocommerce_thankyou_order_received_text', function( $text ) {

        return 'Thank you.   Your order has been received.';

    } );



    add_filter( 'wpex_header_logo_output', function( $html ) {

            $string_to_replace = 'Utopist List';
            $replace_with = 'Utopist <span class="list_class">List</span>';

            $html = str_replace( $string_to_replace, $replace_with, $html );

        return $html;

    }, 10, 1 );



    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Billing The title of your AD' :
                $translated_text = __( 'The title of your AD', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );






    function customize_wc_errors( $error ) {
        if ( strpos( $error, 'Billing ' ) !== false ) {
            $error = str_replace("Billing ", "", $error);
        }
        return $error;
    }
    add_filter( 'woocommerce_add_error', 'customize_wc_errors' );






    add_filter( 'mailster_form', function( $html ) {

    $string_to_replace = '<input name="submit" type="submit" value="Notify me about new lists" class="submit-button button" aria-label="Notify me about new lists">';
            $replace_with = '<button name="submit" type="submit" value="Notify me about new lists" class="submit-button button" aria-label="Notify me about new lists">Notify me about new lists';

            $html = str_replace( $string_to_replace, $replace_with, $html );

        return $html;

    }, 10, 1);





    add_filter( 'mailster_strip_shortcode_tags', function( $shortcode_tags ){
        $shortcode_tags[] = 'br';

        return shortcode_tags;
    });



    /*

    add_filter( 'mailster_form', function( $html ) {

    $string_to_replace = "Notify me about new entries";
            $replace_with = "Notify me about<br /> New Entries";

            $html = str_replace( $string_to_replace, $replace_with, $html );

    return $html;


    }, 10, 1);



    add_filter( 'mailster_form', function( $html ) {

    $string_to_replace = 'Subscribe to Peculiar Niche';
            $replace_with = 'Subscribe to' . 'Peculiar Niche';

            $html = str_replace( $string_to_replace, $replace_with, $html );

    return $html;


    }, 10, 1);


    */

    function remove_query_strings() {
       if(!is_admin()) {
           add_filter('script_loader_src', 'remove_query_strings_split', 15);
           add_filter('style_loader_src', 'remove_query_strings_split', 15);
       }
    }

    function remove_query_strings_split($src){
       $output = preg_split("/(&ver|\?ver)/", $src);
       return $output[0];
    }
    add_action('init', 'remove_query_strings');




    add_filter( 'gettext', function( $translated_text, $text, $domain ) {
        switch ( $translated_text ) {
            case 'Credit or debit card' :
                $translated_text = __( 'Credit or debit card. Secure payment via Stripe.', 'total' );
                break;
        }
        return $translated_text;
    }, 20, 3 );



    add_action( 'mailster_initsend', function($mailobject){

        $mailobject->add_header( 'List-Unsubscribe:', '<https://example.com/mailster/unsubscribe>' );

    } );



    // Change the page header style for posts.
    add_filter( 'wpex_page_header_style', function( $style ) {
        if ( is_singular( 'post' ) ) {
            $style = 'background-image';
        }
        return $style;
    } );
