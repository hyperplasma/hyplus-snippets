<!-- Hyplus 导航 - Profile - Panels
 Code type: Universal Snippet (HTML + JS + PHP)
 Shortcode: [wpcode id="14381"]
-->
<div class="profile-main-row">
    <div class="profile-panel">
        <div class="profile-tabs">
            <button class="profile-tab-btn active" data-tab="stats">Stats</button>
            <button class="profile-tab-btn" data-tab="repos">Repos</button>
        </div>
        <div class="profile-tab-content active" id="tab-stats">
            <img class="profile-stats-img" src="https://www.hyperplasma.top/wp-content/uploads/2025/02/clear-sky-with-blue-sea-min-scaled.jpg" alt="Clear Sky with Blue Sea" />
            <div class="profile-stats-counts"><?php echo do_shortcode('[site_content_counts]'); ?></div>
            <div class="profile-badges">
                <span class="profile-badge"><img src="https://img.shields.io/badge/HP-Hyperplasma-blue" alt="Hyperplasma" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/Java-%23ED8B00.svg?logo=openjdk&logoColor=white" alt="Java" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/Spring%20Boot-6DB33F?logo=springboot&logoColor=fff" alt="Spring Boot" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/Go-%2300ADD8.svg?&logo=go&logoColor=white" alt="Go" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/php-%23777BB4.svg?&logo=php&logoColor=white" alt="PHP" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/WordPress-%2321759B.svg?logo=wordpress&logoColor=white" alt="WordPress" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=fff" alt="MySQL" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/Redis-%23DD0031.svg?logo=redis&logoColor=white" alt="Redis" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/C++-%2300599C.svg?logo=c%2B%2B&logoColor=white" alt="Cpp" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/Rust-%23000000.svg?e&logo=rust&logoColor=white" alt="Rust" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/Python-3776AB?logo=python&logoColor=fff" alt="Python" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/PyTorch-ee4c2c?logo=pytorch&logoColor=white" alt="PyTorch" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/HTML-%23E34F26.svg?logo=html5&logoColor=white" alt="HTML" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/CSS-639?logo=css&logoColor=fff" alt="CSS" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=000" alt="JavaScript" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/Markdown-%23000000.svg?logo=markdown&logoColor=white" alt="Markdown" /></span>
                <span class="profile-badge"><img src="https://img.shields.io/badge/macOS-000000?logo=apple&logoColor=F0F0F0" alt="macOS" /></span>
            </div>
            <div style="margin-top: 30px"><?php echo do_shortcode('[wpcode id="4726"]'); ?></div>
        </div>
        <div class="profile-tab-content" id="tab-repos">
            <div class="profile-repo-list">
                <div class="profile-repo-item">
                    <a class="profile-repo-title" href="https://www.hyperplasma.top/?p=9621/">Ultimate Solutions</a>
                    <div class="profile-repo-desc">Java&nbsp;·&nbsp;Go&nbsp;·&nbsp;JS&nbsp;·&nbsp;PHP&nbsp;·&nbsp;C++&nbsp;|&nbsp;算法题解</div>
                </div>
                <div class="profile-repo-item">
                    <a class="profile-repo-title" href="https://www.hyperplasma.top/?p=8389/">Hyplus Foodie</a>
                    <div class="profile-repo-desc">Spring&nbsp;Boot&nbsp;·&nbsp;Vue&nbsp;|&nbsp;单体项目</div>
                </div>
                <div class="profile-repo-item">
                    <a class="profile-repo-title" href="https://www.hyperplasma.top/?p=13242/">HyFetcher</a>
                    <div class="profile-repo-desc">Rust&nbsp;|&nbsp;爬虫</div>
                </div>
                <div class="profile-repo-item">
                    <a class="profile-repo-title" href="https://www.hyperplasma.top/?p=12766/">HyMTSF(Demo)</a>
                    <div class="profile-repo-desc">Python&nbsp;|&nbsp;多变量时序预测</div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    // 选项卡切换
    const tabBtns = document.querySelectorAll(".profile-tab-btn");
    const tabContents = document.querySelectorAll(".profile-tab-content");
    tabBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            tabBtns.forEach((b) => b.classList.remove("active"));
            tabContents.forEach((tc) => tc.classList.remove("active"));
            btn.classList.add("active");
            document.getElementById("tab-" + btn.dataset.tab).classList.add("active");
        });
    });
</script>