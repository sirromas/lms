<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/common/Utils.php';


class Forum extends Utils
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $title
     * @param $added
     * @return string
     */
    function get_forum_title_block($title, $added)
    {
        $list = "";
        $date = date('F j, Y, g:i a', $added);

        $list .= "<div class='row'>";
        $list .= "<span style='font-weight: bold;'>$title</span>";
        $list .= "</div>";

        $list .= "<div class='row'>";
        $list .= "<span style=''>by Teacher - $date</span>";
        $list .= "</div>";

        return $list;
    }

    /**
     * @param $userid
     * @return string
     */
    function get_student_details($userid)
    {
        $query = "select * from mdl_user where id=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $user = $row['firstname'] . ' ' . $row['lastname'];
        }

        return $user;
    }

    /**
     * @param $title
     * @param $post
     * @param $userid
     * @param $added
     * @param $replyto
     * @return string
     */
    function get_student_title_block($title, $post, $userid, $added, $replyto)
    {
        $list = "";
        $user = $this->get_student_details($userid);



        $mobile = $_SESSION['mobile'];

        if ($mobile) {
            $date = date('m/d/Y', $added);
            $list .= "<div class='row' style='text-align: center;margin-left: 0px;margin-right: 0px;'>";
            $list .= "<span style='margin-left: 15%;'>Re: $post</span>";
            $list .= "</div>";

            $list .= "<div class='row'>";
            $list .= "<span style='margin-left: 15%;'>by $user - $date</span>";
            $list .= "</div>";
        } // end if
        else {
            $date = date('F j, Y, g:i a', $added);
            $list .= "<div class='row' style='text-align: left;'>";
            $list .= "<span style=''>Re: <span style='font-weight: bold'>$title</span><br>$post</span>";
            $list .= "</div>";

            $list .= "<div class='row'>";
            $list .= "<span style=''>by $user - $date</span>";
            $list .= "</div>";
        }
        return $list;

    }

    /**
     * @return int
     */
    function get_post_treshold()
    {
        $query = "select * from mdl_edit_post_treshold";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $threshold = $row['threshold'];
        }
        return $threshold * 3600;
    }

    /**
     * @param $id
     * @param $postuserid
     * @return string
     */
    function get_edit_post_btn($id, $postuserid)
    {
        $list = "";
        $now = time();
        if ($this->user->id == $postuserid) {
            $query = "select * from mdl_board_posts where id=$id and userid=$postuserid";
            $result = $this->db->query($query);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $added = $row['added'];
            }
            $threshold = $this->get_post_treshold();
            if (($now - $added) <= $threshold) {
                $list .= "<button class='btn btn-default' id='student_edit_own_post_$id'>Edit</button>";
            }
        }
        return $list;
    }

    /**
     * @param $id
     * @param $title
     * @param $added
     * @param $userid
     * @return string
     */
    function get_forum_posts($id, $title, $added, $userid)
    {
        $list = "";
        $mobile = $_SESSION['mobile'];

        if ($mobile) {
            $titleBlock = $this->get_forum_title_block($title, $added);
            $list .= "<div class='row' style='background: #f7f8f9;' style='width: 100%; '>";
            $list .= "<span class='col-md-2'><i class='fa fa-address-book-o fa-5x' aria-hidden='true'></i></span>";
            $list .= "<span class='col-md-6' style='margin-top: 10px;'>$titleBlock</span>";
            $list .= "<span class='col-md-2' style='margin-top: 15px;'><button class='btn btn-default' id='root_reply'>Reply</button></span>";
            $list .= "</div>";

            $list .= "<div class='row' style='display: none;' id='root_reply_container'>";
            $list .= "<span class='col-md-2'>&nbsp;</span>";
            $list .= "<span class='col-md-6' style='margin-top: 10px;'><textarea id='root_reply_text' rows='6' style='width: 100%'></textarea></span>";
            $list .= "<span class='col-md-2' style='margin-top: 10px;'><button class='btn btn-default' id='submit_root_reply'>Submit</button></span>";
            $list .= "</div>";

            $query = "select * from mdl_board_posts where bid=$id order by added ";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $list .= "<div class='row' style='margin-bottom: 15px;'>";
                $list .= "<span class='col-md-12'></span>";
                $list .= "</div>";
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $id = $row['id'];
                    $post = $row['post'];
                    $added = $row['added'];
                    $replyto = $row['replyto'];
                    $postuserid = $row['userid'];
                    $status = $this->is_user_belongs_to_current_group($postuserid);
                    if ($status) {
                        $studentpost = $this->get_student_title_block($title, $post, $postuserid, $added, $replyto);
                        $edit_btn = $this->get_edit_post_btn($id, $postuserid);
                        $list .= "<div class='row' style='background: #f7f8f9;' style='width: 100%;'>";
                        $list .= "<span class='col-md-2'><i class='fa fa-user-circle fa-3x' aria-hidden='true'></i></span>";
                        $list .= "<span class='col-md-8' style='margin-top: 10px;margin-left: 3%;'>$studentpost</span>";
                        $list .= "<span class='col-md-2' style='margin-top: 15px;'>
                    <button class='btn btn-default' id='student_post_reply_$id'>Reply</button>
                    <br>$edit_btn
                    </span>";
                        $list .= "</div>";

                        $list .= "<div class='row' style='display: none;' id='edit_post_container_$id'>";
                        $list .= "<span class='col-md-2'>&nbsp;</span>";
                        $list .= "<span class='col-md-6' style='margin-top: 10px;'><textarea id='update_student_post_text_$id' rows='6' style='width: 100%'>$post</textarea></span>";
                        $list .= "<span class='col-md-2' style='margin-top: 10px;'><button class='btn btn-default' id='update_student_reply_$id'>Submit</button></span>";
                        $list .= "</div>";

                        $list .= "<div class='row' style='display: none;' id='student_reply_container_$id'>";
                        $list .= "<span class='col-md-2'>&nbsp;</span>";
                        $list .= "<span class='col-md-6' style='margin-top: 10px;'><textarea id='student_reply_text_$id' rows='6' style='width: 100%'></textarea></span>";
                        $list .= "<span class='col-md-2' style='margin-top: 10px;'><button class='btn btn-default' id='submit_student_reply_$id'>Submit</button></span>";
                        $list .= "</div>";
                    } // end if $status
                } // end while
            } // end if $num>0

        } // end if
        else {
            $titleBlock = $this->get_forum_title_block($title, $added);
            $list .= "<div class='row' style='background: #f7f8f9;' style='width: 700px; '>";
            $list .= "<span class='col-md-2'><i class='fa fa-address-book-o fa-5x' aria-hidden='true'></i></span>";
            $list .= "<span class='col-md-6' style='margin-top: 10px;'>$titleBlock</span>";
            $list .= "<span class='col-md-2' style='margin-top: 15px;'><button class='btn btn-default' id='root_reply'>Reply</button></span>";
            $list .= "</div>";

            $list .= "<div class='row' style='display: none;' id='root_reply_container'>";
            $list .= "<span class='col-md-2'>&nbsp;</span>";
            $list .= "<span class='col-md-6' style='margin-top: 10px;'><textarea id='root_reply_text' rows='6' style='width: 100%'></textarea></span>";
            $list .= "<span class='col-md-2' style='margin-top: 10px;'><button class='btn btn-default' id='submit_root_reply'>Submit</button></span>";
            $list .= "</div>";

            $query = "select * from mdl_board_posts where bid=$id order by added ";
            $num = $this->db->numrows($query);
            if ($num > 0) {
                $list .= "<div class='row' style='margin-bottom: 15px;margin-left: 35px'>";
                $list .= "<span class='col-md-12'></span>";
                $list .= "</div>";
                $result = $this->db->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $id = $row['id'];
                    $post = $row['post'];
                    $added = $row['added'];
                    $replyto = $row['replyto'];
                    $postuserid = $row['userid'];
                    $status = $this->is_user_belongs_to_current_group($postuserid);
                    if ($status) {
                        $studentpost = $this->get_student_title_block($title, $post, $postuserid, $added, $replyto);
                        $edit_btn = $this->get_edit_post_btn($id, $postuserid);
                        $list .= "<div class='row' style='background: #f7f8f9;' style='width: 700px;margin-left: 25px; '>";
                        $list .= "<span class='col-md-2'><i class='fa fa-user-circle fa-3x' aria-hidden='true'></i></span>";
                        $list .= "<span class='col-md-8' style='margin-top: 10px;'>$studentpost</span>";
                        $list .= "<span class='col-md-2' style='margin-top: 15px;'>
                    <button class='btn btn-default' id='student_post_reply_$id'>Reply</button>
                    <br>$edit_btn
                    </span>";
                        $list .= "</div>";

                        $list .= "<div class='row' style='display: none;' id='edit_post_container_$id'>";
                        $list .= "<span class='col-md-2'>&nbsp;</span>";
                        $list .= "<span class='col-md-6' style='margin-top: 10px;'><textarea id='update_student_post_text_$id' rows='6' style='width: 100%'>$post</textarea></span>";
                        $list .= "<span class='col-md-2' style='margin-top: 10px;'><button class='btn btn-default' id='update_student_reply_$id'>Submit</button></span>";
                        $list .= "</div>";

                        $list .= "<div class='row' style='display: none;' id='student_reply_container_$id'>";
                        $list .= "<span class='col-md-2'>&nbsp;</span>";
                        $list .= "<span class='col-md-6' style='margin-top: 10px;'><textarea id='student_reply_text_$id' rows='6' style='width: 100%'></textarea></span>";
                        $list .= "<span class='col-md-2' style='margin-top: 10px;'><button class='btn btn-default' id='submit_student_reply_$id'>Submit</button></span>";
                        $list .= "</div>";
                    } // end if $status
                } // end while
            } // end if $num>0
        }

        return $list;
    }

    /**
     * @param $userid
     * @return mixed
     */
    function get_postuser_group($userid)
    {
        $query = "select * from mdl_groups_members where userid=$userid";
        $result = $this->db->query($query);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $groupid = $row['groupid'];
        }

        return $groupid;
    }

    /**
     * @param $postuserid
     * @return bool
     */
    function is_user_belongs_to_current_group($postuserid)
    {
        $groups = $this->get_user_groups_by_userid($postuserid);
        $pid = $this->get_postuser_group($postuserid);
        $status = (in_array($pid, $groups) == true) ? true : false;

        return $status;

    }

    /**
     * @param $groups
     * @return string
     */
    function get_forum_groups_dropdown($groups)
    {
        $list = "";
        foreach ($groups as $groupid) {

        }

        return $list;
    }

    /**
     * @param $userid
     * @return string
     */
    function get_news_forum($userid)
    {
        $list = "";
        $aid = $this->get_news_id();
        $groups = $this->get_user_groups_by_userid($userid);
        if (count($groups) == 0) {
            $list .= "<div class='row'></div>";
        } // end if
        else {
            if ($aid > 0) {
                $query = "select * from  mdl_board where aid=$aid";
                $num = $this->db->numrows($query);
                if ($num > 0) {

                    $result = $this->db->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $forumid = $row['id'];
                        $title = $row['title'];
                        $added = $row['added'];
                    }

                    $mobile = $_SESSION['mobile'];
                    $posts = $this->get_forum_posts($forumid, $title, $added, $userid);

                    if ($mobile) {


                        $list .= "<input type='hidden' id='forumid' value='$forumid'>";

                        $list .= "<div class='row' style='padding-bottom: 15px;padding-top: 15px'>";
                        $list .= "<span style='font-size: 20px;border-bottom: 2px solid #000000;padding-bottom: 2px;'>Disscussion board</span>";
                        $list .= "</div>";


                        $list .= "<div class='row' style='text-align: center'>";
                        $list .= "<span class=''>$posts</span>";
                        $list .= "</div>";


                    } // end if
                    else {

                        $list .= "<div id='container36' style='width: 738px;height: auto;margin-bottom: 35px;text-align: center;'>";
                        $list .= "<input type='hidden' id='forumid' value='$forumid'>";

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

                    } // end else

                } // end if $num>0
            }
        }

        return $list;
    }


    /**
     * @return string
     */
    function create_news_forum_block()
    {
        $list = "";


        return $list;
    }


    /**
     * @param $item
     */
    function add_forum_post($item)
    {
        $userid = $item->userid;
        $forumid = $item->forumid;
        $text = addslashes($item->text);
        $now = time();
        $replyto = $item->replyto;
        $query = "insert into mdl_board_posts (bid,userid,replyto,post,added) 
				    values ($forumid,$userid,$replyto,'$text','$now')";
        $this->db->query($query);
    }

    /**
     * @param $item
     */
    function update_user_post($item)
    {
        $id = $item->id;
        $text = addslashes($item->post);
        $query = "update mdl_board_posts set post='$text' where id=$id";
        $this->db->query($query);
    }

}