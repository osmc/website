<?php echo file_get_contents($header); ?>
<div class="row clearfix main wiki-page w-cat">
  <div class="container">
    <div class="column three-fourths">

      <h1 class="page-title" itemprop="headline"><?php echo $post_title ?></h1>

      <div class="byline">
        <p>Get help with all things OSMC</p>
        <div class="cat">
          <a></a>
        </div>
      </div>

      <section class="entry-content clearfix" itemprop="articleBody">
      <h2 class="desc"><p>The OSMC Wiki is a comprehensive resource for all things OSMC and should be your first port of call. This Wiki is community managed -- we welcome any contributions!</p></h2>
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
    <?php echo $wp_sidebar; ?>
  </div>
</div>