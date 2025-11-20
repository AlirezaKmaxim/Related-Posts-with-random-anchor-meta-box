<?php
/*
Plugin Name: Custom Related Posts
Description: Shows related posts with image + custom anchor-text meta per post; displays one random anchor per related link.
Version: 1.3
Author: Alireza CarryMI
Text Domain: custom-related-posts-anchor-text-field
*/

/* ------------------------
  Meta box
------------------------- */
add_action('add_meta_boxes', 'crp_add_metabox');
function crp_add_metabox() {
    add_meta_box(
        'custom_related_anchors',
        'انکر تکست‌های مرتبط',
        'crp_render_metabox',
        'post',
        'normal',
        'default'
    );
}

function crp_render_metabox($post) {
    wp_nonce_field('crp_save_meta', 'crp_meta_nonce');
    $anchors = get_post_meta($post->ID, '_crp_anchors', true);
    ?>
    <label for="crp_anchors">انکر تکست‌ها (با کاما انگلیسی `,`، ویرگول فارسی `،` یا هر خط جدید جداش کن)</label><br>
    <textarea name="crp_anchors" id="crp_anchors" rows="4" style="width:100%;"><?php echo esc_textarea($anchors); ?></textarea>
    <p style="font-size:12px;color:#666;margin-top:6px;">مثال: <code>تست 1, تست 2, تست 3</code> یا هر کدام در خط جدا</p>
    <?php
}

/* ------------------------
  Save meta (nonce, autosave)
------------------------- */
add_action('save_post', 'crp_save_meta');
function crp_save_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['crp_meta_nonce']) || !wp_verify_nonce($_POST['crp_meta_nonce'], 'crp_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['crp_anchors'])) {
        $anchors_raw = sanitize_textarea_field($_POST['crp_anchors']);
        update_post_meta($post_id, '_crp_anchors', $anchors_raw);
    }
}

/* ------------------------
  Shortcode: [custom_related_posts posts="4"]
------------------------- */
add_shortcode('custom_related_posts', 'crp_shortcode');
function crp_shortcode($atts) {
    global $post;
    if (!$post) return '';

    $atts = shortcode_atts(array(
        'posts'   => 4,        // show 4 items by default (was 3)
        'orderby' => 'rand',   // randomize so it’s not “latest modified”
    ), $atts, 'custom_related_posts');

    $categories = wp_get_post_categories($post->ID);
    if (empty($categories)) return '';

    // Build query for related posts
    $args = array(
        'category__in'        => $categories,
        'post__not_in'        => array($post->ID),
        'posts_per_page'      => max(1, intval($atts['posts'])),
        'orderby'             => $atts['orderby'], // 'rand' by default
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => true,
        'post_status'         => 'publish',
    );

    // Use WP_Query instead of get_posts for clarity/performance flags
    $q = new WP_Query($args);
    if (!$q->have_posts()) return '';

    ob_start();
    echo '<div class="custom-related-posts">';
    while ($q->have_posts()) {
        $q->the_post();
        $rel_id = get_the_ID();

        $anchors_raw = get_post_meta($rel_id, '_crp_anchors', true);
        if ($anchors_raw) {
            // Split by English comma, Persian comma, semicolon, or new line
            $arr = preg_split('/\s*(?:,|،|;|\r?\n)+\s*/u', $anchors_raw);
            $arr = array_values(array_filter(array_map('trim', $arr), function($v){ return $v !== ''; }));
            if (empty($arr)) $arr = array(get_the_title($rel_id));
        } else {
            $arr = array(get_the_title($rel_id));
        }

        $random_anchor = $arr[array_rand($arr)]; // one random anchor per related item
        ?>
        <div class="crp-item">
            <a href="<?php echo esc_url(get_permalink($rel_id)); ?>">
                <?php if (has_post_thumbnail($rel_id)) {
                    echo get_the_post_thumbnail($rel_id, 'thumbnail');
                } ?>
            </a>
            <p><a href="<?php echo esc_url(get_permalink($rel_id)); ?>"><?php echo esc_html($random_anchor); ?></a></p>
        </div>
        <?php
    }
    echo '</div>';
    wp_reset_postdata();

    return ob_get_clean();
}
