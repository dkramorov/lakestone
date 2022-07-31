<div id="banner<?php echo $module; ?>" class="banner_faq">
  <?php foreach ($banners as $banner) { ?>
  <div class="item">
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></a>
    <?php } else { ?>
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
    <?php } ?>
  </div>
  <? if ($banner['title']) { ?>
  <div class="text-block title"><?=$banner['title']?></div>
  <? } ?>
  <? if ($banner['text']) { ?>
  <div class="text-block text"><?=$banner['text']?></div>
  <? } ?>
  <? if ($banner['button']) { ?>
  <div class="text-block button"><button type="button" class="btn btn-blue" data-toggle="modal" data-target="#faq_modal_question"><?=$banner['button']?></button></div>
  <? } ?>
  <?php } ?>
</div>
