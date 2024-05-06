<?php


// Kopya içerik tespiti işlemini gerçekleştirecek ana işlev
function kopya_icerik_tespiti($post_id) {
    // İçerik karşılaştırma işlemleri burada yapılacak
    // Örnek olarak, metin karşılaştırma algoritması kullanabilirsiniz
    $post_content = get_post_field('post_content', $post_id);
    $other_posts = get_posts(array('post_type' => 'post'));
    $detected_copies = array();

    foreach ($other_posts as $other_post) {
        $other_content = get_post_field('post_content', $other_post->ID);
        $similarity = similar_text($post_content, $other_content, $percent);

        if ($percent > 90) {
            // Belirli bir benzerlik yüzdesi eşiği aşıldığında kullanıcıya uyarı verilebilir
            // Uyarı veya kayıt işlemleri burada yapılacak
            $detected_copies[] = $other_post;
        }
    }

    // Kopya içerikleri ayarlar kısmında göstermek için kaydedin
    update_option('kopya_icerikler', $detected_copies);
}

// İçerik kaydedildiğinde kopya içerik tespiti işlevini çağırın
add_action('save_post', 'kopya_icerik_tespiti');

// Ayarlar kısmına kopya içerikleri listesini eklemek için bir işlev
function kopya_icerik_ayarlar() {
    $detected_copies = get_option('kopya_icerikler');
    ?>
    <div class="wrap">
        <h2>Kopya İçerik Tespiti</h2>
        <ul>
            <?php
            foreach ($detected_copies as $copy) {
                echo '<li><a href="' . get_edit_post_link($copy->ID) . '">' . $copy->post_title . '</a></li>';
            }
            ?>
        </ul>
    </div>
    <?php
}

// Ayarlar kısmında menü eklemek için bir işlev
function kopya_icerik_ayarlar_menu() {
    add_menu_page(
        'Kopya İçerik Tespiti',
        'Kopya İçerik Tespiti',
        'manage_options',
        'kopya-icerik-tespiti',
        'kopya_icerik_ayarlar'
    );
}

add_action('admin_menu', 'kopya_icerik_ayarlar_menu');