<?php

namespace Lucien144\Wordpress\Waly;

use Lucien144\Wordpress\Waly\Post,
	Lucien144\Wordpress\Waly\Category;

class Posts implements \Iterator {

	private $var = array();

	/**
	 * @var false | Lucien144\Wordpress\Waly\Category
	 */
	private $category = NULL;

	private $limit      = 10000;
	private $offset     = 0;
	private $orderby    = 'date';
	private $order      = 'asc';
	private $postStatus = 'publish';

	public function __construct($category)
	{
		$this->category = Waly::getCategory($category);
		$this->getData();
	}

	private function getData() 
	{
		$this->var = array();
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

		$args = array(
			'posts_per_page' => $this->limit,
			'offset'         => $this->offset,
			'orderby'        => $this->orderby,
			'order'          => $this->order,
			'post_status'    => $this->postStatus,
		);

		if ($this->category instanceof Category) {
			$args['category'] = $this->category->id;
		}

		$posts = get_posts($args);
		foreach ($posts as $post) {
			$this->var[] = new Post($post);
		}
	}

	public function limit($limit)
	{
		$this->limit = $limit;
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
					'posts_per_page'   => $this->limit,
					'offset'           => $this->offset,
					'orderby'          => $this->orderby,
					'order'            => $this->order,
					'post_status'      => $this->postStatus,
					'cat'              => $this->category->id,
					'category__not_in' => $exclude,

				));
		$this->var = $query->get_posts();

		return $this;
	}

    public function rewind()
    {
        reset($this->var);
    }
  
    public function current()
    {
        $var = current($this->var);
        return $var;
    }
  
    public function key() 
    {
        $var = key($this->var);
        return $var;
    }
  
    public function next() 
    {
        $var = next($this->var);
        return $var;
    }
  
    public function valid()
    {
        $key = key($this->var);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

}