<div class="row clearfix main wiki-page w-post">
  <div class="container">
    <div class="column three-fourths">

      <h1 class="page-title" itemprop="headline"><?php echo $post_title ?></h1>

      <div class="byline">
        <a href="../../">Wiki</a><span> > </span><a href="../"><?php echo $post_cat ?></a>
        <div class="cat">
          <a href="<?php echo $post_url ?>">Make this page better</a>
        </div>
      </div>

      <section class="entry-content clearfix" itemprop="articleBody">

        <?php echo $post_body ?>

      </section>

    </div>
    <?php echo $wp_sidebar ?>
  </div>
</div>
