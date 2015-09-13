<?php
global $newsletter; // Newsletter object
global $post; // Current post managed by WordPress

// This file is included inside a function so it inherit all the local variables.

// Since a theme has it's own options, it must check if there is new content to send
// out.
// Inside $theme_options['last_time'] there is the time stamps of the last run
// to be used to decide if we need to stop or not.

$filters = array();

$filters['showposts'] = (int)$theme_options['max_posts'];
if ($filters['showposts'] == 0) $filters['showposts'] = 10;

// This theme has an option with categories to be included.
if (is_array($theme_options['categories'])) {
    $filters['cat'] = implode(',', $theme_options['categories']);
}

$posts = get_posts($filters);

// Retrieve the posts asking them to WordPress
$posts = get_posts($filters);

?><?php echo $theme_options['theme_opening_text']; ?>

* <?php echo $theme_options['theme_title']; ?>


<?php
foreach ($posts as $post) {
    // Setup the post (WordPress requirement)
    setup_postdata($post);
?>
<?php the_title(); ?>

<?php the_permalink(); ?>


<?php } ?>


<?php echo $theme_options['theme_footer_text']; ?>

