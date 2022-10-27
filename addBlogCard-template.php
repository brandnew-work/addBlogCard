<a href="<?php echo $args['url'] ?>" target="_blank" class="blog-card">
  <div class="blog-card__header">
    <?php if(!empty($args['img_tag'])): ?>
      <div class="blog-card__thumbnail">
        <?php echo $args['img_tag'] ?>
      </div>
    <?php endif; ?>
    <div class="blog-card__content">
      <div class="blog-card__title"><?php echo $args['title'] ?></div>
      <div class="blog-card__excerpt"><?php echo $args['excerpt'] ?></div>
    </div>
  </div>
  <div class="blog-card__footer">
    <div class="blog-card__site"><?php echo $args['site'] ?></div>
    <div class="blog-card__more"><span>続きを読む</span></div>
  </div>
</a>
