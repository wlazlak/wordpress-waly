<?php

namespace Lucien144\Wordpress\Waly;

class Category {

	private $category;

	public function __construct(\stdClass $category)
	{
		$this->category = $category;
	}

	public function __get($name)
	{

		switch($name) {
			case 'id':
				return $this->category->cat_ID;
				break;
			case 'link':
				return get_category_link($this->category->cat_ID);
				break;
		}

		if (isset($this->category->{'category_' . $name})) {
			return $this->category->{'category_' . $name};
		} elseif (isset($this->category->{$name})) {
			return $this->category->{$name};
		} else {
			throw new \Exception('Unknown category attribute "' . $name . '"');
		}
	}

}