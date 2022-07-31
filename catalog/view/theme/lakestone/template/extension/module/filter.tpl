<div id="module_filter">
  <? $sort_a = $sorts[$sort_num]; ?>
  <div class="mobile filter-root">
    <nav class="flex">
      <div onClick="window.history.back()"><svg class="svg-angle"><use xlink:href="#svg-angle-left"></svg> <a class="black">Назад</a></div>
      <div class="filters text-right" onClick="onMFilter()"><a class="black">Фильтры</a> <svg class="svg-angle"><use xlink:href="#svg-angle-right"></svg><svg class="circle"><circle cx="6" cy="6" r="5"></svg></div>
    </nav>
    <div class="hidden" id="mobile-filter-canvas">
      <div class="menu-filter hidden" id="mobile_filter_root">
        <div class="title text-center"  onClick="offMFilter()">
          Фильтры <svg class="svg-close"><use xlink:href="#svg-close"></svg>
        </div>
        <div class="menu">
          <? foreach ($filter_groups as $filter_group) { ?>
            <div class="item" data-filter_group_id="<?=$filter_group['filter_group_id']?>" onClick="switchFilter(this)"><span><?=$filter_group['title']?></span></div>
          <? } ?>
          <div class="item" onClick="switchFilter(this)"><span>Сортировать</span></div>
        </div>
        <div class="footer grid">
          <button disabled="yes" class="btn btn-default removeFilter" onClick="clearFilter()">Сбросить</button>
          <button class="btn btn-blue run_filter" onClick="doFilter('#mobile-filter-canvas')">Показать</button>
        </div>
      </div>
      <? foreach ($filter_groups as $filter_group) { ?>
      <div class="menu-filter hidden" id="mobile_filter_group_<?=$filter_group['filter_group_id']?>">
        <div class="title text-center" onClick="onMFilter()">
          <svg class="svg-back"><use xlink:href="#svg-angle-left"></svg> <?=$filter_group['title']?>
        </div>
        <div class="menu">
          <? foreach ($filter_group['filter'] as $filter) { ?>
            <div class="item">
              <label class="input-container"><?=$filter['name']?>
                <input onChange="changeFilter(<?=$filter_group['filter_group_id']?>)" name="filter<?=$filter['filter_id']?>" type="checkbox" value="<?=$filter['filter_tag']?>"
                <? if (in_array($filter['filter_tag'], $filter_category)) { ?>
                  checked="true"
                <? } ?>
                ><span class="checkmark"></span>
              </label>
            </div>
          <? } ?>
        </div>
        <div class="footer grid">
          <button disabled="yes" class="btn btn-default removeFilter" onClick="clearFilter(<?=$filter_group['filter_group_id']?>)">Сбросить</button>
          <button class="btn btn-blue run_filter" onClick="onMFilter()">Применить</button>
        </div>
      </div>
      <? } ?>
      <div class="menu-filter hidden" id="mobile_order_group">
        <div class="title text-center" onClick="onMFilter()">
          <svg class="svg-back"><use xlink:href="#svg-angle-left"></svg> Сортировать
        </div>
        <div class="menu">
          <? foreach ($sorts as $num => $sort_b) { ?>
            <div class="item">
              <label class="input-container"><?=$sort_b['text']?>
                <input name="order" type="radio" data-link="<?=$sort_b['href']?>"
                <? if ($sort_a['value'] == $sort_b['value']) { ?>
                  checked="true"
                <? } ?>
                ><span class="radiomark"></span>
              </label>
            </div>
          <? } ?>
        </div>
        <div class="footer grid">
          <button class="btn btn-default" onClick="selectOrder(0)">По умолчанию</button>
          <button class="btn btn-blue run_filter" onClick="applyOrder()">Применить</button>
        </div>
      </div>
    </div>
  </div>
  <div class="desktop grid filter-root">
    <div class="filter-group">
      <?php foreach ($filter_groups as $filter_group) { ?>
      <div class="btn-group btn-filter">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo $filter_group['name']; ?> <svg class="svg-angle"><use xlink:href="#svg-angle"></svg>
        </button>
        <button class="btn btn-default removeFilter" type="button" onClick="removeFilter(this)"><svg class="svg-remove"><use xlink:href="#svg-remove"></svg></button>
        <ul class="dropdown-menu">
          <ul class="scrolled_items">
            <?php foreach ($filter_group['filter'] as $filter) { ?>
              <li>
                <label class="input-container"><?=$filter['name']?>
                  <input name="filter<?php echo $filter['filter_id']; ?>" type="checkbox" value="<?php echo $filter['filter_tag']; ?>"
                  <?php if (in_array($filter['filter_tag'], $filter_category)) { ?>
                    checked="true"
                  <?php } ?>
                  ><span class="checkmark"></span>
                </label>
              </li>
            <?php } ?>
          </ul>
          <li>
            <button class="btn btn-blue run_filter" onClick="doFilter()">Применить</button>
          </li>
        </ul>
      </div>
      <?php } ?>
      <?php if ( ! empty($filter_set) ) { ?>
      <div class="clear_filter">
        <a title="отменить все фильтры" class="filter_set black" type="submit" role="button" data-filter-tag="all_filters"><span class="label label-default">отменить все<span><svg class="svg-remove"><use xlink:href="#svg-remove"></svg></a>
      </div>
      <?php } ?>
    </div>
    <div class="order-group text-right">Сортировка:
      <div class="btn-group btn-order">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?=$sort_a['text']?> <svg class="svg-angle"><use xlink:href="#svg-angle"></svg>
          </button>
          <ul class="dropdown-menu">
            <?php foreach ($sorts as $num => $sort_a) { ?>
              <li>
                <button onClick="location = '<?=$sort_a['href']?>'" type="button" class="btn btn-default
                  <?echo ($num == $sort_num ? 'active' : ''); ?>
                  " data-toggle="button" aria-pressed="false" value=""><?=$sort_a['text']?></button>
                </li>
              <?php } ?>
            </ul>
          </div>
    </div>
  </div>
</div>
<hr>
<script type="text/javascript"><!--
var onMFilter = function() {
  $('body').addClass('modal-open')
  var $p = $('#mobile_filter_root')
  var freeDrop = false
  $('#mobile-filter-canvas').removeClass('hidden').find('.menu-filter').addClass('hidden')
  $p.removeClass('hidden')
  $('.menu .item[data-filter_group_id]', $p).each(function() {
    var $i = $(this)
    var id = $(this).attr('data-filter_group_id')
    var fn = ''
    $('.filter_value', $i).remove()
    $('.filter_action', $i).remove()
    $('#mobile_filter_group_' + id).find('input[name^=\'filter\']:checked').each(function() {
      if (fn.length) fn += ', '
      fn += $(this).parent().text().trim()
    })
    if (fn.length) {
      freeDrop = true
      $i.append('<span class="filter_value">' + fn + '</span>').append('<span class="filter_action" onClick="clearFilter(' + id + ')">Сбросить</span>')
    }
  })
  if (freeDrop) $('.removeFilter', $p).prop('disabled', false)
}
var offMFilter = function() {
  $('body').removeClass('modal-open')
  $('#mobile-filter-canvas').addClass('hidden')
}
var switchFilter = function(t) {
  $('#mobile_filter_root').addClass('hidden')
  var id = $(t).attr('data-filter_group_id')
  if (typeof id === 'undefined') {
    $('#mobile_order_group').removeClass('hidden')
  } else {
    var $p = $('#mobile_filter_group_' + id)
    $p.removeClass('hidden')
    if ($('input[name^="filter"]:checked', $p).length) {
      $('.removeFilter', $p).prop('disabled', false)
    }
  }
}
var doFilter = function(s) {
  //var $r = $('#module_filter')
  s = (typeof s === 'undefined' ? '.btn-filter' : s)
  filter = [];
  $(s).find('input[name^=\'filter\']:checked').each(function(element) {
    filter.push(this.value);
  });
  if ( filter.length ) {
    location = '<?php echo $action_add; ?>' + filter.join(',')
  } else {
    location = '<?php echo $action; ?>'
  }
}
var checkActive = function() {
  $('#module_filter .btn-filter button.dropdown-toggle').each(function(){
    var $p = $(this).parent()
    var $bb = $p.find('>button')
    $bb.removeClass('active')
    if ( $p.find('input[name^="filter"]:checked').length ) {
      $p.addClass('active')
      $bb.addClass('active')
    }
  })
}
var changeFilter = function(id) {
  var $p = $('#mobile_filter_group_' + id)
  if ($('input[name^="filter"]:checked', $p).length) {
    $('.removeFilter', $p).prop('disabled', false)
  } else {
    $('.removeFilter', $p).prop('disabled', true)
  }
}
var clearFilter = function(id) {
  var vid = []
  var $r = $('#mobile_filter_root')
  if (typeof id === 'undefined') {
    $('.menu .item[data-filter_group_id]', $r).each(function(){
      vid.push($(this).attr('data-filter_group_id'))
    })
  } else {
    vid.push(id)
  }
  vid.forEach(function(id) {
    var $p = $('#mobile_filter_group_' + id)
    $('input[name^="filter"]:checked', $p).prop('checked', false)
    $('.removeFilter', $p).prop('disabled', true)
    var $i = $('.menu .item[data-filter_group_id=' + id +']', $r)
    $('.filter_value', $i).remove()
    $('.filter_action', $i).remove()
  })
  if (!$('input[name^="filter"]:checked', $r).length)
    $('.removeFilter', $r).prop('disabled', true)
  this.event.stopPropagation()
  return false
}
// var saveFilter = function(id) {
//   var $p = $('#mobile_filter_group_' + id)
//
// }
var removeFilter = function(t) {
  var $p = $(t).parent();
  var f = $(t).parents('.filter-root');
  if (typeof t === 'string') {
    $p = $(t)
    f = '#mobile-filter-canvas'
  }
  $('input[name^=\'filter\']:checked', $p).prop('checked', false)
  doFilter(f)
}
var selectOrder = function(i) {
  $('#mobile_order_group .menu input[name="order"]').eq(i).click()
}
var applyOrder = function() {
  var l = $('#mobile_order_group .menu input[name="order"]:checked').attr('data-link')
  if (window.location.href != l) window.location = l
  else offMFilter()
}
DocumentReady.push( function() {
  if ($(window).width() >= Defaults['screen_sm']) {
    checkActive()
    $('#module_filter .btn-filter button.dropdown-toggle').parent().on('hide.bs.dropdown', function (e) {
      checkActive()
    })
    $('#module_filter .btn-filter .dropdown-menu').on('click', function(e) {
      e.stopPropagation()
    })
  } else {
    if ($('#mobile-filter-canvas input[name^="filter"]:checked').length)
      $('#module_filter .mobile nav .filters').addClass('active')
  }
  $('.filter_set').on('click', function() {
    if ( $(this).attr('data-filter-tag') == 'all_filters' ) {
      $('#module_filter .btn-filter input[name^="filter"]:checked').prop('checked', false)
    } else {
      $('#module_filter .btn-filterp input[name^="filter"][value="'+$(this).attr('data-filter-tag')+'"]').prop('checked', false)
    }
    doFilter()
  })
})
//--></script>
