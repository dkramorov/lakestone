<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-category" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
		<?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
		<?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-category"
                      class="form-horizontal">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                        <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
                        <li><a href="#tab-design" data-toggle="tab"><?php echo $tab_design; ?></a></li>
                        <li><a href="#tab-links" data-toggle="tab">Добавочные линки</a></li>
                        <li><a href="#tab-add_links" data-toggle="tab">Линки для продуктов</a></li>
                        <li><a href="#tab-add_links_accessories" data-toggle="tab">Линки для аксессуаров</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">
                            <ul class="nav nav-tabs" id="language">
								<?php foreach ($languages as $language) { ?>
                                    <li><a href="#language<?php echo $language['language_id']; ?>"
                                           data-toggle="tab"><img
                                                    src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png"
                                                    title="<?php echo $language['name']; ?>"/> <?php echo $language['name']; ?>
                                        </a></li>
								<?php } ?>
                            </ul>
                            <div class="tab-content">
								<?php foreach ($languages as $language) { ?>
                                    <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                                        <div class="form-group required">
                                            <label class="col-sm-2 control-label"
                                                   for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                                            <div class="col-sm-10">
                                                <input type="text"
                                                       name="category_description[<?php echo $language['language_id']; ?>][name]"
                                                       value="<?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['name'] : ''; ?>"
                                                       placeholder="<?php echo $entry_name; ?>"
                                                       id="input-name<?php echo $language['language_id']; ?>"
                                                       class="form-control"/>
												<?php if (isset($error_name[$language['language_id']])) { ?>
                                                    <div
                                                            class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
												<?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"
                                                   for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                                            <div class="col-sm-10">
												<textarea
                                                        name="category_description[<?php echo $language['language_id']; ?>][description]"
                                                        placeholder="<?php echo $entry_description; ?>"
                                                        id="input-description<?php echo $language['language_id']; ?>"
                                                        class="form-control summernote"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['description'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"
                                                   for="input-tag-h1<?php echo $language['language_id']; ?>"><?php echo $entry_tag_h1; ?></label>
                                            <div class="col-sm-10">
                                                <input type="text"
                                                       name="category_description[<?php echo $language['language_id']; ?>][tag_h1]"
                                                       value="<?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['tag_h1'] : ''; ?>"
                                                       placeholder="<?php echo $entry_tag_h1; ?>"
                                                       id="input-tag-h1<?php echo $language['language_id']; ?>"
                                                       class="form-control"/>
												<?php if (isset($error_tag_h1[$language['language_id']])) { ?>
                                                    <div
                                                            class="text-danger"><?php echo $error_tag_h1[$language['language_id']]; ?></div>
												<?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"
                                                   for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                                            <div class="col-sm-10">
                                                <input type="text"
                                                       name="category_description[<?php echo $language['language_id']; ?>][meta_title]"
                                                       value="<?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_title'] : ''; ?>"
                                                       placeholder="<?php echo $entry_meta_title; ?>"
                                                       id="input-meta-title<?php echo $language['language_id']; ?>"
                                                       class="form-control"/>
												<?php if (isset($error_meta_title[$language['language_id']])) { ?>
                                                    <div
                                                            class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
												<?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"
                                                   for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                                            <div class="col-sm-10">
												<textarea
                                                        name="category_description[<?php echo $language['language_id']; ?>][meta_description]"
                                                        rows="5" placeholder="<?php echo $entry_meta_description; ?>"
                                                        id="input-meta-description<?php echo $language['language_id']; ?>"
                                                        class="form-control"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label"
                                                   for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                                            <div class="col-sm-10">
												<textarea
                                                        name="category_description[<?php echo $language['language_id']; ?>][meta_keyword]"
                                                        rows="5" placeholder="<?php echo $entry_meta_keyword; ?>"
                                                        id="input-meta-keyword<?php echo $language['language_id']; ?>"
                                                        class="form-control"><?php echo isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-data">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-parent"><?php echo $entry_parent; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="path" value="<?php echo $path; ?>"
                                           placeholder="<?php echo $entry_parent; ?>" id="input-parent"
                                           class="form-control"/>
                                    <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>"/>
									<?php if ($error_parent) { ?>
                                        <div class="text-danger"><?php echo $error_parent; ?></div>
									<?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip"
                                                                                               title="<?php echo $help_filter; ?>"><?php echo $entry_filter; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="filter" value="" placeholder="<?php echo $entry_filter; ?>"
                                           id="input-filter" class="form-control"/>
                                    <div id="category-filter" class="well well-sm"
                                         style="height: 150px; overflow: auto;">
										<?php foreach ($category_filters as $category_filter) { ?>
                                            <div id="category-filter<?php echo $category_filter['filter_id']; ?>"><i
                                                        class="fa fa-minus-circle"></i> <?php echo $category_filter['name']; ?>
                                                <input type="hidden" name="category_filter[]"
                                                       value="<?php echo $category_filter['filter_id']; ?>"/>
                                            </div>
										<?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                                <div class="col-sm-10">
                                    <div class="well well-sm" style="height: 150px; overflow: auto;">
                                        <div class="checkbox">
                                            <label>
												<?php if (in_array(0, $category_store)) { ?>
                                                    <input type="checkbox" name="category_store[]" value="0"
                                                           checked="checked"/>
													<?php echo $text_default; ?>
												<?php } else { ?>
                                                    <input type="checkbox" name="category_store[]" value="0"/>
													<?php echo $text_default; ?>
												<?php } ?>
                                            </label>
                                        </div>
										<?php foreach ($stores as $store) { ?>
                                            <div class="checkbox">
                                                <label>
													<?php if (in_array($store['store_id'], $category_store)) { ?>
                                                        <input type="checkbox" name="category_store[]"
                                                               value="<?php echo $store['store_id']; ?>"
                                                               checked="checked"/>
														<?php echo $store['name']; ?>
													<?php } else { ?>
                                                        <input type="checkbox" name="category_store[]"
                                                               value="<?php echo $store['store_id']; ?>"/>
														<?php echo $store['name']; ?>
													<?php } ?>
                                                </label>
                                            </div>
										<?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-keyword"><span data-toggle="tooltip"
                                                                                                title="<?php echo $help_keyword; ?>"><?php echo $entry_keyword; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="keyword" value="<?php echo $keyword; ?>"
                                           placeholder="<?php echo $entry_keyword; ?>" id="input-keyword"
                                           class="form-control"/>
									<?php if ($error_keyword) { ?>
                                        <div class="text-danger"><?php echo $error_keyword; ?></div>
									<?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-yml_path"><span data-toggle="tooltip"
                                                                                                 title="<?php echo $help_yml_path; ?>"><?php echo $entry_yml_path; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="yml_path" value="<?php echo $yml_path; ?>"
                                           placeholder="Все товары/Авто/Автомобильные инструменты/Домкраты и подставки"
                                           id="input-yml_path" class="form-control"/>
									<?php if ($error_yml_path) { ?>
                                        <div class="text-danger"><?php echo $error_yml_path; ?></div>
									<?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-ozon_category"><span
                                            data-toggle="tooltip" title="<?php echo $help_filter; ?>">Категория в классификаторе <a
                                                target="_blank"
                                                href="https://cb-api.ozonru.me/apiref/ru/#t-title_get_categories_tree">Ozon</a></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="ozon_category" value="<?php echo $ozon_category; ?>"
                                           placeholder="" id="input-ozon_category" class="form-control"/>
                                    <input type="hidden" name="ozon_category_id"
                                           value="<?php echo $ozon_category_id; ?>" placeholder=""
                                           id="input-ozon_category_id" class="form-control"/>
									<?php if ($error_ozon_category) { ?>
                                        <div class="text-danger"><?php echo $error_ozon_category; ?></div>
									<?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_image; ?></label>
                                <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image"
                                                          class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt=""
                                                                                     title=""
                                                                                     data-placeholder="<?php echo $placeholder; ?>"/></a>
                                    <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_icon; ?></label>
                                <div class="col-sm-10"><a href="" id="thumb-icon" data-toggle="image"
                                                          class="img-thumbnail"><img src="<?php echo $thumb_icon; ?>"
                                                                                     alt="" title=""
                                                                                     data-placeholder="<?php echo $placeholder; ?>"/></a>
                                    <input type="hidden" name="icon" value="<?php echo $icon; ?>" id="input-icon"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-top"><span data-toggle="tooltip"
                                                                                            title="<?php echo $help_top; ?>"><?php echo $entry_top; ?></span></label>
                                <div class="col-sm-10">
                                    <div class="checkbox">
                                        <label>
											<?php if ($top) { ?>
                                                <input type="checkbox" name="top" value="1" checked="checked"
                                                       id="input-top"/>
											<?php } else { ?>
                                                <input type="checkbox" name="top" value="1" id="input-top"/>
											<?php } ?>
                                            &nbsp; </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-bottom"><span data-toggle="tooltip"
                                                                                               title="<?php echo $help_bottom; ?>"><?php echo $entry_bottom; ?></span></label>
                                <div class="col-sm-10">
                                    <div class="checkbox">
                                        <label>
											<?php if ($bottom) { ?>
                                                <input type="checkbox" name="bottom" value="1" checked="checked"
                                                       id="input-bottom"/>
											<?php } else { ?>
                                                <input type="checkbox" name="bottom" value="1" id="input-bottom"/>
											<?php } ?>
                                            &nbsp; </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-column"><span data-toggle="tooltip"
                                                                                               title="<?php echo $help_column; ?>"><?php echo $entry_column; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="column" value="<?php echo $column; ?>"
                                           placeholder="<?php echo $entry_column; ?>" id="input-column"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="sort_order" value="<?php echo $sort_order; ?>"
                                           placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-special_tag">Специальный таг</label>
                                <div class="col-sm-10">
                                    <input type="text" name="special_tag" value="<?php echo $special_tag; ?>"
                                           id="input-special_tag" class="form-control"/>
                                </div>
                                <div class="col-sm-10 col-sm-offset-2">
                                    <p>Таги перечислять через запятую, без пробелов. Принимаются следующие таги:</p>
                                    <ol>
                                        <li><b>gifts</b> - включает всю категорию и все ее подкатегории в специальный
                                            раздел: <a href="/gifts">подарки</a></li>
                                        <li><b>hidden</b> - скрывает всю категорию и все ее подкатегории во всех
                                            картах/схемах
                                        </li>
                                        <li><b>sale</b> - включает категорию в категорию <a
                                                    href="/sale">"распродажа"</a></li>
                                        <li><b>profitable_set</b> - включает категорию и все ее подкатегории в программу
                                            "выгодный комплект"
                                        </li>
                                    </ol>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-status"><?php echo $entry_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="status" id="input-status" class="form-control">
										<?php if ($status) { ?>
                                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                            <option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
                                            <option value="1"><?php echo $text_enabled; ?></option>
                                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-design">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-left"><?php echo $entry_store; ?></td>
                                        <td class="text-left"><?php echo $entry_layout; ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="text-left"><?php echo $text_default; ?></td>
                                        <td class="text-left"><select name="category_layout[0]" class="form-control">
                                                <option value=""></option>
												<?php foreach ($layouts as $layout) { ?>
													<?php if (isset($category_layout[0]) && $category_layout[0] == $layout['layout_id']) { ?>
                                                        <option value="<?php echo $layout['layout_id']; ?>"
                                                                selected="selected"><?php echo $layout['name']; ?></option>
													<?php } else { ?>
                                                        <option
                                                                value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
													<?php } ?>
												<?php } ?>
                                            </select></td>
                                    </tr>
									<?php foreach ($stores as $store) { ?>
                                        <tr>
                                            <td class="text-left"><?php echo $store['name']; ?></td>
                                            <td class="text-left"><select
                                                        name="category_layout[<?php echo $store['store_id']; ?>]"
                                                        class="form-control">
                                                    <option value=""></option>
													<?php foreach ($layouts as $layout) { ?>
														<?php if (isset($category_layout[$store['store_id']]) && $category_layout[$store['store_id']] == $layout['layout_id']) { ?>
                                                            <option value="<?php echo $layout['layout_id']; ?>"
                                                                    selected="selected"><?php echo $layout['name']; ?></option>
														<?php } else { ?>
                                                            <option
                                                                    value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
														<?php } ?>
													<?php } ?>
                                                </select></td>
                                        </tr>
									<?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-links">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-left">Имя</td>
                                        <td class="text-left">Линк</td>
                                        <td class="text-left">Порядок сортировки</td>
                                    </tr>
                                    </thead>
                                    <tbody>
									<? $CategoryLinkId = 0; ?>
									<? foreach ($category_links as $category_link) { ?>
                                        <tr id="category_link_<?= $CategoryLinkId ?>">
                                            <td><input name="category_link[<?= $CategoryLinkId ?>][name]"
                                                       value="<?= $category_link['name'] ?>" class="form-control"></td>
                                            <td><input name="category_link[<?= $CategoryLinkId ?>][href]"
                                                       value="<?= $category_link['href'] ?>" class="form-control"></td>
                                            <td><input name="category_link[<?= $CategoryLinkId ?>][sort_order]"
                                                       value="<?= $category_link['sort_order'] ?>" class="form-control">
                                            </td>
                                            <td>
                                                <button type="button"
                                                        onclick="$('#category_link_<?= $CategoryLinkId++ ?>').remove();"
                                                        data-toggle="tooltip" title="Удалить" class="btn btn-danger"><i
                                                            class="fa fa-minus-circle"></i></button>
                                            </td>
                                        </tr>
									<? } ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-left">
                                            <button type="button" onclick="addCategoryLink();" data-toggle="tooltip"
                                                    title="Добавить" class="btn btn-primary"><i
                                                        class="fa fa-plus-circle"></i></button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-add_links">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    Тут размещаются линки, которые выводятся в карточке товара как дополнительные линки
                                    "Посмотрите еще". Эти линки будут выводится для всех товаров этой категории, если у
                                    товара нет своих собственных дополнительных линков.
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-left">Имя</td>
                                        <td class="text-left">Линк</td>
                                        <td class="text-left">Порядок сортировки</td>
                                    </tr>
                                    </thead>
                                    <tbody>
									<? $CategoryProductLinkId = 0; ?>
									<? foreach ($category_product_links as $category_product_link) { ?>
                                        <tr id="category_link_<?= $CategoryProductLinkId ?>">
                                            <td><input name="category_product_link[<?= $CategoryProductLinkId ?>][name]"
                                                       value="<?= $category_product_link['name'] ?>"
                                                       class="form-control"></td>
                                            <td><input name="category_product_link[<?= $CategoryProductLinkId ?>][href]"
                                                       value="<?= $category_product_link['href'] ?>"
                                                       class="form-control"></td>
                                            <td><input
                                                        name="category_product_link[<?= $CategoryProductLinkId ?>][sort_order]"
                                                        value="<?= $category_product_link['sort_order'] ?>"
                                                        class="form-control"></td>
                                            <td>
                                                <button type="button"
                                                        onclick="$('#category_product_link_<?= $CategoryProductLinkId++ ?>').remove();"
                                                        data-toggle="tooltip" title="Удалить" class="btn btn-danger"><i
                                                            class="fa fa-minus-circle"></i></button>
                                            </td>
                                        </tr>
									<? } ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="text-left">
                                            <button type="button" onclick="addProductCategoryLink();"
                                                    data-toggle="tooltip" title="Добавить" class="btn btn-primary"><i
                                                        class="fa fa-plus-circle"></i></button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-add_links_accessories">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    Ссылки с иконок для страниц товаров
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <td class="text-left"></td>
                                        <td class="text-left">Имя</td>
                                        <td class="text-left">Линк</td>
                                    </tr>
                                    </thead>
                                    <tbody>
									<? foreach ($category_accessory_links as $icon_id => $category_accessory_link): ?>
                                        <tr>
                                            <td>
                                                <div class="image"><img src="<?= $category_accessory_link['icon'] ?>"
                                                                        alt=""></div>
                                            </td>
                                            <td><input name="category_accessory_link[<?= $icon_id ?>][name]"
                                                       value="<?= $category_accessory_link['name'] ?>"
                                                       class="form-control"
                                                       placeholder="<?= $category_accessory_link['name_ph'] ?>"></td>
                                            <td><input name="category_accessory_link[<?= $icon_id ?>][href]"
                                                       value="<?= $category_accessory_link['href'] ?>"
                                                       class="form-control"
                                                       placeholder="<?= $category_accessory_link['href_ph'] ?>"></td>
                                        </tr>
									<? endforeach; ?>
									<? /* $CategoryProductLinkId = 0; ?>
									<? foreach ($category_product_links as $category_product_link) { ?>
										<tr id="category_link_<?= $CategoryProductLinkId ?>">
											<td><input name="category_product_link[<?= $CategoryProductLinkId ?>][name]"
											           value="<?= $category_product_link['name'] ?>"
											           class="form-control"></td>
											<td><input name="category_product_link[<?= $CategoryProductLinkId ?>][href]"
											           value="<?= $category_product_link['href'] ?>"
											           class="form-control"></td>
											<td><input
												  name="category_product_link[<?= $CategoryProductLinkId ?>][sort_order]"
												  value="<?= $category_product_link['sort_order'] ?>"
												  class="form-control"></td>
											<td>
												<button type="button"
												        onclick="$('#category_product_link_<?= $CategoryProductLinkId++ ?>').remove();"
												        data-toggle="tooltip" title="Удалить" class="btn btn-danger"><i
													  class="fa fa-minus-circle"></i></button>
											</td>
										</tr>
									<? } */ ?>
                                    </tbody>
									<? /* ?>
									<tfoot>
									<tr>
										<td colspan="3"></td>
										<td class="text-left">
											<button type="button" onclick="addProductCategoryLink();"
											        data-toggle="tooltip" title="Добавить" class="btn btn-primary"><i
												  class="fa fa-plus-circle"></i></button>
										</td>
									</tr>
									</tfoot>
                                    <? */ ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
    <link href="view/javascript/summernote/summernote.css" rel="stylesheet"/>
    <script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
    <script type="text/javascript"><!--
		var CategoryLinkId = <?=$CategoryLinkId?>;

		function addCategoryLink() {
			CategoryLinkId += 1;
			var html = '<tr id="category_link_' + CategoryLinkId + '">';
			html += '<td><input name="category_link[' + CategoryLinkId + '][name]" class="form-control"></td>';
			html += '<td><input name="category_link[' + CategoryLinkId + '][href]" class="form-control"></td>';
			html += '<td><input name="category_link[' + CategoryLinkId + '][sort_order]" class="form-control"></td>';
			html += '<td><button type="button" onclick="$(\'#category_link_' + CategoryLinkId + '\').remove();" data-toggle="tooltip" title="Удалить" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
			html += '</td>';
			$('#tab-links tbody').append(html);
		}

		var CategoryProductLinkId = <?=$CategoryProductLinkId?>;

		function addProductCategoryLink() {
			CategoryProductLinkId += 1;
			var html = '<tr id="category_product_link_' + CategoryProductLinkId + '">';
			html += '<td><input name="category_product_link[' + CategoryProductLinkId + '][name]" class="form-control"></td>';
			html += '<td><input name="category_product_link[' + CategoryProductLinkId + '][href]" class="form-control"></td>';
			html += '<td><input name="category_product_link[' + CategoryProductLinkId + '][sort_order]" class="form-control"></td>';
			html += '<td><button type="button" onclick="$(\'#category_product_link_' + CategoryProductLinkId + '\').remove();" data-toggle="tooltip" title="Удалить" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
			html += '</td>';
			$('#tab-add_links tbody').append(html);
		}

		$('input[name=\'path\']').autocomplete({
			'source': function (request, response) {
				$.ajax({
					url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request),
					dataType: 'json',
					success: function (json) {
						json.unshift({
							category_id: 0,
							name: '<?php echo $text_none; ?>'
						});

						response($.map(json, function (item) {
							return {
								label: item['name'],
								value: item['category_id']
							}
						}));
					}
				});
			},
			'select': function (item) {
				$('input[name=\'path\']').val(item['label']);
				$('input[name=\'parent_id\']').val(item['value']);
			}
		});
		//--></script>
    <script type="text/javascript"><!--
		$('input[name=\'yml_path\']').autocomplete({
			'source': function (request, response) {
				$.ajax({
					url: 'index.php?route=catalog/category/autocomplete_yml_path&token=<?php echo $token; ?>&yml_path=' + encodeURIComponent(request),
					dataType: 'json',
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item['path'],
								value: item['path']
							}
						}));
					}
				});
			},
			'select': function (item) {
          console.log(item)
				$('input[name="yml_path"]').val(item['value']);
			}
		});
		$('input[name=\'filter\']').autocomplete({
			'source': function (request, response) {
				$.ajax({
					url: 'index.php?route=catalog/filter/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request),
					dataType: 'json',
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item['name'],
								value: item['filter_id']
							}
						}));
					}
				});
			},
			'select': function (item) {
				$('input[name=\'filter\']').val('');

				$('#category-filter' + item['value']).remove();

				$('#category-filter').append('<div id="category-filter' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category_filter[]" value="' + item['value'] + '" /></div>');
			}
		});

		$('#category-filter').delegate('.fa-minus-circle', 'click', function () {
			$(this).parent().remove();
		});
		//--></script>
    <script type="text/javascript"><!--
		$('input[name=\'ozon_category\']').autocomplete({
			'source': function (request, response) {
				$.ajax({
					url: 'index.php?route=catalog/category/autocomplete_ozon&token=<?php echo $token; ?>&id=' + encodeURIComponent(request),
					dataType: 'json',
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item['name'],
								value: item['id'],
							}
						}));
					}
				});
			},
			'select': function (item) {
				console.log(item)
				$('input[name=\'ozon_category_id\']').val(item['value']);
				$('input[name=\'ozon_category\']').val(item['label']);
			}
		});
		//--></script>
    <script type="text/javascript"><!--
		$('#language a:first').tab('show');
		//--></script>
</div>
<?php echo $footer; ?>
