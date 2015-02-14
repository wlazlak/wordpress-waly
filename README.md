# Waly
An OOP layer for Wordpress 

## Usage
```php
use Lucien144\Wordpress\Waly\Waly;


/**
 * Post
 * @var Lucien144\Wordpress\Waly\Post
 */
$page = Waly::getPost();
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
$categories = Waly::getcategories(); // slug or ID


/**
 * Author
 * @var Lucien144\Wordpress\Waly\Author
 */
$author = Waly::getPost()->author
$author = $author->author;
