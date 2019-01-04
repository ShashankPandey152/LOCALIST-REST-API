<?php

    $link = mysqli_connect("******","******","******","******");

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

    //Resend verification email
    if($_GET['resend'] == 1) {
        $status = -1;

        $to = $_GET['email'];
        $subject = "Email Verification";
        $message = '

Please click this link to activate your account:
http://localist-com.stackstaging.com/verify.php?email='.$_GET['email'].'

This is a system generated mail. Do not reply.
        ';
        $headers = 'From:no-reply@localist.com' . "\r\n";
        if(mail($to, $subject, $message, $headers)) {
            $status = 1;
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

            if($row['status'] == 0) {
                $status = 3;
            } else if(hash('sha512', $_GET['password']) == $row['password']) {
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

        $id = Array();
        $name = Array();
        $latitude = Array();
        $longitude = Array();

        $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";

        $row = mysqli_fetch_array(mysqli_query($link, $query));

        $query = "SELECT * FROM `location` WHERE `cid` = '".$row['id']."'";

        if($result = mysqli_query($link, $query)) {

            while($row = mysqli_fetch_array($result)) {

                array_push($id, $row['id']);
                array_push($name, $row['name']);
                array_push($latitude, $row['latitude']);
                array_push($longitude, $row['longitude']);

            }

        }

        echo json_encode(Array("id" => $id, "name" => $name, "latitude" => $latitude, "longitude" => $longitude));

    }

    //Edit name of location
    if($_GET['edit'] == 1) {

        $status = -1;

        $query = "UPDATE `location` SET `name` = '".mysqli_real_escape_string($link, $_GET['name'])."' WHERE `id` = '".mysqli_real_escape_string($link, $_GET['id'])."'";

        if(mysqli_query($link, $query)) {
            $status = 1;
        }

        echo json_encode(Array("status" => $status));

    } else if($_GET['edit'] == 2) { //Delete a location

        $status = -1;

        $query = "DELETE FROM `location` WHERE `id` = '".mysqli_real_escape_string($link, $_GET['id'])."'";

        if(mysqli_query($link, $query)) {
            $status = 1;
        }

        echo json_encode(Array("status" => $status));

    }

    if($_GET['addItem'] == 1) {

        $status = -1;

        $item = mysqli_real_escape_string($link, $_GET['item']) . "+++";

        $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";

        $row = mysqli_fetch_array(mysqli_query($link, $query));

        $query = "SELECT `id` FROM `items` WHERE `cid` = '".$row['id']."' AND `store` = '".mysqli_real_escape_string($link, $_GET['store'])."'";

        if(mysqli_num_rows(mysqli_query($link, $query)) == 0) {

            $query = "INSERT INTO `items`(`cid`, `store`, `articles`) VALUES('".$row['id']."', '".mysqli_real_escape_string($link, $_GET['store'])."', '".$item."')";

            if(mysqli_query($link, $query)) {
                $status = 1;
            }

        } else {

            $query = "SELECT `articles` FROM `items` WHERE `cid` = '".$row['id']."' AND `store` = '".mysqli_real_escape_string($link, $_GET['store'])."'";

            $row1 = mysqli_fetch_array(mysqli_query($link, $query));

            $item = $row1['articles'].$item;

            $query = "UPDATE `items` SET `articles` = '".$item."' WHERE `cid` = '".$row['id']."' AND `store` = '".mysqli_real_escape_string($link, $_GET['store'])."'";

            if(mysqli_query($link, $query)) {
                $status = 1;
            }

        }

        echo json_encode(Array("status" => $status));

    }

    if($_GET['getChecklist'] == 1) {

        $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";

        $row = mysqli_fetch_array(mysqli_query($link, $query));

        $query = "SELECT * FROM `location` WHERE `cid` = '".$row['id']."'";

        $id2store = Array();
        $store2location = Array();

        if($result = mysqli_query($link, $query)) {

            while($row1 = mysqli_fetch_array($result)) {

                $id2store[$row1['id']] = $row1['name'];
                $store2location[$row1['name']] = Array($row1['latitude'], $row1['longitude']);

            }

        }

        $query = "SELECT * FROM `items` WHERE `cid` = '".$row['id']."'";

        $items = Array();

        if($result = mysqli_query($link, $query)) {

            $article = Array();

            while($row2 = mysqli_fetch_array($result)) {

                $article = array_filter(explode("+++", $row2['articles']));
                $items[$id2store[(int)$row2['store']]] = $article;

            }

        }

        if(sizeof($items) == 0) {
            $items['trash'] = Array("trash");
        }

        echo json_encode(Array("items" => $items, "locations" => $store2location));

    }

    if($_GET['deleteChecklist'] == 1) {

        $status = -1;

        $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_GET['email'])."'";

        $row = mysqli_fetch_array(mysqli_query($link, $query));

        $query = "DELETE FROM `items` WHERE `cid` = '".$row['id']."'";

        if(mysqli_query($link, $query)) {
            $status = 1;
        }

        echo json_encode(Array("status" => $status));

    }

?>
