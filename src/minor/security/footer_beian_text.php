<?php
/**
 * Footer Beian Text PHP
 * 在页脚添加备案信息和相关链接
 * Current status: beian text unused
 */
add_filter( 'generate_copyright','tu_custom_copyright' );
function tu_custom_copyright() {
    ?>
© 2024-<?php echo date("Y"); ?> <a href="https://www.hyperplasma.top/about/">Hyperplasma</a> • <a href="https://www.hyperplasma.top/privacy-policy/">隐私政策</a> • <a href="https://www.hyperplasma.top/hyplus/terms/">服务条款</a><br><a href="http://beian.miit.gov.cn/" target="_blank">XXX</a> <img src="https://www.hyperplasma.top/wp-content/uploads/2024/08/备案图标.png" width=15px height=15px> <a href="https://beian.mps.gov.cn/#/query/webSearch?code=XXX" rel="noreferrer" target="_blank">XXX</a>
    <?php
}