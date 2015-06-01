<?php

namespace Lucien144\Wordpress\Waly;

use Lucien144\Wordpress\Waly\Author;

class Post {

	/*
	   [ID]                    => (integer)
	   [post_author]           => (integer)
	   [post_date]             => (YYYY-MM-DD HH:MM:SS)
	   [post_date_gmt]         => (YYYY-MM-DD HH:MM:SS)
	   [post_content]          => (all post content is in here)
	   [post_title]            => (Post Title Here)
	   [post_excerpt]          => (Post Excerpt)
	   [post_status]           => (? | publish)
	   [comment_status]        => (? | closed)
	   [ping_status]           => (? | closed)
	   [post_password]         => (blank if not specified)
	   [post_name]             => (slug-is-here)
	   [to_ping]               => (?)
	   [pinged]                => (?)
	   [post_modified]         => (YYYY-MM-DD HH:MM:SS)
	   [post_modified_gmt]     => (YYYY-MM-DD HH:MM:SS)
	   [post_content_filtered] => (?)
	   [post_parent]           => (integer)
	   [guid]                  => (a unique identifier that is not necessarily the URL to the Page)
	   [menu_order]            => (integer)
	   [post_type]             => (? | page)
	   [post_mime_type]        => ()?)
	   [comment_count]         => (integer)
	   [ancestors]             => (object|array)
	   [filter]                => (? | raw)
	*/

	private $post;

	public function __construct(\WP_Post $post)
	{
		$this->post = $post;
	}

	public function photo($size = NULL)
	{
		$result = wp_get_attachment_image_src( get_post_thumbnail_id( $this->post->ID ), $size);
		return isset($result[0]) ? $result[0] : FALSE;
	}

	public function slug()
	{
		return $this->name;
	}

	public function id()
	{
		return $this->post->ID;
	}

	public function date()
	{
		return new \DateTime($this->post->post_date);
	}

	public function author()
	{
		return new Author($this->post->post_author);
	}

	public function category()
	{
		$categories = $this->post->post_category;
		$result = array();
		foreach ($categories as $key) {
			$result[] = Waly::getCategory($key);
		}
		return $result;
	}


	public function link()
	{
		return get_permalink($this->post->ID);
	}

	public function children()
	{	
		return Waly::getPosts()->children($this->post->ID);
	}

	public function __get($name)
	{

		if (method_exists($this, $name)) {
			return $this->$name();
		}

		if (isset($this->post->{'post_' . $name})) {
			return $this->post->{'post_' . $name};
		} elseif (isset($this->post->{$name})) {
			return $this->post->{$name};
		} else {
			throw new \Exception('Unknown post attribute "' . $name . '"');
		}
	}
}