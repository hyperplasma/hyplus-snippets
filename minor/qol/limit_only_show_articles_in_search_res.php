<?php
/**
 * Only show articles (literal "posts") in the results page PHP
 */
function ScanWPostFilter($query) {
    if ($query->is_search) {
        $query->set('post_type', 'post');
    }
    return $query;
}
add_filter('pre_get_posts','ScanWPostFilter');