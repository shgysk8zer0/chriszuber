<?php
class blog_post extends _pdo{
	public $post;
	private $is_prepared;
	private $stm;
	
	public function __construct(){
		parent::__construct();
		$this->is_prepared = false;
	}
	
	public function get_titles($lim = 8){
		$query = "SELECT `title` FROM `posts` LIMIT $lim";
		$results = $this->fetch_array($query);
		$titles = array();
		foreach($results as $result){
			array_push($titles, $result->title);
		}
		return $titles;
	}
	
	public function get_posts($title){
		$title = $this->escape($title);
		if(!$this->is_prepared){
			$this->is_prepared = true;
			$query = "SELECT title, content, author, author_url, time FROM posts WHERE title = :title";
			$this->stm = $this->pdo->prepare($query) or die("<h1>Failed to prepare statement: <code>{$query}</code></h1>");
		}
		$this->stm->bindValue(':title', $title, PDO::PARAM_STR) or die("<h1>Failed to bind: '$title' to :title </h1>");
		($this->stm->execute()) ? $results = $this->stm->fetchAll(PDO::FETCH_CLASS)[0] or die("<h1>Failed in fetching</h1>") : die('<h1>Failed to execute</h1>');
		$this->post->title = ucwords(strtolower($results->title));
		$this->post->content = preg_replace('/%URL%/', URL, $results->content);
		$this->post->author = $results->author;
		$this->post->author_url = $results->author_url;
		$this->post->time = $results->time;
	}
	
	protected function title_to_id(){
		$title = preg_replace('/\//', '\/', addslashes(strtolower($this->post->title)));
		return (preg_replace('/\ /', '-', $title));
	}
	
	public function print_post($title = null){
	$url = URL;
	($title !== null) ? $this->get_posts(trim(strtolower($title))) : die('<h2>No post selected</h2>');
	echo <<<EOT
		<article itemscope itemtype="http://schema.org/Blog" id="{$this->title_to_id()}">
	<header>
		<h1 itemprop="name" data-target=".posts" data-url="$url/post/{$this->post->title}">{$this->post->title}</h1>
	</header>
	<section itemprop="text">
		{$this->post->content}
	</section>
	<footer>
		<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" src="{$url}/images/logos/CreativeCommons.svgz"></a><br><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">{$this->post->title}</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="{$this->post->author_url}" property="cc:attributionName" rel="cc:attributionURL" itemprop="author">{$this->post->author}</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>. <time itemprop="datePublished" datetime="">{$this->post->time}</time>
	</footer>
</article>
EOT;
	}
}
?>
