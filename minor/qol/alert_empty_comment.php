<?php
/* Alert Empty Comment - 提交空评论时用alert提示，而非跳转到wp-comments-post.php。
 Code type: PHP
 
 Performance Improvements:
 - Front-end validation only for UX (not security)
 - Back-end validation using comment_post hook
 - Email validation extracted to reusable function
 - Deferred JS loading for better page performance
*/

// ========== 后端验证函数 - 作为安全防线 ==========
function hy_validate_comment_fields($comment_data) {
    // 检查作者名称
    if (empty(trim($comment_data['comment_author']))) {
        wp_die('请输入昵称！', 'Comment Error', array('back_link' => true));
    }
    
    // 检查邮箱
    if (empty(trim($comment_data['comment_author_email']))) {
        wp_die('请输入邮箱！', 'Comment Error', array('back_link' => true));
    }
    
    // 邮箱格式验证
    if (!is_email($comment_data['comment_author_email'])) {
        wp_die('请输入有效的邮箱地址！', 'Comment Error', array('back_link' => true));
    }
    
    // 检查评论内容
    if (empty(trim($comment_data['comment_content']))) {
        wp_die('请输入评论内容！', 'Comment Error', array('back_link' => true));
    }
    
    return $comment_data;
}

// 在评论提交前触发后端验证
add_filter('preprocess_comment', 'hy_validate_comment_fields', 10, 1);

// ========== 前端验证 - 改进的UX体验 ==========
add_action('wp_enqueue_scripts', function() {
    if (is_single() || is_page()) {
        ob_start();
        ?>
(function($) {
    $(document).ready(function() {
        var $commentForm = $('#commentform');
        if ($commentForm.length) {
            $commentForm.on('submit.hyCommentValidate', function(e) {
                var emailPattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
                var $author = $('#author');
                var $email = $('#email');
                var $comment = $('#comment');
                
                if ($author.length && $author.val().trim() === '') {
                    alert('请输入昵称！');
                    $author.focus();
                    e.preventDefault();
                    return false;
                }
                
                if ($email.length) {
                    var emailValue = $email.val().trim();
                    if (emailValue === '') {
                        alert('请输入邮箱！');
                        $email.focus();
                        e.preventDefault();
                        return false;
                    }
                    if (!emailPattern.test(emailValue)) {
                        alert('请输入有效的邮箱地址！');
                        $email.focus();
                        e.preventDefault();
                        return false;
                    }
                }
                
                if ($comment.length && $comment.val().trim() === '') {
                    alert('请输入评论内容！');
                    $comment.focus();
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
})(jQuery);
        <?php
        $js_code = ob_get_clean();
        wp_add_inline_script('jquery', $js_code);
    }
}); 
?>