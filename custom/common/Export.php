<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Export extends Utils {

	public $users=array();

	function __construct() {
		parent::__construct();
	}

	function merge_group_users( $group_users ) {
		foreach ( $group_users as $userid ) {
			$this->users[] = $userid;
		}
	}

	function get_export_page( $userid ) {
		$list   = "";
		$roleid = $this->get_user_role();
		if ( $roleid < 5 ) {
			$list .= $this->get_teacher_export_page( $userid );
		} // end if
		else {
			$list .= $this->get_students_export_pag( $userid );
		}

		return $list;
	}

	function get_teacher_export_page( $userid ) {
		$list = "";
		$groups=$this->get_user_groups();
		foreach ( $groups as $groupid ) {
			$group_users = $this->get_group_users( $groupid );
			$this->merge_group_users( $group_users );
		} // end foreach

		return $list;
	}

	function get_students_export_page( $userid ) {
		$list = "";
		$this->users[] = $userid;
		return $list;
	}

	function create_export_table () {

	}
}