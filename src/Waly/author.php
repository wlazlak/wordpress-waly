<?php

namespace Lucien144\Wordpress\Waly;

class Author {

	private $id;
	private $author;

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function __get($name)
	{
		//user_login
		//user_pass
		//user_nicename
		//user_email
		//user_url
		//user_registered
		//user_activation_key
		//user_status
		//roles
		//display_name
		//nickname
		//first_name
		//last_name
		//description (Biographical Info from the user's profile)
		//jabber
		//aim
		//yim
		//googleplus
		//twitter
		//user_level
		//user_firstname
		//user_lastname
		//rich_editing
		//comment_shortcuts
		//admin_color
		//plugins_per_page
		//plugins_last_view
		//ID
		return get_the_author_meta($name, $this->id);
	}

}