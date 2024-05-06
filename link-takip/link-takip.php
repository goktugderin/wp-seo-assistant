<?php


// Eklenti menüsünü yönetim paneline ekleyin
function link_tracker_menu() {
    add_menu_page('Link Takip', 'Link Takip', 'manage_options', 'link-tracker', 'link_tracker_page');
}
add_action('admin_menu', 'link_tracker_menu');

// Bağlantıları takip etmek için ana fonksiyon
function track_links() {
    $dofollow_links = array();
    $nofollow_links = array();

    $posts = get_posts(array('post_type' => 'post', 'numberposts' => -1));

    foreach ($posts as $post) {
        $content = $post->post_content;
        $dom = new DOMDocument;
        @$dom->loadHTML($content);

        $anchors = $dom->getElementsByTagName('a');
        foreach ($anchors as $anchor) {
            $href = $anchor->getAttribute('href');
            $rel = $anchor->getAttribute('rel');
            $text = $anchor->textContent;
            $added_date = get_post_time('Y-m-d H:i:s', true, $post);
            $removed_date = "Bağlantı hala aktif";

            if (stripos($rel, 'nofollow') !== false) {
                $nofollow_links[] = array(
                    'url' => $href,
                    'page' => get_the_title($post->ID),
                    'text' => $text,
                    'added_date' => $added_date,
                    'removed_date' => $removed_date,
                );
            } else {
                $dofollow_links[] = array(
                    'url' => $href,
                    'page' => get_the_title($post->ID),
                    'text' => $text,
                    'added_date' => $added_date,
                    'removed_date' => $removed_date,
                );
            }
        }
    }

    $all_links = array(
        'dofollow' => $dofollow_links,
        'nofollow' => $nofollow_links,
    );

    return $all_links;
}

// Bağlantıları gösteren sayfa
function link_tracker_page() {
    $all_links = track_links();

    echo '<div class="wrap">';
    echo '<h2>Link Takip</h2>';
    echo '<h3>Dofollow Bağlantılar</h3>';
    echo '<ul>';
    foreach ($all_links['dofollow'] as $link) {
        echo '<li><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_html($link['text']) . '</a> (Sayfa: ' . esc_html($link['page']) . ') - Eklenme Tarihi: ' . esc_html($link['added_date']) . ' - Kaldırılma Tarihi: ' . esc_html($link['removed_date']) . '</li>';
    }
    echo '</ul>';
    echo '<h3>Nofollow Bağlantılar</h3>';
    echo '<ul>';
    foreach ($all_links['nofollow'] as $link) {
        echo '<li><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_html($link['text']) . '</a> (Sayfa: ' . esc_html($link['page']) . ') - Eklenme Tarihi: ' . esc_html($link['added_date']) . ' - Kaldırılma Tarihi: ' . esc_html($link['removed_date']) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
}