<?php
class MailFilter extends Plugin {

	/** @var PluginHost $host */
	private $host;

	function about() {
		return array(null,
			"Send email notification based on filter",
			"mr-torgue");
	}

	function init($host) {
		$this->host = $host;

		//$host->add_hook($host::HOOK_ARTICLE_BUTTON, $this);
		//$host->add_hook($host::HOOK_PREFS_TAB, $this);
		//$host->add_hook($host::HOOK_HEADLINE_TOOLBAR_SELECT_MENU_ITEM2, $this);

		$host->add_filter_action($this, 'test',__('JS Notifications API'));
	}

	function get_js() {
		return file_get_contents(__DIR__ . '/init.js') ?: '';
	}
	
	/**
	 * @param array<string,mixed> $article
	 * @param string $action
	 * @return array<string,mixed> ($article)
	 */
	function hook_article_filter_action($article, $action) {
		return $article;
	}

	function api_version() {
		return 2;
	}

}
