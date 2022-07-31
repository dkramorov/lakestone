<div id="banner_seo_link<?php echo $module; ?>" class="banner_collection banner_seo_link">
  <?php foreach ($banners as $banner) { ?>
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>">
    <?php } ?>
    <div class="item">
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
    <span class="banner_text text-center"><?php echo $category_name; ?></span>
    <?php } ?>
    </div>
    <?php if ($banner['link']) { ?>
    </a>
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
