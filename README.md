# Waly
An OOP layer for Wordpress 

# Main features
- All get methods allow you to find by ID or slug Waly::getPost(1) vs. Waly::getPost('blog')
- Object chaining: Waly::getPost()->author->email

# Usage
```php
use Lucien144\Wordpress\Waly\Waly;


/**
 * Post
 * @var Lucien144\Wordpress\Waly\Post
 */
$page = Waly::getPost(); // Current page/post
$page = Waly::getPost(1); // Find by ID
$page = Waly::getPost('blog'); // Find by slug
$articles = Waly::getPosts('blog'); // Category slug or ID

// Featured image
$imgUrl = $page->photo;

// Featured image - resize
add_image_size("image_300x200", '304', '200'); // Add this to your functions.php
$imgUrl = $page->photo('image_300x200');

// Content
// Waly removes the "post_" prefix automatically and fix the naming conventions like ID -> id
$page->id
$page->title
$page->content
$page->excerpt
...


/**
 * Categories
 * @type Array of Lucien144\Wordpress\Waly\Category
 */
$categories = Waly::getcategories();

/**
 * Category
 * @var Lucien144\Wordpress\Waly\Category
 */
$category = Waly::getCategory(1); // ID
$category = Waly::getCategory('news'); // ID


/**
 * Author
 * @var Lucien144\Wordpress\Waly\Author
 */
$author = Waly::getPost()->author
$author = $author->author;
