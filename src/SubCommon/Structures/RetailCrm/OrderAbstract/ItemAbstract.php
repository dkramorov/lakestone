<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class ItemAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $id; // ID позиции в заказе
  protected float $initialPrice; // Цена товара/SKU
  protected float $discountManualAmount; // Денежная скидка на единицу товара
  protected float $discountManualPercent; // Процентная скидка на единицу товара
  protected int $quantity; // Количество
  protected string $comment; // Комментарий к позиции в заказе
  protected \DateTime $createdAt; // Дата создания позиции в системе
  protected string $productName; // Название товара
  protected string $status; // Статус позиции в заказе
  protected OfferAbstract $offer; // Торговое предложение
  protected PriceTypeAbstract $priceType; // Тип цены
  protected float $purchasePrice; // Закупочная цена
  protected string $vatRate; // Ставка НДС
  protected array $markingCodes; // Коды маркировки
  
  /*
   * @var array <int, ItemProperty>
   */
  protected array $properties = []; // Дополнительные свойства позиции в заказе
  
  /**
   * @var array <int, ExternalId>
   */
  protected array $externalIds = []; // Внешние идентификаторы позиции в заказе
  
  /**
   * @return \DateTime
   */
  public function getCreatedAt(): \DateTime {
    return $this->createdAt;
  }
  
  /**
   * @param \DateTime $createdAt
   * @return ItemAbstract
   */
  public function setCreatedAt(\DateTime $createdAt): ItemAbstract {
    $this->createdAt = $createdAt;
    return $this;
  }
  
  /**
   * @param OfferAbstract $offer
   * @return ItemAbstract
   */
  public function setOffer(OfferAbstract $offer): ItemAbstract {
    $this->offer = $offer;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getProductName(): string {
    return $this->productName;
  }
  
  /**
   * @param string $productName
   * @return ItemAbstract
   */
  public function setProductName(string $productName): ItemAbstract {
    $this->productName = $productName;
    return $this;
  }
  
  public function addProperty(ItemPropertyAbstract $property) {
    $this->properties[] = $property;
    return $this;
  }
  
  /**
   * @return array
   */
  public function getExternalIds(): array {
    return $this->externalIds;
  }
  
  public function addExternalId(ExternalIdAbstract $externalId) {
     $this->externalIds[] = $externalId;
  }
  
  /**
   * @return int
   */
  public function getQuantity(): int {
    return $this->quantity;
  }
  
  /**
   * @param int $quantity
   * @return ItemAbstract
   */
  public function setQuantity(int $quantity): ItemAbstract {
    $this->quantity = $quantity;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getComment(): string {
    return $this->comment;
  }
  
  /**
   * @param string $comment
   * @return ItemAbstract
   */
  public function setComment(string $comment): ItemAbstract {
    $this->comment = $comment;
    return $this;
  }
  
  /**
   * @return array
   */
  public function getProperties(): array {
    return $this->properties;
  }
  
  /**
   * @return array
   */
  public function getOffer(): array {
    return $this->offer;
  }
  
  /**
   * @return string
   */
  public function getId(): string {
    return $this->id;
  }
  
  
  /**
   * @param string $id
   * @return ItemAbstract
   */
  public function setId(string $id): ItemAbstract {
    $this->id = $id;
    return $this;
  }
  
  /**
   * @return float
   */
  public function getInitialPrice(): float {
    return $this->initialPrice;
  }
  
  /**
   * @param float $initialPrice
   * @return ItemAbstract
   */
  public function setInitialPrice(float $initialPrice): ItemAbstract {
    $this->initialPrice = $initialPrice;
    return $this;
  }
  
  /**
   * @return float
   */
  public function getDiscountManualAmount(): float {
    return $this->discountManualAmount;
  }
  
  /**
   * @param float $discountManualAmount
   * @return ItemAbstract
   */
  public function setDiscountManualAmount(float $discountManualAmount): ItemAbstract {
    $this->discountManualAmount = $discountManualAmount;
    return $this;
  }
  
  /**
   * @return float
   */
  public function getDiscountManualPercent(): float {
    return $this->discountManualPercent;
  }
  
  /**
   * @param float $discountManualPercent
   * @return ItemAbstract
   */
  public function setDiscountManualPercent(float $discountManualPercent): ItemAbstract {
    $this->discountManualPercent = $discountManualPercent;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getStatus(): string {
    return $this->status;
  }
  
  /**
   * @param string $status
   * @return ItemAbstract
   */
  public function setStatus(string $status): ItemAbstract {
    $this->status = $status;
    return $this;
  }
  
  /**
   * @return PriceTypeAbstract
   */
  public function getPriceType(): PriceTypeAbstract {
    return $this->priceType;
  }
  
  /**
   * @param PriceTypeAbstract $priceType
   * @return ItemAbstract
   */
  public function setPriceType(PriceTypeAbstract $priceType): ItemAbstract {
    $this->priceType = $priceType;
    return $this;
  }
  
  /**
   * @return float
   */
  public function getPurchasePrice(): float {
    return $this->purchasePrice;
  }
  
  /**
   * @param float $purchasePrice
   * @return ItemAbstract
   */
  public function setPurchasePrice(float $purchasePrice): ItemAbstract {
    $this->purchasePrice = $purchasePrice;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getVatRate(): string {
    return $this->vatRate;
  }
  
  /**
   * @param string $vatRate
   * @return ItemAbstract
   */
  public function setVatRate(string $vatRate): ItemAbstract {
    $this->vatRate = $vatRate;
    return $this;
  }
  
  /**
   * @return array
   */
  public function getMarkingCodes(): array {
    return $this->markingCodes;
  }
  
  /**
   * @param array $markingCodes
   * @return ItemAbstract
   */
  public function setMarkingCodes(array $markingCodes): ItemAbstract {
    $this->markingCodes = $markingCodes;
    return $this;
  }
  
}