<!-- HyProgBar - 进度条遮罩层及相关内容（引言）
 Code Type: HTML
 Current issue: 对锚点链接、各类表单提交的处理不够完善，暂将其排除。
-->
<div id="pageLeavingMask" class="hyplus-unselectable" style="opacity:1;display:none;">
	<div id="pageLeavingProgressWrapper">
		<div id="pageLeavingProgressCircle">
			<svg width="104" height="104">
				<circle cx="52" cy="52" r="46" fill="none" stroke="#e9e9e9" stroke-width="10"/>
				<circle id="pageLeavingProgressCircleBar" cx="52" cy="52" r="46" fill="none" stroke="#2196f3" stroke-width="10"
						stroke-linecap="round" stroke-dasharray="289" stroke-dashoffset="289" />
			</svg>
			<div id="pageLeavingProgressPercent">0%</div>
		</div>
	</div>
	<div id="pageLeavingQuoteWrapper">
		<div id="pageLeavingQuote"></div>
	</div>
</div>
<style>
	#pageLeavingMask {
		position: fixed; top:0; left:0; width:100vw; height:100vh;
		background: #fff; z-index: 1111110;
		display: flex; justify-content: center; align-items: center; flex-direction: column;
		opacity: 1;
		transition: opacity 0.38s cubic-bezier(.4,0,.2,1);
		pointer-events: auto;
		overflow: hidden;
		touch-action: none;
	}
	#pageLeavingProgressWrapper {
		width: 130px; max-width: 85vw; margin-bottom: 32px; text-align: center;
		display: flex; justify-content: center; align-items: center;
	}
	#pageLeavingProgressCircle {
		position: relative; width: 104px; height: 104px; display: inline-block;
	}
	#pageLeavingProgressPercent {
		position: absolute;
		left: 0; top: 0; width: 104px; height: 104px;
		display: flex; justify-content: center; align-items: center;
		font-size: 24px; color: #2196f3; font-family: monospace; font-weight: bold;
		pointer-events: none;
		user-select: none;
	}
	#pageLeavingQuoteWrapper { width: 100%; text-align: center; margin-bottom: 20px;}
	#pageLeavingQuote {
		font-family: "行楷", "楷体", "STKaiti", "华文楷体", "仿宋", "STFangsong", "黑体", "STHeiti", sans-serif;
		font-size: 28px; color: #333; padding: 20px; max-width: 80%; margin: 0 auto; line-height: 1.8; letter-spacing: 1px;
	}
</style>
<script>
	(function(){
		const MASK_ID = 'pageLeavingMask';
		const QUOTE_STORAGE_KEY = 'localQuotes';
		const QUOTE_URL = 'https://www.hyperplasma.top/hpsrc/selection.txt';
		const QUOTE_CUR_KEY = 'pageLeavingQuoteCur';
		const LEAVING_FLAG = 'pageLeavingNeedShow';
		const LEAVING_PROGRESS = 'pageLeavingProgress';
		const RELOAD_FLAG = 'pageLeavingManualReload';

		const mask = document.getElementById(MASK_ID);
		const quoteEl = document.getElementById('pageLeavingQuote');
		const progressCircle = document.getElementById('pageLeavingProgressCircleBar');
		const progressText = document.getElementById('pageLeavingProgressPercent');

		let quotesCache = null;
		let progressTimer = null;
		let jumping = false;

		// 圆环参数
		const CIRCLE_RADIUS = 46;
		const CIRCLE_LEN = 2 * Math.PI * CIRCLE_RADIUS;
		// 动画参数
		const PROGRESS_RAND_MIN = 30;
		const PROGRESS_RAND_MAX = 40;
		const PROGRESS_ANIM_INTERVAL = 16;
		const PROGRESS_ANIM_TIME_BEFORE = 260;
		const PROGRESS_ANIM_TIME_AFTER = 440;

		// 滚动阻止
		function blockScroll() {
			mask.addEventListener('wheel', blockScrollHandler, {passive: false});
			mask.addEventListener('touchmove', blockScrollHandler, {passive: false});
		}
		function unblockScroll() {
			mask.removeEventListener('wheel', blockScrollHandler, {passive: false});
			mask.removeEventListener('touchmove', blockScrollHandler, {passive: false});
		}
		function blockScrollHandler(e) {
			e.preventDefault();
			return false;
		}

		// --- 引言管理 ---
		function getCurQuote() {
			return sessionStorage.getItem(QUOTE_CUR_KEY);
		}
		function setCurQuote(val) {
			if (val) sessionStorage.setItem(QUOTE_CUR_KEY, val);
			else sessionStorage.removeItem(QUOTE_CUR_KEY);
		}
		function showQuote(q) {
			quoteEl.textContent = q || '正在加载...';
		}
		function getLocalQuotes() {
			const stored = localStorage.getItem(QUOTE_STORAGE_KEY);
			if (!stored) return null;
			try {
				const arr = JSON.parse(stored);
				return Array.isArray(arr) && arr.length ? arr : null;
			} catch(e) { localStorage.removeItem(QUOTE_STORAGE_KEY); return null; }
		}
		function setLocalQuotes(arr) {
			try { if (Array.isArray(arr) && arr.length) localStorage.setItem(QUOTE_STORAGE_KEY, JSON.stringify(arr)); } catch(e){}
		}
		function randomQuote(arr) {
			if (!arr || !arr.length) return '正在加载...';
			return arr[Math.floor(Math.random() * arr.length)];
		}
		function loadAndCacheQuotes(cb) {
			if (quotesCache) { cb(quotesCache); return; }
			const localQuotes = getLocalQuotes();
			if (localQuotes) { quotesCache = localQuotes; cb(localQuotes); return; }
			fetch(QUOTE_URL).then(res => res.text()).then(text => {
				const arr = text.split('\n').map(q => q.trim()).filter(Boolean);
				if (arr.length) { setLocalQuotes(arr); quotesCache = arr; cb(arr); }
				else { cb(['正在加载...']); }
			}).catch(() => cb(['正在加载...']));
		}

		function setProgress(val) {
			val = Math.max(0, Math.min(100, Math.round(val)));
			progressCircle.setAttribute('stroke-dashoffset', CIRCLE_LEN - CIRCLE_LEN * (val/100));
			progressText.textContent = val + '%';
		}
		function clearAllTimers() {
			if (progressTimer) clearInterval(progressTimer);
			progressTimer = null;
		}
		function showMaskAndProgress(startVal, fadeIn = true) {
			mask.style.display = 'flex';
			if (fadeIn) {
				mask.style.opacity = '0';
				void mask.offsetWidth;
				mask.style.opacity = '1';
			} else {
				mask.style.opacity = '1';
			}
			blockScroll();
			const cur = getCurQuote();
			if (cur) showQuote(cur);
			else {
				loadAndCacheQuotes(arr => {
					const q = randomQuote(arr);
					setCurQuote(q);
					showQuote(q);
				});
			}
			setProgress(startVal || 0);
		}
		function animateProgress(from, to, duration, cb) {
			clearAllTimers();
			let start = from, end = to;
			let t0 = Date.now();
			progressTimer = setInterval(function(){
				let dt = Date.now() - t0;
				let percent = Math.min(1, dt/duration);
				let val = Math.round(start + (end-start)*percent);
				setProgress(val);
				if (dt >= duration) {
					setProgress(end);
					clearAllTimers();
					if (cb) cb();
				}
			}, PROGRESS_ANIM_INTERVAL);
		}
		function fadeAndHideMask(cb) {
			mask.style.opacity = '0';
			setTimeout(function(){
				mask.style.display = 'none';
				unblockScroll();
				clearAllTimers();
				if (cb) cb();
			}, 400);
		}

		// 跳转前动画
		function animateAndJump(href) {
			if (jumping) return; jumping = true;
			const randTarget = Math.floor(PROGRESS_RAND_MIN + Math.random() * (PROGRESS_RAND_MAX - PROGRESS_RAND_MIN + 1));
			showMaskAndProgress(0, true);
			animateProgress(0, randTarget, PROGRESS_ANIM_TIME_BEFORE, function(){
				sessionStorage.setItem(LEAVING_FLAG, '1');
				sessionStorage.setItem(LEAVING_PROGRESS, randTarget + 1);
				window.location.href = href;
			});
		}
		window.animateAndJump = animateAndJump; // 暴露到全局，便于自定义调用

		// 新页面动画
		function handleNewPage() {
			if (sessionStorage.getItem(LEAVING_FLAG) === '1') {
				let prog = parseInt(sessionStorage.getItem(LEAVING_PROGRESS), 10);
				if (isNaN(prog) || prog < PROGRESS_RAND_MIN) prog = PROGRESS_RAND_MIN+1;
				showMaskAndProgress(prog, false);
				setTimeout(function(){
					animateProgress(prog, 100, PROGRESS_ANIM_TIME_AFTER, function(){
						setTimeout(function(){
							fadeAndHideMask(function(){
								sessionStorage.removeItem(LEAVING_FLAG);
								sessionStorage.removeItem(LEAVING_PROGRESS);
								loadAndCacheQuotes(arr => setCurQuote(randomQuote(arr)));
							});
						}, 120);
					});
				}, 60);
			}
			// 手动刷新后的加载，仅做淡出动画
			else if (sessionStorage.getItem(RELOAD_FLAG) === '1') {
				sessionStorage.removeItem(RELOAD_FLAG);
				showMaskAndProgress(0, false); // 直接显示遮罩，无淡入
				setTimeout(function(){
					animateProgress(0, 100, PROGRESS_ANIM_TIME_AFTER, function(){
						setTimeout(function(){
							fadeAndHideMask(function(){
								loadAndCacheQuotes(arr => setCurQuote(randomQuote(arr)));
							});
						}, 120);
					});
				}, 60);
			}
		}
		handleNewPage();

		function isRealPageJump(a) {
			if (!a || a.tagName !== 'A') return false;
			const href = a.getAttribute('href');
			if (!href) return false;
			if (/^#/.test(href)) return false;
			if (/^javascript:/i.test(href)) return false;
			if (!a.href.startsWith(location.origin)) return false;
			return true;
		}

		// 判断是否为搜索链接
		function isSearchLink(href) {
			return href && href.includes('?s=');
		}

		// 判断是否为登录相关链接或表单
		function isLoginRelated(target) {
			if (target.tagName === 'A') {
				const href = target.getAttribute('href');
				return href && (href.includes('login') || href.includes('logout') || href.includes('register'));
			} else if (target.closest('form')) {
				const form = target.closest('form');
				const action = form.getAttribute('action') || '';
				return action.includes('login') || action.includes('logout') || action.includes('register') || 
					   form.classList.contains('login-form') || form.id === 'login-form';
			}
			return false;
		}

		// 判断是否为评论表单（基于你的HTML结构）
		function isCommentForm(form) {
			return form && (
				form.id === 'commentform' ||
				form.classList.contains('comment-form') ||
				form.querySelector('textarea[name="comment"]') ||
				form.querySelector('input[name="submit"][value="发表评论"]') ||
				form.closest('#respond')
			);
		}

		document.addEventListener('click', function(e){
			let t = e.target;
			while (t && t !== document) {
				// 排除primary-menu中的链接
				if (t.closest('#primary-menu > ul > li > a')) break;
				
				// 排除搜索链接和登录相关链接
				if (t.tagName === 'A') {
					const href = t.getAttribute('href');
					if (isSearchLink(href) || isLoginRelated(t)) {
						break;
					}
				}
				
				if (
					t.tagName === 'A' &&
					isRealPageJump(t) &&
					t.target !== '_blank' &&
					!t.hasAttribute('download')
				) {
					if (e.ctrlKey || e.shiftKey || e.metaKey || e.altKey || e.button !== 0) break;
					e.preventDefault();
					animateAndJump(t.href);
					return;
				}
				if (t.hasAttribute && t.hasAttribute('data-href')) {
					e.preventDefault();
					animateAndJump(t.getAttribute('data-href'));
					return;
				}
				t = t.parentNode;
			}
		}, true);

		document.addEventListener('submit', function(e){
			// 排除搜索表单、登录表单和评论表单
			const form = e.target.closest('form');
			if (!form) return;
			
			// 检测搜索表单
			const isSearchForm = form.getAttribute('action')?.includes('?s=') || form.querySelector('input[name="s"]');
			
			// 检测登录表单
			const isLoginForm = form.getAttribute('action')?.includes('login') || 
								form.classList.contains('login-form') || 
								form.id === 'login-form';
			
			// 检测评论表单（基于你的HTML结构）
			const isCommentForm = 
				form.id === 'commentform' ||
				form.classList.contains('comment-form') ||
				form.querySelector('textarea[name="comment"]') ||
				form.querySelector('input[name="submit"][value="发表评论"]') ||
				form.closest('#respond');
			
			if (isSearchForm || isLoginForm || isCommentForm) {
				// 允许正常提交
				return;
			}
			
			// 其他表单触发遮罩层效果
			e.preventDefault();
			animateAndJump(location.href);
		}, true);

		// 检测手动刷新：F5、Ctrl+R、Cmd+R、浏览器刷新按钮
		window.addEventListener('beforeunload', function(e) {
			// 判断刷新而不是跳转（只有刷新时，LEAVING_FLAG未被设置）
			if (!sessionStorage.getItem(LEAVING_FLAG)) {
				try {
					sessionStorage.setItem(RELOAD_FLAG, '1');
				} catch(e){}
			}
			// 不阻止默认
		});
	})();
</script>