<div id="banner_schedule<?php echo $module; ?>" class="banner_schedule">
  <?php foreach ($banners as $banner) { ?>
    <?php if ($banner['link']) { ?><a href="<?php echo $banner['link']; ?>"><?php } ?>
    <div class="item">
      <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive opacity_2" />
    </div>
    <div class="banner_text text-center">
      <div class="title">ШОУ-РУМ</div>
      <div>г. Москва, ул. Новодмитровская, дом 5А, стр. 2</div>
      <hr>
      <div class="subtitle">Время работы</div>
      <div class="schedule">Мы работаем с 09:00 до 21:00 без выходных</div>
    </div>
    <?php if ($banner['link']) { ?></a><?php } ?>
  <?php } ?>
</div>
