<?php

class ModelToolImage extends Model {
  
  private $finfo;
  
  /**
   * Resize image
   * @param      $filename   - images file in DIR_IMAGE
   * @param      $width
   * @param      $height
   * @param bool $AutoRotate (false) - use EXIF for correcting orientation
   * @param bool $ceil       ()false - resize overflow
   * @return string|void
   */
  public function resize($filename, $width, $height, bool $AutoRotate = false, bool $ceil = false) {
    if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != DIR_IMAGE) {
      return;
    }
    
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    
    $image_old = $filename;
    if ($extension) {
      $image_new = 'cache/'
          . utf8_substr($filename, 0, utf8_strrpos($filename, '.'))
          . '-'
          . ($ceil ? 'ceil-' : '')
          . (int)$width . 'x' . (int)$height . '.' . $extension;
    } else {
      $image_new = str_replace('//', '/', 'cache/' . $filename
          . '-'
          . ($ceil ? 'ceil-' : '')
          . $width . 'x' . $height);
    }
    if (
        !is_file(DIR_IMAGE . $image_new)
//        or (filectime(DIR_IMAGE . $image_old) > filectime(DIR_IMAGE . $image_new))
        or (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))
    ) //		if ($ceil || !is_file(DIR_IMAGE . $image_new) || (filectime(DIR_IMAGE . $image_old) > filectime(DIR_IMAGE . $image_new)))
    {
      if (!$this->is_resizable($filename)) {
        return $this->getUrl($filename);
      }
      list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
/*      d(
          $image_new,
          $image_old,
          filectime(DIR_IMAGE . $image_new),
          filectime(DIR_IMAGE . $image_old),
          filemtime(DIR_IMAGE . $image_new),
          filemtime(DIR_IMAGE . $image_old),
          '--------'
      )*/;
      
      $path = '';
      
      $directories = explode('/', dirname($image_new));
      
      foreach ($directories as $directory) {
        $path = $path . '/' . $directory;
        if (!is_dir(DIR_IMAGE . $path)) {
          @mkdir(DIR_IMAGE . $path, 0777);
        }
      }
  
      if ($width_orig != $width || $height_orig != $height) {
        $image = new Image(DIR_IMAGE . $image_old);
        if ($AutoRotate) {
          $image->autoRotate();
        }
        if ($width || $height) {
          $image->resize($width, $height, $ceil ? 'ceil' : '');
        }
        $image->save(DIR_IMAGE . $image_new, 70);
      } else {
        copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
      }
    }
    
    $image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
    
    return $this->getUrl($image_new);
    
  }
  
  public function getUrl($file) {
    
    if ($this->config->get('site_cdn')) {
      return $this->config->get('site_cdn') . 'image/' . $file;
    } elseif ($this->request->server['HTTPS']) {
      return $this->config->get('config_ssl') . 'image/' . $file;
    } else {
      return $this->config->get('config_url') . 'image/' . $file;
    }
    
  }
  
  public function is_video(string $file) {
    
    $file = $this->checkFile($file);
    if (!file_exists($file)) return false;
    $mime = mime_content_type($file);
    if (in_array($mime, [
        'video/mp4',
    ])) return true;
    return false;
    
  }
  
  private function getFileInfo(): finfo {
    if ($this->finfo === null) {
      $this->finfo = new finfo(FILEINFO_MIME_TYPE);
    }
    return $this->finfo;
  }
  
  public function is_image(string $file) {
    
    if (in_array($this->getFileMime($file), [
        'image/jpeg',
        'image/gif',
        'image/png',
        'image/svg',
        'image/svg+xml',
    ])) return true;
    return false;
    
  }
  
  public function is_resizable(string $file) {
    
    if (in_array($this->getFileMime($file), [
        'image/jpeg',
        'image/gif',
        'image/png',
    ])) return true;
    return false;
    
  }

  private function getFileMime(string $file): string {
    $file = $this->checkFile($file);
    if (!file_exists($file)) return false;
//    $mime = mime_content_type($file);
//    $mime = $this->getFileInfo()->file($file);
    return image_type_to_mime_type(exif_imagetype($file));
  }
  
  private function checkFile($file) {
    
    if (file_exists($file)) return $file;
    return DIR_IMAGE . $file;
    
  }
  
}
