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
	 * Switcher whether we looking for posts or childs
	 * @var boolean
	 */
	private $childSeek = FALSE;

	/**
	 * @var false | Lucien144\Wordpress\Waly\Category
	 */
	private $category = NULL;
	
	private $limit        = 10000;
	private $offset       = 0;
	private $orderby      = 'date';
	private $order        = 'asc';
	private $postStatus   = 'publish';
	private $postMimeType = '';
	private $postType     = 'post';

	public function __construct()
	{
		$this->getData();
	}

	private function getData($args = NULL) 
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

		if (!is_array($args)) {
			$args = array(
				'posts_per_page' => $this->limit,
				'offset'         => $this->offset,
				'orderby'        => $this->orderby,
				'order'          => $this->order,
				'post_status'    => $this->postStatus,
				'post_mime_type' => $this->postMimeType,
				'post_type'      => $this->postType,
			);
		}

		if ($this->category instanceof Category) {
			$args['category'] = $this->category->id;
		}

		if (!$this->childSeek) {
			$posts = get_posts($args);
		} else {
			$posts = get_children($args);
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
		$this->orderby = $orderby;
		$this->getData();
		return $this;
	}

	public function type($post_type)
	{
		$this->postType = $post_type;
		$this->getData();
		return $this;
	}

	public function limit($limit)
	{
		$this->limit = $limit;
		$this->getData();
		return $this;
	}

	public function children($id)
	{
		$args = array();
		
		// (integer) (optional) Number of child posts to retrieve.
		$args['numberposts'] = $this->limit;

		// (integer) (optional) Pass the ID of a post or Page to get its children. Pass 0 to get attachments without parent. Pass null to get any child regardless of parent.
		$args['post_parent'] = $id;

		// (string) (optional) Any value from post_type column of the posts table, such as attachment, page, or revision; or the keyword any.
		$args['post_type'] = $this->postType;

		// (string) (optional) Any value from the post_status column of the wp_posts table, such as publish, draft, or inherit; or the keyword any.
		$args['post_status'] = $this->postStatus;

		// (string) (optional) A full or partial mime-type, e.g. image, video, video/mp4, which is matched against a post's post_mime_type field.
		$args['post_mime_type'] = $this->postMimeType;
		
		$this->childSeek = TRUE;
		$this->getData($args);
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
					'posts_per_page'   => $this->limit,
					'offset'           => $this->offset,
					'orderby'          => $this->orderby,
					'order'            => $this->order,
					'post_status'      => $this->postStatus,
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