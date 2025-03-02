<?php
/**
 * ACF Fields Setup
 *
 * Registers custom fields for the theme using ACF
 *
 * @package Child_Theme
 */

defined('ABSPATH') || exit;

// Only run this code if ACF is active
if (!class_exists('ACF')) {
    return;
}

/**
 * ACF Fields Setup Class
 */
class Child_Theme_ACF_Fields {
    /**
     * Instance of this class
     *
     * @var Child_Theme_ACF_Fields
     */
    private static $instance = null;

    /**
     * Constructor
     */
    private function __construct() {
        add_action('acf/init', [$this, 'register_field_groups']);
        add_filter('acf/settings/load_json', [$this, 'add_acf_json_load_point']);
        add_filter('acf/settings/save_json', [$this, 'set_acf_json_save_point']);
    }

    /**
     * Get instance of this class
     *
     * @return Child_Theme_ACF_Fields
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register ACF field groups
     */
    public function register_field_groups() {
        // Only register fields via PHP if we need to
        // Most fields will be defined in JSON files

        // Theme Options Page Fields
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group([
                'key' => 'group_theme_header_settings',
                'title' => 'Header Settings',
                'fields' => [
                    [
                        'key' => 'field_header_layout',
                        'label' => 'Header Layout',
                        'name' => 'header_layout',
                        'type' => 'select',
                        'instructions' => 'Select header layout style',
                        'required' => 0,
                        'choices' => [
                            'default' => 'Default',
                            'centered' => 'Centered',
                            'transparent' => 'Transparent',
                        ],
                        'default_value' => 'default',
                    ],
                    [
                        'key' => 'field_sticky_header',
                        'label' => 'Sticky Header',
                        'name' => 'sticky_header',
                        'type' => 'true_false',
                        'instructions' => 'Enable sticky header',
                        'required' => 0,
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_transparent_header',
                        'label' => 'Transparent Header',
                        'name' => 'transparent_header',
                        'type' => 'true_false',
                        'instructions' => 'Enable transparent header on selected pages',
                        'required' => 0,
                        'default_value' => 0,
                        'ui' => 1,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_header_layout',
                                    'operator' => '==',
                                    'value' => 'transparent',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_header_cta_button',
                        'label' => 'Header CTA Button',
                        'name' => 'header_cta_button',
                        'type' => 'true_false',
                        'instructions' => 'Show CTA button in header',
                        'required' => 0,
                        'default_value' => 0,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_header_cta_text',
                        'label' => 'Button Text',
                        'name' => 'header_cta_text',
                        'type' => 'text',
                        'instructions' => 'CTA button text',
                        'required' => 0,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_header_cta_button',
                                    'operator' => '==',
                                    'value' => 1,
                                ],
                            ],
                        ],
                    ],
                    [
                        'key' => 'field_header_cta_link',
                        'label' => 'Button Link',
                        'name' => 'header_cta_link',
                        'type' => 'link',
                        'instructions' => 'CTA button link',
                        'required' => 0,
                        'conditional_logic' => [
                            [
                                [
                                    'field' => 'field_header_cta_button',
                                    'operator' => '==',
                                    'value' => 1,
                                ],
                            ],
                        ],
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-header',
                        ],
                    ],
                ],
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
            ]);

            acf_add_local_field_group([
                'key' => 'group_theme_footer_settings',
                'title' => 'Footer Settings',
                'fields' => [
                    [
                        'key' => 'field_footer_layout',
                        'label' => 'Footer Layout',
                        'name' => 'footer_layout',
                        'type' => 'select',
                        'instructions' => 'Select footer layout style',
                        'required' => 0,
                        'choices' => [
                            'default' => 'Default',
                            'minimal' => 'Minimal',
                            'dark' => 'Dark',
                            'light' => 'Light',
                        ],
                        'default_value' => 'default',
                    ],
                    [
                        'key' => 'field_footer_widgets',
                        'label' => 'Footer Widgets',
                        'name' => 'footer_widgets',
                        'type' => 'true_false',
                        'instructions' => 'Show widgets in footer',
                        'required' => 0,
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_footer_social',
                        'label' => 'Social Icons',
                        'name' => 'footer_social',
                        'type' => 'true_false',
                        'instructions' => 'Show social icons in footer',
                        'required' => 0,
                        'default_value' => 1,
                        'ui' => 1,
                    ],
                    [
                        'key' => 'field_footer_copyright',
                        'label' => 'Copyright Text',
                        'name' => 'footer_copyright',
                        'type' => 'wysiwyg',
                        'instructions' => 'Footer copyright text. Use [year] for dynamic year.',
                        'required' => 0,
                        'default_value' => '© [year] Your Company. All rights reserved.',
                        'tabs' => 'visual',
                        'media_upload' => 0,
                        'toolbar' => 'basic',
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-footer',
                        ],
                    ],
                ],
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
            ]);

            acf_add_local_field_group([
                'key' => 'group_theme_social_media',
                'title' => 'Social Media',
                'fields' => [
                    [
                        'key' => 'field_facebook_url',
                        'label' => 'Facebook URL',
                        'name' => 'facebook_url',
                        'type' => 'url',
                        'instructions' => 'Enter full URL including https://',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_twitter_url',
                        'label' => 'Twitter URL',
                        'name' => 'twitter_url',
                        'type' => 'url',
                        'instructions' => 'Enter full URL including https://',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_instagram_url',
                        'label' => 'Instagram URL',
                        'name' => 'instagram_url',
                        'type' => 'url',
                        'instructions' => 'Enter full URL including https://',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_linkedin_url',
                        'label' => 'LinkedIn URL',
                        'name' => 'linkedin_url',
                        'type' => 'url',
                        'instructions' => 'Enter full URL including https://',
                        'required' => 0,
                    ],
                    [
                        'key' => 'field_youtube_url',
                        'label' => 'YouTube URL',
                        'name' => 'youtube_url',
                        'type' => 'url',
                        'instructions' => 'Enter full URL including https://',
                        'required' => 0,
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-social-media',
                        ],
                    ],
                ],
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
            ]);
        }
    }

    /**
     * Add ACF JSON load point
     *
     * @param array $paths Existing ACF JSON load paths
     * @return array
     */
    public function add_acf_json_load_point($paths) {
        // Add our path for loading ACF JSON files
        $paths[] = CHILD_THEME_DIR . '/acf-json';
        
        return $paths;
    }

    /**
     * Set ACF JSON save point
     *
     * @param string $path ACF JSON save path
     * @return string
     */
    public function set_acf_json_save_point($path) {
        // Set our path for saving ACF JSON files
        return CHILD_THEME_DIR . '/acf-json';
    }
}

// Initialize the ACF fields
Child_Theme_ACF_Fields::get_instance();