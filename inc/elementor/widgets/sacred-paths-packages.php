<?php
/**
 * Sacred Paths Packages Widget
 *
 * Shows a tab bar (All / per-deity) and a grid of pilgrimage packages
 * filtered by the "deity" taxonomy.
 */

namespace TripKailash\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Sacred_Paths_Packages extends Widget_Base {

    public function get_name() {
        return 'tk-sacred-paths-packages';
    }

    public function get_title() {
        return __( 'Sacred Paths Packages', 'trip-kailash' );
    }

    public function get_icon() {
        return 'eicon-tabs';
    }

    public function get_categories() {
        return [ 'trip-kailash' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'trip-kailash' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => __( 'Maximum Packages', 'trip-kailash' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => -1,
                'min'     => -1,
                'step'    => 1,
                'description' => __( 'Set to -1 to load all pilgrimage packages.', 'trip-kailash' ),
            ]
        );

        $this->add_control(
            'columns',
            [
                'label'   => __( 'Columns', 'trip-kailash' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    '2' => __( '2 Columns', 'trip-kailash' ),
                    '3' => __( '3 Columns', 'trip-kailash' ),
                    '4' => __( '4 Columns', 'trip-kailash' ),
                ],
                'default' => '3',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $columns = isset( $settings['columns'] ) ? (int) $settings['columns'] : 3;
        if ( $columns < 2 || $columns > 4 ) {
            $columns = 3;
        }
        $grid_class = 'tk-grid-' . $columns;

        $posts_per_page = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : -1;
        if ( 0 === $posts_per_page ) {
            $posts_per_page = -1;
        }

        $deity_terms = get_terms( [
            'taxonomy'   => 'deity',
            'hide_empty' => false,
        ] );

        if ( is_wp_error( $deity_terms ) ) {
            $deity_terms = [];
        }

        $query_args = [
            'post_type'      => 'pilgrimage_package',
            'posts_per_page' => $posts_per_page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        $packages_query = new \WP_Query( $query_args );

        ?>
        <section class="tk-sacred-packages" data-sacred-packages>
            <div class="tk-sacred-tabs" role="tablist">
                <button class="tk-sacred-tab is-active" type="button" role="tab" data-filter="all">
                    <?php esc_html_e( 'All Yatras', 'trip-kailash' ); ?>
                </button>

                <?php foreach ( $deity_terms as $term ) : ?>
                    <button
                        class="tk-sacred-tab"
                        type="button"
                        role="tab"
                        data-filter="<?php echo esc_attr( $term->slug ); ?>">
                        <?php echo esc_html( sprintf( __( '%s Paths', 'trip-kailash' ), $term->name ) ); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="tk-packages-grid <?php echo esc_attr( $grid_class ); ?>" data-sacred-grid>
                <?php
                if ( $packages_query->have_posts() ) {
                    while ( $packages_query->have_posts() ) {
                        $packages_query->the_post();
                        $package_id = get_the_ID();

                        $deity_slugs = wp_get_post_terms( $package_id, 'deity', [ 'fields' => 'slugs' ] );
                        if ( is_wp_error( $deity_slugs ) || ! is_array( $deity_slugs ) ) {
                            $deity_slugs = [];
                        }

                        $this->render_package_card( $package_id, $deity_slugs );
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p>' . esc_html__( 'No packages found.', 'trip-kailash' ) . '</p>';
                }
                ?>
            </div>
        </section>
        <?php
    }

    private function render_package_card( $package_id, $deity_slugs ) {
        $trip_length      = get_post_meta( $package_id, 'trip_length', true );
        $has_lodge        = get_post_meta( $package_id, 'has_lodge', true );
        $has_helicopter   = get_post_meta( $package_id, 'has_helicopter', true );
        $includes_meals   = get_post_meta( $package_id, 'includes_meals', true );
        $includes_rituals = get_post_meta( $package_id, 'includes_rituals', true );
        $key_stops        = get_post_meta( $package_id, 'key_stops', true );

        $featured_image = get_the_post_thumbnail_url( $package_id, 'large' );
        if ( ! $featured_image ) {
            $featured_image = get_template_directory_uri() . '/assets/images/placeholder.svg';
        }

        if ( ! is_array( $key_stops ) ) {
            $key_stops = [];
        }

        $deities_attr = ! empty( $deity_slugs ) ? implode( ' ', array_map( 'sanitize_html_class', $deity_slugs ) ) : '';
        ?>
        <article
            class="tk-package-card tk-package-card--with-stops"
            data-package-id="<?php echo esc_attr( $package_id ); ?>"
            data-deities="<?php echo esc_attr( $deities_attr ); ?>"
            data-package-url="<?php echo esc_url( get_permalink( $package_id ) ); ?>">
            <div class="tk-package-card__image">
                <img src="<?php echo esc_url( $featured_image ); ?>" alt="<?php echo esc_attr( get_the_title( $package_id ) ); ?>" loading="lazy">
            </div>
            <div class="tk-package-card__content">
                <h3 class="tk-package-card__title"><?php echo esc_html( get_the_title( $package_id ) ); ?></h3>
                <?php if ( $trip_length ) : ?>
                    <p class="tk-package-card__duration"><?php echo esc_html( $trip_length ); ?></p>
                <?php endif; ?>
                <div class="tk-package-card__icons">
                    <span class="tk-icon <?php echo $has_lodge ? 'tk-icon--active' : 'tk-icon--inactive'; ?>">🛏️</span>
                    <span class="tk-icon <?php echo $has_helicopter ? 'tk-icon--active' : 'tk-icon--inactive'; ?>">🚁</span>
                    <span class="tk-icon <?php echo $includes_meals ? 'tk-icon--active' : 'tk-icon--inactive'; ?>">🍛</span>
                    <span class="tk-icon <?php echo $includes_rituals ? 'tk-icon--active' : 'tk-icon--inactive'; ?>">🙏</span>
                </div>
                <?php if ( ! empty( $key_stops ) ) : ?>
                    <div class="tk-package-card__key-stops">
                        <h4><?php esc_html_e( 'Key Stops', 'trip-kailash' ); ?></h4>
                        <ul>
                            <?php foreach ( array_slice( $key_stops, 0, 3 ) as $stop ) : ?>
                                <li><?php echo esc_html( $stop ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}
