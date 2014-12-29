<?php
	$resp = \shgysk8zer0\core\json_response::load();
	if(preg_match('/^[A-z]$/', $_REQUEST['hangman'])) {
		if(preg_match("/{$_REQUEST['hangman']}/", $session->hangman_phrase)) {
			$session->hangman_matches++;
			preg_match_all("/{$_REQUEST['hangman']}/i", preg_replace('/[^A-z]/', null, $session->hangman_phrase), $matches, PREG_OFFSET_CAPTURE);

			foreach($matches[0] as $index) {
				$pos = (int)$index[1] + 1;
				$resp->text(
					"#hangman_phrase > u:nth-of-type({$pos})",
					$index[0]
				);
			}
			$resp->disable(
				"button[data-request=\"hangman={$_REQUEST['hangman']}\"]"
			);
			if((int)count(array_unique(str_split(preg_replace('/[^A-z]/', null, $session->hangman_phrase)))) === (int)$session->hangman_matches) {
				$resp->notify(
					'Congratulation',
					'You have won'
				)->disable(
					'button[data-request^="hangman"]:not([data-request="hangman=restart"])'
				);
			}
		}
		else {
			$limbs = [
				'head',
				'torso',
				'left_arm',
				'right_arm',
				'left_leg',
				'right_leg'
			];

			if($session->hangman_bad_guesses >= count($limbs) - 1) {
				$resp->notify(
					'You lose',
					'Try again?'
				)->disable(
					'button[data-request^="hangman"]:not([data-request="hangman=restart"])'
				)->text(
					'#hangman_phrase',
					$session->hangman_phrase
				)->attributes(
					'#hangman_' . $limbs[$session->hangman_bad_guesses++],
					'opacity',
					1
				);
			}
			else {
				$resp->attributes(
					'#hangman_' . $limbs[$session->hangman_bad_guesses++],
					'opacity',
					1
				)->disable(
					"button[data-request=\"hangman={$_REQUEST['hangman']}\"]"
				);
			}
		}
	}


	else {
		switch($_REQUEST['hangman']) {
			case 'restart': {
				$phrases = [
					'this is the song that never ends',
					'i love php & javascript',
					'sudo apt-get install life',
					'supercalifragilisticexpialidocious',
					'antidisestablishmentarianism',
					'cards against humanity'
				];

				$session->hangman_phrase = strtoupper($phrases[mt_rand(0, count($phrases) - 1)]);
				$session->hangman_matches = 0;
				$session->hangman_bad_guesses = 0;

				$resp->remove(
					'#hangman_phrase'
				)->after(
					'section svg',
					'<h1 id="hangman_phrase">' . preg_replace('/[A-z]/', '<u>&nbsp;&nbsp;</u>', $session->hangman_phrase) . '</h1>'
				)->enable(
					'button[data-request^="hangman"]'
				)->attributes(
					'svg path[id^=hangman]',
					'opacity',
					0
				);
			} break;
		}
	}
?>
