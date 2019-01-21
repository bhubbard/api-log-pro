<?php
/**
 * API_Log_Pro_File_Checks
 *
 * @package Api_Log_Pro
 */

/**
 * API_Log_Pro_File_Checks
 */
class API_Log_Pro_File_Checks extends WP_UnitTestCase {

	/**
	 * Verify Readme Exists.
	 *
	 * @access public
	 */
	public function test_readme_md_exists() {
		$this->assertFileExists( 'README.md' );
	}

	/**
	 * Verify Uninstall File Exists.
	 *
	 * @access public
	 */
	public function test_uninstall_exists() {
		$this->assertFileExists( 'uninstall.php' );
	}

	/**
	 * Verify API Cache Pro Class File Exists.
	 *
	 * @access public
	 */
	public function test_class_api_log_pro_exists() {
		$this->assertFileExists( 'class-api-log-pro.php' );
	}

	/**
	 * test_admin_file_exists function.
	 *
	 * @access public
	 * @return void
	 */
	public function test_admin_file_exists() {
		$this->assertFileExists( 'admin/admin-page.php' );
	}
}
