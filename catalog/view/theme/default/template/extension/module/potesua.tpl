<style type="text/css">
  #product_main .product_icons .content {
    height: 100%;
    display: flex;
    padding: 20px;
  }
  #product_main .product_icons .content .icon {
    padding: 10px;
  }
  #product_main .product_icons .content .img {
    width: 65px;
    height: 65px;
    background-color: #f3f3f3;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
  }
  #product_main .product_icons .content .desc {
    padding-top: 7px;
  }
  #product_main .product_icons .content .img,
  #product_main .product_icons .content .desc {
    text-align: center;
  }
</style>
<div class="content">
  <!--
  <div class="icon">
    <div class="img">
      <img src="/image/faq1.png" />
    </div>
    <div class="desc">
      Ноутбук до 15'
    </div>
  </div>
  -->
<?php
echo($test);
foreach ($thumbs as $image) {

?>
  <div class="icon">
    <div class="img">
      <img class="image img-responsive" src="<?php echo($image['preview']); ?>">
    </div>
    <div class="desc">
      <? echo $image['name']; ?>
    </div>
  </div>
<?php
  }
?>
  <div class="clearfix"></div>
</div>
