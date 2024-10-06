<?php
/**
 * Plugin Name: Company Viewer
 * Description: A plugin to manage and display companies with a popup view. Users can add companies with specific fields like logo, contact, and address.
 * Version: 1.0
 * Author: Halit Yavuz
 */

function company_viewer_register_company_post_type() {
    $labels = array(
        'name' => __( 'Companies', 'text_domain' ),
        'singular_name' => __( 'Company', 'text_domain' ),
        'menu_name' => __( 'Companies', 'text_domain' ),
        'name_admin_bar' => __( 'Company', 'text_domain' ),
    );

    $args = array(
        'label' => __( 'Company', 'text_domain' ),
        'labels' => $labels,
        'supports' => array( 'title', 'thumbnail', 'custom-fields' ), // İçerik düzenleyici yok
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-building',
    );
    register_post_type( 'company', $args );
}
add_action( 'init', 'company_viewer_register_company_post_type' );

function company_viewer_custom_metaboxes() {
    add_meta_box(
        'company_details',
        __( 'Company Details', 'text_domain' ),
        'company_viewer_metabox_callback',
        'company',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'company_viewer_custom_metaboxes' );

function company_viewer_metabox_callback( $post ) {
    $logo = get_post_meta( $post->ID, 'company_logo', true );
    $contact = get_post_meta( $post->ID, 'company_contact', true );
    $address = get_post_meta( $post->ID, 'company_address', true );

    ?>
    <table class="form-table">
        <tr>
            <th><label for="company_logo"><?php _e( 'Company Logo', 'text_domain' ); ?></label></th>
            <td>
                <input type="text" id="company_logo" name="company_logo" value="<?php echo esc_attr( $logo ); ?>" style="width: 100%;" />
                <button type="button" class="button upload_logo_button"><?php _e( 'Upload Logo', 'text_domain' ); ?></button>
            </td>
        </tr>
        <tr>
            <th><label for="company_contact"><?php _e( 'Contact Info', 'text_domain' ); ?></label></th>
            <td>
                <input type="text" id="company_contact" name="company_contact" value="<?php echo esc_attr( $contact ); ?>" style="width: 100%;" />
            </td>
        </tr>
        <tr>
            <th><label for="company_address"><?php _e( 'Address', 'text_domain' ); ?></label></th>
            <td>
                <input type="text" id="company_address" name="company_address" value="<?php echo esc_attr( $address ); ?>" style="width: 100%;" />
            </td>
        </tr>
    </table>
    <?php
}

function company_viewer_save_company_meta( $post_id ) {
    // Logo
    if ( isset( $_POST['company_logo'] ) ) {
        update_post_meta( $post_id, 'company_logo', sanitize_text_field( $_POST['company_logo'] ) );
    }
    
    if ( isset( $_POST['company_contact'] ) ) {
        update_post_meta( $post_id, 'company_contact', sanitize_text_field( $_POST['company_contact'] ) );
    }
    
    if ( isset( $_POST['company_address'] ) ) {
        update_post_meta( $post_id, 'company_address', sanitize_text_field( $_POST['company_address'] ) );
    }
}
add_action( 'save_post', 'company_viewer_save_company_meta' );

function company_viewer_remove_editor() {
    remove_post_type_support( 'company', 'editor' );
}
add_action( 'init', 'company_viewer_remove_editor' );

function company_viewer_admin_scripts() {
    wp_enqueue_media();
    wp_enqueue_script( 'company-viewer-admin', plugin_dir_url( __FILE__ ) . 'admin.js', array( 'jquery' ), null, true );
}
add_action( 'admin_enqueue_scripts', 'company_viewer_admin_scripts' );

function render_company_viewer_widget($atts) {
    $companies = new WP_Query(array('post_type' => 'company', 'posts_per_page' => -1));

    ob_start();
    if ($companies->have_posts()) :
        ?>
        <div class="company-viewer-wrapper">
            <input type="text" id="company-search" placeholder="Search companies..." style="margin-bottom: 20px; padding: 8px; width: 100%; max-width: 400px;" />

            <table id="company-table" class="company-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 8px;">Company Name</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Contact</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($companies->have_posts()) : $companies->the_post(); ?>
                        <tr class="company-row" data-company-id="<?php the_ID(); ?>">
                            <td style="border: 1px solid #ddd; padding: 8px;">
                                <a href="#" class="company-name"><?php the_title(); ?></a>
                            </td>
                            <td style="border: 1px solid #ddd; padding: 8px;">
                                <?php echo esc_html( get_post_meta( get_the_ID(), 'company_contact', true ) ); ?>
                            </td>
                            <td style="border: 1px solid #ddd; padding: 8px;">
                                <?php echo esc_html( get_post_meta( get_the_ID(), 'company_address', true ) ); ?>
                            </td>
                            <td class="company-details" style="display: none;">
                                <p><strong>Contact:</strong> <?php echo esc_html( get_post_meta( get_the_ID(), 'company_contact', true ) ); ?></p>
                                <p><strong>Address:</strong> <?php echo esc_html( get_post_meta( get_the_ID(), 'company_address', true ) ); ?></p>
                                <p><strong>Logo:</strong> <img src="<?php echo esc_url( get_post_meta( get_the_ID(), 'company_logo', true ) ); ?>" alt="<?php the_title(); ?>" style="max-width: 100px;" /></p>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pop-up and script -->
        <div id="company-popup" style="display:none;">
            <div id="popup-content"></div>
            <span class="close-popup">&times;</span>
        </div>

        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                var companies = document.querySelectorAll('.company-name');
                var popup = document.getElementById('company-popup');
                var popupContent = document.getElementById('popup-content');
                var closePopup = document.querySelector('.close-popup');

                companies.forEach(function(company) {
                    company.addEventListener('click', function(e) {
                        e.preventDefault();
                        var companyDetails = this.closest('tr').querySelector('.company-details').innerHTML;
                        popupContent.innerHTML = companyDetails;
                        popup.style.display = 'block';
                    });
                });

                closePopup.addEventListener('click', function() {
                    popup.style.display = 'none';
                });

                window.addEventListener('click', function(e) {
                    if (e.target == popup) {
                        popup.style.display = 'none';
                    }
                });

                var searchInput = document.getElementById('company-search');
                searchInput.addEventListener('keyup', function() {
                    var filter = searchInput.value.toLowerCase();
                    var rows = document.querySelectorAll('#company-table tbody tr');

                    rows.forEach(function(row) {
                        var companyName = row.querySelector('.company-name').innerText.toLowerCase();
                        if (companyName.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        </script>

        <style>
            #company-popup {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            }

            #popup-content {
                background-color: #fff;
                padding: 20px;
                border-radius: 10px;
                max-width: 600px;
                width: 80%;
            }

            .close-popup {
                position: absolute;
                top: 10px;
                right: 20px;
                font-size: 24px;
                cursor: pointer;
            }
        </style>
        <?php
    endif;

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('company_viewer_widget', 'render_company_viewer_widget');
