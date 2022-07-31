<?php if ($reviews) { ?>
<table class="table table-striped">
<?php foreach ($reviews as $review) { ?>
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
      "ratingCount": "<?=$review['ratingCount']?>",
      "worstRating": "1"
    },
  }
  </script>
  <tr>
    <td style="width: 50%;">
      <strong><?php echo $review['author']; ?></strong>
      <div class="date"><?php echo $review['date_added']; ?></div>
    </td>
    <td class="text-right likes">
      <span class="text">Отзыв полезен:</span>
      <div class="module">
        <span data-id="<?=$review['review_id']?>" data-token="<?=$review['like_token']?>" class="like btn" onclick="like(this)">
          <svg class="svg-like"><use xlink:href="#svg-like"></use></svg>
        </span><span class="value like_value"><?=$review['like']?></span>
        <span data-id="<?=$review['review_id']?>" data-token="<?=$review['unlike_token']?>" class="unlike btn" onclick="unlike(this)">
          <svg class="svg-unlike"><use xlink:href="#svg-like"></use></svg>
        </span><span class="value unlike_value"><?=$review['unlike']?></span>
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="rating">
      <? if ($review['type'] == 0) { ?>
      <span class="stars">
        <?php for ($i = 1; $i <= 5; $i++) { ?>
        <?php if ($review['useful_description'] < $i) { ?>
        <svg class="star"><use xlink:href="#svg-star"></svg>
        <?php } else { ?>
        <svg class="star full"><use xlink:href="#svg-star"></svg>
        <?php } ?>
        <?php } ?>
      </span>
      <? } ?>
      <p><?php echo $review['text']; ?></p>
      <? if (!empty($review['answer'])) { ?>
        <div class="respond">
          <img class="logo" src="/image/logo.svg" alt="Lakestone">
          <? echo $review['answer']; ?>
        </div>
      <? } ?>
    </td>
  </tr>
<?php } ?>
</table>
<div class="text-right"><?php echo $pagination; ?></div>
<?php } else { ?>
<p><?php echo $text_no_reviews; ?></p>
<?php } ?>
