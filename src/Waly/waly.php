<?php

namespace Lucien144\Wordpress\Waly;

use Lucien144\Wordpress\Waly\Post,
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
		$return = [];

		$args = array(
			'posts_per_page'   => $limit,
			'offset'           => $offset,
			'orderby'          => $orderby,
			'order'            => $order,
			'post_status'      => $postStatus,
		);

		if (is_int($category)) {
			$args['category'] = $category;
		} elseif (is_string($category)) {
			$args['category_name'] = $category;
		}

		$posts = get_posts($args);
		foreach ($posts as $post) {
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
			foreach ($values as $value) {
				if (!in_array($value, $$argument)) {
					throw new \Exception("Unknow argumenta value '{$value}' for argument '{$argument}' in method '{" . __FUNCTION__ . "}'");
				}
			}
		}

		if (is_string($childOf)) {
			$parent = new Category(get_category_by_slug($childOf));
			$childOf = $parent->id;
		}

		$args = array(
			'type'                     => 'post',
			'child_of'                 => $childOf,
			'parent'                   => '',
			'orderby'                  => $orderby,
			'order'                    => 'ASC',
			'hide_empty'               => 0, //!!!!
			'hierarchical'             => $hierarchical,
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
			return get_category($id);
		} else if (is_string($id)) {
			return get_category_by_slug($id);
		} else {
			throw new \Exception("Unknown category ID. Unable to return category");
		}

	}

}