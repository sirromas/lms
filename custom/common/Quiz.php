<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Quiz extends Utils {

	function __construct() {
		parent::__construct();
	}

	function get_question_answers( $qid, $type ) {
		$list   = "";
		$list   .= "<table border='0' style='475px;margin-left: 12px;'>";
		$class  = ( $type == 1 ) ? 'poll_answers' : 'quiz_answers';
		$name   = 'name_' . $qid;
		$query  = "select * from mdl_poll_a where qid=$qid";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$list .= "<tr>";
			$list .= "<td style='padding: 5px;text-align: left;'><input class='$class' name='$name' type='radio' value='" . $row['id'] . "'></td>";
			$list .= "<td style='padding: 5px;text-align: left;width: 400px;'>" . $row['a'] . "</td>";
			$list .= "</tr>";
		} // end while
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
			if ( $pid > 0 ) {
				$query  = "select * from mdl_poll_q where pid=$pid";
				$num=$this->db->numrows($query);
				$result = $this->db->query( $query );
				$i=1;
				while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
					$answers = $this->get_question_answers( $row['id'], $type );
					$title   = $row['title'];
					$list    .= "<div class='row' style='text-align: left;'>";
                    $list    .= "<input type='hidden' id='total_items_$type' value='$num'>";
					$list    .= "<span class='col-md-12' style='margin-bottom: 10px;font-weight:bold;'>$i)&nbsp;$title</span>";
					$list    .= $answers;
					$list    .= "</div>";
					$list .= "<div class='row'>";
					$list .= "<span class='col-md-12'><br></span>";
					$list .= "</div>";
					$i++;
				} // end while
			} // end if $pid > 0
		} // end if $num>0

		return $list;

	}

	function get_poll_id( $aid, $type ) {
		$pid   = 0;
		$query = "select * from mdl_poll where aid=$aid and type=$type";
		$num   = $this->db->numrows( $query );
		if ( $num > 0 ) {
			$result = $this->db->query( $query );
			while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
				$pid = $row['id'];
			}
		} // end if $num > 0

		return $pid;
	}

	function get_poll_questions( $pid ) {
		$q     = array();
		$query = "select * from mdl_poll_q where pid=$pid";
		$num   = $this->db->numrows( $query );
		if ( $num > 0 ) {
			$result = $this->db->query( $query );
			while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
				$q[] = $row['id'];
			}
		} // end if $num>0

		return $q;
	}

	function get_poll_answers( $aid, $type ) {
		$a   = array();
		$pid = $this->get_poll_id( $aid, $type );
		if ( $pid > 0 ) {
			$q = $this->get_poll_questions( $pid );
			if ( count( $q ) > 0 ) {
				$qs    = implode( ',', $q );
				$query = "select * from mdl_poll_a where qid in ($qs)";
				$num   = $this->db->numrows( $query );
				if ( $num > 0 ) {
					$result = $this->db->query( $query );
					while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
						$a[] = $row['id'];
					}
				} // end if $num>0
			} // end if count($q)>0
		} // end if $pid > 0

		return $a;

	}


	function is_student_already_took_poll( $aid, $type, $userid ) {
		$status = 0;
		$a      = $this->get_poll_answers( $aid, $type );
		if ( count( $a ) > 0 ) {
			$as     = implode( ',', $a );
			$query  = "select * from mdl_poll_student_answers where userid=$userid and aid in ($as)";
			$status = $this->db->numrows( $query );
		} // end if count($a)>0

		return $status;
	}

	function get_poll_submit_btn( $aid, $type, $userid ) {
		$list     = "";
		$btnID    = ( $type == 1 ) ? 'submit_poll' : 'submit_quiz';
		$btnTitle = ( $type == 1 ) ? 'Submit Research' : 'Submit Quiz';
		$status   = $this->is_student_already_took_poll( $aid, $type, $userid );
		if ( $status > 0 ) {
			$list .= "<button class='btn btn-primary' id='$btnID' disabled>$btnTitle</button>";
		} // end if
		else {
			$list .= "<button class='btn btn-primary' id='$btnID'>$btnTitle</button>";
		}

		return $list;
	}

	function get_poll_page( $type ) {
		$list = "";
		$aid  = $this->get_news_id();
		if ( $aid > 0 ) {
			$title  = ( $type == 1 ) ? 'Polling Questions' : 'News Quiz';
			$userid = $this->user->id;
			$data   = $this->get_poll_data( $aid, $type );
			$btn    = $this->get_poll_submit_btn( $aid, $type, $userid );

			$list.="<div id='container138'>
						<div id='container127'></div>
						<div id='container137'>
							<div id='container136'>
								<div id='container128'></div>
								<div id='container135'>
									<div id='container145'>
										<br><p><span class='underline' style='margin-top: 15px;'>$title</span></p>
										<div id='container144'>
											
												<br >$data <br>
												$btn
											    <br><br>
											        
										</div>
									</div>
								</div>
							</div>
						</div>	
					</div>";


			/*
			$list .= "<div id='container36' style='width: 738px;height: auto;'>";

			$list .= "<div class='row' style='margin-top: 15px;margin-bottom: 15px;'>";
			$list .= "<span style='font-size: 20px;border-bottom: 2px solid #000000;padding-bottom: 2px;'>$title</span>";
			$list .= "</div>";

			$list .= "<div class='row'>";
			$list .= "<span class='col-md-12'>$data</span>";
			$list .= "</div>";

			$list .= "<div class='row' style='text-align: center;margin-bottom: 25px;'>";
			$list .= "<span class='sol-md-12'>$btn</span>";
			$list .= "</dv>";

			$list .= "</div>";
			*/
		}

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