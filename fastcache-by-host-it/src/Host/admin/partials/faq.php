<?php
class faqText {
	public function __construct() {
	}
	public function faqs() {
		$qa = array(
			array(
				"name" => "faqa",
				"question"    => __ ('How can i get an authentication token for FastCache?', 'fastcache'),
				"answer"    => __ ('You can obtain an authentication token by visiting https://host.it. Once you have the token, you can enter it into the FastCache plugin interface on your WordPress site.', 'fastcache'),
			),
			array(
				"name" => "faqb",
				"question"    => __ ('What happens if a CDN server goes offline?', 'fastcache'),
				"answer"    => __ ('If a CDN server goes offline, user requests will simply be redirected to another server in the network. This ensures that your site always remains available and fast, even in the event of problems with a single server.', 'fastcache')
			),
			array(
				"name" => "faqc",
				"question"    => __ ('How can i set up FastCache on my site?', 'fastcache'),
				"answer"    => __ ('After installing and activating the plugin, you will be able to access the plugin interface on your WordPress site. Here, you can enter your authentication token and configure all the operating parameters of the CDN', 'fastcache')
			),
//			array(
//				"name" => "4faq",
//				"question"    => "",
//				"answer"    => "",
//			),
//			array(
//				"name" => "5faq",
//				"question"    => "",
//				"answer"    => "",
//			),
//			array(
//				"name" => "6faq",
//				"question"    => "",
//				"answer"    => "",
//			),
		);
		return $qa;
	}
}
