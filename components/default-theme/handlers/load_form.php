<?php
$resp = \shgysk8zer0\Core\JSON_Response::load();
switch($_POST['load_form']) {
	case 'login':
		$resp->remove(
			'main > :not(aside)'
		)->prepend(
			'main',
			load_results('forms/login')
		);
		break;

	case 'new_post':
		require_login('user');

		$form = \shgysk8zer0\Core\Template::load('form');
		$post = \shgysk8zer0\Core\Template::load('posts');

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
			"{$post}"
		);

		$form->inputs .= '<textarea name="description" id="description" placeholder="Description will appear in searches. 160 character limit" maxlength="160" required></textarea><br/>';

		$resp->remove(
			'main > :not(aside)'
		)->prepend(
			'main',
			"{$form}"
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
		break;

	case 'compose_email':
		require_login('admin');
		$resp->prepend(
			'body',
			load_results('forms/compose_email')
		)->showModal(
			'#compose_email_dialog'
		);
		break;

	case 'php_errors':
		require_login('admin');

		$table = array_reduce(
			$DB(
				'SELECT `message`, `file`, `line`
				FROM `errors`
				GROUP BY `file`, `line`;'
			),
			function(\DOMElement $table, \stdClass $error)
			{
				return $table->message($error->message)
					->file($error->file)
					->line($error->line)
					->nextRow();
			},
			new \shgysk8zer0\Core\Elements\Table('message', 'file', 'line')
		);
		$table->caption = 'PHP Errors';

		$table = $table->getNode();

		$dialog = new \shgysk8zer0\Core\Elements\Dialog('php-errors', $table);

		$resp->append('body', $dialog)->showModal($dialog->id);

		break;
}
exit($resp);
