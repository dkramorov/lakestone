<div id="banner<?php echo $module; ?>" class="banner_phone">
  <?php foreach ($banners as $banner) { ?>
  <div class="item">
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>">
    <? } ?>
      <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /><div class="img-cover"></div>
  </div>
  <? if ($banner['title']) { ?>
  <div class="text-block title"><?=$banner['title']?></div>
  <? } ?>
  <? if ($banner['text']) { ?>
  <div class="text-block text"><?=$banner['text']?></div>
  <? } ?>
  <? if ($banner['button']) { ?>
  <div class="text-block button"><?=$banner['button']?></div>
  <div class="text-block button2">бесплатный звонок</div>
  <? } ?>
  <?php } ?>
  <?php if ($banner['link']) { ?>
  </a>
  <? } ?>
</div>
