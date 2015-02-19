<?php

namespace Lucien144\Wordpress\Waly;

use Lucien144\Wordpress\Waly\Post,
	Lucien144\Wordpress\Waly\Posts,
	Lucien144\Wordpress\Waly\Category;

class Waly {

	public function getPost($id = NULL) {
		if (is_string($id)) {
			global $wpdb;
			$id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '" . mysql_escape_string($id) . "'");
			return new Post(get_post($id));
		} else {
			return new Post(get_post($id == NULL ? get_the_ID() : $id));
		}
	}

	public static function getPosts($category, $limit = 10000, $orderby = 'date', $order = 'asc', $offset = 0, $postType = 'post', $postStatus = 'publish')
	{
		$posts = new Posts($category);
		return $posts;
	}


	public function getPostsAfter($date, $category, $limit = 10000, $orderby = 'date', $order = 'asc', $offset = 0, $postType = 'post', $postStatus = 'publish')
	{
		$return = array();
        
		if (is_numeric($date)) {
			$date = date('Y-m-d H:i:s', $date);
		}

        $args = array(
			'post_type'      => 'post',
			'posts_per_page' => $limit,
			'offset'         => $offset,
			'orderby'        => $orderby,
			'order'          => $order,
			'post_status'    => $postStatus,
			'date_query'     => array(
                'column'  => 'post_date',
                'after'   => $date
            )
        );

		if (is_int($category)) {
			$args['category'] = $category;
		} elseif (is_string($category)) {
			$args['category_name'] = $category;
		}

		$posts = new \WP_Query( $args );
		foreach ($posts->posts as $post) {
			$return[] = new Post($post);
		}

		return $return;
	}

	public function getPostsBetween($after, $before, $category, $limit = 10000, $orderby = 'date', $order = 'asc', $offset = 0, $postType = 'post', $postStatus = 'publish')
	{
		$return = array();
        
		if (is_numeric($after)) {
			$after = date('Y-m-d H:i:s', $after);
		}
		
		if (is_numeric($before)) {
			$before = date('Y-m-d H:i:s', $before);
		}

        $args = array(
			'post_type'      => 'post',
			'posts_per_page' => $limit,
			'offset'         => $offset,
			'orderby'        => $orderby,
			'order'          => $order,
			'post_status'    => $postStatus,
			'date_query'     => array(
                'column'  => 'post_date',
                'after'   => $after,
                'before'   => $before
            )
        );

		if (is_int($category)) {
			$args['category'] = $category;
		} elseif (is_string($category)) {
			$args['category_name'] = $category;
		}

		$posts = new \WP_Query( $args );
		foreach ($posts->posts as $post) {
			$return[] = new Post($post);
		}

		return $return;
	}


	/**
	 * Finds subcategories by ID or slug
	 * @param  integer|string $childOf ID or category slug
	 * @param  string  $orderby      Sort categories alphabetically or by unique category.
	 * @param  string  $order        Sort order for categories (either ascending or descending).
	 * @param  boolean $hierarchical  When true, the results will include sub-categories that are empty, as long as those sub-categories have sub-categories that are not empty.
	 * @return array|exception
	 */
	public function getCategories($childOf = 0, $orderby = 'name', $order = 'ASC', $hierarchical = FALSE)
	{
		$return = array();

		$allowedArgValues = array(
			'orderby'      => array('id','name','slug','count','term_group'),
			'order'        => array('ASC', 'DESC'),
			'hierarchical' => array(TRUE, FALSE),
		);
		foreach ($allowedArgValues as $argument => $values) {
			if (!in_array($$argument, $values)) {
				throw new \Exception("Unknown argument value '{$$argument}' for argument '{$argument}' in method Wally::'" . __FUNCTION__ . "'");
			}
		}

		if (is_string($childOf)) {
			$parent = new Category(get_category_by_slug($childOf));
			$childOf = $parent->id;
		}

		$args = array(
			'type'                     => 'post',
			'child_of'                 => $hirealchical ? $childOf : '',
			'parent'                   => $hirealchical ? '' : $childOf,
			'orderby'                  => $orderby,
			'order'                    => 'ASC',
			'hide_empty'               => 0, // Forced
			'hierarchical'             => 0, // Forced
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false 

		); 
		$categories = get_categories( $args );
		
		foreach($categories as $category) {
			$return[] = new Category($category);
		}

		return $return;

	}

	public static function getCategory($id)
	{
		if (is_int($id)) {
			$result = get_category($id);
			return is_object($result) ? new Category($result) : FALSE;
		} else if (is_string($id)) {
			$result = get_category_by_slug($id);
			return is_object($result) ? new Category($result) : FALSE;
		} else {
			return FALSE;
		}

	}

}