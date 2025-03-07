<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listing_With_Popular extends Widget_Base {

    public function get_name() {
        return 'listing-with-populars';
    }

    public function get_title() {
        return __( 'LP Popular Listings', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );

        $this->add_control(
            'listing_layout',
            [
                'label' => __( 'Listing Layout', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'list_view' => __( 'List View', 'elementor-listingpro' ),
                    'grid_view' => __( 'Grid View', 'elementor-listingpro' ),
                ],
                'default' => 'list_view'
            ]
        );
        $this->add_control(
            'listing_grid_style',
            [
                'label' => __( 'Listing Layout', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'grid_view1' => __( 'Grid Style 1', 'elementor-listingpro' ),
                    'grid_view2' => __( 'Grid Style 2', 'elementor-listingpro' ),
                    'grid_view3' => __( 'Grid Style 3', 'elementor-listingpro' ),
                    'grid_view4' => __( 'Grid Style 4', 'elementor-listingpro' ),
                    'grid_view5' => __( 'Grid Style 5', 'elementor-listingpro' ),
                ],
                'condition' => [
                    'listing_layout' => 'grid_view'
                ],
                'default' => 'grid_view1'
            ]
        );
        $this->add_control(
            'listing_list_style',
            [
                'label' => __( 'Listing Layout', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'listing_views_1' => __( 'List Style 1', 'elementor-listingpro' ),
                    'list_view_v2' => __( 'List Style 2', 'elementor-listingpro' ),
                ],
                'condition' => [
                    'listing_layout' => 'list_view'
                ],
                'default' => 'listing_views_1'
            ]
        );
        $this->add_control(
            'number_posts',
            [
                'label' => __( 'Posts per page', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '3' => __( '3 Posts', 'elementor-listingpro' ),
                    '6' => __( '6 Posts', 'elementor-listingpro' ),
                    '9' => __( '9 Posts', 'elementor-listingpro' ),
                    '12' => __( '12 Posts', 'elementor-listingpro' ),
                    '15' => __( '15 Posts', 'elementor-listingpro' ),
                    '-1' => __( 'All Posts', 'elementor-listingpro' ),
                ],
                'default' => '3'
            ]
        );

        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_listing_grids_with_populars( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_listing_grids_with_populars')) {
    function listingpro_shortcode_listing_grids_with_populars($atts, $content = null) {
        extract(shortcode_atts(array(
            'listing_grid_style'   => 'grid_view1',
            'listing_list_style'   => 'listing_views_1',
            'number_posts'   => '3',
            'listing_layout'   => 'grid_view',
            'number_posts'   => '',
        ), $atts));

        $output =  listing_elements_loop_cb( 'listing_grids_with_popular', $atts );
        return $output;
    }
}