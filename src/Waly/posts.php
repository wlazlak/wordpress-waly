<?php

namespace Lucien144\Wordpress\Waly;

use Lucien144\Wordpress\Waly\Post,
	Lucien144\Wordpress\Waly\Category;

class Posts implements \Iterator {

	/**
	 * Finded posts.
	 * @var array of Lucien144\Waly\Post
	 */
	private $posts = array();

	/**
	 * Arguments for finding posts
	 * @var array
	 */
	private $args = array(
		'posts_per_page' => 10000,
		'offset'         => 0,
		'orderby'        => 'date',
		'order'          => 'asc',
		'post_status'    => 'publish',
		'post_mime_type' => '',
		'post_type'      => 'post',
	);

	/**
	 * Switcher whether we looking for posts or childs
	 * @var boolean
	 */
	private $childSeek = FALSE;

	/**
	 * @var false | Lucien144\Wordpress\Waly\Category
	 */
	private $category = NULL;

	public function __construct()
	{
		$this->getData();
	}

	private function getData() 
	{
		$this->posts = array();
		/*
		$args = array(
		'posts_per_page'   => 5,
		'offset'           => 0,
		'category'         => '',
		'category_name'    => '',
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'post',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'post_status'      => 'publish',
		'suppress_filters' => true );
		 */

		if ($this->category instanceof Category) {
			$this->args['category'] = $this->category->id;
		}

		if (!$this->childSeek) {
			$posts = get_posts($this->args);
		} else {
			$posts = get_children($this->args);
		}
		
		foreach ($posts as $post) {
			$this->posts[] = new Post($post);
		}
	}

	public function category($category)
	{
		$this->category = Waly::getCategory($category);
		$this->getData();
		return $this;
	}

	public function order($orderby)
	{
		$this->args['orderby'] = $orderby;
		$this->getData();
		return $this;
	}

	public function type($post_type)
	{
		$this->args['post_type'] = $post_type;
		$this->getData();
		return $this;
	}

	public function limit($limit)
	{
		$this->args['posts_per_page'] = $limit;
		$this->getData();
		return $this;
	}

	public function children($id)
	{
		$args = array();
		
		// (integer) (optional) Number of child posts to retrieve.
		$args['numberposts'] = $this->args['posts_per_page'];

		// (integer) (optional) Pass the ID of a post or Page to get its children. Pass 0 to get attachments without parent. Pass null to get any child regardless of parent.
		$args['post_parent'] = $id;

		// (string) (optional) Any value from post_type column of the posts table, such as attachment, page, or revision; or the keyword any.
		$args['post_type'] = $this->args['post_type'];

		// (string) (optional) Any value from the post_status column of the wp_posts table, such as publish, draft, or inherit; or the keyword any.
		$args['post_status'] = $this->args['post_status'];

		// (string) (optional) A full or partial mime-type, e.g. image, video, video/mp4, which is matched against a post's post_mime_type field.
		$args['post_mime_type'] = $this->args['post_mime_type'];
		
		$this->childSeek = TRUE;
		$this->args = $args;
		$this->getData();
		return $this;
	}

	public function excludeChilds()
	{
		if (!$this->category instanceof Category) {
			throw new \Exception('Category has to be known to exclude its childs.');
		}
		
		$categories = Waly::getCategories($this->category->id);
		$exclude = array();
		foreach ($categories as $category) {
			$exclude[] = $category->id;
		}
		
		$query = new \WP_Query(array(
					'posts_per_page'   => $this->args['posts_per_page'],
					'offset'           => $this->args['offset'],
					'orderby'          => $this->args['orderby'],
					'order'            => $this->args['order'],
					'post_status'      => $this->args['post_status'],
					'cat'              => $this->category->id,
					'category__not_in' => $exclude,

				));
		$this->posts = $query->get_posts();

		return $this;
	}

    public function rewind()
    {
        reset($this->posts);
    }
  
    public function current()
    {
        $post = current($this->posts);
        return $post;
    }
  
    public function key() 
    {
        $post = key($this->posts);
        return $post;
    }
  
    public function next() 
    {
        $post = next($this->posts);
        return $post;
    }
  
    public function valid()
    {
        $key = key($this->posts);
        $post = ($key !== NULL && $key !== FALSE);
        return $post;
    }

}