<?php session_start(); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <script src="https://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="utils.js"></script>          
        <link rel="stylesheet" href="../register.css" />

    </head>

    <body>

        <br/><br/><br/> <p align="center"><img src="globalizationplus.jpg"></p>

        <?php
        if ($_POST) {
            if ($_POST['username'] == 'manager' && $_POST['password'] == 'strange12') {
                $_SESSION['username'] = 'manager';
                $_SESSION['loggedin'] = true;
            } // end if $_POST['username'] == 'manager' && $_POST['password'] == 'strange12'
        } // end if $_POST

        if ($_SESSION['username'] == 'manager' && $_SESSION['loggedin'] == true) {

            require_once './utils.php';
            $util = new utils();
            echo "<p align='center'>Welcome, Manager of the system </p>";
            $tbl = "";
            $tbl = $tbl . "<table align='center' width='675px;' border='0'>";
            $tbl = $tbl . "<tr>";
            $tbl = $tbl . "<th><span id='group_codes' style='cursor:pointer;'>Show group codes</span></th>"
                    . "<th><span id='promo_codes' style='cursor:pointer;'>Show promo codes</span></th>";
            $tbl = $tbl . "</tr>";

            $tbl = $tbl . "<tr>";
            $tbl = $tbl . "<td colspan='2'><div id='group_codes_content' style='display:none;'>" . $util->getGroupPage() . "</div></td>";
            $tbl = $tbl . "</tr>";

            $tbl = $tbl . "<tr>";
            $tbl = $tbl . "<td colspan='2'><div id='promo_codes_content' style='display:none;'>" . $util->getPromoPage() . "</div></td>";
            $tbl = $tbl . "</tr>";
            
            $tbl = $tbl . "<tr>";
            $tbl = $tbl . "<td align='center' colspan='2'>";
            $tbl = $tbl . "<p align='center'><span id='new_promo' style='cursor:pointer;'>Generate new promo codes</span></p>";
            $tbl = $tbl . "<p align='center'><span id='logout' style='cursor:pointer;'>Logout</span></p>";
            $tbl = $tbl . "</td>";
            $tbl = $tbl . "</tr>";           
            
            echo $tbl;
        } // end if $_SESSION['username'] && $_SESSION['loggedin']==true
        else {
            ?>
            <div class="wrapper clearfix">
                <div align="center">
                    <section class="userLogin userForm clearfix oneCol">
                        <div class="loginForm dsR21">

                            <div class='CSSTableGenerator' id='signupwrap'
                                 style='table-layout: fixed; width: 620px; align: center;'>

                                <form class='cmxform' id='loginform' method="post" action=''>
                                    <table>
                                        <tr>
                                            <td colspan='2'>USER SIGNIN</td>
                                        </tr>
                                        <tr>
                                            <td style='width: 250px;'><label for='email'>Username*</label></td>
                                            <td><input id='username' name='username' 
                                                       style='background-color: rgb(250, 255, 189);width:173px;'/>&nbsp;<span
                                                       style='color: red; font-size: 12px;' id='email_err'></span></td>
                                        </tr>												
                                        <tr>
                                            <td><label for='password'>Password*</label></td>
                                            <td><input id='password' name='password' type='password'
                                                       style='background-color: rgb(250, 255, 189);width:173px;'/>
                                                &nbsp;<span style='color: red; font-size: 12px;width:173px;' id='pwd_err'></span></td>
                                        </tr>

                                        <tr id='tr_code' style='display: none;'>
                                            <td><label for='code'>Enroll code:*</label></td>
                                            <td><input id='code' name='code' style='background-color: rgb(250, 255, 189);width:180px'></input>														
                                                &nbsp;<span style='color: red; font-size: 12px;' id='code_err'></span></td>
                                        </tr>
                                        <td colspan='2'><input class='submit' type='submit' value='Submit'/></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>                   
                        </div>
                </div>
            </div>

            <?php
        }
        ?>

        <script type="text/javascript" src="jPaginate/jquery.paginate.js"></script> 
        <script type="text/javascript">
            
        </script>

    </body>    

</html>
