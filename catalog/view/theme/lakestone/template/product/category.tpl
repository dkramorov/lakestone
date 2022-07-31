<?php echo $header; ?>
<div class="container">
  <ul itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $breadcrumb['href']; ?>"><span itemprop="name"><?php echo $breadcrumb['text']; ?></span></a></li>
    <?php } ?>
  </ul>
  <hr>
  <div class="row">
    <?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
     <? if ($banner) { ?>
     <div class="banner_collection">
       <a href="<? echo $breadcrumbs[count($breadcrumbs)-1]['href'] ?>">
        <div class="item">
          <img src="<? echo $banner ?>" alt="<? echo $heading_title ?>" class="img-responsive" />
          <h1 class="banner_text text-center"><? echo $heading_title ?></h1>
        </div>
        </a>
     </div>
     <? } ?>
     <div class="content_wrap">
       <? if ($heading_title_h1) { ?>
       <h1 class="heading"><? echo $heading_title ?></h1>
      <? } else { ?>
        <div class="heading"><? echo $heading_title ?></div>
      <? } ?>
      <? if ($sub_categories) { ?>
       <div id="SubCategories">
         <? foreach ($sub_categories as $sub_category) { ?>
         <div class="col">
           <? if ($sub_category['icon']) { ?>
           <div class="icon">
             <? if ($sub_category['href']) { ?>
             <a href="<?=$sub_category['href']?>"><img src="<?=$sub_category['icon']?>" alt="<?=$sub_category['name']?>"></a>
             <? } else { ?>
             <img src="<?=$sub_category['icon']?>" alt="<?=$sub_category['name']?>">
             <? } ?>
           </div>
           <? } ?>
           <div class="content">
             <div class="header">
               <? if ($sub_category['href']) { ?>
               <a class="black" href="<?=$sub_category['href']?>"><?=$sub_category['name']?></a>
               <? } else { ?>
               <?=$sub_category['name']?>
               <? } ?>
             </div>
             <? if ($sub_category['links']) { ?>
             <div class="links">
             <? foreach ($sub_category['links'] as $link) { ?>
             <div class="item"><a class="black" href="<?=$link['href']?>"><?=$link['name']?></a></div>
             <? } ?>
             </div>
             <? } ?>
           </div>
         </div>
         <? } ?>
       </div>
      <? } ?>
      <?php echo $content_filter; ?>
      <?php if ($products) { ?>
      <div class="product-cssgrid">
        <?php foreach ($products as $product) { ?>
        <div class="product-layout">
          <div itemscope itemtype="http://schema.org/Product" class="product-thumb raizer <?echo (empty($product['images'][1])?'':'slider')?>">
            <meta itemprop="category" content="<? echo $breadcrumbs[count($breadcrumbs)-1]['text'] ?>" />
            <meta itemprop="image" content="<? echo $product['images'][0]; ?>" />
            <meta itemprop="productID" content="<?=$product['product_id']?>" />
            <div class="image">
              <a href="<?php echo $product['href']; ?>">
                <? if (!empty($product['images'][1])) { ?>
                  <img src="image/empty.png" data-src="<?php echo $product['images'][1]; ?>" alt="<?php echo $product['name']; ?>" class="img-responsive lazyLoad" style="width:<?=$setting['width']?>px; height:<?=$setting['height']?>px" />
                <? } ?>
                <img src="image/empty.png" data-src="<?php echo $product['images'][0]; ?>" alt="<?php echo $product['name']; ?>" class="img-responsive lazyLoad" style="width:<?=$setting['width']?>px; height:<?=$setting['height']?>px" />
              </a>
              <? if ($product['special']) {?>
                <span class="badge sale"><?=$product['sale']?></span>
              <? } ?>
              <? if ($product['quantity'] <= 0) {?>
                <span class="preorder">Предзаказ</span>
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
                    <a title="Оставить отзыв" class="blue" href="<?=$product['href']?>#reviews_anchor"><span itemprop="reviewCount"><?=$product['reviews_num']?></span><?=$product['reviews']?></a>
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
      <div class=""><?=$text_empty?></div>
    <? } ?>
      <? if ($reviews) { ?>
      </div>
    </div>
  </div>
</div>
<?php if (!empty($filter_tags)) { ?>
<div class="container">
  <div class="filter-tags"><div class="text">Также ищут: </div>
    <?php foreach ($filter_tags as $tag) { ?>
    <div class="item"><a href="<?=$tag['href']?>"><?=$tag['name']?></a></div>
    <?php } ?>
  </div>
</div>
<?php } ?>
<div class="container-fluid reviews">
  <div class="container">
        <div class="heading">Последние отзывы</div>
        <? foreach ($reviews as $review) { ?>
        <div class="review">
          <div class="left">
            <div class="name">
              <span class="author"><?=$review['name']?></span><span class="date"><?=$review['date']?></span>
            </div>
            <div class="rating">
              <?php for ($i = 1; $i <= 5; $i++) { ?>
              <?php if ($review['rating'] < $i) { ?>
              <svg class="star"><use xlink:href="#svg-star"></svg>
              <?php } else { ?>
              <svg class="star full"><use xlink:href="#svg-star"></svg>
              <?php } ?>
              <?php } ?>
            </div>
          </div>
          <div class="right">
            <div class="title"><a class="black" href="<?=$review['href']?>"><?=$review['product']?></a></div>
            <div class="text"><?=$review['text']?></div>
          </div>
        </div>
        <? } ?>
  </div>
</div>
<div class="container">
  <div class="row">
    <div class="<?php echo $class; ?>">
      <? } ?>
      <div class="row">
        <div class="col-sm-12"><?php echo $description; ?></div>
      </div>
      <?php if (!$categories && !$products) { ?>
      <p><?php echo $text_empty; ?></p>
      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
      </div>
      <?php } ?>
      <?php echo $content_bottom; ?>
     </div>
    </div>
    <?php echo $column_right; ?>
  </div>
</div>
<?php echo $footer; ?>
