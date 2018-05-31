<?php
namespace App;
use \Webuni\FrontMatter\FrontMatter;
use \cebe\markdown\GithubMarkdown;

class Page{

	private $path;
	private $parsed_data;

	public function __construct($path)
	{
		$this->path = $path;
	}

	public function __get($property)
	{
		if(!property_exists($this, $property)) {
			$this->$property = $this->get($property);
		}
		return $this->property;

	}

	public function getUrl()
	{
		$path = str_replace(CONTENT_PATH, '', $this->path);
		$path = current(explode('.', $path));
		$path = str_replace('index', '', $path);
		return $path;
	}

	public function render()
	{
		$page = $this;
		return $this->renderLayout($page->get('layout'), $page->content);
	}

	public function get($key)
	{
		$frontMatter = new FrontMatter();
		if(!$this->parsed_data){
			$this->parsed_data = $frontMatter->parse(file_get_contents($this->path));
		}
		if($key === "content"){
			$document_type = end(explode('.', pathinfo($this->path, PATHINFO_FILENAME)));
			$method = "parse_".$document_type;
			return $this->$method($this->parsed_data->getContent());
		}
		return nl2br($this->parsed_data->getData()[$key]);
	}

	public function parse_markdown($content)
	{
		$parser = new GithubMarkdown();
		$parser->enableNewLines = True;
		return $parser->parse($content);
	}

	public function parse_html($content)
	{
		return $content;
	}

	private function renderLayout($layout, $content)
	{
		if(!$layout)
		{
			$layout = 'default';
		}
		$page = $this;
		ob_start();
		require(LAYOUTS_PATH. DIRECTORY_SEPARATOR . $layout . '.php');
		$content = ob_get_clean();
		return $content;
	}
}


?>
