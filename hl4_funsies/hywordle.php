<!-- HyWordle - Wordle Game
 Code Type: HTML + PHP
 Shortcode: [wpcode id="14656"] (auto-generated)
-->

<?php
// 启动 session
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// ===== 最优先：检查AJAX请求 =====
$is_wordle_ajax_request = (isset($_POST['hywordle_action']) || isset($_GET['hywordle_action']));

if ($is_wordle_ajax_request) {
	// 防止任何额外输出
	error_reporting(0);
	ini_set('display_errors', 0);
	
	// 清除缓冲区
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	
	// 设置JSON响应
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: no-cache, no-store, must-revalidate');
	
	// 获取动作
	$action = isset($_POST['hywordle_action']) ? $_POST['hywordle_action'] : $_GET['hywordle_action'];
	
	// 定义所需函数（在AJAX请求中）
	if (!function_exists('getWordleWordList')) {
		function getWordleWordList() {
			static $GLOBALS_cache = null;
			if (!empty($GLOBALS_cache)) {
				return $GLOBALS_cache;
			}
			
			$url = 'wp-content/uploads/hyplus-assets/valid-wordle-words.txt';
			$words = @file($url, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			
			if ($words === false || empty($words)) {
				$GLOBALS_cache = array('HYPER');
				return $GLOBALS_cache;
			}
			
			$cleanWords = array();
			foreach ($words as $word) {
				$cleanWord = strtoupper(trim($word));
				if (strlen($cleanWord) === 5 && !empty($cleanWord)) {
					$cleanWords[] = $cleanWord;
				}
			}
			
			if (empty($cleanWords)) {
				$cleanWords = array('HYPER');
			}
			
			$GLOBALS_cache = $cleanWords;
			return $cleanWords;
		}
	}
	
	// ===== 测试AJAX连接 =====
	if ($action === 'test') {
		echo json_encode(['status' => 'ok', 'message' => 'AJAX working']);
		exit;
	}
	
	// ===== 验证单词 =====
	if ($action === 'validate_word') {
		$word = isset($_POST['word']) ? strtoupper(trim($_POST['word'])) : '';
		$wordList = getWordleWordList();
		$valid = (strlen($word) === 5) && in_array($word, $wordList, true);
		echo json_encode(['valid' => $valid]);
		exit;
	}
	
	// ===== 检查猜测 =====
	if ($action === 'check_guess') {
		$guess = isset($_POST['guess']) ? strtoupper(trim($_POST['guess'])) : '';
		$mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'standard';
		
		// 为AJAX请求定义所需函数
		if (!function_exists('getDailyWordle')) {
			function getDailyWordle() {
				$words = getWordleWordList();
				if (empty($words)) {
					return 'HYPER';
				}
				// 使用今天的日期作为随机种子
				$seed = (int)date('Ymd');
				srand($seed);
				return $words[array_rand($words)];
			}
		}
		
		if (!function_exists('getRandomWordle')) {
			function getRandomWordle() {
				$words = getWordleWordList();
				if (empty($words)) {
					return 'HYPER';
				}
				return $words[array_rand($words)];
			}
		}
		
		// 检查答案是否已初始化，如果没有则根据模式初始化
		if (!isset($_SESSION['hywordle_answer'])) {
			if ($mode === 'daily') {
				$_SESSION['hywordle_answer'] = getDailyWordle();
			} else {
				$_SESSION['hywordle_answer'] = getRandomWordle();
			}
		}
		
		$answer = $_SESSION['hywordle_answer'];
		
		if (strlen($guess) !== 5) {
			echo json_encode(['error' => 'Invalid word length']);
			exit;
		}
		
		// 计算状态
		$states = array_fill(0, 5, 'absent');
		$answerLetters = str_split($answer);
		$guessLetters = str_split($guess);
		$usedLetters = array_fill(0, 5, false);
		
		// 第一遍：正确位置
		for ($i = 0; $i < 5; $i++) {
			if ($guessLetters[$i] === $answerLetters[$i]) {
				$states[$i] = 'correct';
				$usedLetters[$i] = true;
			}
		}
		
		// 第二遍：错误位置
		for ($i = 0; $i < 5; $i++) {
			if ($states[$i] === 'absent') {
				for ($j = 0; $j < 5; $j++) {
					if (!$usedLetters[$j] && $guessLetters[$i] === $answerLetters[$j]) {
						$states[$i] = 'present';
						$usedLetters[$j] = true;
						break;
					}
				}
			}
		}
		
		// 初始化猜测历史（如果还没有）
		if (!isset($_SESSION['hywordle_guesses'])) {
			$_SESSION['hywordle_guesses'] = array();
		}
		$_SESSION['hywordle_guesses'][] = $guess;
		
		// 判断游戏是否真正结束
		$guessCount = count($_SESSION['hywordle_guesses']);
		$isCorrect = ($guess === $answer);
		$gameEnded = $isCorrect || ($guessCount >= 6);
		
		$response = [
			'states' => $states,
			'correct' => $isCorrect
		];
		
		// 只在游戏结束时才返回答案（防止玩家通过浏览器开发者工具偷看）
		if ($gameEnded) {
			$response['answer'] = $answer;
		}
		
		echo json_encode($response);
		exit;
	}
	
	// 未知动作
	echo json_encode(['error' => 'Unknown action: ' . htmlspecialchars($action)]);
	exit;
}

// 使用全局变量缓存词库（对每个请求有效）
$GLOBALS['_hywordle_word_list'] = null;

function getWordleWordList() {
	// 尝试从全局缓存获取
	if (!empty($GLOBALS['_hywordle_word_list'])) {
		return $GLOBALS['_hywordle_word_list'];
	}
	
	$url = 'https://www.hyperplasma.top/hpsrc/valid-wordle-words.txt';
	$words = @file($url, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	if ($words === false || empty($words)) {
		// 如果加载失败，使用备用单词
		$GLOBALS['_hywordle_word_list'] = array('HYPER');
		return $GLOBALS['_hywordle_word_list'];
	}
	
	// 转换为大写并清理
	$cleanWords = array();
	foreach ($words as $word) {
		$cleanWord = strtoupper(trim($word));
		// 只添加5字母的单词
		if (strlen($cleanWord) === 5 && !empty($cleanWord)) {
			$cleanWords[] = $cleanWord;
		}
	}
	
	// 如果最终没有有效单词，使用备用单词
	if (empty($cleanWords)) {
		$cleanWords = array('HYPER');
	}
	
	$GLOBALS['_hywordle_word_list'] = $cleanWords;
	return $cleanWords;
}

// 获取随机单词
function getRandomWordle() {
	$words = getWordleWordList();
	
	if (empty($words)) {
		return 'HYPER'; // 最后的备用词
	}
	
	return $words[array_rand($words)];
}

// 根据日期获取今日单词（确定性）
function getDailyWordle() {
	$words = getWordleWordList();
	
	if (empty($words)) {
		return 'HYPER'; // 最后的备用词
	}
	
	// 使用今天的日期作为随机种子
	$seed = (int)date('Ymd'); // 例如：20251222
	srand($seed);
	
	// 现在调用 array_rand 会基于固定种子生成相同的随机数
	return $words[array_rand($words)];
}

// 初始化或获取当前游戏的答案
function getGameAnswer($mode = 'standard') {
	if (!isset($_SESSION['hywordle_answer'])) {
		if ($mode === 'daily') {
			$_SESSION['hywordle_answer'] = getDailyWordle();
		} else {
			$_SESSION['hywordle_answer'] = getRandomWordle();
		}
	}
	return $_SESSION['hywordle_answer'];
}

// ===== 正常页面加载 =====
// 获取游戏模式（如果有GET参数，否则从session获取）
$gameMode = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : (isset($_SESSION['hywordle_mode']) ? $_SESSION['hywordle_mode'] : 'standard');

// 为每次页面加载生成游戏ID
// 对于Daily模式，使用日期作为ID以确保整天内固定答案
// 对于Standard模式，使用随机ID以每次获得不同答案
if ($gameMode === 'daily') {
	$gameId = date('Ymd'); // 日期作为ID：20251222
} else {
	$gameId = bin2hex(random_bytes(8)); // 随机ID
}

// 如果是新游戏，清除旧的答案和游戏状态
if (!isset($_SESSION['hywordle_game_id']) || $_SESSION['hywordle_game_id'] !== $gameId) {
	$_SESSION['hywordle_game_id'] = $gameId;
	$_SESSION['hywordle_mode'] = $gameMode;
	unset($_SESSION['hywordle_answer']);
	unset($_SESSION['hywordle_guesses']);
}

// 获取当前游戏答案
$answer = isset($_SESSION['hywordle_answer']) ? $_SESSION['hywordle_answer'] : '';
// 获取AJAX处理URL（当前页面URL）
$ajax_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>

<div class="hywordle-container">
	<!-- Mode Selection Screen -->
	<div class="hywordle-mode-selection" id="wordleModeSelection">
		<div class="mode-selection-content">
			<h2 class="mode-selection-title">Choose Game Mode</h2>
			<div class="mode-cards-container">
				<button class="mode-card" id="modeStandard" onclick="selectGameMode('standard')">
					<div class="mode-icon">🎲</div>
					<div class="mode-name">Standard</div>
					<div class="mode-description">Guess a random 5-letter word!</div>
				</button>
				<button class="mode-card" id="modeDaily" onclick="selectGameMode('daily')">
					<div class="mode-icon">📅</div>
					<div class="mode-name">Daily Word</div>
					<div class="mode-description">Progress every day!</div>
				</button>
			</div>
		</div>
	</div>

	<div class="hywordle-game" id="wordleGameContainer" style="display: none;">
		<!-- Header -->
		<div class="wordle-header">
			<h1 class="wordle-title" id="wordleTitle">Standard</h1>
			<p class="wordle-subtitle" id="wordleSubtitle">Guess a random 5-letter word!</p>
		</div>

		<!-- Game Board -->
		<div class="wordle-board">
			<div class="wordle-grid" id="wordleGrid"></div>
		</div>

		<!-- Virtual Keyboard -->
		<div class="wordle-keyboard" id="wordleKeyboard"></div>

		<!-- Game Status -->
		<div class="wordle-status hyplus-unselectable" id="wordleStatus" style="display: none;">
			<div class="status-message" id="statusMessage"></div>
			<button class="wordle-restart-btn" onclick="if(confirm('Return to Mode Selection?')) location.reload();">Return to Menu</button>
		</div>

		<!-- Game Guide -->
		<div class="wordle-guide hyplus-unselectable">
			<div class="guide-item">
				<div class="guide-color guide-gray"></div>
				<span>Gray - Letter not in word</span>
			</div>
			<div class="guide-item">
				<div class="guide-color guide-yellow"></div>
				<span>Yellow - Letter in word, wrong spot</span>
			</div>
			<div class="guide-item">
				<div class="guide-color guide-green"></div>
				<span>Green - Letter in correct spot</span>
			</div>
		</div>
	</div>
	<div class="game-version hyplus-unselectable">HyWordle v0.1</div>
</div>

<style>
	.hywordle-container {
		width: 100%;
		max-width: 1000px;
		margin: 20px auto;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
	}

	/* Mode Selection Styles */
	.hywordle-mode-selection {
		width: 100%;
		max-width: 1000px;
		margin: 20px auto;
		padding: 20px;
		background: #f9f9f9;
		border-radius: 12px;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.mode-selection-content {
		width: 100%;
		text-align: center;
	}

	.mode-selection-title {
		margin: 0 0 30px 0;
		font-size: 28px;
		font-weight: 800;
		color: #3b4d7a;
		letter-spacing: 1px;
	}

	.mode-cards-container {
		display: flex;
		gap: 20px;
		justify-content: center;
		flex-wrap: wrap;
	}

	.mode-card {
		flex: 0 1 auto;
		width: 200px;
		padding: 30px 20px;
		border: 2px solid #d3d6da;
		border-radius: 12px;
		background: white;
		cursor: pointer;
		transition: all 0.3s ease;
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 12px;
		font-family: inherit;
		outline: none;
	}

	.mode-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
		border-color: #1976d2;
		background: white;
	}

	.mode-card:active {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
		background: white;
	}

	.mode-icon {
		font-size: 48px;
		margin-bottom: 8px;
	}

	.mode-name {
		font-size: 20px;
		font-weight: 700;
		color: #1a1a1a;
		letter-spacing: 0.5px;
	}

	.mode-description {
		font-size: 13px;
		color: #666;
		line-height: 1.4;
	}

	@media (max-width: 568px) {
		.hywordle-mode-selection {
			padding: 15px;
		}

		.mode-selection-title {
			font-size: 22px;
			margin-bottom: 20px;
		}

		.mode-cards-container {
			gap: 12px;
		}

		.mode-card {
			width: 160px;
			padding: 20px 15px;
			gap: 10px;
		}

		.mode-icon {
			font-size: 36px;
			margin-bottom: 4px;
		}

		.mode-name {
			font-size: 16px;
		}

		.mode-description {
			font-size: 12px;
		}
	}

	.hywordle-game {
		padding: 20px;
		background: #f9f9f9;
		border-radius: 12px;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		display: flex;
		flex-direction: column;
		gap: 20px;
	}

	/* Header */
	.wordle-header {
		text-align: center;
		padding: 10px 0;
		border-bottom: 2px solid #e0e0e0;
	}

	.wordle-title {
		margin: 0;
		font-size: 32px;
		font-weight: 800;
		color: #3b4d7a;
		letter-spacing: 2px;
	}

	.wordle-subtitle {
		margin: 6px 0 0 0;
		font-size: 14px;
		color: #999;
		letter-spacing: 0.5px;
	}

	/* Game Board */
	.wordle-board {
		padding: 20px;
		background: white;
		border-radius: 8px;
		box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
	}

	.wordle-grid {
		display: grid;
		grid-template-columns: repeat(5, 1fr);
		gap: 8px;
		width: fit-content;
		margin: 0 auto;
	}

	.wordle-row {
		display: contents;
	}

	.wordle-tile {
		width: 50px;
		height: 50px;
		display: flex;
		align-items: center;
		justify-content: center;
		border: 2px solid #d3d6da;
		border-radius: 6px;
		font-size: 24px;
		font-weight: 700;
		color: #1a1a1a;
		background: white;
		transition: all 0.2s ease;
		user-select: none;
	}

	.wordle-tile.filled {
		border-color: #878a8c;
		transform: scale(1.05);
	}

	.wordle-tile.revealed {
		border: 0;
		transform: scale(1);
		opacity: 1;
	}

	.wordle-tile.correct {
		background: #6aaa64;
		color: white;
		border-color: #6aaa64;
	}

	.wordle-tile.present {
		background: #b59f3b;
		color: white;
		border-color: #b59f3b;
	}

	.wordle-tile.absent {
		background: #787c7e;
		color: white;
		border-color: #787c7e;
	}

	/* Virtual Keyboard */
	.wordle-keyboard {
		display: flex;
		flex-direction: column;
		gap: 8px;
		padding: 0;
		margin: 0 auto;
	}

	.keyboard-row:last-of-type {
		margin-bottom: 4px;
	}

	.keyboard-row {
		display: flex;
		gap: 6px;
		justify-content: center;
		flex-wrap: nowrap;
	}

	.keyboard-action-row {
		display: flex;
		gap: 6px;
		justify-content: center;
		flex-wrap: nowrap;
	}

	.keyboard-key {
		flex: 0 1 auto;
		min-width: 35px;
		min-height: 44px;
		height: 44px;
		padding: 0 4px;
		border: 2px solid transparent;
		border-radius: 6px;
		background: #d3d6da;
		color: #1a1a1a;
		font-size: 13px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.15s ease;
		user-select: none;
		display: flex;
		align-items: center;
		justify-content: center;
		outline: none;
	}

	.keyboard-key:focus {
		outline: none;
	}

	.keyboard-key:hover {
		transform: translateY(-2px);
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
	}

	.keyboard-key:not(.correct):not(.present):not(.absent):not(.backspace):not(.enter):hover {
		background: #1976d2;
		color: white;
		border: 2px solid #1976d2;
	}

	.keyboard-key:active {
		transform: translateY(0);
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
		border: 2px solid #000;
	}

	.keyboard-key:not(.correct):not(.present):not(.absent):not(.backspace):not(.enter):active {
		background: #1976d2;
		color: white;
	}

	.keyboard-key.correct {
		background: #6aaa64;
		color: white;
	}

	.keyboard-key.correct:hover {
		background: #6aaa64;
		color: white;
		transform: translateY(-2px);
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
	}

	.keyboard-key.correct:active {
		background: #6aaa64;
		color: white;
		border: 2px solid #000;
	}

	.keyboard-key.present {
		background: #b59f3b;
		color: white;
	}

	.keyboard-key.present:hover {
		background: #b59f3b;
		color: white;
		transform: translateY(-2px);
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
	}

	.keyboard-key.present:active {
		background: #b59f3b;
		color: white;
		border: 2px solid #000;
	}

	.keyboard-key.absent {
		background: #787c7e;
		color: white;
	}

	.keyboard-key.absent:hover {
		background: #787c7e;
		color: white;
		transform: translateY(-2px);
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
	}

	.keyboard-key.absent:active {
		background: #787c7e;
		color: white;
		border: 2px solid #000;
	}

	.keyboard-key.enter,
	.keyboard-key.backspace {
		flex: 1 1 0;
		min-width: 40px;
		font-size: 12px;
		font-weight: 700;
		letter-spacing: 0.5px;
	}

	.keyboard-key.backspace {
		background: #d32f2f;
		color: white;
	}

	.keyboard-key.backspace:hover {
		background: #b71c1c;
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(240, 128, 128, 0.3);
	}

	.keyboard-key.backspace:active {
		background: #b71c1c;
		color: white;
		border: 2px solid #000;
	}

	.keyboard-key.enter {
		background: #1976d2;
		color: white;
	}

	.keyboard-key.enter:hover {
		background: #1256a2;
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
	}

	.keyboard-key.enter:active {
		background: #1256a2;
		color: white;
		border: 2px solid #000;
	}

	/* Game Status */
	.wordle-status {
		padding: 20px;
		background: white;
		border-radius: 8px;
		text-align: center;
		border: 2px solid #d3d6da;
	}

	.status-message {
		font-size: 24px;
		font-weight: 700;
		color: #3b4d7a;
		margin-bottom: 16px;
		letter-spacing: 0.5px;
	}

	.wordle-restart-btn {
		padding: 12px 32px;
		border: none;
		border-radius: 6px;
		background: #1976d2;
		color: white;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: all 0.2s ease;
		box-shadow: 0 2px 8px rgba(25, 118, 210, 0.2);
		letter-spacing: 0.5px;
	}

	.wordle-restart-btn:hover {
		background: #1256a2;
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
	}

	.wordle-restart-btn:active {
		transform: translateY(0);
		box-shadow: 0 2px 6px rgba(25, 118, 210, 0.2);
	}

	/* Game Guide */
	.wordle-guide {
		display: flex;
		flex-direction: column;
		gap: 8px;
		padding: 12px;
		background: white;
		border-radius: 8px;
		border: 1px solid #e0e0e0;
		font-size: 13px;
		color: #666;
	}

	.guide-item {
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.guide-color {
		width: 24px;
		height: 24px;
		border-radius: 4px;
		flex-shrink: 0;
	}

	.guide-gray {
		background: #787c7e;
	}

	.guide-yellow {
		background: #b59f3b;
	}

	.guide-green {
		background: #6aaa64;
	}

	.game-version {
		margin-top: 10px;
		color: #aaa;
		font-size: 15px;
		font-family: inherit;
		user-select: none;
		letter-spacing: 1px;
		background: transparent;
		z-index: 2;
		align-self: flex-end;
		text-align: right;
		width: 100%;
	}

	/* Responsive Design */
	@media (max-width: 768px) {
		.hywordle-container {
			max-width: 100%;
			padding: 0 10px;
		}

		.hywordle-game {
			padding: 15px;
			gap: 12px;
		}

		.wordle-title {
			font-size: 24px;
		}

		.wordle-tile {
			width: 42px;
			height: 42px;
			font-size: 20px;
		}

		.wordle-board {
			padding: 15px;
		}

		.keyboard-key {
			flex: 0 1 auto;
			min-width: 30px;
			min-height: 40px;
			height: 40px;
			padding: 0 2px;
			font-size: 11px;
		}

		.wordle-grid {
			gap: 5px;
		}

		.wordle-keyboard {
			gap: 5px;
		}

		.keyboard-row {
			gap: 3px;
		}

		.keyboard-row:last-of-type {
			margin-bottom: 6px;
		}

		.guide-item {
			font-size: 12px;
			gap: 8px;
		}

		.guide-color {
			width: 20px;
			height: 20px;
		}
	}

	/* Extreme small screens (320px and below) */
	@media (max-width: 568px) {
		.hywordle-container {
			padding: 0 5px;
		}

		.hywordle-game {
			padding: 10px;
			gap: 10px;
		}

		.wordle-title {
			font-size: 20px;
			letter-spacing: 1px;
		}

		.wordle-subtitle {
			font-size: 12px;
		}

		.wordle-tile {
			width: 38px;
			height: 38px;
			font-size: 18px;
		}

		.wordle-board {
			padding: 10px;
		}

		.keyboard-key {
			flex: 0 1 auto;
			min-width: 26px;
			min-height: 36px;
			height: 36px;
			padding: 0 1px;
			font-size: 9px;
		}

		.keyboard-key.enter,
		.keyboard-key.backspace {
			min-width: 35px;
			font-size: 9px;
		}

		.wordle-grid {
			gap: 4px;
		}

		.wordle-keyboard {
			gap: 4px;
		}

		.keyboard-row {
			gap: 2px;
		}

		.keyboard-row:last-of-type {
			margin-bottom: 7px;
		}

		.status-message {
			font-size: 18px;
		}

		.wordle-restart-btn {
			padding: 10px 24px;
			font-size: 14px;
		}

		.wordle-guide {
			padding: 10px;
			gap: 6px;
		}

		.guide-item {
			font-size: 11px;
			gap: 6px;
		}

		.guide-color {
			width: 18px;
			height: 18px;
		}
	}
</style>

<script>
	// 从PHP获取AJAX处理URL
	const WORDLE_AJAX_URL = "<?php echo esc_attr($ajax_url); ?>";
	
	// 游戏模式（全局变量，由选择模式时设置）
	let WORDLE_GAME_MODE = 'standard';
	
	// Game constants
	const MAX_ATTEMPTS = 6;
	const WORD_LENGTH = 5;

	// Keyboard layout
	const KEYBOARD_LAYOUT = [
		['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'],
		['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'],
		['Z', 'X', 'C', 'V', 'B', 'N', 'M']
	];

	// 选择游戏模式
	function selectGameMode(mode) {
		WORDLE_GAME_MODE = mode;
		const modeSelectionDiv = document.getElementById('wordleModeSelection');
		const gameContainerDiv = document.getElementById('wordleGameContainer');
		
		// 更新Header中的模式信息
		const wordleTitle = document.getElementById('wordleTitle');
		const wordleSubtitle = document.getElementById('wordleSubtitle');
		
		if (mode === 'daily') {
			wordleTitle.textContent = 'Daily Word';
			wordleSubtitle.textContent = 'Progress every day!';
		} else {
			wordleTitle.textContent = 'Standard';
			wordleSubtitle.textContent = 'Guess a random 5-letter word!';
		}
		
		// 隐藏模式选择，显示游戏
		modeSelectionDiv.style.display = 'none';
		gameContainerDiv.style.display = 'flex';
		
		// 初始化游戏
		game = new WordleGame(mode);
	}

	class WordleGame {
		constructor(mode = 'standard') {
			this.mode = mode;
			this.attempts = 0;
			this.currentGuess = '';
			this.guesses = [];
			this.gameOver = false;
			this.won = false;
			this.keyStates = {};
			this.submitting = false; // 防止快速重复提交
			this.initUI();
			this.setupEventListeners();
		}

		initUI() {
			// Create game board
			const grid = document.getElementById('wordleGrid');
			for (let i = 0; i < MAX_ATTEMPTS; i++) {
				const row = document.createElement('div');
				row.className = 'wordle-row';
				for (let j = 0; j < WORD_LENGTH; j++) {
					const tile = document.createElement('div');
					tile.className = 'wordle-tile';
					tile.id = `tile-${i}-${j}`;
					row.appendChild(tile);
				}
				grid.appendChild(row);
			}

			// Create virtual keyboard
			const keyboard = document.getElementById('wordleKeyboard');
			KEYBOARD_LAYOUT.forEach((row, rowIndex) => {
				const rowDiv = document.createElement('div');
				rowDiv.className = 'keyboard-row';
				rowDiv.id = `keyboard-row-${rowIndex}`;

				row.forEach(key => {
					const btn = document.createElement('button');
					btn.className = 'keyboard-key';
					btn.textContent = key;
					btn.id = `key-${key}`;
					btn.addEventListener('click', () => this.onKeyClick(key));
					rowDiv.appendChild(btn);
				});

				keyboard.appendChild(rowDiv);
			});

			// Create action buttons (delete and submit)
			const actionRow = document.createElement('div');
			actionRow.className = 'keyboard-row keyboard-action-row';
			
			const backspaceBtn = document.createElement('button');
			backspaceBtn.className = 'keyboard-key backspace';
			backspaceBtn.textContent = '⌫ Delete';
			backspaceBtn.addEventListener('click', () => this.onBackspace());
			actionRow.appendChild(backspaceBtn);
			
			const enterBtn = document.createElement('button');
			enterBtn.className = 'keyboard-key enter';
			enterBtn.textContent = '⏎ Submit';
			enterBtn.addEventListener('click', () => this.onSubmit());
			actionRow.appendChild(enterBtn);
			
			keyboard.appendChild(actionRow);
		}

		setupEventListeners() {
			document.addEventListener('keydown', (e) => {
				const key = e.key.toUpperCase();
				if (/^[A-Z]$/.test(key)) {
					this.onKeyClick(key);
				} else if (key === 'BACKSPACE') {
					e.preventDefault();
					this.onBackspace();
				} else if (key === 'ENTER') {
					e.preventDefault();
					this.onSubmit();
				}
			});
		}

		onKeyClick(letter) {
			if (this.gameOver || this.currentGuess.length >= WORD_LENGTH || this.submitting) {
				return;
			}
			this.currentGuess += letter;
			this.updateDisplay();
		}

		onBackspace() {
			if (this.gameOver || this.currentGuess.length === 0 || this.submitting) {
				return;
			}
			this.currentGuess = this.currentGuess.slice(0, -1);
			this.updateDisplay();
		}

		updateDisplay() {
			const rowIndex = this.attempts;
			for (let i = 0; i < WORD_LENGTH; i++) {
				const tile = document.getElementById(`tile-${rowIndex}-${i}`);
				const letter = this.currentGuess[i] || '';
				tile.textContent = letter;
				if (letter) {
					tile.classList.add('filled');
				} else {
					tile.classList.remove('filled');
				}
			}
		}

		async onSubmit() {
			if (this.gameOver || this.submitting) return;
			if (this.currentGuess.length !== WORD_LENGTH) {
				this.showMessage('Word needs 5 letters');
				return;
			}

			// 设置提交标志，防止重复提交
			this.submitting = true;

			// 通过AJAX验证单词有效性
			const isValid = await this.validateWord(this.currentGuess);
			if (!isValid) {
				this.showMessage('Not a valid word');
				this.submitting = false;
				return;
			}

			// 提交猜测并获取结果
			this.guesses.push(this.currentGuess);
			const result = await this.revealGuess();
			
			if (result.correct) {
				this.won = true;
				this.gameOver = true;
				this.showResult(true, result.answer);
			} else if (this.attempts >= MAX_ATTEMPTS - 1) {
				this.gameOver = true;
				this.showResult(false, result.answer);
			} else {
				this.attempts++;
				this.currentGuess = '';
				this.updateDisplay();
			}
			
			// 重置提交标志
			this.submitting = false;
		}

		// 通过AJAX验证单词有效性
		async validateWord(word) {
			try {
				console.log('Sending validation request for:', word, 'to URL:', WORDLE_AJAX_URL);
				const response = await fetch(WORDLE_AJAX_URL, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: `hywordle_action=validate_word&word=${encodeURIComponent(word)}`
				});
				
				const text = await response.text();
				console.log('Raw response text:', text);
				
				let data;
				try {
					data = JSON.parse(text);
				} catch (e) {
					console.error('JSON parse error:', e);
					console.error('Response was:', text);
					return false;
				}
				
				console.log('Parsed validation response:', data);
				return data.valid === true;
			} catch (error) {
				console.error('Validation fetch error:', error);
				alert('检查浏览器控制台获取详细错误信息');
				return false;
			}
		}

		async revealGuess() {
			const rowIndex = this.attempts;
			
			try {
				// 通过AJAX向后端提交猜测，获取字母状态
				const response = await fetch(WORDLE_AJAX_URL, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: `hywordle_action=check_guess&guess=${encodeURIComponent(this.currentGuess)}&mode=${encodeURIComponent(this.mode)}`
				});
				
				const result = await response.json();
				
				if (result.error) {
					console.error('Check guess error:', result.error);
					return { correct: false };
				}
				
				const states = result.states;
				
				// 更新键盘状态
				for (let i = 0; i < WORD_LENGTH; i++) {
					const letter = this.currentGuess[i];
					const state = states[i];
					const keyBtn = document.getElementById(`key-${letter}`);
					if (keyBtn) {
						// 只在更好的状态时更新
						if (!this.keyStates[letter] || 
							(state === 'correct') ||
							(state === 'present' && this.keyStates[letter] !== 'correct')) {
							this.keyStates[letter] = state;
							keyBtn.classList.remove('correct', 'present', 'absent');
							keyBtn.classList.add(state);
						}
					}
				}

				// 显示tile动画
				await this.revealTiles(rowIndex, states);
				
				// 返回结果和答案（如果游戏已结束）
				return { 
					correct: result.correct === true,
					answer: result.answer || null
				};
			} catch (error) {
				console.error('Reveal guess error:', error);
				return { correct: false, answer: null };
			}
		}

		revealTiles(rowIndex, states) {
			return new Promise((resolve) => {
				let revealed = 0;
				for (let i = 0; i < WORD_LENGTH; i++) {
					setTimeout(() => {
						const tile = document.getElementById(`tile-${rowIndex}-${i}`);
						tile.classList.add('revealed', states[i]);
						revealed++;
						if (revealed === WORD_LENGTH) resolve();
					}, i * 100);
				}
			});
		}

		showMessage(message) {
			// 使用浏览器 alert 显示错误信息
			alert(message);
		}

		async showResult(won, answer) {
			const statusDiv = document.getElementById('wordleStatus');
			const messageDiv = document.getElementById('statusMessage');
			
			let resultHTML = '';
			if (won) {
				const attempts = this.attempts + 1;
				const message = attempts === 1 ? 'Perfect! First attempt!' : `Congratulations! You solved it in ${attempts} tries!`;
				resultHTML = message;
			} else {
				resultHTML = `Game Over! Better luck next time!`;
			}
			
			// 添加答案展示（只有在游戏结束时才会收到答案）
			if (answer) {
				resultHTML += `<br><span style="font-size: 16px; margin-top: 12px; display: block;">ANSWER: <span style="color: #6aaa64; font-weight: bold;">${answer}</span></span>`;
				
				// 词典链接
				const dictionaries = [
					{
						name: 'Wiktionary',
						url: `https://en.wiktionary.org/wiki/${encodeURIComponent(answer.toLowerCase())}`
					},
					{
						name: 'Merriam-Webster',
						url: `https://www.merriam-webster.com/dictionary/${answer.toLowerCase()}`
					},
					{
						name: 'Oxford',
						url: `https://www.oxfordlearnersdictionaries.com/definition/english/${answer.toLowerCase()}`
					},
					{
						name: 'Cambridge',
						url: `https://dictionary.cambridge.org/dictionary/english/${answer.toLowerCase()}`
					}
				];
				
				let dictLinksHTML = '<span style="font-size: 12px; margin-top: 8px; display: block;">';
				dictLinksHTML += dictionaries.map(dict => 
					`<a href="${dict.url}" target="_blank" style="color: #0066cc; text-decoration: underline; cursor: pointer; margin: 0 4px;">${dict.name}</a>`
				).join(' ');
				dictLinksHTML += '</span>';
				
				resultHTML += dictLinksHTML;
			}
			
			messageDiv.innerHTML = resultHTML;
			statusDiv.style.display = 'block';
		}
	}

	// Initialize game
	let game;
	document.addEventListener('DOMContentLoaded', () => {
		console.log('HyWordle initialized');
		console.log('AJAX URL:', WORDLE_AJAX_URL);
		
		// Test AJAX connectivity
		fetch(WORDLE_AJAX_URL, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: 'hywordle_action=test'
		}).then(r => r.text()).then(text => {
			console.log('AJAX test response:', text);
		}).catch(e => {
			console.error('AJAX test failed:', e);
		});
		
		// 显示模式选择界面
		const modeSelectionDiv = document.getElementById('wordleModeSelection');
		const gameContainerDiv = document.getElementById('wordleGameContainer');
		modeSelectionDiv.style.display = 'flex';
		gameContainerDiv.style.display = 'none';
	});
</script>
