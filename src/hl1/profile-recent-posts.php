<!-- Hyplus 导航 - Profile - Recent Posts
 Code type: Universal Snippet (HTML + PHP)
 Shortcode: [wpcode id="14382"]
-->
<div class="profile-main-row">
    <div class="profile-card" style="text-align: left">
        <div style="font-size: 26px; font-weight: 600; text-align: center">热门文章</div>
        <?php echo do_shortcode('[recently_modified_posts posts_per_page=20 show_modified_date=true]'); ?>
    </div>
</div>
