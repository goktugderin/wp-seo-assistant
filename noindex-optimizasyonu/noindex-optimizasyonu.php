<?php

// Eklenti etkinleştirildiğinde çalışacak işlev
function noindex_ayarlarini_ekle() {
    add_option('noindex_etiketler', ''); // Etiketler için noindex ayarı
    add_option('noindex_sayfalama', ''); // Sayfalama için noindex ayarı
    add_option('noindex_admin', ''); // Admin sayfası için noindex ayarı
    add_option('noindex_kategori', ''); // Kategoriler için noindex ayarı
    add_option('noindex_anasayfa', ''); // Anasayfa için noindex ayarı
}

register_activation_hook(__FILE__, 'noindex_ayarlarini_ekle');

// Eklenti devre dışı bırakıldığında çalışacak işlev
function noindex_ayarlarini_sil() {
    delete_option('noindex_etiketler');
    delete_option('noindex_sayfalama');
    delete_option('noindex_admin');
    delete_option('noindex_kategori');
    delete_option('noindex_anasayfa');
}

register_deactivation_hook(__FILE__, 'noindex_ayarlarini_sil');

// Admin menüsüne ayar sayfasını ekler
function noindex_ayarlar_menu() {
    add_options_page('Noindex Ayarları', 'Noindex Ayarları', 'manage_options', 'noindex-ayarlar', 'noindex_ayarlar_form');
}

add_action('admin_menu', 'noindex_ayarlar_menu');

// Ayarlar sayfasını oluşturur
function noindex_ayarlar_form() {
    ?>
    <div class="wrap">
        <h2>Noindex Ayarları</h2>
        <form method="post" action="options.php">
            <?php settings_fields('noindex_settings_group'); ?>
            <?php do_settings_sections('noindex_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Etiketler için Noindex:</th>
                    <td><input type="checkbox" name="noindex_etiketler" <?php checked(1, get_option('noindex_etiketler'), true); ?> value="1" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Sayfalama için Noindex:</th>
                    <td><input type="checkbox" name="noindex_sayfalama" <?php checked(1, get_option('noindex_sayfalama'), true); ?> value="1" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Admin Sayfası için Noindex:</th>
                    <td><input type="checkbox" name="noindex_admin" <?php checked(1, get_option('noindex_admin'), true); ?> value="1" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Kategoriler için Noindex:</th>
                    <td><input type="checkbox" name="noindex_kategori" <?php checked(1, get_option('noindex_kategori'), true); ?> value="1" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Anasayfa için Noindex:</th>
                    <td><input type="checkbox" name="noindex_anasayfa" <?php checked(1, get_option('noindex_anasayfa'), true); ?> value="1" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Ayarları kaydeder
function noindex_ayarlarini_kaydet() {
    register_setting('noindex_settings_group', 'noindex_etiketler', 'intval');
    register_setting('noindex_settings_group', 'noindex_sayfalama', 'intval');
    register_setting('noindex_settings_group', 'noindex_admin', 'intval');
    register_setting('noindex_settings_group', 'noindex_kategori', 'intval');
    register_setting('noindex_settings_group', 'noindex_anasayfa', 'intval');
}

add_action('admin_init', 'noindex_ayarlarini_kaydet');

// Head bölümüne noindex meta etiketlerini ekler
function noindex_ekle() {
    if (get_option('noindex_etiketler') == 1 && is_tag()) {
        echo '<meta name="robots" content="noindex, follow" />' . PHP_EOL;
    }

    if (get_option('noindex_sayfalama') == 1 && is_paged()) {
        global $wp_query;

        $max_sayfa = $wp_query->max_num_pages;

        if ($max_sayfa > 1) {
            echo '<meta name="robots" content="noindex, follow" />' . PHP_EOL;
        }
    }

    if (get_option('noindex_admin') == 1 && strpos($_SERVER['REQUEST_URI'], '/wp-admin/') !== false) {
        echo '<meta name="robots" content="noindex, nofollow" />' . PHP_EOL;
    }

    if (get_option('noindex_kategori') == 1 && is_category()) {
        echo '<meta name="robots" content="noindex, follow" />' . PHP_EOL;
    }

    if (get_option('noindex_anasayfa') == 1 && is_home()) {
        echo '<meta name="robots" content="noindex, follow" />' . PHP_EOL;
    }
}

add_action('wp_head', 'noindex_ekle');
