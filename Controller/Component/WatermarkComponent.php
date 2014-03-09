<?php

class WatermarkComponent extends Component {

  var $controller = null;
  var $components = array('FileManager');
  // To speedup multiple calls
  var $_watermarkCache = array();

  public function initialize(Controller $controller) {
    $this->controller = $controller;
  }

  /**
   * Add watermark to given image
   *
   * @param string $src Image filename
   * @param string $watermark Watermark filename
   */
  private function applyWatermarkImage($src, $watermark) {
    App::uses('WatermarkCreator', 'Lib');
    $watermarkCreator = new WatermarkCreator();
    $scaleMode = '' . Configure::read('plugin.watermark.scaleMode');
    $position = '' . Configure::read('plugin.watermark.position');

    if (!$watermarkCreator->create($src, $watermark, $scaleMode, $position)) {
      CakeLog::error("Could not apply watermark: " . join(', ', $watermarkCreator->errors));
      return;
    }
    CakeLog::debug("Applied watermark to $src (watermark: $watermark)");
  }

  /**
   * Returns the global watermark if exists
   *
   * @return mixed Watermark filename if exists. False otherwise
   */
  private function findGlobalWatermark() {
    $cacheKey = '_global';

    if (isset($this->_watermarkCache[$cacheKey])) {
      return $this->_watermarkCache[$cacheKey];
    }

    $watermark = Configure::read('plugin.watermark.image');
    if (!$watermark) {
      $this->_watermarkCache[$cacheKey] = false;
      return false;
    }
    if (!is_readable($watermark)) {
      CakeLog::error("Global watermark file $watermark is not readable");
      $this->_watermarkCache[$cacheKey] = false;
      return false;
    }
    $this->_watermarkCache[$cacheKey] = $watermark;
    return $watermark;
  }

  /**
   * Search for watermark file in user directory
   *
   * @return {mixed} filename of watermark or fals
   */
  private function findUserWatermark() {
    $userDir = $this->FileManager->getUserDir();

    if (isset($this->_watermarkCache[$userDir])) {
      return $this->_watermarkCache[$userDir];
    }

    App::uses('Folder', 'Utility');
    $dir = new Folder($userDir);
    $files = $dir->find('watermark\.(png|gif)', true);
    if (!count($files)) {
      $this->_watermarkCache[$userDir] = false;
      return false;
    }
    if (count($files) > 1) {
      CakeLog::warn("Found multiple watermark files. Take first ${files[0]} of " . join(', ', $files));
    }

    $watermark = Folder::addPathElement($userDir, $files[0]);;
    if (!is_readable($watermark)) {
      CakeLog::warn("Watermark file $watermark is not readable");
      $this->_watermarkCache[$userDir] = false;
      return false;
    }

    $this->_watermarkCache[$userDir] = $watermark;
    return $watermark;
  }

  /**
   * Returns user watermark if exists or global watermark file if
   * configuration of plugin.watermark.image is set.
   *
   * @return mixed Filename if watermark exists. False otherwise
   */
  private function findWatermark() {
    $watermark = $this->findUserWatermark();
    if (!$watermark) {
      $watermark = $this->findGlobalWatermark();
    }
    return $watermark;
  }

  public function addWatermark($src) {
    $watermarkFilename = $this->findWatermark();

    if ($watermarkFilename) {
      $this->applyWatermarkImage($src, $watermarkFilename);
    }
    return true;
  }
}
