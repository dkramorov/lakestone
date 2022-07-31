<?php echo $header; ?>
<div class="container">
  <ul itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo $breadcrumb['href']; ?>"><span itemprop="name"><?php echo $breadcrumb['text']; ?></span></a></li>
    <?php } ?>
  </ul>
  <hr>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6 col-md-7'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9 col-md-10'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>">
      <div class="banner_gifts">
        <img class="background-image" src="image/banner_top_high.jpg">
        <div class="image-cover"></div>
        <div class="banner-content">
          <div class="row">
            <div class="col-sm-12">
              <div class="title">
                <div class="line-throw line-throw-left"></div>
                <h1 class=""><?php echo $heading_title; ?></h1>
                <div class="line-throw line-throw-right"></div>
              </div>
            </div>
          </div>
          <div class="row t-logo">
            <div class="col-xs-3 text-center">
              <img src="image/t-logo1.png" alt="technic-logo">
              <div class="text">Все изделия выполнены только из натуральной кожи</div>
            </div>
            <div class="col-xs-3  text-center">
              <img src="image/t-logo2.png" alt="technic-logo">
              <div class="text">Мы придерживаемся традиций ручной работы</div>
            </div>
            <div class="col-xs-3 text-center">
              <img src="image/t-logo3.png" alt="technic-logo">
              <div class="text">Гарантия на все изделия 365 дней и 20 дней на обмен / возврат</div>
            </div>
            <div class="col-xs-3 text-center">
              <img src="image/t-logo4.png" alt="technic-logo">
              <div class="text">Бесплатная доставка по всей РФ соплатой при получении</div>
            </div>
          </div>
        </div>
<?/*
        <div class="banner-search">
          <div class="grid">
            <div class="col text-center">
              <div class="text">Поиск подарка по категориям:</div>
            </div>
            <div class="col text-center">
              <div class="input-group search">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input name="gifts_search" type="text" class="form-control" placeholder="Например: подарок для папы">
              </div>
            </div>
            <div class="col text-center">
              <div class="button"><button class="btn btn-blue">Искать подарок</button></div>
            </div>
          </div>
        </div>
*/?>
      </div>
      <div class="content_wrap">
        <?php echo $content_top; ?>
        <div class="text_canvas-gifts">
          <?php foreach ($categories as $i => $category) { ?>
          <div class="row">
            <div class="col-xs-3 col-sm-2 col-md-1"><img class="cat_image" src="/image/<?=$category['image']?>" alt="<?=$category['title']?>"></div>
            <div class="col-xs-9 col-sm-10 col-md-11">
              <div class="cat_title"><?=$category['title']?></div>
              <div class="row">
                <?php foreach ($category['links'] as $link) { ?>
                <div class="col-sm-4"><div class="gifts-item"><a href="<?=$link['href']?>"><?=$link['title']?></a></div></div>
              <? }?>
            </div>
            </div>
          </div>
          <? if ($i < sizeof($categories) - 1) { ?><hr><? } ?>
          <? } ?>
        </div>
      </div>
      <?php echo $content_bottom; ?>
    </div>
    <?php //echo $column_right; ?>
  </div>
</div>
<script>
$(document).ready(function() {
  $('.banner-search input[name="gifts_search"]').on('input change', function(e) {
    console.log(e, this)
  })
})
</script>
<?php echo $footer; ?>
