<div id="banner_collection<?php echo $module; ?>" class="banner_collection">
  <?php foreach ($banners as $banner) { ?>
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>">
    <?php } ?>
    <div class="item">
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
    <?php } ?>
    </div>
    <?php if ($banner['link']) { ?>
    </a>
    <?php } ?>
</div>
