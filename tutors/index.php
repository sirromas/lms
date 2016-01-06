<?php session_start(); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <script src="https://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="tutors.js"></script>          
        <link rel="stylesheet" href="../register.css" />

    </head>

    <body>

        <br/><br/><br/> <p align="center"><img src="globalizationplus.jpg"></p>

        <?php 
        
        require_once './tutors.php';
        $tutor=new Tutors();
        $groups=$tutor->getGroupsList();
        
        ?>
        
        <p align="center"><strong>Please provide your email, group secret code you 
            received from Manager and select desired group</strong></p>

        <div class="wrapper clearfix">
            <div align="center">
                <section class="userLogin userForm clearfix oneCol">
                    <div class="loginForm dsR21">

                        <div class='CSSTableGenerator' id='signupwrap'
                             style='table-layout: fixed; width: 620px; align: center;'>

                            <form class='cmxform' id='loginform' method="post" action=''>
                                <table>
                                    <tr>
                                        <td colspan='2'>Professors confirmation</td>
                                    </tr>
                                    <tr>
                                        <td style='width: 250px;'><label for='email'>Email*</label></td>
                                        <td><input id='email' name='email' 
                                                   style='background-color: rgb(250, 255, 189);width:173px;'/>&nbsp;<span
                                                   style='color: red; font-size: 12px;' id='email_err'></span></td>
                                    </tr>												
                                    <tr>
                                        <td><label for='code'>Secret code*</label></td>
                                        <td><input id='code' name='code' 
                                                   style='background-color: rgb(250, 255, 189);width:173px;'/>
                                            &nbsp;<span style='color: red; font-size: 12px;width:173px;' id='code_err'></span></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><label for='groups'>Group*</label></td>
                                        <td> <?php echo $groups; ?>
                                            &nbsp;<span style='color: red; font-size: 12px;width:173px;' id='group_err'></span></td>
                                    </tr>
                                    
                                    <td colspan='2'><input class='submit' type='submit' value='Submit'/></td>
                                    </tr>
                                </table>
                            </form>
                        </div>                   
                    </div>
                    <br/><br/><br/>
                    <div align="center" id="confirm_status" name="confirm_status"></div>
            </div>
        </div>


    </body>    

</html>
