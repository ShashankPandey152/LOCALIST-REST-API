<?php

    $link = mysqli_connect("shareddb-h.hosting.stackcp.net", "prodData-3333764a", "password98@", "prodData-3333764a");

    //Signup 
    if($_GET['signup'] == 1) {
        
        $status = -1;
        
        $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";
        
        if(mysqli_num_rows(mysqli_query($link, $query)) > 0) {
            $status = 2;
        } else {
            
            $query = "INSERT INTO `users`(`name`, `email`, `password`) VALUES('".mysqli_real_escape_string($link, $_GET['name'])."', '".mysqli_real_escape_string($link, $_GET['email'])."', '".mysqli_real_escape_string($link, hash('sha512', $_GET['password']))."')";
        
            if(mysqli_query($link, $query)) {

                $to = $_GET['email'];
                $subject = "Email Verification";
                $message = '
Thanks for signing up!
Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.

------------------------
Username: '.$_GET['email'].'
Password: '.$_GET['password'].'
------------------------

Please click this link to activate your account:
http://localist-com.stackstaging.com/verify.php?email='.$_GET['email'].'

This is a system generated mail. Do not reply. 
                ';
                $headers = 'From:no-reply@localist.com' . "\r\n"; 
                if(mail($to, $subject, $message, $headers)) {
                    $status = 1;    
                }
                
            } else {
                $status = 0;
            }
            
        }
            
        echo json_encode(Array("status" => $status));        
        
    }
    
    //Login
    if($_GET['login'] == 1) {
        
        $status = -1;
        
        $query = "SELECT * FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";
        
        if(mysqli_num_rows(mysqli_query($link, $query)) == 0) {
            $status = 0;
        } else {
            $row = mysqli_fetch_array(mysqli_query($link, $query));
            
            if(hash('sha512', $_GET['password']) == $row['password']) {
                $status = 1;
            } else {
                $status = 2;
            }
        }
        
        echo json_encode(Array("status" => $status));
        
    }

    //Forgot password
    if($_GET['forgot'] == 1) {
        
        $status = -1;
        
        $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";
        
        if(mysqli_num_rows(mysqli_query($link, $query)) == 0) {
            $status = 0;
        } else {
            $to = $_GET['email'];
            $subject = "Password reset";
            $message = '

Please click this link to reset your password:
http://localist-com.stackstaging.com/forgot.php?email='.$_GET['email'].'

This is a system generated mail. Do not reply. 
            ';
            $headers = 'From:no-reply@localist.com' . "\r\n"; 
            if(mail($to, $subject, $message, $headers)) {
                $status = 1;    
            }
        }
        
        echo json_encode(Array("status" => $status));
        
    }

    //Check if email address is verified
    if($_GET['status'] == 1) {
        
        $status = -1;
        
        $query = "SELECT `status` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";
        
        $row = mysqli_fetch_array(mysqli_query($link, $query));
        
        echo json_encode(Array("status" => $row['status']));
        
    }

    //Add a new location
    if($_GET['location'] == 1) { 
        
        $status = -1;
        
        $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";
        
        $row = mysqli_fetch_array(mysqli_query($link, $query));
        
        $query = "INSERT INTO `location`(`cid`, `name`, `latitude`, `longitude`) VALUES('".$row['id']."', '".mysqli_real_escape_string($link, $_GET['name'])."', '".mysqli_real_escape_string($link, $_GET['latitude'])."', '".mysqli_real_escape_string($link, $_GET['longitude'])."')";
        
        if(mysqli_query($link, $query)) {
            $status = 1;
        }
        
        echo json_encode(Array("status" => $status));
        
    } else if($_GET['location'] == 2) { //Get list of all the location
        
        $name = Array();
        $latitude = Array();
        $longitude = Array();
        
        $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";
        
        $row = mysqli_fetch_array(mysqli_query($link, $query));
        
        $query = "SELECT * FROM `location` WHERE `cid` = '".$row['id']."'";
        
        if($result = mysqli_query($link, $query)) {
            
            while($row = mysqli_fetch_array($result)) {
                
                array_push($name, $row['name']);
                array_push($latitude, $row['latitude']);
                array_push($longitude, $row['longitude']);
                
            }
            
        }
        
        echo json_encode(Array("name" => $name, "latitude" => $latitude, "longitude" => $longitude));
        
    }

?>