<?php
/**
 * Plugin Name: SwiperJS Slider
 * Description: A WordPress plugin to create a slider using SwiperJS with arrows and dot pagination.
 * Version: 1.0.0
 * Author: Jennifer Murrin
 * Author URI: https://jennifermurrin.com 
 */


// Enqueue SwiperJS assets
function swiperjs_slider_enqueue_assets() {
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css', [], '10.0.4');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js', [], '10.0.4', true);
    wp_enqueue_script('swiper-custom', plugin_dir_url(__FILE__) . 'js/swiper-custom.js', ['swiper-js'], '1.0.0', true);

    // Add custom CSS for slider height and image containment
    wp_add_inline_style('swiper-css', "
        .swiper-container {
            width: 100%;
            height: 500px;
            overflow: hidden;
        }
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        html,
        body {
            position: relative;
            height: 100%;
        }

        body {
            background: #eee;
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .swiper {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        .swiper-wrapper {
            height: 600px;
            background-color: black;
        }

    ");
}
add_action('wp_enqueue_scripts', 'swiperjs_slider_enqueue_assets');

// Shortcode to display the slider
function swiperjs_slider_shortcode($atts) {
    $atts = shortcode_atts([
        'images' => '', // Comma-separated list of image URLs
    ], $atts);

    $images = array_map('trim', explode(',', $atts['images']));

    if (empty($images)) {
        return '<p>No images provided for the slider.</p>';
    }

    ob_start();
    ?>
    <div class="container">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php foreach ($images as $image) : ?>
                    <div class="swiper-slide">
                        <img src="<?php echo esc_url($image); ?>" alt="Slide Image">
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            <!-- Add Navigation Arrows -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
}
add_shortcode('swiper_slider', 'swiperjs_slider_shortcode');

// Create the JavaScript file dynamically for Swiper initialization
function swiperjs_create_custom_js() {
    ?>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.swiper-container', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    });
    <?php
}

// Save the JavaScript code to a file
register_activation_hook(__FILE__, function() {
    $custom_js = plugin_dir_path(__FILE__) . 'js/swiper-custom.js';
    if (!file_exists(dirname($custom_js))) {
        mkdir(dirname($custom_js), 0755, true);
    }
    file_put_contents($custom_js, "");
    ob_start();
    swiperjs_create_custom_js();
    file_put_contents($custom_js, ob_get_clean());
});