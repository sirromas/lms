<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';


class Forum extends Utils {

	function __construct() {
		parent::__construct();
	}

	function get_forum_title_block( $title, $added ) {
		$list = "";
		$date = date( 'F j, Y, g:i a', $added );

		$list .= "<div class='row'>";
		$list .= "<span style='font-weight: bold;'>$title</span>";
		$list .= "</div>";

		$list .= "<div class='row'>";
		$list .= "<span style=''>by Teacher - $date</span>";
		$list .= "</div>";

		return $list;
	}

	function get_student_details( $userid ) {
		$query  = "select * from mdl_user where id=$userid";
		$result = $this->db->query( $query );
		while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
			$user = $row['firstname'] . ' ' . $row['lastname'];
		}

		return $user;
	}

	function get_student_title_block( $title, $post, $userid, $added, $replyto ) {
		$list = "";
		$user = $this->get_student_details( $userid );

		$date = date( 'F j, Y, g:i a', $added );

		$list .= "<div class='row' style='text-align: left;'>";
		$list .= "<span style=''>Re: <span style='font-weight: bold'>$title</span><br>$post</span>";
		$list .= "</div>";

		$list .= "<div class='row'>";
		$list .= "<span style=''>by $user - $date</span>";
		$list .= "</div>";

		return $list;

	}

	function get_forum_posts( $id, $title, $added, $userid ) {
		$list       = "";
		$titleBlock = $this->get_forum_title_block( $title, $added );
		$list       .= "<div class='row' style='background: #f7f8f9;' style='width: 700px; '>";
		$list       .= "<span class='col-md-2'><i class='fa fa-address-book-o fa-5x' aria-hidden='true'></i></span>";
		$list       .= "<span class='col-md-6' style='margin-top: 10px;'>$titleBlock</span>";
		$list       .= "<span class='col-md-2' style='margin-top: 15px;'><button class='btn btn-default' id='root_reply'>Reply</button></span>";
		$list       .= "</div>";

		$list .= "<div class='row' style='display: none;' id='root_reply_container'>";
		$list .= "<span class='col-md-2'>&nbsp;</span>";
		$list .= "<span class='col-md-6' style='margin-top: 10px;'><textarea id='root_reply_text' rows='6' style='width: 100%'></textarea></span>";
		$list .= "<span class='col-md-2' style='margin-top: 10px;'><button class='btn btn-default' id='submit_root_reply'>Submit</button></span>";
		$list .= "</div>";

		$query = "select * from mdl_board_posts where bid=$id order by added desc";
		$num   = $this->db->numrows( $query );
		if ( $num > 0 ) {
			$current_user_gtoupid = $this->get_postuser_group( $this->user->id );
			//echo "Current user group: " . $current_user_gtoupid . "<br>";
			$list   .= "<div class='row' style='margin-bottom: 15px;margin-left: 35px'>";
			$list   .= "<span class='col-md-12'></span>";
			$list   .= "</div>";
			$result = $this->db->query( $query );
			while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
				$id         = $row['id'];
				$post       = $row['post'];
				$added      = $row['added'];
				$replyto    = $row['replyto'];
				$postuserid = $row['userid'];
				$status     = $this->is_user_belongs_to_current_group( $postuserid );
				if ( $status ) {
					$studentpost = $this->get_student_title_block( $title, $post, $postuserid, $added, $replyto );
					$list        .= "<div class='row' style='background: #f7f8f9;' style='width: 700px;margin-left: 25px; '>";
					$list        .= "<span class='col-md-2'><i class='fa fa-user-circle fa-3x' aria-hidden='true'></i></span>";
					$list        .= "<span class='col-md-8' style='margin-top: 10px;'>$studentpost</span>";
					$list        .= "<span class='col-md-2' style='margin-top: 15px;'><button class='btn btn-default' id='student_post_reply_$id'>Reply</button></span>";
					$list        .= "</div>";

					$list .= "<div class='row' style='display: none;' id='student_reply_container_$id'>";
					$list .= "<span class='col-md-2'>&nbsp;</span>";
					$list .= "<span class='col-md-6' style='margin-top: 10px;'><textarea id='student_reply_text_$id' rows='6' style='width: 100%'></textarea></span>";
					$list .= "<span class='col-md-2' style='margin-top: 10px;'><button class='btn btn-default' id='submit_student_reply_$id'>Submit</button></span>";
					$list .= "</div>";
				}
			}
		} // end if $num>0

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

	function is_user_belongs_to_current_group( $postuserid ) {
		$groups = $this->get_user_groups();
		$pid    = $this->get_postuser_group( $postuserid );

		//echo "Post user id: " . $postuserid . "<br>";
		//echo "Post user group ID:" . $pid . "<br>";

		$status = ( in_array( $pid, $groups ) == true ) ? true : false;

		return $status;

	}

	function get_news_forum( $userid ) {
		$list  = "";
		$aid   = $this->get_news_id();
		$query = "select * from  mdl_board where aid=$aid";
		$num   = $this->db->numrows( $query );
		if ( $num > 0 ) {

			$result = $this->db->query( $query );
			while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) {
				$forumid = $row['id'];
				$title   = $row['title'];
				$added   = $row['added'];
			}
			$posts = $this->get_forum_posts( $forumid, $title, $added, $userid );
			$list  .= "<div id='container36' style='width: 738px;height: auto;margin-bottom: 35px;text-align: center;'>";
			$list  .= "<input type='hidden' id='forumid' value='$forumid'>";

			$list .= "<div class='row' style='padding-bottom: 15px;padding-top: 15px'>";
			$list .= "<span style='font-size: 20px;border-bottom: 2px solid #000000;padding-bottom: 2px;'>Disscussion board</span>";
			$list .= "</div>";

			$list .= "<div class='row' style='text-align: center;margin: 15px;'>";
			$list .= "<span class='col-md-10'>$posts</span>";
			$list .= "</div>";

			$list .= "<div class='row' style='margin-top: 15px;margin-bottom: 35px;'>";
			$list .= "<span class='col-md-2'></span>";
			$list .= "</div>";

			$list .= "</div>";

		} // end if $num>0

		return $list;
	}


	function add_forum_post( $item ) {
		$userid  = $item->userid;
		$forumid = $item->forumid;
		$text    = addslashes( $item->text );
		$now     = time();
		$replyto = $item->replyto;
		$query   = "insert into mdl_board_posts (bid,userid,replyto,post,added) 
				    values ($forumid,$userid,$replyto,'$text','$now')";
		$this->db->query( $query );
	}

}