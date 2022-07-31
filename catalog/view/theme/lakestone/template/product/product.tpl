<?php echo $header; ?>
<div class="modal fade full" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="canvas">
                <div class="content"></div>
                <div class="wait-spinner">
                    <svg class="spinner">
                        <use xlink:href="#svg-spinner"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <ul itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">
		<?php foreach ($breadcrumbs as $i => $breadcrumb) { ?>
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a
                        id="breadcrumb_item_<?= $i + 1 ?>" itemscope itemtype="http://schema.org/Thing" itemprop="item"
                        href="<?php echo $breadcrumb['href']; ?>"><span
                            itemprop="name"><?php echo $breadcrumb['text']; ?></span></a>
                <meta itemprop="position" content="<?= $i + 1 ?>"/>
            </li>
		<?php } ?>
    </ul>
    <hr>
    <div class="row">
        <div id="content"><?php echo $content_top; ?>
            <div itemscope itemtype="http://schema.org/Product" class="content_wrap">
                <div id="product_main">
                    <div class="thumbs">
						<? foreach ($images as $i => $image) { ?>
                            <div class="item<? echo($i == 0 ? ' active' : '') ?>">
								<? if ($image['video']) { ?>
									<? if ($image['preview']) { ?>
                                        <img data-num="<?= $i ?>" src="<?= $image['preview'] ?>"
                                             class="video img-responsive">
									<? } else { ?>
                                        <img data-num="<?= $i ?>" src="image/empty.png" class="video img-responsive"
                                             width="90" height="90">
									<? } ?>
                                    <svg class="over_icon play" viewBox="0 0 512 512" data-video="<?= $image['href'] ?>"
                                         xml:space="preserve"><path
                                                d="m256 0c-140.96875 0-256 115.050781-256 256 0 140.96875 115.050781 256 256 256 140.96875 0 256-115.050781 256-256 0-140.96875-115.050781-256-256-256zm0 482c-124.617188 0-226-101.382812-226-226s101.382812-226 226-226 226 101.382812 226 226-101.382812 226-226 226zm0 0"/>
                                        <path
                                                d="m181 404.027344 222.042969-148.027344-222.042969-148.027344zm30-240 137.957031 91.972656-137.957031 91.972656zm0 0"/></svg>
                                    <svg class="over_icon pause" viewBox="0 0 512 512" style="display: none"
                                         xml:space="preserve"><path
                                                d="m256 0c-140.96875 0-256 115.050781-256 256 0 140.96875 115.050781 256 256 256 140.96875 0 256-115.050781 256-256 0-140.972656-115.050781-256-256-256zm0 482c-124.617188 0-226-101.382812-226-226s101.382812-226 226-226 226 101.382812 226 226-101.382812 226-226 226zm0 0"/>
                                        <path d="m151 361h90v-210h-90zm30-180h30v150h-30zm0 0"/>
                                        <path d="m271 361h90v-210h-90zm30-180h30v150h-30zm0 0"/></svg>
								<? } else { ?>
                                    <img data-num="<?= $i ?>" src="<?= $image['thumb'] ?>"
                                         data-big_src="<?= $image['big'] ?>" data-popup_src="<?= $image['popup'] ?>"
                                         class="image img-responsive">
								<? } ?>
                            </div>
						<? } ?>
                    </div>
                    <div class="image">
                        <div class="big">
                            <div class="canvas">
                                <div class="img" style="background-image:url('<?= $images[0]['popup'] ?>')"><img
                                            itemprop="image" src="<?= $images[0]['big'] ?>"
                                            data-popup_src="<?= $images[0]['popup'] ?>" class="img-responsive"></div>
                                <div class="video" style="display: none">
                                    <video _controls autoplay loop muted class="img-responsive"></video>
                                </div>
                                <div class="wait-spinner hidden">
                                    <svg class="spinner">
                                        <use xlink:href="#svg-spinner"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="popup hidden">
                            <div class="canvas">
                                <div class="zoom-content"></div>
                                <div class="wait-spinner">
                                    <svg class="spinner">
                                        <use xlink:href="#svg-spinner"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="product_icons" id="custom_product_icons"></div>

<!--
                    <div class="promo">
                        <div class="content">

                            <svg viewBox="0 0 16.21 16" width="16px">
                                <path
                                        d="M16,4.14,14.24,1.93a.71.71,0,0,0-.83-.23,2.76,2.76,0,0,1-1,.18A2.41,2.41,0,0,1,10.16.44.74.74,0,0,0,9.49,0H6.69A.74.74,0,0,0,6,.45,2.44,2.44,0,0,1,3.75,1.88a2.67,2.67,0,0,1-1-.18.72.72,0,0,0-.84.22L.16,4.15A.77.77,0,0,0,0,4.71a.7.7,0,0,0,.33.51,4.75,4.75,0,0,1,1.87,4A5.21,5.21,0,0,1,1,12.69a.77.77,0,0,0-.17.57.74.74,0,0,0,.32.51L3.6,15.4a.69.69,0,0,0,.89-.05,2,2,0,0,1,1.35-.45,1.84,1.84,0,0,1,1.59.72.74.74,0,0,0,.65.38.79.79,0,0,0,.65-.36h0a1.77,1.77,0,0,1,1.58-.71,2.13,2.13,0,0,1,1.37.46.75.75,0,0,0,.87,0L15,13.77a.72.72,0,0,0,.22-1l-.07-.1A5.21,5.21,0,0,1,14,9.24a4.76,4.76,0,0,1,1.88-4,.73.73,0,0,0,.32-.5A.75.75,0,0,0,16,4.14ZM3.65,9.24A6.46,6.46,0,0,0,1.74,4.48l1-1.26a4.37,4.37,0,0,0,1,.12,3.88,3.88,0,0,0,3.4-1.87H9a3.9,3.9,0,0,0,3.38,1.87,4.27,4.27,0,0,0,1-.12l1,1.26a6.46,6.46,0,0,0-1.91,4.76A6.94,6.94,0,0,0,13.54,13l-1.39.93a3.7,3.7,0,0,0-1.85-.47,3.58,3.58,0,0,0-2.22.71,3.58,3.58,0,0,0-2.22-.71A3.9,3.9,0,0,0,4,13.91L2.59,13A6.83,6.83,0,0,0,3.65,9.24Z"/>
                            </svg>
                            <div class="text">Изделия выполнены из отборной натуральной кожи</div>
                            <svg viewBox="0 0 17 18.3" width="17">
                                <path
                                        d="M15,2.61a2,2,0,0,0-.65.11V2.61a1.92,1.92,0,0,0-3-1.67,2,2,0,0,0-3.61.47,1.6,1.6,0,0,0-.65-.14,2,2,0,0,0-2,2V9.41a6.2,6.2,0,0,0-2.62-.59A2.64,2.64,0,0,0,0,11.11,2.07,2.07,0,0,0,1.18,13c2.94,1.64,4.7,3.37,4.7,4.64a.65.65,0,1,0,1.31,0c0-2.32-2.93-4.43-5.39-5.8H1.71a.8.8,0,0,1-.4-.7c0-.45.66-1,1.31-1a5.87,5.87,0,0,1,2.94,1,.64.64,0,0,0,.65,0,.78.78,0,0,0,.33-.65V3.26a.65.65,0,1,1,1.31,0V7.84a.66.66,0,1,0,1.31,0h0V2a.65.65,0,0,1,1.31,0V7.84a.65.65,0,0,0,1.31,0V2.61a.65.65,0,0,1,1.31,0V7.84a.65.65,0,0,0,1.31,0V4.57a.65.65,0,1,1,1.31,0v8.5A3.93,3.93,0,0,1,11.77,17H9.15a.65.65,0,1,0,0,1.31h2.62A5.24,5.24,0,0,0,17,13.07V4.57A2,2,0,0,0,15,2.61Z"/>
                            </svg>
                            <div class="text">Каждое изделие собирается вручную на всем этапе его производства</div>
                            <svg viewBox="0 0 17 16.42" width="17">
                                <path
                                        d="M6.61,8.59l.83.88,3.81-3.7a.5.5,0,0,1,.66.66L7.77,10.53a.5.5,0,0,1-.33.17.5.5,0,0,1-.33-.17L6.06,9.42,5.84,9.2a.61.61,0,0,1-.11-.44.5.5,0,0,1,.17-.28.44.44,0,0,1,.33-.11A.72.72,0,0,1,6.61,8.59Z"
                                        stroke="#000" stroke-miterlimit="10"/>
                                <path
                                        d="M15.9,5.79c-.26-.84-.05-2-.52-2.68s-1.63-.79-2.31-1.26S11.91.33,11.12.07,9.39.38,8.5.38,6.72-.19,5.88.07,4.62,1.27,3.93,1.8s-1.78.58-2.31,1.31S1.31,4.95,1.1,5.73,0,7.26,0,8.15s.79,1.63,1.1,2.47.05,1.94.52,2.62S3.25,14,3.93,14.5s1.15,1.57,1.94,1.84S7.61,16,8.5,16s1.78.63,2.62.31S12.38,15,13.06,14.5s1.78-.52,2.31-1.26.31-1.84.52-2.62S17,9.09,17,8.2,16.16,6.57,15.9,5.79ZM8.5,14.64a6.43,6.43,0,1,1,6.43-6.43A6.43,6.43,0,0,1,8.5,14.64Z"/>
                            </svg>
                            <div class="text">Все изделия сертифицированы и изготовлены в соответствии с техническим
                                регламентом таможенного союза
                            </div>
                        </div>
                    </div>
-->
                    <div class="info">
                        <h1 itemprop="name" class="title"><?= $heading_title ?></h1>
                        <meta itemprop="brand" content="<?= $brand ?>">
                        <meta itemprop="gtin13" content="<?= $ean ?>">
                        <link itemprop="url" href="<?= $product_href ?>">
                        <div itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
							<? foreach ($images as $image) { ?>
								<? if ($image['video']) continue; ?>
                                <link itemprop="contentUrl" href="<?= $image['big'] ?>">
							<? } ?>
                        </div>
                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="status">
                            <link itemprop="url" href="<?= $product_href ?>">
                            <meta itemprop="priceCurrency" content="<?= $currency ?>">
                            <meta itemprop="price" content="<?= $price_num ?>">
                            <meta itemprop="priceValidUntil" itemtype="https://schema.org/Date"
                                  content="<?= date('Y-m-d') ?>">
							<? if ($status and $quantity > 0) { ?>
                                <link itemprop="availability" href="http://schema.org/InStock"/>
                                <span class="blue-icon status-ok"></span> Товар есть в наличии
							<? } elseif ($status and $quantity <= 0) { ?>
                                <link itemprop="availability" href="http://schema.org/OutOfStock"/>
                                <span class="blue-icon status-not"></span> Ожидается поступление
							<? } else { ?>
                                <link itemprop="availability" href="http://schema.org/Discontinued"/>
                                <span class="blue-icon status-not"></span> Товар снят с производства
							<? } ?>
                        </div>
                        <div class="figures">
                            <div class="price">
								<?php if (!$special) { ?>
                                    <span class="price"><?= $price ?></span>
								<?php } else { ?>
                                    <span class="price price-new"><?= $special ?></span>
                                    <span class="price-old"><?= $price ?></span>
								<?php } ?>
                            </div>
                            <div class="model">Артикул: <span itemprop="sku"><?= $model ?></span></div>
                        </div>
                        <hr>
						<? if (!empty($attributes)) { ?>
                            <div class="attributes">
								<? foreach ($attributes as $attribute) { ?>
                                    <div class="attribute">
                                        <div class="name"><?= $attribute['name'] ?>:</div>
                                        <div class="value"><?= $attribute['text'] ?></div>
                                    </div>
								<? } ?>
                            </div>
                            <hr>
						<? } ?>
						<? if (sizeof($colors) > 1) { ?>
                            <div class="colors">
                                <div class="text">Выберите цвет:</div>
                                <div class="related">
									<? foreach ($colors as $product) { ?>
										<? /*
                 <div class="item<?=($product['product_id'] == $product_id ? ' active' : '')?>"><img src="<?=$product['image'][0]['thumb']?>" title="<?=$product['name']?>" data-big="<?=$product['image'][0]['image']?>" data-product_id="<?=$product['product_id']?>" class="img-responsive"></div>
*/ ?>
										<? if ($product['product_id'] == $product_id) { ?>
                                            <div class="item active"><img src="<?= $product['image'][0]['thumb'] ?>"
                                                                          title="<?= $product['name'] ?>"
                                                                          class="img-responsive"></div>
										<? } else { ?>
                                            <a class="item" href="<?= $product['href'] ?>"><img
                                                        src="<?= $product['image'][0]['thumb'] ?>"
                                                        title="<?= $product['name'] ?>" class="img-responsive"></a>
										<? } ?>
									<? } ?>
                                </div>
                            </div>
                            <hr>
						<? } ?>
						<?php if ($review_status and $reviews_num > 0) { ?>
							<? foreach ($reviews_array as $review) { ?>
                                <div itemprop="review" itemscope itemtype="http://schema.org/Review">
                                    <meta itemprop="datePublished" content="<?= $review['date_added'] ?>">
                                    <meta itemprop="author" content="<?= $review['author'] ?>">
                                    <meta itemprop="description" content="<?= $review['text'] ?>">
                                    <div itemprop="itemReviewed" itemscope itemtype="https://schema.org/Thing">
                                        <link itemprop="url" href="<?= $product_href ?>">
                                        <meta itemprop="name" content="<?= $heading_title ?>">
                                    </div>
                                    <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                                        <meta itemprop="ratingValue" content="<?= $review['rating'] ?>">
                                        <meta itemprop="bestRating" content="5">
                                        <meta itemprop="worstRating" content="1">
                                    </div>
                                </div>
							<? } ?>
                            <div class="reviews" itemprop="aggregateRating" itemscope
                                 itemtype="http://schema.org/AggregateRating">
                                <meta itemprop="ratingValue" content="<?= $rating ?>"/>
                                <div class="text">
                                    <a class="blue" href="<?= $review_href ?>#reviews_anchor">
                                        Отзывы покупателей: <span
                                                itemprop="reviewCount"><?php echo $reviews_num; ?></span><?= $reviews ?>
                                    </a>
                                </div>
								<? /*
             <a class="black" onClick="openReviewWrite()" href="<?=$review_write_href?>#review_write"><?php echo $text_write; ?></a>
             */ ?>
                                <div class="stars">
                                    <a href="<?= $review_href ?>#reviews_anchor">
										<?php for ($i = 1; $i <= 5; $i++) { ?>
											<?php if ($rating < $i) { ?>
                                                <svg class="star">
                                                    <use xlink:href="#svg-star">
                                                </svg>
											<?php } else { ?>
                                                <svg class="star full">
                                                    <use xlink:href="#svg-star">
                                                </svg>
											<?php } ?>
										<?php } ?></a>
                                </div>
                            </div>
						<?php } ?>
						<? if ($status) { ?>
                            <div class="buttons">
								<? if ($quantity > 0) { ?>
                                    <div>
                                        <button class="btn btn-red" onClick="cart.add(<?= $product_id ?>)">Добавить в
                                            корзину
                                        </button>
                                    </div>
                                    <div>
                                        <button class="btn btn-blue" onClick="cart.oneclick(<?= $product_id ?>)">Купить
                                            в один клик
                                        </button>
                                    </div>
								<? } else { ?>
                                    <div>
                                        <button class="btn btn-red" onClick="cart.preorder(<?= $product_id ?>)">
                                            Предзаказ
                                        </button>
                                    </div>
                                    <div></div>
								<? } ?>
                            </div>
						<? } ?>
                        <div class="advantages">
                            <ul class="ul-red">
                                <li>Бесплатная доставка;</li>
                                <li>Оплата при получении: никакой предоплаты;</li>
                                <li>Гарантия 365 дней;</li>
                                <li>Обмен / возврат в течении 30 дней.</li>
                            </ul>
                        </div>
                        <div class="delivery">
                            <div class="header flex2">
                                <div class="city">
                                    <strong>Доставка:</strong> <span class="locality"><?= $Locality ?></span>
                                </div>
                                <div class="manage">
                                    <a class="black" title="Посмотреть и выбрать город Доставки" role="button"
                                       data-toggle="modal" data-target="#SelectLocality">Изменить</a>
                                </div>
                            </div>
                            <div class="content"><?= $product_delivery ?></div>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="product_tabs">
                    <div class="header">
                        <div class="title">
                            Характеристики изделия
                        </div>
                        <div class="menu">
                            <div onClick="viewTab(this)" data-tab="description" class="item active">Описание и
                                характеристики
                            </div>
                            <div onClick="viewTab(this)" data-tab="warranty" class="item">Гарантия</div>
                            <div onClick="viewTab(this)" data-tab="moneyback" class="item">Условия обмена и возврата
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="content">
                        <div class="tab description"><?= $TabDescription ?></div>
                        <div class="tab warranty hide">
                            <p>Сумки и аксессуары от LAKESTONE производятся только из 100% натуральной кожи. Гарантия на
                                всю нашу продукцию составляет 365 дней со дня покупки. В случае обнаружения
                                производственного брака в процессе эксплуатации изделия, мы произведем бесплатный
                                ремонт. Вся продукция марки LAKESTONE сертифицирована и изготовлена в соответствии с
                                техническим регламентом Таможенного союза. Ссылка на сертификат.</p>

                            <p>Гарантия не распространяется на случаи, когда причиной порчи изделия стали причины,
                                вызванные некорректным использованием продукции, а именно:</p>
                            <ul>
                                <li>нанесение механических повреждений (царапин, потертостей, разрыва кожи/ткани);</li>
                                <li>носка очень тяжелых или острых предметов;</li>
                                <li>пребывание изделий в жидкой среде или в условиях экстремальных температур;</li>
                                <li>попадание на изделие любых жидкостей, кроме воды и т.д.</li>
                            </ul>
                            <h4 class="black">Обратите внимание, не является браком:</h4>
                            <ul>
                                <li>неоднородности и морщины кожи, небольшие царапины на фурнитуре, не портящие внешний
                                    вид изделия;
                                </li>
                                <li>неточное совпадение цвета товара с цветом картинки на Вашем мониторе (настройки
                                    монитора могут быть разными; цвет, который Вы видите на своем мониторе, цвет на
                                    фотографии и реальный цвет изделия могут иметь разные оттенки - причина как в
                                    искажениях мониторов компьютеров, так и фотоаппарата);
                                </li>
                                <li>естественный износ кожи со временем – появление царапин, потертостей, изменение
                                    формы;
                                </li>
                                <li>разрывы подкладки в местах отсутствия шва;</li>
                                <li>утеря элементов фурнитуры, закрепленных на винтах;</li>
                                <li>приобретение светлой кожей оттенка темной красящей одежды (джинсы, джинсовые куртки
                                    и т.п.)
                                </li>
                            </ul>
                            <p>Если у вас возникли вопросы, будем рады на них ответить по телефону: <strong><a
                                            class="black" href="<?= $telephone_href ?>"><?= $telephone ?></a></strong>.
                            </p>
                            <p>Либо, напишите нам по электронной почте: <a class="black"
                                                                           href="mailto:<?= $email ?>"><?= $email ?></a>
                            </p>
                        </div>
                        <div class="tab moneyback hide">
                            <h4 class="black">Возврат денег, если товар не подошел</h4>
                            <p>В случае, если товар по каким-либо причинам не подошел, его можно вернуть или обменять на
                                другой товар. Возврат товаров надлежащего качества (не нарушена комплектация товара,
                                отсутствуют признаки носки) осуществляется в течение 30 дней с момента покупки.</p>
                            <p>Для возврата или обмена необходимо отправить заявку в свободной форме на эл. почту: <a
                                        class="black" href="mailto:<?= $email ?>"><?= $email ?></a>, либо согласовать с
                                менеджером по телефону:</p>
                            <h4 class="black"><a class="black" href="<?= $telephone_href ?>"><?= $telephone ?></a></h4>
                            <p>Возврат или обмен товаров в Москве осуществляется по адресу: 127015, г. Москва, ул.
                                Новодмитровская, д. 5А, стр. 2. Денежные средства возвращаются в безналичной форме в
                                течение 10 дней с момента возврата товара (с момента получения товара магазином) на
                                основании ст.22 ФЗ «О защите прав потребителей».</p>
                            <p>Возврат товаров в других городах России осуществляется путем отправки Почтой России или
                                любым другим способом по адресу: 127015, г. Москва, ул. Новодмитровская, д. 5А, стр. 2
                                (для ООО "Лэйкстоун"). Денежные средства возвращаются в безналичной форме в течение 10
                                дней с момента возврата товара (с момента получения товара магазином) на основании ст.22
                                ФЗ «О защите прав потребителей».</p>
                            <p>Транспортные расходы оплачиваются покупателем. Возмещение транспортных расходов
                                покупателю производится только в случае, если товар имеет производственный брак, либо
                                был отправлен другой товар по ошибке.</p>
                        </div>
                    </div>
                </div>
				<?= $banner1 ?>

				<? if (!empty($accessoriesLinks)): ?>
                    <div class="_container-fluid grey">
                        <div class="_container">
							<? /* ?>
							<div class="heading">Не забудьте добавить любой аксессуар со скидкой 30%</div>
							<? */ ?>
                            <div id="profitable_set">
                                <div class="header">
                                    <div class="title">Выгодный комплект</div>
                                    <div class="text">30% скидка на аксессуар при покупки сумки</div>
                                    <div data-toggle="modal" data-target="#ModalDiscont" class="faq"><span
                                                class="blue-icon question"></span> <a class="blue">Как получить
                                            скидку</a>
                                    </div>
                                </div>
                            </div>
                            <div class="accessories">
								<? foreach ($accessoriesLinks as $link): ?>
                                    <div class="item">
                                        <a href="<?= $link['href'] ?>">
                                            <div class="image"><img class="img-fluid-soft" src="<?= $link['icon'] ?>"
                                                                    alt=""></div>
                                            <div class="text"><?= $link['name'] ?></div>
                                        </a>
                                    </div>
								<? endforeach; ?>
                            </div>
                        </div>
                    </div>
				<? endif; ?>
				<? /* if ($profitable_set) { ?>
					<div id="profitable_set">
						<div class="header">
							<div class="title">Выгодный комплект</div>
							<div class="text">30% скидка на аксессуар при покупке сумки</div>
							<div data-toggle="modal" data-target="#ModalDiscont" class="faq"><span
								  class="blue-icon question"></span> <a class="blue">Как получить скидку</a></div>
						</div>
						<?= $profitable_set ?>
					</div>
				<? } */ ?>
				<? if ($review_status) { ?>
            </div>
        </div>
    </div>
</div>
    <div id="reviews_anchor"></div>
    <div id="product_reviews" class="container-fluid">
        <div class="container">
            <div class="row">
                <div id="product_reviews_content">
                    <div class="header">
                        <div class="menu">
                            <div onClick="viewRTab(this)" data-tab="reviews" class="item active"><span>Отзывы покупателей
                                (<?= $reviews_count ?>)</span>
                            </div>
                            <div onClick="viewRTab(this)" data-tab="screens" class="item"><span>Отзывы WhatsApp
                                (<?=(int) $screens_count ?>)</span>
                            </div>
                            <div onClick="viewRTab(this)" data-tab="questions" class="item"><span>Вопросы-ответы
                                (<?= $questions_count ?>)</span>
                            </div>
                        </div>
                        <hr class="screen-sm-max">
                        <div class="buttons">
                            <button data-toggle="modal" data-target="#ModalReview" role="button" class="btn btn-red">
                                Написать отзыв
                            </button>
                        </div>
                    </div>
                    <hr class="screen-sm-min">
                    <div class="product_rating">
                        <span class="text">Средняя оценка: </span>
                        <span class="float"><?= $rating_float ?></span>
                        <span class="stars">
			 <? for ($i = 1; $i <= 5; $i++) { ?>
				 <? if ($rating < $i) { ?>
                     <svg class="star"><use xlink:href="#svg-star"></svg>
				 <? } else { ?>
                     <svg class="star full"><use xlink:href="#svg-star"></svg>
				 <? } ?>
			 <? } ?>
		 </span>
                    </div>
                    <div class="content">
                        <div class="tab reviews">
                            <div id="reviews"></div>
                        </div>
                        <div class="tab screens hide">
                            <div id="screens"></div>
                        </div>
                        <div class="tab questions hide">
                            <div id="questions"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="container">
    <div class="row">
        <div class="content">
            <div class="content_wrap">
				<? } ?>
				<? if ($similar) echo $similar; ?>
				<? if ($ShowRoomBanner) echo $ShowRoomBanner; ?>
				<?php echo $content_bottom; ?>
            </div>
        </div>
		<?php echo $column_right; ?>
    </div>
</div>

<div id="ModalQuestion" class="modal fade feedback" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><span aria-hidden="true"><svg><use
                                    xlink:href="#svg-close"/></svg></span><span class="sr-only">Закрыть</span></button>
            <div class="content">
                <div class="modal-default">
                    <form id="form-question">
                        <div class="title">Задать вопрос</div>
                        <div class="result-message"></div>
                        <div class="form">
                            <input type="hidden" name="rating" value="1">
                            <input type="hidden" name="rating_photo" value="1">
                            <input type="hidden" name="rating_description" value="1">
                            <input type="hidden" name="review_type" value="1">
                            <div class="form-group">
                                <input class="form-control" type="text" name="name" placeholder="Ваше имя">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="text" placeholder="Ваш вопрос"></textarea>
                            </div>
                        </div>
                        <div class="buttons">
                            <button id="button-question" type="button" class="btn btn-red">Отправить вопрос</button>
                            <p class="disclaimer text-right">Нажимая на кнопку <q>Отправить вопрос</q>, вы принимаете
                                условия <a class="blue" target="_blank" href="/publichnaya-oferta">Публичной оферты</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="ModalReview" class="modal fade feedback" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><span aria-hidden="true"><svg><use
                                    xlink:href="#svg-close"/></svg></span><span class="sr-only">Закрыть</span></button>
            <div class="content">
                <div class="modal-default">
                    <form id="form-review">
                        <input type="hidden" name="review_type" value="0">
                        <div class="title">Оставить отзыв</div>
                        <div class="ratings">
                            <div class="text">Общая оценка:</div>
                            <div class="stars" data-rating="main" title="кликните, чтобы зафиксировать">
                                <input type="hidden" name="rating" value="5" autocomplete="off">
								<?php for ($i = 1; $i <= 5; $i++) { ?>
                                    <svg class="star full">
                                        <use xlink:href="#svg-star">
                                    </svg>
								<?php } ?>
                            </div>
                            <div class="text">Соответствие фотографии:</div>
                            <div class="stars" data-rating="photo" title="кликните, чтобы зафиксировать">
                                <input type="hidden" name="rating_photo" value="5" autocomplete="off">
								<?php for ($i = 1; $i <= 5; $i++) { ?>
                                    <svg class="star full">
                                        <use xlink:href="#svg-star">
                                    </svg>
								<?php } ?>
                            </div>
                            <div class="text">Соответствие описанию:</div>
                            <div class="stars" data-rating="description" title="кликните, чтобы зафиксировать">
                                <input type="hidden" name="rating_description" value="5" autocomplete="off">
								<?php for ($i = 1; $i <= 5; $i++) { ?>
                                    <svg class="star full">
                                        <use xlink:href="#svg-star">
                                    </svg>
								<?php } ?>
                            </div>
                        </div>
                        <div class="result-message"></div>
                        <div class="form">
                            <div class="form-group">
                                <input class="form-control" type="text" name="name" placeholder="Ваше имя">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="text" placeholder="Ваш отзыв"></textarea>
                            </div>
                            <div id="FileDropField" data-token="<?= $UploadToken ?>">
                                <p class="note note-grey">Добавьте фотографии</p>
                                <div id="FileDropContainer"><label for="FileUploadInput" title="Добавить фотографию"
                                                                   id="FileAddIcon"><img
                                                src="/image/image_add.svg"></label>
                                    <label class="note note-label" for="FileUploadInput" role="button"><span><span
                                                    class="special">Добавьте</span> несколько фото, так ваш отзыв станет интереснее</span></label>
                                </div>
                                <div>
                                    <input class="hide" type="file" name="files[]" id="FileUploadInput" multiple/>
                                </div>
                            </div>
                        </div>
                        <div class="buttons">
                            <button id="button-review" type="button" class="btn btn-red">Отправить отзыв</button>
                            <p class="disclaimer text-right">Нажимая на кнопку <q>Отправить отзыв</q>, вы принимаете
                                условия <a class="blue" target="_blank" href="/publichnaya-oferta">Публичной оферты</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="ModalDiscont" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><span aria-hidden="true"><svg><use
                                    xlink:href="#svg-close"/></svg></span><span class="sr-only">Закрыть</span></button>
            <div class="content">
                <div class="modal-default">
                    <p>При покупке сумки, рюкзака или любого другого товара Вы получаете скидку 30% на любой товар из
                        раздела <a href="<?= $profitable_href ?>"><?= $profitable_name ?></a>. Скидка действует на товар
                        с меньшей стоимостью.</p>
                    <p>О предоставлении скидки Вас оповестит менеджер непосредственно при оформлении / подтверждении
                        заказа.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script><!--
	FileUploader.config = <?=$ReviewFiles?>;
	window.ProductDetails = {
		'id': '<?=$product_id?>',
		'name': '<?=$heading_title?>',
		'price': '<?=(int)$price?>',
		'category': '<?=$breadcrumbs[sizeof($breadcrumbs) - 2]['text']?>',
	}
	DocumentReady.push(function ($) {
		setupThumbs()
		checkShowRoom('<?=$Locality?>')
		$('#input_locality').on('change_locality', function (e, d) {
			checkShowRoom(d)
			$('#product_main .info .delivery .header .locality').text(d)
			$('#product_main .info .delivery .content').load('/index.php?route=product/product/loadDelivery')
		})
		setInterval(function () {
			$('#product_main .info .delivery .content').load('/index.php?route=product/product/loadDelivery')
		}, 60000)
        $('#custom_product_icons').load('index.php?route=extension/module/potesua/getCustomIcons&product_id=<?php echo $product_id; ?>');
		$('#ModalReview .stars .star').on('mouseenter click', getRating)
		$('#ModalReview .stars').on('mouseleave', getRating)
		// console.log($(window).width(), Defaults['screen_sm']);
		if ($(window).width() > Defaults['screen_sm']) {
			$('#product_main .image .big img')
				.on('mousemove', imgZoom)
			// .on('mousemove', function() {zoomIn(this)})
			// .on('click', function() {getFullScreen(this)})
		} else {
			createSwipe($('#product_main .thumbs'), $('#product_main .image .big .img'))
		}
		$('#reviews').load('index.php?route=product/product/reviews&product_id=<?php echo $product_id; ?>');
		$('#questions').load('index.php?route=product/product/questions&product_id=<?php echo $product_id; ?>');
		$('#screens').load('index.php?route=product/product/screens&product_id=<?php echo $product_id; ?>');
		// $('#tab-rules').load('index.php?route=product/product/review_rules');
		$('#button-review').on('click', function () {
			var $f = $('#form-review')
			var $m = $('.result-message', $f)
			$('input, textarea', $f).each(function () {
				if ($(this).attr('type') === 'hidden') return
				if (!$(this).val()) $(this).parent().addClass('has-error')
				else $(this).parent().removeClass('has-error')
			});
			let data = $f.serializeArray();
			$('#FileDropContainer .item').each(function () {
				data.push({
					'name': $(this).attr('data-name'),
					'value': $('img', this).attr('src').split('/').pop(),
				});
			})
			// if ($('.has-error', $f).length > 0) return
			$.ajax({
				url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
				type: 'post',
				dataType: 'json',
				data: data,
				beforeSend: function () {
					$('#button-review').button('loading');
					$m.empty()
				},
				complete: function () {
					$('#button-review').button('reset');
				},
				success: function (json) {
					$('.alert-success, .alert-danger').remove();

					if (json['error']) {
						$m.append('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
					}

					if (json['success']) {
						$m.append('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

						$('#FileDropContainer').empty()
						$('input[name=\'name\']', $f).val('');
						$('textarea[name=\'text\']', $f).val('');
						$('.stars', $f).each(function () {
							$('.star', this).last().click()
						})
					}
				}
			})
		});
		$('#button-question').on('click', function () {
			var $f = $('#form-question')
			var $m = $('.result-message', $f)
			$('input, textarea', $f).each(function () {
				if ($(this).attr('type') === 'hidden') return
				if (!$(this).val()) $(this).parent().addClass('has-error')
				else $(this).parent().removeClass('has-error')
			})
			// if ($('.has-error', $f).length > 0) return
			$.ajax({
				url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
				type: 'post',
				dataType: 'json',
				data: $("#form-question").serialize(),
				beforeSend: function () {
					$('#button-question').button('loading');
					$m.empty()
				},
				complete: function () {
					$('#button-question').button('reset');
				},
				success: function (json) {
					$('.alert-success, .alert-danger').remove();

					if (json['error']) {
						$m.append('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
					}

					if (json['success']) {
						$m.append('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

						$('input[name=\'name\']', $f).val('');
						$('textarea[name=\'text\']', $f).val('');
					}
				}
			})
		});
	})
	var like = function (t) {
		var id = $(t).attr('data-id'),
			tk = $(t).attr('data-token'),
			l = $(t).parent().find('.like_value'),
			ul = $(t).parent().find('.unlike_value')
		$.getJSON("<?=$review_like_href?>", {'review_id': id, 'token': tk}, function (d) {
			l.text(d.like)
			ul.text(d.unlike)
		})
	}
	var unlike = function (t) {
		console.log(t)
		var id = $(t).attr('data-id'),
			tk = $(t).attr('data-token'),
			l = $(t).parent().find('.like_value'),
			ul = $(t).parent().find('.unlike_value')
		$.getJSON("<?=$review_unlike_href?>", {'review_id': id, 'token': tk}, function (d) {
			l.text(d.like)
			ul.text(d.unlike)
		})
	}
	var moreReviews = function (t) {
		var $p = $(t).parents('.tab')
		if ($p.hasClass('reviews')) {
			$.get('index.php?route=product/product/reviews&product_id=<?php echo $product_id; ?>&page=' + $(t).attr('data-page'), function (d) {
				$('.review', $p).parent().append(d)
			})
		} else {
			$.get('index.php?route=product/product/questions&product_id=<?php echo $product_id; ?>&page=' + $(t).attr('data-page'), function (d) {
				$('.review', $p).parent().append(d)
			})
		}
		$(t).parent().remove()
	}
	//--></script>
<?php echo $footer; ?>
