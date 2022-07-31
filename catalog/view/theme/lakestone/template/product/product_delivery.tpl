<? if ($courier) { ?>
<div class="item flex2">
  <div class="name">Курьерская доставка: <span class="cond"><?=$courier_cond?></span></div>
  <div class="cont"><?=$courier_cont?></div>
</div>
<? } ?>
<? if ($pickpoint) { ?>
<div class="item flex2">
  <div class="name">Пункты выдачи заказов: <span class="cond"><?=$pickpoint_cond?></span>
    <div class="addon"><a class="blue" title="Посмотреть и выбрать Пункт Выдачи Заказа" role="button" data-toggle="modal" data-target="#order_placing"><span class="red_arrow">&#8628;</span>Смотреть на карте</a></div>
  </div>
  <div class="cont"><?=$pickpoint_cont?></div>
</div>
<? } ?>
<? if ($post) { ?>
<div class="item flex2">
  <div class="name">Почта России: <span class="cond"><?=$post_cond?></span></div>
  <div class="cont"><?=$post_cont?></div>
</div>
<? } ?>
