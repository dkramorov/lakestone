<?php
class ControllerToolErrorlog extends Controller {

	public function index() {
	
		$json = 'ok';
		$maxlen = 1000;
		
		$msg = $this->request->clean(file_get_contents('php://input'));
		if ( strlen($msg) > $maxlen )
			$msg = substr($msg, 0, $maxlen);
		$this->log->write('LOG SYSTEM: ' . $msg);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}
}
