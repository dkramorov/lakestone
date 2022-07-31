<?php

class Document {

  private $title;
  private $description;
  private $keywords;
  private $links = array();
  private $styles = array();
  private $scripts = array();
  private $meta = array();

  public function setTitle($title) {
    $this->title = $title;
  }

  public function getTitle() {
    return $this->title;
  }

  public function setDescription($description) {
    $this->description = $description;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setKeywords($keywords) {
    $this->keywords = $keywords;
  }

  public function getKeywords() {
    return $this->keywords;
  }

  public function addLink($href, $rel, $type = false) {
    $this->links[trim($href)] = array(
        'href' => trim($href),
        'rel' => $rel,
        'type' => $type,
    );
  }

  public function getLinks() {
    return $this->links;
  }

  public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
    $this->styles[$href] = array(
        'href' => $href,
        'rel' => $rel,
        'media' => $media
    );
  }

  public function getStyles() {
    return $this->styles;
  }

  public function addMeta($name, $content) {
    $this->meta[$name] = $content;
  }

  public function getMeta($name = '') {
    if (!empty($name))
      return $this->meta[$name];
    else
      return $this->meta;
  }

  public function addScript($href, $postion = 'header') {
    $this->scripts[$postion][$href] = $href;
  }

  public function getScripts($postion = 'header') {
    if (isset($this->scripts[$postion])) {
      return $this->scripts[$postion];
    } else {
      return array();
    }
  }

}
