<div id="order_placing" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
    	  <div class="row">
  	      <div class="col-sm-3">
            <svg class="map-marker" viewBox="0 0 512 512"><use xlink:href="#svg-map-marker"><span class="city"><?php echo $Locality ?></span>
            <button type="button" class="visible-xs close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><svg><path d="M0 0 L17 17 M0 17 L17 0"></svg></span></button>
          </div>
          <div class="col-sm-9 text-center">
            <span class="text"><?=$PickPointMessage?></span>
            <button type="button" class="hidden-xs close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><svg><path d="M0 0 L17 17 M0 17 L17 0"></svg></span></button>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-3 col-xs-12">
            <div class="title"><h3>Пункты выдачи заказов:</h3></div>
	           <div class="addresses"></div>
          </div>
          <div class="col-sm-9 col-xs-12">
            <div class="map-canvas">
              <div class="map"></div>
              <div class="cover">
                <div class="message"></div>
                <svg class="spinner"><use xlink:href="#svg-spinner"/></svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="SelectLocality" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <svg class="map-marker" viewBox="0 0 512 512"><use xlink:href="#svg-map-marker"><span class="city"><?php echo $Locality ?></span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><svg><path d="M0 0 L17 17 M0 17 L17 0"></svg></span></button>
      </div>
      <div class="modal-body">
        <div class="canvas">
          <div class="content">
            <div>Укажите ваш регион доставки. От выбора зависят условия доставки.</div>
            <div class="search form-group">
              <div class="dropdown">
               <div class="input-group">
               <input class="form-control input-sm" id="input_locality" type="text" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" placeholder="Начните писать название города">
               <?php /*<span role="button" class="input-group-addon" onClick="setLocality()"><svg class="svg-search" viewBox="0 0 56.966 56.966"><use xlink:href="#svg-search"></svg></span>*/?>
               </div>
              </div>
            </div>
            <div class="LikesLocality">
              <div class="row">
                <div class="col-sm-12"><div class="title">Популярные города:</div></div>
                <?php
                  foreach ($LikesLocality as $locality) {
                    echo '<div class="col-sm-4"><a ' . (!empty($locality['SubURL']) ? ' href="' . $locality['SubURL'] . '" ' : '') . '' . ($locality['RemoteLocality']?'onClick="setRemoteLocality(this)"':'') . '' . ($locality['LocalLocality']?'onClick="setLocality(\'' . $locality['Locality'] . '\')"':'') . ' class="priority_' . $locality['Priority'] . '" role="button" data-locality="' . $locality['RemoteLocality'] . '"  data-sub_domain="' . $locality['SubDomain'] . '">' . $locality['Locality'] . '</a></div>';
                  }
                ?>
              </div>
            </div>
          </div>
          <div class="wait-spinner hidden">
            <svg class="spinner"><use xlink:href="#svg-spinner"/></svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>