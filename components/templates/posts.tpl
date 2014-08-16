		<article>
			<header>
				<h1 itemprop="headline">%TITLE%</h1>
				<nav>
					<details>
						<summary title="Tags" data-icon=","></summary>
						<div itemprop="keywords">%TAGS%</div>
					</details>
				</nav>
			</header>
			<section itemprop="text">%CONTENT%</section>
			<footer>
				<details>
					<summary>
						<img alt="Creative Commons License" src="images/logos/CreativeCommons.svgz" />
					</summary>
						<div>
							<span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">%TITLE%</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://plus.google.com/%AUTHOR_URL%?rel=author" property="cc:attributionName" rel="cc:attributionURL author" itemprop="author">%AUTHOR%</a> is licensed under a <a rel="license" itemprop="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.<time datetime="%DATETIME%" itemprop="datePublished">%DATE%</time>
						</div>
				</details>
<!--
	Navigation Section: How will I get previous and next?
	Try something like :
	$DB->prepare("
		SELECT `url` AS `next`
		FROM `posts`
		WHERE `id` = (
			SELECT `id`
			FROM `posts`
			WHERE `title` = :title
		) + 1
	")
				<hr />
				<a href="%PREV%" rel="prev" title="Previous"></a>
				<a href="%HOME%" rel="bookmark" title="home" class="logo"><img src="images/icons/home.svgz"/ alt="Home"></a>
				<a href="%NEXT%" rel="next" title="Next"></a>
-->
			</footer>
		</article>