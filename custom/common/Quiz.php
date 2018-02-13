<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/class.database.php';

class Quiz {

	public $db;

	function __construct() {
		$this->db = new pdo_db();
	}


	function get_news_id() {
		$now    = time();
		$query  = "select * from mdl_article where expire>=$now order by id desc limit 0,1";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$aid = $row['id'];
		}

		return $aid;
	}

	function get_question_answers( $qid, $type ) {
		$list   = "";
		$list   .= "<table border='0' style='475px;'>";
		$class  = ( $type == 1 ) ? 'poll_answers' : 'quiz_answers';
		$name   = 'name_' . $qid;
		$query  = "select * from mdl_poll_a where qid=$qid";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$list .= "<tr>";
			$list .= "<td style='padding: 15px;text-align: right;width: 75px;'><input class='$class' name='$name' type='radio' value='" . $row['id'] . "'></td>";
			$list .= "<td style='padding: 15px;text-align: left;width: 400px;'>" . $row['a'] . "</td>";
			$list .= "</tr>";
		}


		$list .= "</table>";

		return $list;
	}

	function get_poll_data( $aid, $type ) {
		$list  = "";
		$query = "select * from mdl_poll where aid=$aid and type=$type";
		$num   = $this->db->numrows( $query );
		if ( $num > 0 ) {
			$result = $this->db->query( $query );
			while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
				$pid = $row['id'];
			} // end while;

			$query  = "select * from mdl_poll_q where pid=$pid";
			$result = $this->db->query( $query );
			while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
				$answers = $this->get_question_answers( $row['id'], $type );
				$title   = $row['title'];
				$list    .= "<div class='row'>";
				$list    .= "<span class='col-md-12' style='margin-bottom: 10px;font-weight: bold; '>$title</span>";
				$list    .= $answers;
				$list    .= "</div>";

				$list .= "<div class='row'>";
				$list .= "<span class='col-md-12'><br></span>";
				$list .= "</div>";

			}
		} // end if $num>0

		return $list;

	}

	function get_poll_page( $type ) {
		$list = "";

		$title    = ( $type == 1 ) ? 'Polling Questions' : 'News Quiz';
		$btnID    = ( $type == 1 ) ? 'submit_poll' : 'submit_quiz';
		$btnTitle = ( $type == 1 ) ? 'Submit Research' : 'Submit Quiz';
		$aid      = $this->get_news_id();
		$data     = $this->get_poll_data( $aid, $type );
		$list     .= "<div id='container36' style='width: 738px;height: auto;'>";

		$list .= "<div class='row' style='margin-top: 15px;margin-bottom: 15px;'>";
		$list .= "<span style='font-size: 20px;border-bottom: 2px solid #000000;padding-bottom: 2px;'>$title</span>";
		$list .= "</div>";

		$list .= "<div class='row'>";
		$list .= "<span class='col-md-12'>$data</span>";
		$list .= "</div>";

		$list .= "<div class='row' style='text-align: center;margin-bottom: 25px;'>";
		$list .= "<span class='sol-md-12'><button class='btn btn-primary' id='$btnID'>$btnTitle</button></span>";
		$list .= "</dv>";

		$list .= "</div>";

		return $list;
	}

	function submit_quiz_results( $data ) {
		$userid = $data->userid;
		$items  = $data->items;
		$now    = time();
		foreach ( $items as $aid ) {
			$query = "insert into mdl_poll_student_answers (userid, aid, added) 
					values ($userid,$aid,'$now')";
			$this->db->query( $query );
		}
		$list = "<p style='text-align: center;'>Data are submitted successfully</p>";

		return $list;
	}


}