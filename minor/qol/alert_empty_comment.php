<?php
/* Alert Empty Comment - 提交空评论时用alert提示，而非跳转到wp-comments-post.php。
 Code type: PHP
*/

// 前端插入JS脚本
add_action('wp_footer', function() {
    if (is_single() || is_page()) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var commentForm = document.getElementById('commentform');
            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    // 获取表单字段
                    var authorField = document.getElementById('author');
                    var emailField = document.getElementById('email');
                    var commentField = document.getElementById('comment');
                    // 检查昵称
                    if (authorField && authorField.required !== false && authorField.value.trim() === '') {
                        alert('请输入昵称！');
                        authorField.focus();
                        e.preventDefault();
                        return false;
                    }
                    // 检查邮箱
                    if (emailField && emailField.required !== false) {
                        var emailValue = emailField.value.trim();
                        if (emailValue === '') {
                            alert('请输入邮箱！');
                            emailField.focus();
                            e.preventDefault();
                            return false;
                        }
                        // 邮箱格式校验
                        var emailPattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
                        if (!emailPattern.test(emailValue)) {
                            alert('请输入有效的邮箱地址！');
                            emailField.focus();
                            e.preventDefault();
                            return false;
                        }
                    }
                    // 检查评论内容
                    if (commentField && commentField.value.trim() === '') {
                        alert('请输入评论内容！');
                        commentField.focus();
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });
        </script>
        <?php
    }
}); 