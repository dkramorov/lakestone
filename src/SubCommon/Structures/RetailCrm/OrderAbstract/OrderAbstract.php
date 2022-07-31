<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class OrderAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $number; // Номер заказа
  protected string $externalId; // Внешний ID заказа
  protected string $privilegeType; // Тип привилегии
  protected string $countryIso; // ISO код страны (ISO 3166-1 alpha-2)
  protected \DateTime $createdAt; // Дата оформления заказа
  protected \DateTime $statusUpdatedAt; // Дата последнего изменения статуса
  protected float $discountManualAmount; // Денежная скидка на весь заказ
  protected float $discountManualPercent; // Процентная скидка на весь заказ
  protected int $mark; // Оценка заказа
  protected SourceAbstract $source; // Источник заказа
  protected DeliveryAbstract $delivery; // Данные о доставке
  protected ContragentAbstract $contragent; // Реквизиты
  protected CustomerAbstract $customer; // Клиент
  protected ContactAbstract $contact; // Контактное лицо
  protected CompanyAbstract $company; // Компания
  protected int $managerId; // Менеджер, прикрепленный к заказу
  protected string $status; // Статус заказа
  protected \DateTime $markDatetime; // Дата и время получение оценки от покупателя
  protected string $lastName; // Фамилия
  protected string $firstName; // Имя
  protected string $patronymic; // Отчество
  protected string $phone; // Телефон
  protected string $additionalPhone; // Дополнительный телефон
  protected string $email; // E-mail
  protected array $customFields; // Ассоциативный массив пользовательских полей
  protected string $customerComment; // Комментарий клиента
  protected string $managerComment; // Комментарий оператора
  protected string $statusComment; // Комментарий к последнему изменению статуса
  protected bool $call; // Требуется позвонить
  protected bool $expired; // Просрочен
  protected float $weight; // Вес
  protected int $length; // Длина
  protected int $width; // Ширина
  protected int $height; // Высота
  protected \DateTime $shipmentDate; // Дата отгрузки
  protected bool $shipped; // Заказ отгружен
  protected string $orderType; // Тип заказа
  protected string $orderMethod; // Способ оформления
  protected string $shipmentStore; // Склад отгрузки
  protected int $loyaltyEventDiscountId; // ID скидки по событию программы лояльности
  protected bool $applyRound; // Применять настройку округления стоимости заказа
  
  /*
   *
order[dialogId]	object (MGDialog)		Идентификатор диалога Чатов
   */
  
  
  /**
   * @var array<int, ItemAbstract>
   */
  protected array $items;
  /**
   * @var array<int, PaymentAbstract>
   */
  protected array $payments; // Платежи
  
  /**
   * @return \DateTime
   */
  public function getMarkDatetime(): \DateTime {
    return $this->markDatetime;
  }
  
  /**
   * @param \DateTime $markDatetime
   * @return OrderAbstract
   */
  public function setMarkDatetime(\DateTime $markDatetime): OrderAbstract {
    $this->markDatetime = $markDatetime;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getLastName(): string {
    return $this->lastName;
  }
  
  /**
   * @param string $lastName
   * @return OrderAbstract
   */
  public function setLastName(string $lastName): OrderAbstract {
    $this->lastName = $lastName;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getFirstName(): string {
    return $this->firstName;
  }
  
  /**
   * @param string $firstName
   * @return OrderAbstract
   */
  public function setFirstName(string $firstName): OrderAbstract {
    $this->firstName = $firstName;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getPatronymic(): string {
    return $this->patronymic;
  }
  
  /**
   * @param string $patronymic
   * @return OrderAbstract
   */
  public function setPatronymic(string $patronymic): OrderAbstract {
    $this->patronymic = $patronymic;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getPhone(): string {
    return $this->phone;
  }
  
  /**
   * @param string $phone
   * @return OrderAbstract
   */
  public function setPhone(string $phone): OrderAbstract {
    $this->phone = $phone;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getAdditionalPhone(): string {
    return $this->additionalPhone;
  }
  
  /**
   * @param string $additionalPhone
   * @return OrderAbstract
   */
  public function setAdditionalPhone(string $additionalPhone): OrderAbstract {
    $this->additionalPhone = $additionalPhone;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getEmail(): string {
    return $this->email;
  }
  
  /**
   * @param string $email
   * @return OrderAbstract
   */
  public function setEmail(string $email): OrderAbstract {
    $this->email = $email;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getNumber(): string {
    return $this->number;
  }
  
  /**
   * @param string $number
   * @return OrderAbstract
   */
  public function setNumber(string $number): OrderAbstract {
    $this->number = $number;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getExternalId(): string {
    return $this->externalId;
  }
  
  /**
   * @param string $externalId
   * @return OrderAbstract
   */
  public function setExternalId(string $externalId): OrderAbstract {
    $this->externalId = $externalId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getPrivilegeType(): string {
    return $this->privilegeType;
  }
  
  /**
   * @param string $privilegeType
   * @return OrderAbstract
   */
  public function setPrivilegeType(string $privilegeType): OrderAbstract {
    $this->privilegeType = $privilegeType;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCountryIso(): string {
    return $this->countryIso;
  }
  
  /**
   * @param string $countryIso
   * @return OrderAbstract
   */
  public function setCountryIso(string $countryIso): OrderAbstract {
    $this->countryIso = $countryIso;
    return $this;
  }
  
  /**
   * @return \DateTime
   */
  public function getCreatedAt(): \DateTime {
    return $this->createdAt;
  }
  
  /**
   * @param \DateTime $createdAt
   * @return OrderAbstract
   */
  public function setCreatedAt(\DateTime $createdAt): OrderAbstract {
    $this->createdAt = $createdAt;
    return $this;
  }
  
  /**
   * @return \DateTime
   */
  public function getStatusUpdatedAt(): \DateTime {
    return $this->statusUpdatedAt;
  }
  
  /**
   * @param \DateTime $statusUpdatedAt
   * @return OrderAbstract
   */
  public function setStatusUpdatedAt(\DateTime $statusUpdatedAt): OrderAbstract {
    $this->statusUpdatedAt = $statusUpdatedAt;
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
   * @return OrderAbstract
   */
  public function setDiscountManualAmount(float $discountManualAmount): OrderAbstract {
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
   * @return OrderAbstract
   */
  public function setDiscountManualPercent(float $discountManualPercent): OrderAbstract {
    $this->discountManualPercent = $discountManualPercent;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getMark(): int {
    return $this->mark;
  }
  
  /**
   * @param int $mark
   * @return OrderAbstract
   */
  public function setMark(int $mark): OrderAbstract {
    $this->mark = $mark;
    return $this;
  }
  
  public function addItem(ItemAbstract $item): self {
    $this->items[] = $item;
    return $this;
  }
  
  public function addPayment(PaymentAbstract $payment): self {
    $this->payments[] = $payment;
    return $this;
  }
  
  /**
   * @return array
   */
  public function getItems(): array {
    return $this->items;
  }
  
  /**
   * @return PaymentAbstract[]
   */
  public function getPayments(): array {
    return $this->payments;
  }
  
  /**
   * @return SourceAbstract
   */
  public function getSource(): SourceAbstract {
    return $this->source;
  }
  
  /**
   * @param SourceAbstract $source
   * @return OrderAbstract
   */
  public function setSource(SourceAbstract $source): OrderAbstract {
    $this->source = $source;
    return $this;
  }
  
  /**
   * @return DeliveryAbstract
   */
  public function getDelivery(): DeliveryAbstract {
    return $this->delivery;
  }
  
  /**
   * @param DeliveryAbstract $delivery
   * @return OrderAbstract
   */
  public function setDelivery(DeliveryAbstract $delivery): OrderAbstract {
    $this->delivery = $delivery;
    return $this;
  }
  
  /**
   * @return ContragentAbstract
   */
  public function getContragent(): ContragentAbstract {
    return $this->contragent;
  }
  
  /**
   * @param ContragentAbstract $contragent
   * @return OrderAbstract
   */
  public function setContragent(ContragentAbstract $contragent): OrderAbstract {
    $this->contragent = $contragent;
    return $this;
  }
  
  /**
   * @return CustomerAbstract
   */
  public function getCustomer(): CustomerAbstract {
    return $this->customer;
  }
  
  /**
   * @param CustomerAbstract $customer
   * @return OrderAbstract
   */
  public function setCustomer(CustomerAbstract $customer): OrderAbstract {
    $this->customer = $customer;
    return $this;
  }
  
  /**
   * @return ContactAbstract
   */
  public function getContact(): ContactAbstract {
    return $this->contact;
  }
  
  /**
   * @param ContactAbstract $contact
   * @return OrderAbstract
   */
  public function setContact(ContactAbstract $contact): OrderAbstract {
    $this->contact = $contact;
    return $this;
  }
  
  /**
   * @return CompanyAbstract
   */
  public function getCompany(): CompanyAbstract {
    return $this->company;
  }
  
  /**
   * @param CompanyAbstract $company
   * @return OrderAbstract
   */
  public function setCompany(CompanyAbstract $company): OrderAbstract {
    $this->company = $company;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getManagerId(): int {
    return $this->managerId;
  }
  
  /**
   * @param int $managerId
   * @return OrderAbstract
   */
  public function setManagerId(int $managerId): OrderAbstract {
    $this->managerId = $managerId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getStatus(): string {
    return $this->status;
  }
  
  /**
   * Use one of RetailCrmInterface::orderStatus*
   * @param string $status
   * @return OrderAbstract
   */
  public function setStatus(string $status): OrderAbstract {
    $this->status = $status;
    return $this;
  }
  
  /**
   * @return array
   */
  public function getCustomFields(): array {
    return $this->customFields;
  }
  
  /**
   * @param array <string, string> $customFields
   * @return OrderAbstract
   */
  public function setCustomFields(array $customFields): OrderAbstract {
    $this->customFields = $customFields;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCustomerComment(): string {
    return $this->customerComment;
  }
  
  /**
   * @param string $customerComment
   * @return OrderAbstract
   */
  public function setCustomerComment(string $customerComment): OrderAbstract {
    $this->customerComment = $customerComment;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getManagerComment(): string {
    return $this->managerComment;
  }
  
  /**
   * @param string $managerComment
   * @return OrderAbstract
   */
  public function setManagerComment(string $managerComment): OrderAbstract {
    $this->managerComment = $managerComment;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getStatusComment(): string {
    return $this->statusComment;
  }
  
  /**
   * @param string $statusComment
   * @return OrderAbstract
   */
  public function setStatusComment(string $statusComment): OrderAbstract {
    $this->statusComment = $statusComment;
    return $this;
  }
  
  /**
   * @return bool
   */
  public function isCall(): bool {
    return $this->call;
  }
  
  /**
   * @param bool $call
   * @return OrderAbstract
   */
  public function setCall(bool $call): OrderAbstract {
    $this->call = $call;
    return $this;
  }
  
  /**
   * @return bool
   */
  public function isExpired(): bool {
    return $this->expired;
  }
  
  /**
   * @param bool $expired
   * @return OrderAbstract
   */
  public function setExpired(bool $expired): OrderAbstract {
    $this->expired = $expired;
    return $this;
  }
  
  /**
   * @return float
   */
  public function getWeight(): float {
    return $this->weight;
  }
  
  /**
   * @param float $weight
   * @return OrderAbstract
   */
  public function setWeight(float $weight): OrderAbstract {
    $this->weight = $weight;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getLength(): int {
    return $this->length;
  }
  
  /**
   * @param int $length
   * @return OrderAbstract
   */
  public function setLength(int $length): OrderAbstract {
    $this->length = $length;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getWidth(): int {
    return $this->width;
  }
  
  /**
   * @param int $width
   * @return OrderAbstract
   */
  public function setWidth(int $width): OrderAbstract {
    $this->width = $width;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getHeight(): int {
    return $this->height;
  }
  
  /**
   * @param int $height
   * @return OrderAbstract
   */
  public function setHeight(int $height): OrderAbstract {
    $this->height = $height;
    return $this;
  }
  
  /**
   * @return \DateTime
   */
  public function getShipmentDate(): \DateTime {
    return $this->shipmentDate;
  }
  
  /**
   * @param \DateTime $shipmentDate
   * @return OrderAbstract
   */
  public function setShipmentDate(\DateTime $shipmentDate): OrderAbstract {
    $this->shipmentDate = $shipmentDate;
    return $this;
  }
  
  /**
   * @return bool
   */
  public function isShipped(): bool {
    return $this->shipped;
  }
  
  /**
   * @param bool $shipped
   * @return OrderAbstract
   */
  public function setShipped(bool $shipped): OrderAbstract {
    $this->shipped = $shipped;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getOrderType(): string {
    return $this->orderType;
  }
  
  /**
   * use one of consts RetailCrmInterface::orderType*
   * @param string $orderType
   * @return OrderAbstract
   */
  public function setOrderType(string $orderType): OrderAbstract {
    $this->orderType = $orderType;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getOrderMethod(): string {
    return $this->orderMethod;
  }
  
  /**
   * use one of consts RetailCrmInterface::orderMethod*
   * @param string $orderMethod
   * @return OrderAbstract
   */
  public function setOrderMethod(string $orderMethod): OrderAbstract {
    $this->orderMethod = $orderMethod;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getShipmentStore(): string {
    return $this->shipmentStore;
  }
  
  /**
   * @param string $shipmentStore
   * @return OrderAbstract
   */
  public function setShipmentStore(string $shipmentStore): OrderAbstract {
    $this->shipmentStore = $shipmentStore;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getLoyaltyEventDiscountId(): int {
    return $this->loyaltyEventDiscountId;
  }
  
  /**
   * @param int $loyaltyEventDiscountId
   * @return OrderAbstract
   */
  public function setLoyaltyEventDiscountId(int $loyaltyEventDiscountId): OrderAbstract {
    $this->loyaltyEventDiscountId = $loyaltyEventDiscountId;
    return $this;
  }
  
  /**
   * @return bool
   */
  public function isApplyRound(): bool {
    return $this->applyRound;
  }
  
  /**
   * @param bool $applyRound
   * @return OrderAbstract
   */
  public function setApplyRound(bool $applyRound): OrderAbstract {
    $this->applyRound = $applyRound;
    return $this;
  }
  
  /**
   * use RetailCrmInterface::customerField* as a key
   * @param string $key
   * @param string $value
   * @return $this
   */
  public function addCustomField(string $key, string $value): self {
    $this->customFields[$key] = $value;
    return $this;
  }
  
}