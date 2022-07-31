<div id="banner_home<?php echo $module; ?>" class="banner_home">
  <?php foreach ($banners as $banner) { ?>
  <div class="item">
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></a>
    <?php } else { ?>
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
    <?php } ?>
  </div>
  <p class="banner_text text-center">Ваш<br>персональный<br>гид по стилю!<br>
  <a class="btn btn-black2orange" href="/collection">Подробнее</a></p>
  <?php } ?>
</div>
<script type="text/javascript"><!--
/*
$('#banner<?php echo $module; ?>').owlCarousel({
	items: 6,
	autoPlay: 3000,
	singleItem: true,
	navigation: false,
	pagination: false,
	transitionStyle: 'fade'
});*/
--></script>
