<?php if ($reviews) { ?>
  <? if (sizeof($all_images) > 0) { ?>
  <div><strong>Фотографии покупателей</strong></div>
  <div class="module_owl_product_list">
    <div id="carousel-review_all_images" class="owl-carousel review-images">
      <? foreach ($all_images as $image) { ?>
      <div class="item text-center">
        <img data-src="<?=$image['thumb']?>" class="img-responsive owl-lazy" onclick="showReviewImages(this)" alt="photo" data-popup_src="<?=$image['popup']?>" role="button">
      </div>
      <? } ?>
    </div>
  </div>
  <hr>
  <script type="text/javascript"><!--
  let $owl = $('#carousel-review_all_images');
  let owl_config = {
      autoPlay: 3000,
      // center: true,
      stagePadding: 20,
      loop: true,
      margin: 10,
      dots: false,
      lazyLoad: true,
      lazyLoadEager: 1,
      navText: ['<svg class="owl_button"><path d="M10 0 l-10 10 l10 10" filter="url(#feShadow)"></svg>', '<svg class="owl_button"><path d="M0 0 l10 10 l-10 10" filter="url(#feShadow)"></svg>'],
      pagination: true,
      responsive: {
          0: {
              items: 2,
              margin: 5,
          },
          320: {
              items: 3,
              margin: 5,
          },
          414: {
              items: 4,
              margin: 5,
          },
          480: {
              items: 6,
          },
          768: {
              items: 8,
              nav: true,
          },
          991: {
              items: 10,
              // nav: true,
          },
          1200: {
              items: 12,
              // dotsEach: 3,
              slideBy: 3,
              nav: true,
          },
      },
  }
  let count = $('.item', $owl).length;
  let responsive_sizes = Object.keys(owl_config.responsive).sort(function(a, b) {return a - b})
  let w = $(window).width();
  let current;
  for (let i=0; i<responsive_sizes.length; i++) {
      let size = responsive_sizes[i];
      if (w >= size) current = owl_config.responsive[size];
  }
  if (count < current.items) {
      // owl_config.center = false;
      owl_config.loop = false;
  }
  console.log(owl_config)
  $owl.owlCarousel(owl_config);
  --></script>
  <? } ?>
  <? foreach ($reviews as $review) { ?>
  <div class="review">
    <? if ($review['type'] == 0) { ?>
    <script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@type": "Review",
      "author": "<?php echo $review['author']; ?>",
      "datePublished": "<?php echo $review['date_added']; ?>",
      "description": "<?php echo $review['text']; ?>",
      "reviewRating": {
        "@type": "Rating",
        "bestRating": "5",
        "ratingValue": "<? echo $review['rating'] ?>",
        "worstRating": "1"
      },
      <?php
      if ($review['review_images']) {
        foreach ($review['review_images'] as $image) {
          echo '"image":"' . $image['popup'] . '",';
        }
      }
      ?>
      "itemReviewed": {
        "@type": "Thing",
        "name": "<?=$product_name ?? ''?>",
        "image": "<?=$product_image ?? ''?>",
        "url": "<?=$product_href ?? ''?>"
      }
    }
    </script>
    <? } ?>
    <div class="review-header flex2">
      <div class="left">
        <div class="name">
          <span class="author"><?=$review['author']?></span><span class="date"><?=$review['date_added']?></span>
        </div>
      </div>
      <div class="right likes">
        <div class="module">
          <span data-id="<?=$review['review_id']?>" data-token="<?=$review['like_token']?>" class="like btn" onclick="like(this)"><svg class="svg-like"><use xlink:href="#svg-like"></svg></span><span class="value like_value"><?=$review['like']?></span>
          <span data-id="<?=$review['review_id']?>" data-token="<?=$review['unlike_token']?>" class="unlike btn" onclick="unlike(this)"><svg class="svg-unlike"><use xlink:href="#svg-like"></svg></span><span class="value unlike_value"><?=$review['unlike']?></span>
        </div>
      </div>
    </div>
    <div class="review-body"><?=$review['text']?></div>
    <?php if ($review['review_images']) { ?>
    <hr>
    <div class="review-images">
      <?php foreach ($review['review_images'] as $image) { ?><img onclick="showReviewImages(this)" class="" src="<?=$image['thumb']?>" alt="photo" data-popup_src="<?=$image['popup']?>" role="button"><?php } ?>
    </div>
    <?php } ?>
    <?php if ($review['type'] == 0) { ?>
    <hr>
    <div class="review-footer">
      <div class="rating rating-main">
        <span class="text">Общая оценка:</span>
        <span class="stars">
          <?php for ($i = 1; $i <= 5; $i++) { ?>
          <?php if ($review['rating'] < $i) { ?>
          <svg class="star"><use xlink:href="#svg-star"></svg>
          <?php } else { ?>
          <svg class="star full"><use xlink:href="#svg-star"></svg>
          <?php } ?>
          <?php } ?>
        </span>
      </div>
      <div class="rating rating-photo">
        <span class="text">Соответствие фотографии:</span>
        <span class="stars">
          <?php for ($i = 1; $i <= 5; $i++) { ?>
          <?php if ($review['useful_photo'] < $i) { ?>
          <svg class="star"><use xlink:href="#svg-star"></svg>
          <?php } else { ?>
          <svg class="star full"><use xlink:href="#svg-star"></svg>
          <?php } ?>
          <?php } ?>
        </span>
      </div>
      <div class="rating rating-description">
      <span class="text">Соответствие описанию:</span>
      <span class="stars">
        <?php for ($i = 1; $i <= 5; $i++) { ?>
        <?php if ($review['useful_description'] < $i) { ?>
        <svg class="star"><use xlink:href="#svg-star"></svg>
        <?php } else { ?>
        <svg class="star full"><use xlink:href="#svg-star"></svg>
        <?php } ?>
        <?php } ?>
      </span>
      </div>
    </div>
    <? } ?>
    <? if (!empty($review['answer'])) { ?>
    <div class="respond-delim"></div>
    <div class="respond">
      <div class="name">
        <span class="author">Администрация</span><span class="date"><?=$review['date_responded']?></span>
      </div>
      <div class="text">
        <?=$review['answer']?>
      </div>
    </div>
    <? } ?>
  </div>
  <? } ?>
  <? if ($more) { ?>
    <div class="text-center"><button class="btn btn-blue" data-page="<?=$page?>" onClick="moreReviews(this)">показать еще</button></div>
  <? } ?>
<?php } else { ?>
<p><?php echo $text_no_reviews; ?></p>
<?php } ?>
