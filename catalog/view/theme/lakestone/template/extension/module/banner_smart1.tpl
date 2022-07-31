<style>
.hover-effect01 {
  position: relative;
/*  z-index: 1; */
}
.hover-effect01:before {
  background-color: rgba(255, 255, 255, 0.15);
  content: "";
  height: 0;
  left: 0;
  margin: auto;
  position: absolute;
  top: 0;
  -webkit-transition: all 0.3s ease-out 0s;
  transition: all 0.3s ease-out 0s;
  width: 0;
/*  z-index: -1; */
}
.hover-effect01:after {
  background-color: rgba(255, 255, 255, 0.15);
  bottom: 0;
  content: "";
  height: 0;
  position: absolute;
  right: 0;
  -webkit-transition: all 0.3s ease-out 0s;
  transition: all 0.3s ease-out 0s;
  width: 0;
/*  z-index: -1; */
}
.hover-effect01:hover:after,
.hover-effect01:hover:before {
  height: 100%;
  width: 100%;
}
.hover-effect07 {
  overflow: hidden;
  position: relative;
}
/*
.hover-effect07 > span {
  z-index: 10;
}
*/
.hover-effect07:hover:after {
    left: 120%;
    -webkit-transition: all 1000ms cubic-bezier(0.19, 1, 0.22, 1);
    transition: all 1000ms cubic-bezier(0.19, 1, 0.22, 1);
}
.hover-effect07:after {
  background: #fff;
  content: "";
  height: 155px;
  left: -75px;
  opacity: .5;
  position: absolute;
  top: -50px;
  -webkit-transform: rotate(35deg);
  -ms-transform: rotate(35deg);
  transform: rotate(35deg);
  -webkit-transition: all 1000ms cubic-bezier(0.19, 1, 0.22, 1);
  transition: all 1000ms cubic-bezier(0.19, 1, 0.22, 1);
  width: 50px;
  z-index: 1;
}
.banner_smart1 img {
  border: solid thin black;
}
.banner_smart1 .delim {
  height: 23px;
}
.banner_smart1 .banner_smart_slider .owl-dots {
  position: absolute;
  opacity: 0;
  transition: opacity ease 0.5s;
  bottom: 5%;
  right: 5%;
}
.banner_smart1 .banner_smart_slider:hover .owl-dots {
  transition: opacity ease 0.5s;
  opacity: 1;
}
.banner_smart1 .banner_smart_slider .owl-dots .owl-dot {
  border: solid thin black;
  height: 4px;
  width: 15px;
  margin: 0 2px;
}
.banner_smart1 .banner_smart_slider .owl-dots .owl-dot.active {
  background-color: black;
}
.banner_smart1 .banner_smart_slider:hover .owl-nav > button {
  opacity: 0.5;
}
.banner_smart1 .banner_smart_slider .owl-nav > button:hover {
  opacity: 1;
}
.banner_smart1 .banner_smart_slider .owl-nav > button {
  transition: opacity ease 0.3s;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  opacity: 0.1;
  color: white;
  text-shadow: 2px 2px 4px black;
}
.banner_smart1 .banner_smart_slider .owl-nav .fa {
  font-size: 40px;
}
.banner_smart1 .banner_smart_slider .owl-nav .owl-prev {
  left: 1%;
}
.banner_smart1 .banner_smart_slider .owl-nav .owl-next {
  right: 1%;
}
.banner_smart1 .banner {
  position: relative;
}
.banner_smart1 .title, .banner_smart1 .text, .banner_smart1 .button {
  position: absolute;
}
.banner_smart1 .animated {
  opacity: 0;
}
.banner_smart1 .banner_smart_slider .animated {
  right: 10%;
}
.banner_smart1 .banner_smart_slider .title {
  top: 0;
  width: 40%;
}
.banner_smart1 .title,
.banner_smart1 .title1 {
  text-transform: uppercase;
  color: black;
  font-size: 4vh;
  line-height: 4vh;
  font-weight: bold;
}
.banner_smart1 .title1 {
  font-size: 3vh;
}
.banner_smart1 .text {
  top: 20%;
  width: 40%;
}
.banner_smart1 .button {
  top: 60%;
  width: 40%;
}
.banner_smart1 .button-black {
  /* margin: auto; */
  display: inline-block;
  padding: 10px 15px;
  text-transform: uppercase;
  text-align: center;
  font-weight: bold;
  color: white;
  background-color: black;
}
.banner_smart1 .banner_smart_right .button {
  top: 70%;
  z-index: 10;
}
.banner_smart1 .banner_smart_right .title {
  top: 5%;
}
.banner_smart1 .banner_smart_right .text {
  top: 30%;
}
.banner_smart1 div.to_right > div {
  right: 10%;
  float: right;
  text-align: right;
}
.banner_smart1 div.to_center > div {
  margin: auto;
  right: 0;
  width: 100%;
  text-align: center;
}
.banner_smart1 div.black {
  background-color: black;
}
.banner_smart1 div.black img {
  opacity: 0;
}
.banner_smart1 div.black .button > div {
  border: solid thin white;
  border-bottom: none;
}
</style>
<div id="banner_smart<?php echo $module; ?>" class="banner_smart1">
  <div class="row">
    <div class="col-sm-8">
      <div class="owl-carousel banner banner_smart_slider">
        <?php foreach ($banners as $banner) { ?>
        <div class="item">
          <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" class="img-responsive" />
          <? if ($banner['title']) { ?>
          <div class="animated title"><?=$banner['title']?></div>
          <? } ?>
          <? if ($banner['text']) { ?>
          <div class="animated text"><?=$banner['text']?></div>
          <? } ?>
          <? if ($banner['button']) { ?>
          <div class="animated button"><div class="button-black hover-effect07" role="button" <? if ($banner['link']) { echo 'onClick="document.location = \'' . $banner['link'] . '\'"';}?>><?=$banner['button']?></div></div>
          <? } ?>
        </div>
        <?php } ?>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="row">
        <div class="col-xs-12">
          <div class="banner banner_smart_right to_right hover-effect01">
            <img src="<?php echo $banner1['image']; ?>" alt="<?php echo $banner1['title']; ?>" class="img-responsive" />
            <? if ($banner1['title']) { ?>
            <div class="title"><?=$banner1['title']?></div>
            <? } ?>
            <? if ($banner1['text']) { ?>
            <div class="text"><?=$banner1['text']?></div>
            <? } ?>
              <? if ($banner1['button']) { ?>
              <div class="button"><div class="title1" role="button" <? if ($banner1['link']) { echo 'onClick="document.location = \'' . $banner1['link'] . '\'"';}?>><?=$banner1['button']?></div></div>
              <? } ?>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12"><div class="delim"></div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="banner black banner_smart_right to_center">
            <img src="<?php echo $banner2['image']; ?>" alt="<?php echo $banner2['title']; ?>" class="img-responsive" />
            <? if ($banner2['title']) { ?>
            <div class="title"><?=$banner2['title']?></div>
            <? } ?>
            <? if ($banner2['text']) { ?>
            <div class="text"><?=$banner2['text']?></div>
            <? } ?>
            <? if ($banner2['button']) { ?>
              <div class="button"><div class="button-black hover-effect07" role="button" <? if ($banner2['link']) { echo 'onClick="document.location = \'' . $banner2['link'] . '\'"';}?>><?=$banner2['button']?></div></div>
            <? } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.2/TweenMax.min.js"></script>
<script type="text/javascript"><!--
var owlTr = function(e) {
  console.log('drag start', e)
  bannerOff($(e.target).find('.owl-item.active'))
}
var owlTred = function(e) {
  console.log('drag end', e)
  bannerOn($(e.target).find('.owl-item.active'))
}
var bannerOn = function(t) {
  console.log('ON', t)
  TweenLite.to( $(t).find('.title'), 1, {ease:Elastic.easeOut.config( 1.5, 0.75), y:'+=200%', opacity: 1})
  TweenLite.to( $(t).find('.text'), 1, {delay:0.5, ease:Elastic.easeOut.config( 1.5, 0.75), y:'+=200%', opacity: 1})
  TweenLite.to( $(t).find('.button'), 1, {delay:1, ease:Elastic.easeOut.config( 1.5, 0.75), y:'+=200%', opacity: 1})
}
var bannerOff = function(t) {
  console.log('OFF', t)
  TweenLite.to( $(t).find('.title'), 1, {ease:Elastic.easeOut.config( 1.5, 0.75), y: '-=200%', opacity: 0})
  TweenLite.to( $(t).find('.text'), 1, {ease:Elastic.easeOut.config( 1.5, 0.75), y: '-=200%', opacity: 0})
  TweenLite.to( $(t).find('.button'), 1, {ease:Elastic.easeOut.config( 1.5, 0.75), y: '-=200%', opacity: 0})
}
$('#banner_smart<?php echo $module; ?> .banner_smart_slider').owlCarousel({
	items: 1,
	autoPlay: 5000,
	onTranslate: owlTr,
	onRefreshed: owlTred,
	onTranslated: owlTred,
	animateOut: 'fadeOut',
	nav: true,
	rewind: true,
	slideTransition: 'fade',
  navText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
})
//.on('drag.owl.carousel next.owl.carousel prev.owl.carousel changed.owl.carousel', owlDrag)
--></script>
