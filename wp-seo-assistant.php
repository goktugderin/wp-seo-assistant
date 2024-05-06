<?php
/*
Plugin Name: WP SEO Assistant
Description: Çeşitli özellikleri birleştiren özel bir eklenti.
Version: 1.0
Author: Göktuğ Derin
*/

// Eklentiyi yükle
add_action('plugins_loaded', 'custom_plugin_load');

function custom_plugin_load() {
    // Diğer eklentileri yükle
    include_once plugin_dir_path(__FILE__) . 'kopya-icerik-kontrol/kopya-icerik-kontrol.php';
    include_once plugin_dir_path(__FILE__) . 'seo-oto-linkleme/seo-oto-linkleme.php';
    include_once plugin_dir_path(__FILE__) . 'noindex-optimizasyonu/noindex-optimizasyonu.php';
    include_once plugin_dir_path(__FILE__) . 'dofollow-to-nofollow/dofollow-to-nofollow.php';
    include_once plugin_dir_path(__FILE__) . 'link-takip/link-takip.php';
    // ve böyle devam eder...
}

// Eklenti etkinleştirildiğinde çalışacak işlev - gorsel seo
function custom_alt_text_caption_activate() {
    update_option('custom_alt_text_caption_enabled', true);
}

// Eklenti devre dışı bırakıldığında çalışacak işlev
function custom_alt_text_caption_deactivate() {
    update_option('custom_alt_text_caption_enabled', false);
}

// Medya dosyalarının alt metin ve açıklamalarını dosya adlarına göre dolduran işlev
function custom_alt_text_caption_update_metadata($post_ID) {
    // Eklenti etkinse devam et
    if (get_option('custom_alt_text_caption_enabled')) {
        // Medya dosyasının URL'sini al
        $image_url = wp_get_attachment_url($post_ID);

        // Dosya adını al
        $file_name = basename($image_url, '.' . pathinfo($image_url, PATHINFO_EXTENSION));

        // Alt metni güncelle
        update_post_meta($post_ID, '_wp_attachment_image_alt', $file_name);

        // Açıklamayı güncelle
        $attachment = get_post($post_ID);
        $attachment_data = array(
            'ID' => $post_ID,
            'post_excerpt' => $file_name
        );
        wp_update_post($attachment_data);
    }
}

// Eklenti etkinse medya dosyalarının alt metin ve açıklamalarını güncelle
add_action('add_attachment', 'custom_alt_text_caption_update_metadata');

// Eklenti etkinleştirildiğinde ve devre dışı bırakıldığında ilgili işlevleri çağır
register_activation_hook(__FILE__, 'custom_alt_text_caption_activate');
register_deactivation_hook(__FILE__, 'custom_alt_text_caption_deactivate');
