<!-- HyWordle - Wordle Game
 Code Type: HTML + PHP
 Shortcode: [wpcode id="14656"] (auto-generated)
-->

<?php
// 获取随机单词
function getRandomWordle() {
	$url = 'https://www.hyperplasma.top/hpsrc/valid-wordle-words.txt';
	$words = @file($url, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	if ($words === false || empty($words)) {
		return 'WORDLE'; // 默认备用词
	}
	
	$word = trim(strtoupper($words[array_rand($words)]));
	
	// 确保是5字母词
	if (strlen($word) !== 5) {
		return getRandomWordle(); // 递归直到找到5字母词
	}
	
	return $word;
}

$answer = getRandomWordle();
$answerHash = hash('sha256', $answer);
$answerJson = json_encode($answer);
?>

<div class="hywordle-container">
	<div class="hywordle-game">
		<div class="wordle-header">
			<h1 class="wordle-title">Guess the word!</h1>
		<p class="wordle-subtitle">5-letter word</p>
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
			<button class="wordle-restart-btn" onclick="location.reload();">Restart</button>
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
	// Pass answer from PHP to JS
	const CORRECT_ANSWER = <?php echo $answerJson; ?>;
	const ANSWER_HASH = "<?php echo $answerHash; ?>";
	const MAX_ATTEMPTS = 6;
	const WORD_LENGTH = 5;

	// Keyboard layout
	const KEYBOARD_LAYOUT = [
		['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'],
		['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'],
		['Z', 'X', 'C', 'V', 'B', 'N', 'M']
	];

	class WordleGame {
		constructor() {
			this.attempts = 0;
			this.currentGuess = '';
			this.guesses = [];
			this.gameOver = false;
			this.won = false;
			this.keyStates = {};
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
			if (this.gameOver || this.currentGuess.length >= WORD_LENGTH) {
				return;
			}
			this.currentGuess += letter;
			this.updateDisplay();
		}

		onBackspace() {
			if (this.gameOver || this.currentGuess.length === 0) {
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
			if (this.gameOver) return;
			if (this.currentGuess.length !== WORD_LENGTH) {
				this.showMessage('Word needs 5 letters');
				return;
			}

			// Validate word (allow any 5-letter combination for now)
			this.guesses.push(this.currentGuess);
			await this.revealGuess();
			
			if (this.currentGuess === CORRECT_ANSWER) {
				this.won = true;
				this.gameOver = true;
				this.showResult(true);
			} else if (this.attempts >= MAX_ATTEMPTS - 1) {
				this.gameOver = true;
				this.showResult(false);
			} else {
				this.attempts++;
				this.currentGuess = '';
				this.updateDisplay();
			}
		}

		async revealGuess() {
			const answer = CORRECT_ANSWER;
			const rowIndex = this.attempts;
			const answerLetters = answer.split('');
			const guessLetters = this.currentGuess.split('');
			const states = new Array(WORD_LENGTH).fill('absent');

			// 第一遍：找到正确位置的字母
			for (let i = 0; i < WORD_LENGTH; i++) {
				if (guessLetters[i] === answerLetters[i]) {
					states[i] = 'correct';
					answerLetters[i] = null; // Mark as used
				}
			}

			// Second pass: find letters in word but wrong position
			for (let i = 0; i < WORD_LENGTH; i++) {
				if (states[i] === 'absent') {
					const idx = answerLetters.indexOf(guessLetters[i]);
					if (idx !== -1) {
						states[i] = 'present';
						answerLetters[idx] = null; // Mark as used
					}
				}
			}

			// Update keyboard state
			for (let i = 0; i < WORD_LENGTH; i++) {
				const letter = guessLetters[i];
				const state = states[i];
				const keyBtn = document.getElementById(`key-${letter}`);
				if (keyBtn) {
					// Only update if this is a better state
					if (!this.keyStates[letter] || 
						(state === 'correct') ||
						(state === 'present' && this.keyStates[letter] !== 'correct')) {
						this.keyStates[letter] = state;
						keyBtn.classList.remove('correct', 'present', 'absent');
						keyBtn.classList.add(state);
					}
				}
			}

			// Reveal tiles with animation
			await this.revealTiles(rowIndex, states);
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
			// Simple implementation: show as status message without blocking game
			const statusDiv = document.getElementById('wordleStatus');
			const messageDiv = document.getElementById('statusMessage');
			messageDiv.textContent = message;
			statusDiv.style.display = 'block';
			setTimeout(() => {
				statusDiv.style.display = 'none';
			}, 2000);
		}

		async showResult(won) {
			const answer = CORRECT_ANSWER;
			const statusDiv = document.getElementById('wordleStatus');
			const messageDiv = document.getElementById('statusMessage');
			
			if (won) {
				const attempts = this.attempts + 1;
				const message = attempts === 1 ? 'Perfect! First attempt!' : `Congratulations! You solved it in ${attempts} tries!`;
				messageDiv.textContent = message;
			} else {
				messageDiv.innerHTML = `Game Over!<br>Answer: <strong>${answer}</strong>`;
			}
			statusDiv.style.display = 'block';
		}
	}

	// Initialize game
	let game;
	document.addEventListener('DOMContentLoaded', () => {
		game = new WordleGame();
	});
</script>
