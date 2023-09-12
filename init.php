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
		$host->add_hook($host::HOOK_ACTION_ITEM, $this);

		$host->add_filter_action($this, "send_mail_notification", "Send Mail Notification");
	}

	function hook_article_filter_action($article, $action) {
		// if ($action == "...") { .... }

		$article["title"] = "[EXAMPLE FILTER WAS HERE: $action] " . $article["title"];

		return $article;
	}

	function hook_action_item() {
		return "testing";
	}

	function api_version() {
		return 2;
	}

}
?>
