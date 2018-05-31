<?php
	use Psr\Http\Message\ServerRequestInterface;

	require '../vendor/autoload.php';

	define('BASE_PATH', dirname(__DIR__));
	define('APP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'app');
	define('CONTENT_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'content');
	define('LAYOUTS_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts');

	$app = new \Slim\App(['debug' => true, $container]);

	$pages = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(CONTENT_PATH));
	$pages = new RegexIterator($pages, '/^.+\.yaml$/i', RecursiveRegexIterator::GET_MATCH);

	foreach($pages as $p){
		$p = current($p);
		$page = new App\Page($p);
		$app->any($page->getUrl(), function(ServerRequestInterface $request) use ($page, $app){
			require APP_PATH . DIRECTORY_SEPARATOR . 'helpers.php';
			echo $page->render();
		});
	}

	$app->run();
?>
