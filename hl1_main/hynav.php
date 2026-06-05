<?php
/* HyNav: Hyplus Nav Page, Ultimate Button popup addon
 * Description: currently only implemented in Ultimate Button, but the CSS is shared with other components.
 * Code type: PHP/HTML (no need to compress the codes)
 * Special Permissions: Direct Edit; No Header Desc; No Formatting
 * Shortcode: [hynav_panel_render]
 */
add_shortcode('hynav_panel_render', 'hynav_panel_render');

function hynav_panel_render() {
?>
<div class="hyplus-nav-container hyplus-unselectable">
	<div class="hyplus-nav-section">
		<div class="hyplus-nav-title">综合大模型</div>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				<a href="https://chat.deepseek.com" class="hyplus-nav-link" target="_blank">DeepSeek</a>
				<a href="https://www.doubao.com/" class="hyplus-nav-link" target="_blank">豆包</a>
				<a href="https://kimi.moonshot.cn" class="hyplus-nav-link" target="_blank">KIMI</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://gptgod.online/" class="hyplus-nav-link" target="_blank">GPT-GOD</a>
				<a href="https://chatgpt.com/" class="hyplus-nav-link" target="_blank">ChatGPT</a>
				<a href="https://gemini.google.com/" class="hyplus-nav-link" target="_blank">Gemini</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://www.coze.cn/home" class="hyplus-nav-link" target="_blank">Coze</a>
				<a href="https://open.bigmodel.cn/console/overview" class="hyplus-nav-link" target="_blank">智谱AI</a>
				<a href="https://console.bce.baidu.com/#/index/overview" class="hyplus-nav-link" target="_blank">百度智能云</a>
			</div>
		</div>
	</div>

	<div class="hyplus-nav-section">
		<div class="hyplus-nav-title">控制台</div>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				<a href="https://www.aliyun.com/benefit/select/ecs" class="hyplus-nav-link" target="_blank">阿里云</a>
				<a href="https://www.bt.cn/login.html?ReturnUrl=https://www.bt.cn/admin/servers" class="hyplus-nav-link" target="_blank">宝塔面板</a>
				<a href="https://hutaocards.com/home" class="hyplus-nav-link" target="_blank">Hutao</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://onedrive.live.com/?view=0" class="hyplus-nav-link" target="_blank">OneDrive</a>
				<a href="https://drive.google.com/drive/my-drive" class="hyplus-nav-link" target="_blank">谷歌云端硬盘</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://mail.google.com/" class="hyplus-nav-link" target="_blank">Gmail</a>
				<a href="https://wx.mail.qq.com/home/index" class="hyplus-nav-link" target="_blank">QQ邮箱</a>
				<?php if (current_user_can('administrator')): ?>
				<a href="https://mail.hzcu.edu.cn/" class="hyplus-nav-link" target="_blank">HZCU邮箱</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="hyplus-nav-section">
		<div class="hyplus-nav-title">实用工具·极</div>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				<a href="https://www.photopea.com/" class="hyplus-nav-link" target="_blank">Photopea</a>
				<a href="https://www.iloveimg.com/photo-editor" class="hyplus-nav-link" target="_blank">图片编辑</a>
				<a href="https://dewatermark.ai/" class="hyplus-nav-link" target="_blank">在线去水印</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://www.latexlive.com" class="hyplus-nav-link" target="_blank">LaTeXLive</a>
				<a href="https://app.diagrams.net/" class="hyplus-nav-link" target="_blank">draw.io</a>
				<a href="https://translate.google.com/?hl=zh-cn&sl=auto&tl=zh-CN&op=translate" class="hyplus-nav-link" target="_blank">谷歌翻译</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://tool.ip138.com/underlinehump/" class="hyplus-nav-link" target="_blank">iP138(驼峰)</a>
				<a href="https://www.matools.com" class="hyplus-nav-link" target="_blank">MaTools</a>
				<a href="https://www.hionlinetools.com/zh-cn/tool/html-compress" class="hyplus-nav-link" target="_blank">Hi,Online Tools</a>
				<a href="https://htmlmarkdown.com/" class="hyplus-nav-link" target="_blank">HTML-MD互转</a>
			</div>
		</div>
	</div>

	<div class="hyplus-nav-section">
		<div class="hyplus-nav-title">信息检索</div>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				<a href="https://www.baidu.com" class="hyplus-nav-link" target="_blank">百度</a>
				<a href="https://www.google.com.hk" class="hyplus-nav-link" target="_blank">谷歌</a>
				<a href="https://www.bing.com" class="hyplus-nav-link" target="_blank">Bing</a>
				<a href="https://akira.blog.csdn.net/" class="hyplus-nav-link" target="_blank">CSDN</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://x.com/home" class="hyplus-nav-link" target="_blank">𝕏</a>
				<a href="https://weibo.com/" class="hyplus-nav-link" target="_blank">微博</a>
				<a href="https://www.bilibili.com" class="hyplus-nav-link" target="_blank">Bilibili</a>
				<a href="https://www.youtube.com" class="hyplus-nav-link" target="_blank">Youtube</a>
				<a href="https://www.reddit.com" class="hyplus-nav-link" target="_blank">Reddit</a>
				<a href="https://github.com/hyperplasma" class="hyplus-nav-link" target="_blank">GitHub</a>
				<a href="https://gitee.com/hyperplasma" class="hyplus-nav-link" target="_blank">Gitee</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://scholar.google.com.hk" class="hyplus-nav-link" target="_blank">谷歌学术</a>
				<a href="https://www.zhipin.com/" class="hyplus-nav-link" target="_blank">BOSS</a>
				<a href="https://www.ccf.org.cn/Academic_Evaluation/By_category/" class="hyplus-nav-link" target="_blank">CCF推荐目录</a>
			</div>
		</div>
	</div>

	<div class="hyplus-nav-section">
		<div class="hyplus-nav-title">其他工具/资源</div>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				<a href="https://perchance.org/ai-story-generator" class="hyplus-nav-link" target="_blank">Perchance</a>
				<a href="https://toolbaz.com/writer/ai-story-generator" class="hyplus-nav-link" target="_blank">ToolBaz</a>
				<a href="https://deepai.org/machine-learning-model/text2img" class="hyplus-nav-link" target="_blank">DeepAI</a>
				<a href="https://boredhumans.com/photo_story.php" class="hyplus-nav-link" target="_blank">Photo2Story</a>
				<a href="https://anifun.ai/manga-translator/" class="hyplus-nav-link" target="_blank">Manga TL</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://cn.overleaf.com/" class="hyplus-nav-link" target="_blank">Overleaf</a>
				<a href="https://colab.research.google.com" class="hyplus-nav-link" target="_blank">Google Colab</a>
				<a href="https://www.kaggle.com" class="hyplus-nav-link" target="_blank">Kaggle</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://www.tiobe.com/tiobe-index/" class="hyplus-nav-link" target="_blank">TIOBE</a>
			</div>
		</div>
	</div>

	<div class="hyplus-nav-section">
		<div class="hyplus-nav-title">非常规搜索引擎</div>
		<div class="hyplus-nav-links">
			<div class="hyplus-nav-group">
				<a href="https://xclient.info" class="hyplus-nav-link" target="_blank">Xclient</a>
				<a href="https://mac.filehorse.com" class="hyplus-nav-link" target="_blank">FileHorse</a>
				<a href="https://www.macbed.com" class="hyplus-nav-link" target="_blank">AppKed</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://www.duplichecker.com" class="hyplus-nav-link" target="_blank">Duplichecker</a>
				<a href="https://ascii2d.net" class="hyplus-nav-link" target="_blank">画像詳細検索</a>
				<a href="https://web.archive.org/" class="hyplus-nav-link" target="_blank">Wayback Machine</a>
			</div>
			<div class="hyplus-nav-group">
				<a href="https://steamdb.info/" class="hyplus-nav-link" target="_blank">SteamDB</a>
				<a href="https://www.steamgriddb.com" class="hyplus-nav-link" target="_blank">SteamGridDB</a>
			</div>
		</div>
	</div>
</div>
<?php
}
?>