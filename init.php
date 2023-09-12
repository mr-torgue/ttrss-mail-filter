<?php
class Mail_Filter extends Plugin {
	private $host;

	function about() {
		return array(1.0,
			"Example Filter plugin",
			"fox");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_FILTER_ACTION, $this);

		$host->add_filter_action($this, "action_example", "Example action");
		$host->add_filter_action($this, "action_another", "Another action");
	}

	function hook_article_filter_action($article, $action) {
		// if ($action == "...") { .... }

		$article["title"] = "[EXAMPLE FILTER WAS HERE: $action] " . $article["title"];

		return $article;
	}

	function api_version() {
		return 2;
	}

}
?>
