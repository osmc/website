<?php echo file_get_contents($header); ?>
<div class="row clearfix main wiki-page w-subcat">
  <div class="container">
    <div class="column three-fourths">

      <h1 class="page-title" itemprop="headline"><?php echo $post_title ?></h1>

      <div class="byline">
        <a href="../">Wiki</a>
      </div>

      <section class="entry-content clearfix" itemprop="articleBody">
        <h2 class="desc"><?php echo $post_desc ?></h2>
        <ul>

        <?php foreach($subcat_titles as $key=>$value): ?>
          <a href="<?php echo $subcat_links[$key]; ?>">
            <li>
              <h3><?php echo $value; ?></h3>
            </li>
          </a>
        <?php endforeach; ?>
        
        </ul>
      </section>

    </div>
    <?php echo $wp_sidebar ?>
  </div>
</div>
<?php echo file_get_contents($footer); ?>