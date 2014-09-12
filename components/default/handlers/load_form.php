<?php
	$resp = \core\json_response::load();
	switch($_POST['load_form']) {
		case 'login': {
			$resp->remove(
				'main > :not(aside)'
			)->prepend(
				'main',
				load_results('forms/login')
			);
			} break;

		case 'new_post': {
			require_login('user');

			$form = \core\template::load('form');
			$post = \core\template::load('posts');

			$post->title(
				'Title'
			)->tags(
				'Keywords'
			)->content(
				'Article Content Here'
			)->license(
				null
			)->comments(
				null
			);

			$form->name(
				'new_post'
			)->action(
				URL . '/'
			)->method(
				'post'
			)->inputs(
				$post->out()
			);

			$form->inputs .= '<textarea name="description" id="description" placeholder="Description will appear in searches. 160 character limit" maxlength="160" required></textarea><br/>';

			$resp->remove(
				'main > :not(aside)'
			)->prepend(
				'main',
				$form->out()
			)->setAttributes([
				'article header details' => [
					'open' => true
				],
				'article [itemprop="keywords"], article [itemprop="text"], article [itemprop="headline"]' => [
					'contenteditable' => 'true',
				],
				'article [itemprop="text"]' => [
					'contextmenu' => 'wysiwyg_menu',
					'data-input-name'=> 'content',
					'data-dropzone'=> 'main'
				],
				'article [itemprop="headline"]' => [
					'data-input-name' => 'title'
				],
				'article [itemprop="keywords"]' => [
					'data-input-name' => 'keywords'
				]
			]);
		} break;

		case 'php_errors': {
			require_login('admin');

			$resp->html(
				'main',
				load_results("forms/php_errors")
			);
		} break;
	}
?>
