<?php
/**
 * Helper functions for the child theme
 *
 * @package Child_Theme
 */

/**
 * Get theme option from ACF options page
 *
 * @param string $option_name The option name
 * @param mixed $default Default value if option doesn't exist
 * @return mixed
 */
function child_theme_get_option($option_name, $default = null) {
    if (function_exists('get_field')) {
        $value = get_field($option_name, 'option');
        return $value !== null ? $value : $default;
    }
    return $default;
}

/**
 * Get featured image URL, with fallback
 *
 * @param int $post_id Post ID
 * @param string $size Image size
 * @param string $fallback_url Fallback image URL
 * @return string
 */
function child_theme_get_featured_image_url($post_id = null, $size = 'large', $fallback_url = '') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    if (has_post_thumbnail($post_id)) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
        return $image[0];
    }
    
    return $fallback_url;
}

/**
 * Limit string to specific length and add ellipsis
 *
 * @param string $string String to limit
 * @param int $limit Character limit
 * @param string $ellipsis String to append
 * @return string
 */
function child_theme_limit_string($string, $limit = 150, $ellipsis = '...') {
    if (mb_strlen($string) <= $limit) {
        return $string;
    }
    
    return mb_substr($string, 0, $limit) . $ellipsis;
}

/**
 * Print SVG from theme assets
 *
 * @param string $filename SVG filename (without extension)
 * @param string $directory Directory within assets
 * @param array $args Additional arguments
 * @return void
 */
function child_theme_svg($filename, $directory = 'icons', $args = []) {
    $defaults = [
        'class' => '',
        'width' => null,
        'height' => null,
        'title' => '',
    ];
    
    $args = wp_parse_args($args, $defaults);
    $file_path = CHILD_THEME_DIR . '/assets/' . $directory . '/' . $filename . '.svg';
    
    if (!file_exists($file_path)) {
        return;
    }
    
    $svg_content = file_get_contents($file_path);
    
    // Add class if specified
    if (!empty($args['class'])) {
        $svg_content = preg_replace('/^<svg /', '<svg class="' . esc_attr($args['class']) . '" ', $svg_content);
    }
    
    // Add width if specified
    if (!empty($args['width'])) {
        $svg_content = preg_replace('/^<svg ([^>]*)width="[^"]*"/', '<svg $1', $svg_content);
        $svg_content = preg_replace('/^<svg /', '<svg width="' . esc_attr($args['width']) . '" ', $svg_content);
    }
    
    // Add height if specified
    if (!empty($args['height'])) {
        $svg_content = preg_replace('/^<svg ([^>]*)height="[^"]*"/', '<svg $1', $svg_content);
        $svg_content = preg_replace('/^<svg /', '<svg height="' . esc_attr($args['height']) . '" ', $svg_content);
    }
    
    // Add title for accessibility
    if (!empty($args['title'])) {
        $title_tag = '<title>' . esc_html($args['title']) . '</title>';
        $svg_content = preg_replace('/^<svg ([^>]*)>/', '<svg $1>' . $title_tag, $svg_content);
    }
    
    echo $svg_content;
}

/**
 * Get social media links as array
 *
 * @return array
 */
function child_theme_get_social_links() {
    $social_links = [];
    
    // Default social platforms
    $platforms = [
        'facebook' => [
            'name' => 'Facebook',
            'icon' => 'facebook',
        ],
        'twitter' => [
            'name' => 'Twitter',
            'icon' => 'twitter',
        ],
        'instagram' => [
            'name' => 'Instagram',
            'icon' => 'instagram',
        ],
        'linkedin' => [
            'name' => 'LinkedIn',
            'icon' => 'linkedin',
        ],
        'youtube' => [
            'name' => 'YouTube',
            'icon' => 'youtube',
        ],
    ];
    
    // If ACF is active, get social URLs from options page
    if (function_exists('get_field')) {
        foreach ($platforms as $key => $platform) {
            $url = get_field($key . '_url', 'option');
            
            if ($url) {
                $social_links[$key] = [
                    'name' => $platform['name'],
                    'url' => $url,
                    'icon' => $platform['icon'],
                ];
            }
        }
    } else {
        // Fallback to WordPress customizer or theme mods
        foreach ($platforms as $key => $platform) {
            $url = get_theme_mod('social_' . $key, '');
            
            if ($url) {
                $social_links[$key] = [
                    'name' => $platform['name'],
                    'url' => $url,
                    'icon' => $platform['icon'],
                ];
            }
        }
    }
    
    return $social_links;
}

/**
 * Display social links
 *
 * @param array $args Display arguments
 * @return void
 */
function child_theme_social_links($args = []) {
    $defaults = [
        'wrapper_class' => 'social-links',
        'link_class' => 'social-link',
        'show_labels' => false,
        'target' => '_blank',
    ];
    
    $args = wp_parse_args($args, $defaults);
    $social_links = child_theme_get_social_links();
    
    if (empty($social_links)) {
        return;
    }
    
    echo '<div class="' . esc_attr($args['wrapper_class']) . '">';
    
    foreach ($social_links as $key => $network) {
        echo '<a href="' . esc_url($network['url']) . '" class="' . esc_attr($args['link_class']) . ' ' . esc_attr($key) . '" target="' . esc_attr($args['target']) . '" rel="noopener noreferrer">';
        
        // Display icon using SVG helper
        child_theme_svg($network['icon'], 'social', [
            'class' => 'icon icon-' . $key,
            'title' => $network['name'],
        ]);
        
        // Display label if enabled
        if ($args['show_labels']) {
            echo '<span class="label">' . esc_html($network['name']) . '</span>';
        } else {
            echo '<span class="screen-reader-text">' . esc_html($network['name']) . '</span>';
        }
        
        echo '</a>';
    }
    
    echo '</div>';
}

/**
 * Get ACF image with responsive srcset
 *
 * @param string|array $field ACF field name or array
 * @param string|int $post_id Post ID
 * @param string $size Image size
 * @param array $attr Image attributes
 * @return string
 */
function child_theme_acf_image($field, $post_id = '', $size = 'medium_large', $attr = []) {
    if (!function_exists('get_field')) {
        return '';
    }
    
    // Get image ID
    $image_id = null;
    
    if (is_array($field)) {
        $image_id = $field['ID'] ?? 0;
    } else {
        $image = get_field($field, $post_id);
        if ($image) {
            $image_id = is_array($image) ? ($image['ID'] ?? 0) : $image;
        }
    }
    
    if (!$image_id) {
        return '';
    }
    
    // Generate image with srcset
    return wp_get_attachment_image($image_id, $size, false, $attr);
}

/**
 * Get related posts based on taxonomy
 *
 * @param int $post_id Post ID
 * @param string $taxonomy Taxonomy name
 * @param int $limit Number of posts
 * @return array
 */
function child_theme_get_related_posts($post_id = null, $taxonomy = 'category', $limit = 3) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Get terms for the post
    $terms = get_the_terms($post_id, $taxonomy);
    
    if (!$terms || is_wp_error($terms)) {
        return [];
    }
    
    // Get term IDs
    $term_ids = wp_list_pluck($terms, 'term_id');
    
    // Get related posts
    $args = [
        'post_type' => get_post_type($post_id),
        'posts_per_page' => $limit,
        'post__not_in' => [$post_id],
        'tax_query' => [
            [
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term_ids,
            ],
        ],
    ];
    
    $query = new WP_Query($args);
    
    return $query->posts;
}