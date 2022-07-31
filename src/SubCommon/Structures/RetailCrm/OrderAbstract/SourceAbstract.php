<?php

namespace Lakestone\SubCommon\Structures\RetailCrm\OrderAbstract;

use Lakestone\SubCommon\Structures\RetailCrm\RetailCrmStructureInterface;
use Lakestone\SubCommon\Structures\StructureAbstract;

abstract class SourceAbstract extends StructureAbstract implements RetailCrmStructureInterface {

  protected string $source; // Источник
  protected string $medium; // Канал
  protected string $campaign; // Кампания
  protected string $keyword; // Ключевое слово
  protected string $content; // Содержание кампании
  
  /**
   * @return string
   */
  public function getSource(): string {
    return $this->source;
  }
  
  /**
   * @param string $source
   * @return SourceAbstract
   */
  public function setSource(string $source): SourceAbstract {
    $this->source = $source;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getMedium(): string {
    return $this->medium;
  }
  
  /**
   * @param string $medium
   * @return SourceAbstract
   */
  public function setMedium(string $medium): SourceAbstract {
    $this->medium = $medium;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getCampaign(): string {
    return $this->campaign;
  }
  
  /**
   * @param string $campaign
   * @return SourceAbstract
   */
  public function setCampaign(string $campaign): SourceAbstract {
    $this->campaign = $campaign;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getKeyword(): string {
    return $this->keyword;
  }
  
  /**
   * @param string $keyword
   * @return SourceAbstract
   */
  public function setKeyword(string $keyword): SourceAbstract {
    $this->keyword = $keyword;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getContent(): string {
    return $this->content;
  }
  
  /**
   * @param string $content
   * @return SourceAbstract
   */
  public function setContent(string $content): SourceAbstract {
    $this->content = $content;
    return $this;
  }
  
  
}