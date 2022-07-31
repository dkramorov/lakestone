<div class="tab_description">
  <div class="characteristics">
    <div class="title">Технические характеристики:</div>
    <div class="attributes">
    <? foreach ($attributes as $attribute) { ?>
      <div class="item <?=$attribute['class']?>">
        <div class="name">
          <?=$attribute['name']?>
        </div>
        <div class="value">
          <?=$attribute['value']?>
        </div>
      </div>
    <? } ?>
    </div>
  </div>
  <div class="description">
    <div class="title">Описание:</div>
    <div class="text_description"><?=$fullDescription?></div>
    <div class="disclaimer">
      Производитель обладает исключительным правом менять изделие на свое усмотрение, внедряя таким образом результат работ по усовершенствованию конструктивных особенностей или технологии изготовления, не допуская при этом ухудшения технических и эксплуатационных характеристик изделия. Изображение готовой продукции, размещенное на сайте производителя, может иметь отличия от фактического внешнего вида товара.
    </div>
    <? if (sizeof($add_links) > 0) { ?>
    <div class='add_links'>
        <div class="title">Посмотрите еще:</div>
    <? foreach ($add_links as $link) { ?>
        <div class="add_link"><a target="_blank" href="<?=$link['href']?>" class="blue"><?=$link['name']?></a></div>
    <? } ?>
    </div>
    <? } ?>
  </div>
</div>
