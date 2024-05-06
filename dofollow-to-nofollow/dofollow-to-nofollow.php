<?php


// Eklenti ayarlarını oluştur
function dofollow_to_nofollow_converter_settings_page() {
    add_menu_page(
        'Dofollow to Nofollow Converter',
        'Dofollow to Nofollow',
        'manage_options',
        'dofollow-to-nofollow-converter',
        'dofollow_to_nofollow_converter_settings',
        'dashicons-admin-links'
    );
}
add_action('admin_menu', 'dofollow_to_nofollow_converter_settings_page');

// Eklenti ayarlar sayfası
function dofollow_to_nofollow_converter_settings() {
    if (isset($_POST['convert_dofollow_to_nofollow'])) {
        // Tüm dofollow bağlantıları nofollow yap
        convert_all_dofollow_to_nofollow();
        echo '<div class="notice notice-success"><p>Tüm dofollow bağlantılar nofollow yapıldı!</p></div>';
    }
    ?>
    <div class="wrap">
        <h2>Dofollow to Nofollow Converter Ayarları</h2>
        <form method="post">
            <p>Tüm dofollow bağlantıları nofollow yapmak için aşağıdaki düğmeyi tıklayın.</p>
            <p>
                <input type="submit" class="button button-primary" name="convert_dofollow_to_nofollow" value="Dofollow bağlantıları Nofollow yap">
            </p>
        </form>
    </div>
    <?php
}

// Tüm dofollow bağlantıları nofollow yap
function convert_all_dofollow_to_nofollow() {
    $content = get_posts(array('numberposts' => -1));

    foreach ($content as $post) {
        $post_content = $post->post_content;
        $post_content = str_replace('rel="dofollow"', 'rel="nofollow"', $post_content);
        $post->post_content = $post_content;
        wp_update_post($post);
    }
}