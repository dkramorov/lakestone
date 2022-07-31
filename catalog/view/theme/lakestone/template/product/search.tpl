<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
     <div class="content_wrap">
      <h1><?php echo $heading_title; ?></h1>
      <label class="control-label" for="input-search"><?php echo $entry_search; ?></label>
      <div class="row">
        <div class="col-sm-4">
          <input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo $text_keyword; ?>" id="input-search" class="form-control" />
        </div>
        <div class="col-sm-3">
          <select name="category_id" class="form-control">
            <option value="0"><?php echo $text_category; ?></option>
            <?php foreach ($categories as $category_1) { ?>
            <?php if ($category_1['category_id'] == $category_id) { ?>
            <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
            <?php } ?>
            <?php foreach ($category_1['children'] as $category_2) { ?>
            <?php if ($category_2['category_id'] == $category_id) { ?>
            <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
            <?php } ?>
            <?php foreach ($category_2['children'] as $category_3) { ?>
            <?php if ($category_3['category_id'] == $category_id) { ?>
            <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
            <?php } ?>
            <?php } ?>
            <?php } ?>
            <?php } ?>
          </select>
        </div>
        <div class="col-sm-3">
          <label class="checkbox-inline">
            <?php if ($sub_category) { ?>
            <input type="checkbox" name="sub_category" value="1" checked="checked" />
            <?php } else { ?>
            <input type="checkbox" name="sub_category" value="1" />
            <?php } ?>
            <?php echo $text_sub_category; ?></label>
        </div>
      </div>
      <p>
        <label class="checkbox-inline">
          <?php if ($description) { ?>
          <input type="checkbox" name="description" value="1" id="description" checked="checked" />
          <?php } else { ?>
          <input type="checkbox" name="description" value="1" id="description" />
          <?php } ?>
          <?php echo $entry_description; ?></label>
      </p>
      <input type="button" value="<?php echo $button_search; ?>" id="button-search" class="btn btn-primary" />
      <h2><?php echo $text_search; ?></h2>
      <?php if ($products) { ?>


       <div class="product-cssgrid">
         <?php foreach ($products as $product) { ?>
         <div class="product-layout">
           <div itemscope itemtype="http://schema.org/Product" class="product-thumb raizer">
             <meta itemprop="category" content="<? echo $breadcrumbs[count($breadcrumbs)-1]['text'] ?>" />
             <meta itemprop="image" content="<? echo $product['images'][0]; ?>" />
             <div class="image">
               <a href="<?=$product['href']?>">
                 <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" data-src="<?=$product['images'][0]?>" alt="<?$product['name']?>" title="<?$product['name']?>" class="img-responsive lazyLoad" style="width:<?= (!empty($setting['width']) ? $setting['width'] . 'px' : 'auto') ?>; height:<?= (!empty($setting['height']) ? $setting['height'] . 'px' : 'auto') ?>" />
               </a>
               <? if ($product['special']) {?>
                 <span class="badge sale"><?=$product['sale']?></span>
               <? } ?>
             </div>
             <div class="caption">
                 <a class="black" title="<?=$product['name']?>" href="<?php echo $product['href']; ?>">
                   <?php if ($product['price']) { ?>
                   <div class="price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                     <meta itemprop="priceCurrency" content="RUB" />
                     <meta itemprop="price" content="<?php echo (int) $product['price']; ?>" />
                     <?php if (!$product['special']) { ?>
                     <?php echo $product['price']; ?>
                     <?php } else { ?>
                     <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
                     <?php } ?>
                     <?php if ($product['tax']) { ?>
                     <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                     <?php } ?>
                   </div>
                   <?php } ?>
                   <div class="title" itemprop="name"><?php echo $product['name']; ?></div>
                   <? if ($product['short']) { ?>
                   <div class="short-description" itemprop="description"><?=$product['short']?></div>
                   <? } ?>
                   <? if ($product['sku']) { ?>
                     <meta itemprop="sku" content="<?=$product['sku']?>" />
                   <? } ?>
                   <? if ($product['attributes']) { ?>
                   <div class="attributes">
                   <? foreach ($product['attributes'] as $attribute) { ?>
                     <div class="attribute">
                       <span class="name"><?=$attribute['name']?></span>:
                       <span class="value"><?=$attribute['text']?></span>
                     </div>
                   <? } ?>
                   </div>
                   <? } ?>
                 </a>
                 <? if ($product['rating']) { ?>
                 <div class="review" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                   <meta itemprop="ratingValue" content="<?=$product['rating']?>" />
                   <span class="stars">
                     <a href="<?=$product['href']?>#reviews">
                     <span class="rating">
                       <?php for ($i = 1; $i <= 5; $i++) { ?>
                       <?php if ($product['rating'] < $i) { ?>
                       <svg class="star"><use xlink:href="#svg-star"></svg>
                       <?php } else { ?>
                       <svg class="star full"><use xlink:href="#svg-star"></svg>
                       <?php } ?>
                       <?php } ?>
                     </span>
                     </a>
                   </span>
                   <span class="text">
                     <a title="Оставить отзыв" class="blue" href="<?=$product['href']?>#reviews"><span itemprop="reviewCount"><?=$product['reviews_num']?></span><?=$product['reviews']?></a>
                   </span>
                 </div>
                 <? } else { ?>
                 <div class="review">
                   <span class="stars">
                     <a href="<?=$product['href']?>#reviews">
                     <span class="rating">
                       <?php for ($i = 1; $i <= 5; $i++) { ?>
                       <svg class="star"><use xlink:href="#svg-star"></svg>
                       <?php } ?>
                     </span>
                     </a>
                   </span>
                   <span class="text">
                     <a class="blue" href="<?=$product['href']?>#reviews">отзывов пока нет</a>
                   </span>
                 </div>
                 <? } ?>
               </div>
             <div class="quickview">
               <div class="addon">
                 <div class="button" onclick="showQuickview(<?=$product['product_id']?>)">Быстрый просмотр</div>
               </div>
             </div>
           </div>
         </div>
         <?php } ?>
       </div>
       <hr>
       <div class="row">
         <div class="col-sm-12 text-center"><?php echo $pagination; ?></div>
       </div>











      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <?php echo $content_bottom; ?>
    </div>
   </div>
   <?php echo $column_right; ?>
  </div>
</div>
<script type="text/javascript"><!--
DocumentReady.push(function(){
	$('#button-search').bind('click', function() {
		url = 'index.php?route=product/search';

		var search = $('#content input[name=\'search\']').prop('value');

		if (search) {
			url += '&search=' + encodeURIComponent(search);
		}

		var category_id = $('#content select[name=\'category_id\']').prop('value');

		if (category_id > 0) {
			url += '&category_id=' + encodeURIComponent(category_id);
		}

		var sub_category = $('#content input[name=\'sub_category\']:checked').prop('value');

		if (sub_category) {
			url += '&sub_category=true';
		}

		var filter_description = $('#content input[name=\'description\']:checked').prop('value');

		if (filter_description) {
			url += '&description=true';
		}

		location = url;
	});

	$('#content input[name=\'search\']').bind('keydown', function(e) {
		if (e.keyCode == 13) {
			$('#button-search').trigger('click');
		}
	});

	$('select[name=\'category_id\']').on('change', function() {
		if (this.value == '0') {
			$('input[name=\'sub_category\']').prop('disabled', true);
		} else {
			$('input[name=\'sub_category\']').prop('disabled', false);
		}
	});

	$('select[name=\'category_id\']').trigger('change');
})
--></script>
<?php echo $footer; ?>
