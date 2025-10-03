<!-- Hyplus 导航 - Profile - Recent Posts
 Code type: Universal Snippet (HTML + PHP)
 Shortcode: [wpcode id="14382"]
-->
<div class="profile-main-row">
    <div class="profile-card profile-card-mobile-categories" style="text-align: left">
        <div style="font-size: 26px; font-weight: 600; text-align: left">资源分类</div>
        <?php
        echo '<ul class="home-categories">';
        wp_list_categories([
            'show_count' => true,
            'title_li' => '',
            'hierarchical' => true
        ]);
        echo '</ul>';
        ?>
    </div>
    <div class="profile-card" style="text-align: left">
        <div style="font-size: 26px; font-weight: 600; text-align: left">热门文章</div>
        <?php echo do_shortcode('[recently_modified_posts posts_per_page=10 show_modified_date=true]'); ?>
    </div>
    <div style="margin-top: 30px"><?php echo do_shortcode('[wpcode id="4726"]'); ?></div>
</div>