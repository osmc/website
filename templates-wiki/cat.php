<div class="row clearfix main wiki-page w-cat">
  <div class="container">
    <div class="column three-fourths">

      <h1 class="page-title" itemprop="headline"><?php echo $post_title ?></h1>

      <div class="byline">
        <p>Text here</p>
        <div class="cat">
          <a></a>
        </div>
      </div>

      <section class="entry-content clearfix" itemprop="articleBody">
      <h2 class="desc"><p>This is a place to get help and support for OSMC. Please post in the category you are running OSMC on, unless it is a generic issue.</p></h2>
      <ul>
      <?php foreach($cat_titles as $key=>$value): ?>
        <a href="<?php echo $cat_links[$key]; ?>">
          <li>
            <h3><?php echo $value; ?></h3>
            <?php echo $cat_descs[$key]; ?>
          </li>
        </a>
      <?php endforeach; ?>
      </ul>
      </section>

    </div>
    <?php echo $wp_sidebar ?>
  </div>
</div>