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
      <?php if ( sizeof($categories) > 1 ) { ?>
      <nav class="navbar navbar-default black"><div class="container-fluid">
            <div class="navbar-header">
              <!--<span class="visible-xs-inline">Категории товаров</span>-->
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-categories-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <div style="white-space: nowrap !important;">
                    <span style="padding-right: 5px;">Категории товаров</span>
                    <!--<span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>-->
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>
              </button>
            </div>
            <div class="collapse navbar-collapse" id="bs-categories-navbar-collapse-1" style="margin-bottom:0">
              <ul class="nav navbar-nav">
        <?
            foreach ($categories as $categorie) {
                    if ( $categorie['name'] == 'Коллекция' ) continue;
                    //var_dump($categorie);
                    echo '<li' . ($categorie['active'] ? ' class="active"' : '') . '><a href="' . $categorie['href'] . '">' . $categorie['name'] . '</a></li>';
            }
        ?>
              </ul>
            </div>
      </div></nav>
      <?php } ?>
      <?php echo $content_bottom; ?>
      <?php if ($products) { ?>
<?php /*
      <div class="row">
        <div class="col-md-5 col-sm-4 col-xs-6">
          <div class="form-group input-group input-group-sm">
            <label class="input-group-addon" for="input-sort"><?php echo $text_sort; ?></label>
            <select id="input-sort" class="form-control" onchange="location = this.value;">
              <?php foreach ($sorts as $sorts) { ?>
              <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
              <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="col-md-5 col-xs-6">
          <div class="form-group input-group input-group-sm">
            <label class="input-group-addon" for="input-limit"><?php echo $text_limit; ?></label>
            <select id="input-limit" class="form-control" onchange="location = this.value;">
              <?php foreach ($limits as $limits) { ?>
              <?php if ($limits['value'] == $limit) { ?>
              <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
*/ ?>
      <div class="row">
        <?php foreach ($products as $product) { ?>
        <div class="product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12">
          <div class="product-thumb slider">
            <div class="image">
              <a href="<?php echo $product['href']; ?>">
                <img src="<?php echo $product['images'][0]; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
                <img src="<?php echo $product['images'][1]; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" />
                <span class="slider-link">Подробнее</span>
              </a>
              <span class="slider-cart" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i></span>
            </div>
            <div>
              <div class="caption">
                <span class="ersatz_head4"><a class="black tr2orange" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></span>
                <?php if ($product['price']) { ?>
                <p class="price">
                  <?php if (!$product['special']) { ?>
                  <?php echo $product['price']; ?>
                  <?php } else { ?>
                  <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
                  <?php } ?>
                  <?php if ($product['tax']) { ?>
                  <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                  <?php } ?>
                </p>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
      <hr>
      <div class="row">
        <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
        <div class="col-sm-6 text-right"><?php echo $results; ?></div>
      </div>
      <div class="row">
        <div class="col-sm-12"><?php echo $seo_text; ?></div>
      </div>
      <?php } ?>
      <?php if (!$categories && !$products) { ?>
      <p><?php echo $text_empty; ?></p>
      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
      </div>
      <?php } ?>
      <?php //echo $content_bottom; ?>
     </div>
    </div>
    <?php echo $column_right; ?>
  </div>
</div>
<?php echo $footer; ?>
