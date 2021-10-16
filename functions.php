```
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
```
