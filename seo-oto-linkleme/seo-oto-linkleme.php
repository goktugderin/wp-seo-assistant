<?php


// Eklenti menüsünü oluştur
function internal_linking_menu() {
    add_menu_page(
        'Internal Linking',
        'Internal Linking',
        'manage_options',
        'internal-linking',
        'internal_linking_page'
    );
}

add_action('admin_menu', 'internal_linking_menu');

// Eklenti sayfasını oluştur
function internal_linking_page() {
    ?>
    <div class="wrap">
        <h2>Internal Linking</h2>
        <form method="post" action="">
            <?php
            // Formdan gelen veriyi işle
            if ($_POST) {
                $keywords = sanitize_text_field($_POST['keywords']);
                $links = sanitize_text_field($_POST['links']);
                $post_types = isset($_POST['post_types']) ? $_POST['post_types'] : array();

                // Anahtar kelimeleri ve linkleri diziye ayır
                $keyword_array = explode("\n", $keywords);
                $link_array = explode("\n", $links);

                // Her bir anahtar kelime için iç link oluştur
                foreach ($keyword_array as $key => $keyword) {
                    $keyword = trim($keyword);
                    $link = trim($link_array[$key]);

                    if (!empty($keyword) && !empty($link)) {
                        internal_linking_insert($keyword, $link, $post_types);
                    }
                }
            }
            ?>
            <label for="keywords">Anahtar Kelimeler:</label>
            <textarea name="keywords" id="keywords" rows="5" cols="30"></textarea><br>

            <label for="links">Linkler:</label>
            <textarea name="links" id="links" rows="5" cols="30"></textarea><br>

            <label for="post_types">Görünecek Yerler:</label><br>
            <input type="checkbox" name="post_types[]" value="post"> Blog Yazıları<br>
            <input type="checkbox" name="post_types[]" value="page"> Sayfalar<br>

            <input type="submit" name="submit" class="button button-primary" value="Kaydet">
        </form>

        <h3>Eklenen İç Linklemeler</h3>
        <?php
        // Eklenen iç linklemeleri listele
        $internal_links = get_option('internal_links', array());

        if (!empty($internal_links)) {
            echo '<ul>';
            foreach ($internal_links as $internal_link) {
                echo '<li>' . esc_html($internal_link['keyword']) . ' - ' . esc_url($internal_link['link']) . ' - ' . esc_html($internal_link['post_types']) . ' <a href="#" class="remove-link" data-keyword="' . esc_attr($internal_link['keyword']) . '">Sil</a></li>';
            }
            echo '</ul>';
        } else {
            echo 'Henüz iç linkleme eklenmemiş.';
        }
        ?>
    </div>

    <script>
        // İç linki kaldır
        document.addEventListener('DOMContentLoaded', function () {
            var removeLinks = document.querySelectorAll('.remove-link');
            removeLinks.forEach(function (removeLink) {
                removeLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    var keyword = this.getAttribute('data-keyword');
                    removeInternalLink(keyword);
                });
            });
        });

        // İç linki kaldırma fonksiyonu
        function removeInternalLink(keyword) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxurl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                }
            };
            xhr.send('action=remove_internal_link&keyword=' + encodeURIComponent(keyword));
        }
    </script>
    <?php
}

// Anahtar kelimeleri ve linkleri veritabanına ekle
function internal_linking_insert($keyword, $link, $post_types) {
    $internal_links = get_option('internal_links', array());

    $internal_links[] = array(
        'keyword' => $keyword,
        'link' => $link,
        'post_types' => implode(', ', $post_types),
    );

    update_option('internal_links', $internal_links);

    $args = array(
        'post_type' => $post_types,
        'posts_per_page' => -1,
    );

    $posts = get_posts($args);

    foreach ($posts as $post) {
        $content = $post->post_content;
        $content = str_replace($keyword, '<a href="' . esc_url($link) . '">' . esc_html($keyword) . '</a>', $content);

        $update_post = array(
            'ID'           => $post->ID,
            'post_content' => $content,
        );

        wp_update_post($update_post);
    }
}

// İç linki kaldır
add_action('wp_ajax_remove_internal_link', 'remove_internal_link');

function remove_internal_link() {
    $keyword = sanitize_text_field($_POST['keyword']);
    $internal_links = get_option('internal_links', array());

    foreach ($internal_links as $key => $internal_link) {
        if ($internal_link['keyword'] === $keyword) {
            unset($internal_links[$key]);
            update_option('internal_links', $internal_links);
            break;
        }
    }

    // İlgili iç linki kaldır
    $args = array(
        'post_type' => $internal_link['post_types'],
        'posts_per_page' => -1,
    );

    $posts = get_posts($args);

    foreach ($posts as $post) {
        $content = $post->post_content;
        $content = str_replace('<a href="' . esc_url($internal_link['link']) . '">' . esc_html($internal_link['keyword']) . '</a>', esc_html($internal_link['keyword']), $content);

        $update_post = array(
            'ID'           => $post->ID,
            'post_content' => $content,
        );

        wp_update_post($update_post);
    }

    wp_die();
}
