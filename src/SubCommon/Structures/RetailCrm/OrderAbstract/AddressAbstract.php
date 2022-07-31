<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class AddressAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  
  protected string $index; // Индекс
  protected string $countryIso; // ISO код страны (ISO 3166-1 alpha-2)
  protected string $region; // Регион
  protected int $regionId; // Идентификатор региона в Geohelper
  protected string $city; // Город
  protected int $cityId; // Идентификатор города в Geohelper
  protected string $cityType; // Тип населенного пункта
  protected string $street; // Улица
  protected int $streetId; // Идентификатор улицы в Geohelper
  protected string $streetType; // Тип улицы
  protected string $building; // Дом
  protected string $flat; // Номер квартиры/офиса
  protected int $floor; // Этаж
  protected int $block; // Подъезд
  protected string $house; // Строение
  protected string $housing; // Корпус
  protected string $metro; // Метро
  protected string $notes; // Примечания к адресу
  protected string $text; // Адрес в текстовом виде
  
  /**
   * @return string
   */
  public function getIndex(): string {
    return $this->index;
  }
  
  /**
   * @param string $index
   * @return AddressAbstract
   */
  public function setIndex(string $index): AddressAbstract {
    $this->index = $index;
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
   * @return AddressAbstract
   */
  public function setCountryIso(string $countryIso): AddressAbstract {
    $this->countryIso = $countryIso;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getRegion(): string {
    return $this->region;
  }
  
  /**
   * @param string $region
   * @return AddressAbstract
   */
  public function setRegion(string $region): AddressAbstract {
    $this->region = $region;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getRegionId(): int {
    return $this->regionId;
  }
  
  /**
   * @param int $regionId
   * @return AddressAbstract
   */
  public function setRegionId(int $regionId): AddressAbstract {
    $this->regionId = $regionId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCity(): string {
    return $this->city;
  }
  
  /**
   * @param string $city
   * @return AddressAbstract
   */
  public function setCity(string $city): AddressAbstract {
    $this->city = $city;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getCityId(): int {
    return $this->cityId;
  }
  
  /**
   * @param int $cityId
   * @return AddressAbstract
   */
  public function setCityId(int $cityId): AddressAbstract {
    $this->cityId = $cityId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCityType(): string {
    return $this->cityType;
  }
  
  /**
   * @param string $cityType
   * @return AddressAbstract
   */
  public function setCityType(string $cityType): AddressAbstract {
    $this->cityType = $cityType;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getStreet(): string {
    return $this->street;
  }
  
  /**
   * @param string $street
   * @return AddressAbstract
   */
  public function setStreet(string $street): AddressAbstract {
    $this->street = $street;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getStreetId(): int {
    return $this->streetId;
  }
  
  /**
   * @param int $streetId
   * @return AddressAbstract
   */
  public function setStreetId(int $streetId): AddressAbstract {
    $this->streetId = $streetId;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getStreetType(): string {
    return $this->streetType;
  }
  
  /**
   * @param string $streetType
   * @return AddressAbstract
   */
  public function setStreetType(string $streetType): AddressAbstract {
    $this->streetType = $streetType;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getBuilding(): string {
    return $this->building;
  }
  
  /**
   * @param string $building
   * @return AddressAbstract
   */
  public function setBuilding(string $building): AddressAbstract {
    $this->building = $building;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getFlat(): string {
    return $this->flat;
  }
  
  /**
   * @param string $flat
   * @return AddressAbstract
   */
  public function setFlat(string $flat): AddressAbstract {
    $this->flat = $flat;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getFloor(): int {
    return $this->floor;
  }
  
  /**
   * @param int $floor
   * @return AddressAbstract
   */
  public function setFloor(int $floor): AddressAbstract {
    $this->floor = $floor;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getBlock(): int {
    return $this->block;
  }
  
  /**
   * @param int $block
   * @return AddressAbstract
   */
  public function setBlock(int $block): AddressAbstract {
    $this->block = $block;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getHouse(): string {
    return $this->house;
  }
  
  /**
   * @param string $house
   * @return AddressAbstract
   */
  public function setHouse(string $house): AddressAbstract {
    $this->house = $house;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getHousing(): string {
    return $this->housing;
  }
  
  /**
   * @param string $housing
   * @return AddressAbstract
   */
  public function setHousing(string $housing): AddressAbstract {
    $this->housing = $housing;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getMetro(): string {
    return $this->metro;
  }
  
  /**
   * @param string $metro
   * @return AddressAbstract
   */
  public function setMetro(string $metro): AddressAbstract {
    $this->metro = $metro;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getNotes(): string {
    return $this->notes;
  }
  
  /**
   * @param string $notes
   * @return AddressAbstract
   */
  public function setNotes(string $notes): AddressAbstract {
    $this->notes = $notes;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getText(): string {
    return $this->text;
  }
  
  /**
   * @param string $text
   * @return AddressAbstract
   */
  public function setText(string $text): AddressAbstract {
    $this->text = $text;
    return $this;
  }
  
  
}