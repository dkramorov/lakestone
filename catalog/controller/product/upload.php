<?php
class ControllerProductUpload extends Controller {

  public function image() {

    $json = [];
  
    $json['debug'] = $this->request->post;
    $json['files'] = $_FILES;

    try {

      if (empty($this->session->data['UploadToken'])) throw new Exception('Token not defined');
      $token = $this->session->data['UploadToken'];
      if (empty($this->request->post['token'])) throw new \Exception('Token not found');
      if ($token !== hex2bin($this->request->post['token'])) throw new Exception('Token is wrong');
      if (empty($_FILES)) throw new \Exception('Files was not transferred');
  
//      $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, DIR_UPLOAD . DIRECTORY_SEPARATOR . bin2hex($token) . DIRECTORY_SEPARATOR);
      $path = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, DIR_UPLOAD . DIRECTORY_SEPARATOR);

      $config = $this->config->get('review_files');
      $counter = 0;
      
      foreach ($_FILES as $name => $file) {
        if ($file['size'] > $config['max_size']) throw new \Exception('The file is too large: ' . $file['name']);
        if (!in_array(mime_content_type($file['tmp_name']), $config['acceptable'])) throw new \Exception('The file ia not acceptable: ' . $file['name']);
        if ($counter++ > $config['max_count']) throw new \Exception('Too many files');
        $file_name = bin2hex(random_bytes(32));
        if (!move_uploaded_file ( $file['tmp_name'], $path . $file_name)) throw new \Exception('File moving error');
        $json[$name]['file_href'] = '/system/storage/upload/' . $file_name;
      }
  
      $json['status'] = 'OK';
  
    } catch (\Exception $e) {
      $json['status'] = 'ERROR';
      $json['error'] = $e->getMessage();
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  
  }
  
  
}