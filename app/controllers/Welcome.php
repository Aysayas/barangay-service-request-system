<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Welcome extends Controller {
	public function index() {
		$announcements = safe_db_rows(
			"SELECT *
			 FROM announcements
			 WHERE is_published = 1
			 ORDER BY published_at DESC, created_at DESC
			 LIMIT 3"
		);

		$this->call->view('home', [
			'title' => 'Barangay Service Request System',
			'announcements' => $announcements,
		]);
	}
}
?>
