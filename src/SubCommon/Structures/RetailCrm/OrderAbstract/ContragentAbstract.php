<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Cassandra\Date;
use Lakestone\SubCommon\Interface\RetailCrmInterface;
use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class ContragentAbstract extends StructureAbstract implements RetailCrmStructureInterface {
  protected string $contragentType; // Тип контрагента
  protected string $legalName; // Полное наименование
  protected string $legalAddress; // Адрес регистрации
  protected string $INN; // ИНН
  protected string $OKPO; // ОКПО
  protected string $KPP; // КПП
  protected string $OGRN; // ОГРН
  protected string $OGRNIP; // ОГРНИП
  protected string $certificateNumber; // Номер свидетельства
  protected \DateTime $certificateDate; // Дата свидетельства
  protected string $BIK; // БИК
  protected string $bank; // Банк
  protected string $bankAddress; // Адрес банка
  protected string $corrAccount; // Корр. счёт
  protected string $bankAccount; // Расчётный счёт
  
  private array $validType = [
      RetailCrmInterface::contragentTypeIndividual,
      RetailCrmInterface::contragentTypeEnterpreneur,
      RetailCrmInterface::contragentTypeLegalEntity,
  ];
  
  /**
   * @return string
   */
  public function getContragentType(): string {
    return $this->contragentType;
  }
  
  /**
   * use $this->validType for correct value
   * @param string $contragentType
   * @return ContragentAbstract
   */
  public function setContragentType(string $contragentType): ContragentAbstract {
    if (!in_array($contragentType, $this->validType)) {
      throw new \Exception('Invalid type');
    }
    $this->contragentType = $contragentType;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getLegalName(): string {
    return $this->legalName;
  }
  
  /**
   * @param string $legalName
   * @return ContragentAbstract
   */
  public function setLegalName(string $legalName): ContragentAbstract {
    $this->legalName = $legalName;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getLegalAddress(): string {
    return $this->legalAddress;
  }
  
  /**
   * @param string $legalAddress
   * @return ContragentAbstract
   */
  public function setLegalAddress(string $legalAddress): ContragentAbstract {
    $this->legalAddress = $legalAddress;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getINN(): string {
    return $this->INN;
  }
  
  /**
   * @param string $INN
   * @return ContragentAbstract
   */
  public function setINN(string $INN): ContragentAbstract {
    $this->INN = $INN;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getOKPO(): string {
    return $this->OKPO;
  }
  
  /**
   * @param string $OKPO
   * @return ContragentAbstract
   */
  public function setOKPO(string $OKPO): ContragentAbstract {
    $this->OKPO = $OKPO;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getKPP(): string {
    return $this->KPP;
  }
  
  /**
   * @param string $KPP
   * @return ContragentAbstract
   */
  public function setKPP(string $KPP): ContragentAbstract {
    $this->KPP = $KPP;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getOGRN(): string {
    return $this->OGRN;
  }
  
  /**
   * @param string $OGRN
   * @return ContragentAbstract
   */
  public function setOGRN(string $OGRN): ContragentAbstract {
    $this->OGRN = $OGRN;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getOGRNIP(): string {
    return $this->OGRNIP;
  }
  
  /**
   * @param string $OGRNIP
   * @return ContragentAbstract
   */
  public function setOGRNIP(string $OGRNIP): ContragentAbstract {
    $this->OGRNIP = $OGRNIP;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCertificateNumber(): string {
    return $this->certificateNumber;
  }
  
  /**
   * @param string $certificateNumber
   * @return ContragentAbstract
   */
  public function setCertificateNumber(string $certificateNumber): ContragentAbstract {
    $this->certificateNumber = $certificateNumber;
    return $this;
  }
  
  /**
   * @return \DateTime
   */
  public function getCertificateDate(): \DateTime {
    return $this->certificateDate;
  }
  
  /**
   * @param \DateTime $certificateDate
   * @return ContragentAbstract
   */
  public function setCertificateDate(\DateTime $certificateDate): ContragentAbstract {
    $this->certificateDate = $certificateDate;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getBIK(): string {
    return $this->BIK;
  }
  
  /**
   * @param string $BIK
   * @return ContragentAbstract
   */
  public function setBIK(string $BIK): ContragentAbstract {
    $this->BIK = $BIK;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getBank(): string {
    return $this->bank;
  }
  
  /**
   * @param string $bank
   * @return ContragentAbstract
   */
  public function setBank(string $bank): ContragentAbstract {
    $this->bank = $bank;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getBankAddress(): string {
    return $this->bankAddress;
  }
  
  /**
   * @param string $bankAddress
   * @return ContragentAbstract
   */
  public function setBankAddress(string $bankAddress): ContragentAbstract {
    $this->bankAddress = $bankAddress;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCorrAccount(): string {
    return $this->corrAccount;
  }
  
  /**
   * @param string $corrAccount
   * @return ContragentAbstract
   */
  public function setCorrAccount(string $corrAccount): ContragentAbstract {
    $this->corrAccount = $corrAccount;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getBankAccount(): string {
    return $this->bankAccount;
  }
  
  /**
   * @param string $bankAccount
   * @return ContragentAbstract
   */
  public function setBankAccount(string $bankAccount): ContragentAbstract {
    $this->bankAccount = $bankAccount;
    return $this;
  }
  
}