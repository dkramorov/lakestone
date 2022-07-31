<?php
class ControllerToolStatus extends Controller {

	public function index() {
    
    $this->load->model('tool/status');

		$query = $this->db->query("SHOW PROCESSLIST");
    if (isset($this->request->get['metrics'])) {
      header("HTTP/1.0 200 OK");
      foreach ($this->model_tool_status->get() as $name => $data) {
        printf("job_status{name=\"%s\"} %d\n", $name, $data['status']);
        printf("job_lateness{name=\"%s\"} %d\n", $name, $data['lateness']);
      }
      exit;
    }
		if ($query->num_rows < 80) {

      header("HTTP/1.0 200 OK");
			echo json_encode([
			    'sql_processlist' => 'ok',
          'sql_processlist_rows' => $query->num_rows,
          'hostname' => gethostname(),
          'status' => $this->model_tool_status->get(),
      ]);
		}
		exit;
	}
}
