<style>
.hover-effect02 {
  position: relative;
}
.hover-effect02:before {
  content: "";
  height: 100%;
  left: 0;
  position: absolute;
  top: 0;
  background: transparent;
  -moz-transition: all 300ms ease-in-out;
  -webkit-transition: all 300ms ease-in-out;
  -o-transition: all 300ms ease-in-out;
  transition: all 300ms ease-in-out;
  width: 100%;
}
.hover-effect02:hover:before {
  box-shadow: 0 0 0 40px rgba(255, 255, 255, 0.6) inset;
}
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
.banner_smart2 .block_maintitle,
.banner_smart2 .block_title {
  text-transform: uppercase;
  text-align: center;
  color: black;
  font-size: 3vh;
  line-height: 3vh;
  font-weight: bold;
  padding: 10px;
}
.banner_smart2 .block_maintitle {
  font-size: 5vh;
  line-height: 5vh;
  padding-top: 20px;
}
.banner_smart2 img {
  border: solid thin black;
}
.banner_smart2 .delim {
  height: 23px;
}
.banner_smart2 .banner {
  position: relative;
}
.banner_smart2 .title, .banner_smart2 .text, .banner_smart2 .button {
  position: absolute;
}
.banner_smart2 .title,
.banner_smart2 .title1 {
  text-transform: uppercase;
  color: black;
  font-size: 4vh;
  line-height: 4vh;
  font-weight: bold;
}
.banner_smart2 .title1 {
  font-size: 3vh;
}
.banner_smart2 .text {
  top: 20%;
  width: 40%;
}
.banner_smart2 .button {
  top: 60%;
  width: 40%;
}
.banner_smart2 .button-black {
  /* margin: auto; */
  display: inline-block;
  padding: 10px 15px;
  text-transform: uppercase;
  text-align: center;
  font-weight: bold;
  color: white;
  background-color: black;
}
.banner_smart2 .button-white {
  /* margin: auto; */
  display: inline-block;
  padding: 10px 15px;
  text-transform: uppercase;
  text-align: center;
  font-weight: bold;
  color: white;
  background-color: transaprent;
  border: solid thin white;
}
.banner_smart2 .banner_smart_static .button {
  top: 70%;
  z-index: 10;
}
.banner_smart2 .banner_smart_static .title {
  top: 5%;
}
.banner_smart2 .banner_smart_static .text {
  top: 30%;
}
.banner_smart2 div.to_right > div {
  right: 10%;
  float: right;
  text-align: right;
}
.banner_smart2 div.to_center {
  text-align: center;
}
.banner_smart2 div.to_center > div {
  margin: auto;
  right: 0;
  width: 100%;
  text-align: center;
}
.banner_smart2 div.black {
  background-color: black;
}
.banner_smart2 div.black img {
  opacity: 0;
}
.banner_smart2 div.black .button > div {
  border: solid thin white;
  border-bottom: none;
}
.banner_smart2 .banner-narrow > div {
  padding: 0 20%;
}
</style>
<div id="banner_smart<?php echo $module; ?>" class="banner_smart2">
  <? if ($maintitle) { ?>
    <div class="block_maintitle"><?=$maintitle?></div>
  <? } ?>
  <div class="row">
    <div class="col-sm-4">
      <? if ($title) { ?>
        <div class="block_title"><?=$title?></div>
      <? } ?>
      <div class="banner banner_smart_static banner-narrow to_center">
        <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
        <? if ($banner['title']) { ?>
        <div class="title"><?=$banner['title']?></div>
        <? } ?>
        <? if ($banner['text']) { ?>
        <div class="text"><?=$banner['text']?></div>
        <? } ?>
          <? if ($banner['button']) { ?>
          <div class="button"><div class="button-white hover-effect02" role="button" <? if ($banner['link']) { echo 'onClick="document.location = \'' . $banner['link'] . '\'"';}?>><?=$banner['button']?></div></div>
          <? } ?>
      </div>
    </div>
    <div class="col-sm-8">
      <div class="row">
        <div class="col-xs-12">
          <? if (isset($banner1)) { ?>
          <? if ($title1) { ?>
            <div class="block_title"><?=$title1?></div>
          <? } ?>
          <div class="banner banner_smart_static to_right hover-effect01">
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
          <? } ?>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12"><div class="delim"></div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <? if (isset($banner2)) { ?>
          <? if ($title2) { ?>
          <div class="block_title"><?=$title2?></div>
          <? } ?>
          <div class="banner banner_smart_static to_center">
            <? if ($banner2['video']) { ?>
            <img class="opacity_0" src="<?php echo $banner2['image']; ?>" alt="<?php echo $banner2['title']; ?>" class="img-responsive" />
            <iframe class="video hide" width="100%" height="100%" src="https://www.youtube.com/embed/<?=$banner2['video']['vid']?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            <? } else { ?>
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
            <? } ?>
          </div>
          <? } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$(window).load(function(){
  $('#banner_smart<?php echo $module; ?> iframe.video').each(function() {fixVideo($(this))})
})
var fixVideo = function(f) {
  var p = f.parent(),
      w = p.width(),
      h = p.height(),
      nw = w,
      nh = nw * 0.562
  if (nh > h) {
    nh = h
    nw = nh * 1.777
  }
  p.find('img').addClass('hide')
  f.removeClass('hide').width(nw).height(nh)
}
--></script>
