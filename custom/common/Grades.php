<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';

class Grades extends Utils {

	public $users = array();
	public $student_poll_score;
	public $student_quiz_score;
	public $studemt_forum_score;
	public $articleID;

	function __construct() {
		parent::__construct();
	}

	function merge_group_users( $group_users ) {
		foreach ( $group_users as $userid ) {
			$this->users[] = $userid;
		}
	}

	function get_grades_page( $userid ) {
		$list   = "";
		$roleid = $this->get_user_role();
		if ( $roleid < 5 ) {
			// It is teacher
			$groups = $this->get_user_groups();
			foreach ( $groups as $groupid ) {
				$group_users = $this->get_group_users( $groupid );
				$this->merge_group_users( $group_users );
			} // end foreach
		} // end if
		else {
			// It is student
			$this->users[] = $userid;
		}
		$list .= $this->create_grades_page( $this->users );

		return $list;

	}

	function get_postuser_group( $userid ) {
		$query  = "select * from mdl_groups_members where userid=$userid";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$groupid = $row['groupid'];
		}

		return $groupid;
	}

	function create_grades_page( $users ) {
		$list            = "";
		$this->articleID = $this->get_news_id();
		$list            .= "<div class='row' style='margin-top: 25px;'>";
		$list            .= "<span class='col-md-12'>";

		$list .= "<table id='grades_table' class='table table-striped table-bordered' cellspacing='0' width='100%'>";

		$list .= "<thead>";
		$list .= "<tr>";
		$list .= "<th>Student</th>";
		$list .= "<th>Class</th>";
		$list .= "<th>Poll Score</th>";
		$list .= "<th>Quiz Score</th>";
		$list .= "<th>Forum Score</th>";
		$list .= "<th>Course Total</th>";
		$list .= "<th>Ops</th>";
		$list .= "</tr>";
		$list .= "</thead>";

		$list .= "<tbody>";


		foreach ( $users as $userid ) {
			$userdata = $this->get_user_details( $userid );
			$groupid  = $this->get_postuser_group( $userid );
			$class    = $this->get_group_name( $groupid );

			$pollA     = $this->get_student_pol_scores( $userid );
			$pollScore = $this->get_section_score_block( $pollA );

			$quizA     = $this->get_student_quiz_scores( $userid );
			$quizScore = $this->get_section_score_block( $quizA );

			$forumA     = $this->get_student_forum_scores( $userid );
			$forumScore = count( $forumA );

			$courseTotal = $pollScore + $quizScore + $forumScore;

			$ops = $this->get_ops_block( $this->articleID, $userid );

			$path   = $_SERVER['DOCUMENT_ROOT'] . "/lms/custom/common/data/grades_$userid.csv";
			$output = fopen( $path, 'w' );

			$data = array( $userdata->firstname, $userdata->lastname, $class, $pollScore, $quizScore, $forumScore, $courseTotal );
			fputcsv( $output, $data );
			fclose( $path );

			$list .= "<tr>";
			$list .= "<td>$userdata->firstname $userdata->lastname</td>";
			$list .= "<td>$class</td>";
			$list .= "<td>$pollScore</td>";
			$list .= "<td>$quizScore</td>";
			$list .= "<td>$forumScore</td>";
			$list .= "<td>$courseTotal</td>";
			$list .= "<td>$ops</td>";
			$list .= "</tr>";

		} // end foreach


		$list .= "</tbody>";

		$list .= "</table>";
		$list .= "</span>";
		$list .= "</div>";

		return $list;
	}

	function get_ops_block( $aid, $userid ) {
		$list = "";

		$link="https://".$_SERVER['SERVER_NAME']."/lms/custom/common/data/grades_$userid.csv";
		$list .= "<a href='$link' target='_blank'><button id='export_student_grades' data-aid='$aid' data-userid='$userid'>Export</button></a>";

		return $list;
	}

	function is_answer_correct( $aid ) {
		$query  = "select * from mdl_poll_a where id=$aid";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$correct = $row['correct'];
		}

		return $correct;
	}

	function get_section_score_block( $answers, $table = true ) {
		$list    = "";
		$total   = 0;
		$correct = 0;
		if ( count( $answers ) == 0 ) {
			$list .= "N/A";
		} // end if
		else {
			foreach ( $answers as $a ) {
				$status = $this->is_answer_correct( $a->aid );
				if ( $status == 1 ) {
					$correct ++;
				}
				$total ++;
			} // end foreach
			if ( $table ) {
				$list .= $correct . ' out of ' . $total;

				return $list;
			} // end if
			else {
				$data = array( 'correct' => $correct, 'total' => $total );

				return $data;
			}
		} // end else


	}

	function get_news_qestions( $aid, $type ) {
		$query  = "select * from mdl_poll where aid=$aid and type=$type";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$pid = $row['id'];
		}
		$query  = "select * from mdl_poll_q where pid=$pid";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$q[] = $row['id'];
		}

		return $q;
	}

	function get_news_student_answers( $userid, $questions ) {
		$answers = array();
		$qs      = implode( ',', $questions );
		$query   = "select * from mdl_poll_a where qid in ($qs)";
		$result  = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$a[] = $row['id'];
		}
		$as    = implode( ',', $a );
		$query = "select * from mdl_poll_student_answers where userid=$userid and aid in ($as)";
		//echo 'Query: '.$query."<br>";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$sta = new stdClass();
			foreach ( $row as $key => $value ) {
				$sta->$key = $value;
			} // end foreach
			$answers[] = $sta;
		} // end while

		return $answers;
	}

	function get_student_quiz_scores( $userid ) {
		$questions = $this->get_news_qestions( $this->articleID, 2 );
		$answers   = $this->get_news_student_answers( $userid, $questions );

		return $answers;
	}

	function get_student_pol_scores( $userid ) {
		$questions = $this->get_news_qestions( $this->articleID, 1 );
		$answers   = $this->get_news_student_answers( $userid, $questions );

		return $answers;
	}

	function get_board_id( $aid ) {
		$query  = "select * from mdl_board where aid=$aid";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$bid = $row['id'];
		}

		return $bid;
	}

	function get_student_forum_scores( $userid ) {
		$answers = array();
		$bid     = $this->get_board_id( $this->articleID );
		$query   = "select * from mdl_board_posts where bid=$bid and userid=$userid";
		$result  = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$sta = new stdClass();
			foreach ( $row as $key => $value ) {
				$sta->$key = $value;
			} // end foreach
			$answers[] = $sta;
		} // end while

		return $answers;
	}

	function get_csv_student_grades( $item ) {
		$aid      = $item->aid;
		$userid   = $item->userid;
		$userdata = $this->get_user_details( $userid );
		$groupid  = $this->get_postuser_group( $userid );
		$class    = $this->get_group_name( $groupid );

		$path   = $_SERVER['DOCUMENT_ROOT'] . "/lms/custom/tutors/grades_$userid.csv";
		$output = fopen( $path, 'w' );

		$pollA         = $this->get_student_pol_scores( $userid );
		$pollScoreData = $this->get_section_score_block( $pollA, false );
		$pollScore     = $pollScoreData['correct'];

		$quizA         = $this->get_student_quiz_scores( $userid );
		$quizScoreData = $this->get_section_score_block( $quizA, false );
		$quizScore     = $quizScoreData['correct'];

		$forumA     = $this->get_student_forum_scores( $userid, false );
		$forumScore = count( $forumA );

		$data = array( $userdata->firstname, $userdata->lastname, $class, $pollScore, $quizScore, $forumScore );
		fputcsv( $output, $data );
		fclose( $path );
	}

}