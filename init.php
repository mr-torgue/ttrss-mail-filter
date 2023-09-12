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
		if($action == "send_mail_notification") { 
			$this->send_notification($article, 'Alert');
		}
		return $article;
	}

	function hook_action_item() {
		return "testing";
	}

	/*
 	sends a notification to the email address of the user that is logged in.
  	TODO: allow user to specify custom email address
   	TODO: allow user to specify from email address
    	TODO: specify the filter it was triggered on
	*/
	function send_notification(array $article, string $notification_type) : void {	
		$subject = $notification_type . ": " . $article['title'];
		$content = "Alert triggered on the following article: " . $article;
		$sth = $this->pdo->prepare("SELECT email, full_name FROM ttrss_users WHERE id = ?");
		$sth->execute([$_SESSION['uid']]);
		if ($row = $sth->fetch()) {
			$reply = array();
			$user_email = htmlspecialchars($row['email']);
			$user_name = htmlspecialchars($row['full_name']);

			$to = $user_email;
			$subject = strip_tags($subject);
			$message = strip_tags($content);
			$from = "notify-ttrss@local.host";
			
			$mailer = new Mailer();
			$rc = $mailer->mail(["to_address" => $to,
				"headers" => ["Reply-To: $from"],
				"subject" => $subject,
				"message" => $message]);
	
			if (!$rc) {
				$reply['error'] =  $mailer->error();
			} else {
				$reply['message'] = "UPDATE_COUNTERS";
			}
			print json_encode($reply);
		}
	}


	function api_version() {
		return 2;
	}

}
?>
