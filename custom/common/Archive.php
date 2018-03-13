<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Archive extends Utils {

	function __construct() {
		parent::__construct();
	}

	function get_archive_items() {
		$items = array();
		$query = "select * from mdl_article where active=1 order by title";
		$num   = $this->db->numrows( $query );
		if ( $num > 0 ) {
			$result = $this->db->query( $query );
			while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
				$item = new stdClass();
				foreach ( $row as $key => $value ) {
					$item->$key = $value;
				}
				$items[] = $item;
			} // end while
		} // end if $num > 0

		return $items;
	}

	function get_archive_page() {
		$list  = "";
		$items = $this->get_archive_items();

		if ( count( $items ) > 0 ) {
			$list .= "<div class='row-fluid'>";
			$list .= "<br><br><table id='archive_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";
			$list .= "<thead>";
			$list .= "<tr>";
			$list .= "<th>Title</th>";
			$list .= "<th>Link</th>";
			$list .= "<th>Date</th>";
			//$list .= "<th>Operations</th>";
			$list .= "</tr>";
			$list .= "</thead>";
			$list .= "<tbody>";

			foreach ( $items as $item ) {
				$date = $item->path;
				$link = "https://" . $_SERVER['SERVER_NAME'] . "/lms/articles/$item->path";
				$path = "<a href='$link' target='_blank'>$item->path</a>";
				$list .= "<tr>";
				$list .= "<td>$item->title</td>";
				$list .= "<td>$path</td>";
				$list .= "<td>$date</td>";
				//$list .= "<td><a href='#' onclick='return false;' class='ar_item_del' data-id='$item->id'>Delete</a></td>";
				$list .= "</tr>";
			} // end foreach
			$list .= "</tbody>";
			$list .= "</table>";
			$list .= "</div>";
		} // end if (count($items) > 0
		else {
			$list .= "<div class='row-fluid' style='padding-top:10px;'>";
			$list .= "<p style='text-align: center;'>There are no any archive files uploaded</p>";
			$list .= "</div>";
		} // end else

		return $list;
	}


}