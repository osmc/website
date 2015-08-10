<?php

/*
 * wiki.php
 *
 * (c) 2014-205 Sam Nazarko
 * email@samnazarko.co.uk
 *
 */

$num_errors = 0;
$calls = 0;

$_SERVER['HTTPS'] = 'on';
require_once('cms/wp-blog-header.php');

/* Generate footer and sidebar */
ob_start();
get_footer();
$wp_footer = ob_get_clean();
ob_end_clean();

ob_start();
get_sidebar();
$wp_sidebar = ob_get_clean();
ob_end_clean();

/* Make wiki directory */
$directory = "wiki-" . rand();
$base_file = "index.html";
$parent_contents_url = "https://discourse.osmc.tv/t/table-of-contents/6543.json";
mkdir($directory, 0775);
chdir($directory);

function get_json_obj($url) {
  global $num_errors;
  global $calls;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($ch);
  $errno = curl_errno($ch);
  curl_close($ch);
  if ($errno) {
    $num_errors +=1;
  }
  else {
    $calls +=1;
    return json_decode($output);
  }
}

foreach(glob("wiki-*") as $wikidir) {
  /* Clean up old Wiki */
  system("rm -rf $wikidir");
}

function get_slug_url($url) {
  $url = strtolower($url);
  $url = str_replace(" ", "-", $url);
  return $url;
}

function make_slug_dir($dirname) {
  $dirname = strtolower($dirname);
  $dirname = str_replace(" ", "-", $dirname);
  mkdir($dirname, 0755);
  return $dirname;
}

/* The page doesn't exist in Wordpress' SQL database, so we need to customise it */
$wp_title;
$page_title = "Wiki";

add_filter('wp_title', 'replace_title');
function replace_title() {
  global $wp_title;
  return " - " . $wp_title;
}

add_filter('body_class', 'replace_class');
function replace_class() {
  return array('wiki');
}

function wp_header($title) {
  global $wp_title;
  $wp_title = $title;

  ob_start();
  $return = include TEMPLATEPATH . "/header.php";
  $header = ob_get_clean();
  ob_end_clean();
  return $header;
}

/* Get the list of all available categories */
echo ("Downloading list of all available categories <br>");
$json_categories = get_json_obj($parent_contents_url);

$cat_titles = array();
$cat_links = array();
$cat_ids = array();
$cat_descs = array();

/* foreach $json_categories */
for ($i = 0; $i < count($json_categories->details->links); $i++) {

  $subcat_titles = array();
  $subcat_links = array();

  $tz = $json_categories->details->links[$i];

  /* Make a directory for each category */
  chdir(make_slug_dir($tz->title));

  $cat_title = $tz->title;
  array_push($cat_titles, $cat_title);

  $cat_link = get_slug_url($tz->title);
  array_push($cat_links, $cat_link);

  echo ("<br>" . $tz->title . "<br>");
  
  $json_category_pages = get_json_obj($tz->url . ".json");
  
  /* get urls from body - sorted */
  $cat_body = $json_category_pages->post_stream->posts[0]->cooked;
  $dom = new DOMDocument();
  $dom->loadHTML($cat_body);
  $divs = $dom->getElementsByTagName('img');
  
  /* get post ids */
  $sorted_ids = array();
  foreach ($divs as $key=>$div) {
    $class = $div->getAttribute("class");
    if ($class === "avatar") {
      $url = $div->nextSibling->getAttribute("href");
      $sorted_id = substr($url, strrpos($url, '/') + 1);
      array_push($sorted_ids, $sorted_id);
    }
  }
  
  /* unsorted ids to search */
  $unsorted_ids = array();
  for ($i2 = 0; $i2 < count($json_category_pages->details->links); $i2++) {
    $url = $json_category_pages->details->links[$i2]->url;
    $unsorted_id = substr($url, strrpos($url, '/') + 1);
    array_push($unsorted_ids, $unsorted_id);
  }
  
  /* foreach https://discourse.osmc.tv/t/vero/6559.json */
  for ($i3 = 0; $i3 < count($json_category_pages->details->links); $i3++) {
    
    $sorted_key = array_search($sorted_ids[$i3], $unsorted_ids);
    $tz = $json_category_pages->details->links[$sorted_key];
    
    if ($sorted_key && $tz->reflection == "0" && substr($tz->title, 0, 6) !== '/About') {

      chdir(make_slug_dir($tz->title));

      $subcat_title = $tz->title;
      array_push($subcat_titles, $subcat_title);

      $subcat_link = get_slug_url($tz->title);
      array_push($subcat_links, $subcat_link);

      /* Get the post contents */
      echo ("- " . $tz->title . "<br>");

      $post = get_json_obj($tz->url . ".json");
      $post_content = $post->post_stream->posts[0];

      $post_title = $post->title;
      $post_cat = $post->details->links[0]->title;
      $post_url = $tz->url;
      $post_body = $post_content->cooked;

      $wp_header = wp_header($post_title);

      ob_start();
      $return = include "templates-wiki/post.php";
      $wp_template = ob_get_clean();
      ob_end_clean();

      file_put_contents($base_file, $wp_header . $wp_template . $wp_footer);
      chdir("../");

    }
    elseif (substr($tz->title, 0, 6) === '/About') {

      /* Get description */
      $post = get_json_obj($tz->url . ".json");
      $post_content = $post->post_stream->posts[0];
      $excerpt = $post_content->cooked;
      array_push($cat_descs, $excerpt);
    }
  }

  /* Generate subcategory Wiki pages */
  $post_title = $cat_title;
  $post_desc = $cat_descs[$i];
  ob_start();
  $return = include "templates-wiki/subcat.php";
  $wp_template = ob_get_clean();
  ob_end_clean();
  $wp_header = wp_header($post_title);
  file_put_contents($base_file,$wp_header . $wp_template . $wp_footer);

  chdir("../");
}

/* Generate Wiki page */
$post_title = "Wiki";
ob_start();
$return = include "templates-wiki/cat.php";
$wp_template = ob_get_clean();
ob_end_clean();
$wp_header = wp_header($post_title);
file_put_contents($base_file, $wp_header . $wp_template . $wp_footer);


echo "<br><br> Errors: " . $num_errors;
echo "<br> Calls: " . $calls;
if ($num_errors == 0) {
  chdir("../");
  /* Replace the old Wiki */
  system("rm -rf wiki");
  rename(getcwd() . "/" . $directory, "wiki");
}

?>
