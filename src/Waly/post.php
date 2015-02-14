<?php

namespace Lucien144\Wordpress\Waly;

use Lucien144\Wordpress\Waly\Author;

class Post {

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

	public function author()
	{
		return new Author($this->post->post_author);
	}


	public function link()
	{
		return get_permalink($this->post->ID);
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