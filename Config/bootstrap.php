<?php

$Watermark = null;

CakeEventManager::instance()->attach(function($event) use (&$Watermark) {
  // Trigger watermark creation only on original sources
  if ($event->data['options']['isOriginal']) {
    if (!$Watermark) {
      $ImageResizer = $event->subject();
      App::uses('WatermarkComponent', 'Watermark.Compontent');
      $ImageResizer->controller->loadComponent('Watermark.Watermark');
      $Watermark = $ImageResizer->controller->Watermark;
    }
    
    $success = $Watermark->addWatermark($event->data['dst']);
    if (!$success) {
      $event->stopPropagation();
    }
  }
}, 'Component.ImageResizer.afterResize');
