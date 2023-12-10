<?php
class Mail_Filter extends Plugin {
	private $host;
	
	const MAIL_FILTER_FROM = "MAIL_FILTER_FROM";

	function about() {
		return array(0.2,
			"A Mail Notification Filter Plugin",
			"mr-torgue",
			false, 
			'https://github.com/mr-torgue/ttrss-mail-filter',);
	}

	function init($host) {
		$this->host = $host;
		Config::add(self::MAIL_FILTER_FROM, "ttrss@localhost", Config::T_STRING);
		$host->add_hook($host::HOOK_ARTICLE_FILTER_ACTION, $this);
		//$host->add_hook($host::HOOK_ACTION_ITEM, $this);

		$host->add_filter_action($this, "send_mail_notification", "Send Mail Notification");
	}

	function hook_article_filter_action($article, $action) {
		if($action == "send_mail_notification") { 
			$this->send_notification($article, 'Alert');
		}
		return $article;
	}

	//function hook_action_item() {
	//	return "testing";
	//}

	/*
 	sends a notification to the email address of the user that is logged in.
  	from address is specified in MAIL_FILTER_FROM
  	TODO: allow user to specify custom email address
    	TODO: specify the filter it was triggered on
	*/ 
	function send_notification(array $article, string $notification_type) : void {	
		$subject = $notification_type . ": " . $article['title'];
		$content = $article['content'];
		$sth = $this->pdo->prepare("SELECT email, full_name FROM ttrss_users WHERE id = ?");
		$sth->execute([$this->host->get_owner_uid()]);
		if ($row = $sth->fetch()) {
			$reply = array();
			$user_email = htmlspecialchars($row['email']);
			$user_name = htmlspecialchars($row['full_name']);

			$to = $user_email;
			$subject = strip_tags($subject);
			$message = "We found the following: <a href='" . $article['link'] . "'>article</a> for you:<br>" . strip_tags($content);
			$from = Config::get(self::MAIL_FILTER_FROM);
			
			$mailer = new Mailer();
			$rc = $mailer->mail(["to_address" => $to,
				"headers" => ["Reply-To: $from"],
				"subject" => $subject,
				"message_html" => $message,
				"message" => $message]);
			
			if (!$rc) {
				Logger::log(E_USER_NOTICE, "ERROR: mail from " . $from . " to " . $to . " failed. Error: " . $mailer->error());
			} else {
				Logger::log(E_USER_NOTICE, "SUCCESS: sent article " . $article['link'] . " from " . $from . " to " . $to);
			}
		}
	}


	function api_version() {
		return 2;
	}

}
?>
