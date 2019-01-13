<?php


    $error = "";

    //Getting logout key to logout user.
    if (array_key_exists("logout", $_GET)) {

        unset($_SESSION);
        setcookie("id", "", time() - 60 *60);
        $_COOKIE["id"] = "";

        //If session and cookie ids exists then keep user logged in and redirect to "loggedinpage".
    } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {

        header("Location: loggedinpage.php");
    }

    if (array_key_exists("submit", $_POST)) {

        include("connection.php");

    $username = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $diarytext = mysqli_real_escape_string($conn, $_POST['diary']);

    //Email and Password verification
    if ($_POST['email'] == '') {

        session_start();

    $error .= "Email address is required";

    } else if ($_POST['password'] == '') {

    $error .= "password is required";
    }

    //Errors in verification
    if ($error!= "") {

    $error = "<p>There were error(s) in your form:</p>".$error;

    } else {

        if ($_POST['signUp'] == '1') {


            //Verifying if Email already exists
            $query = "SELECT * FROM `users` WHERE email = '" . $username . "'";

            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) >= 1) {

                $error = "That email address already exists.";

            } else {

                //Inserting new email and password in database.
                $query = "INSERT INTO `users` (`email`, `password`, diary) VALUES ('$username', '$password', '$diarytext')";

                //Verifying the insertion.
                if (mysqli_query($conn, $query)) {

                    //Creating hash for password.
                    $query = "UPDATE `users` SET password = '" . md5(md5(mysqli_insert_id($conn)) . $_POST['password']) . "' WHERE id = " . mysqli_insert_id($conn) . " LIMIT 1";

                    mysqli_query($conn, $query);

                    //Creating session for staying logged in.
                    $_SESSION['id'] = mysqli_insert_id($conn);

                    if ($_POST['stayLoggedIn'] == '1') {

                        //Setting cookie to hold data
                        setcookie("id", mysqli_insert_id($conn), time() + 60 * 60 * 24 * 365);

                    }

                    header("Location: loggedinpage.php");


                } else {

                    //This will show where the error is.
                    echo "Error: " . $query . "<br>" . mysqli_error($conn);

                    //Verifying if there is a problem.
                    $error = "<p>There was a problem signing you up - please try again later.</p>";
                }

            }
        } else {

            //Doing query to match entered email with the saved email data.
             $query = "SELECT * FROM users WHERE email = '$username'";

             $result = mysqli_query($conn, $query);

             //Fetching related array from $result and storing in $row.
             $row = mysqli_fetch_array($result);

             if (isset($row) ) {

                 //Matching the password entered with the password stored.
                 $hashedPassword = md5(md5($row['id']).$_POST['password']);

                 if ($hashedPassword == $row['password']) {

                     $_SESSION['id'] = $row['id'];

                     if ($_POST['stayLoggedIn'] == '1') {

                         //Setting cookie to hold data
                         setcookie("id", $row['id'], time() + 60 * 60 * 24 * 365);


                     }
                     //Redirecting to "loggedinpage" page.
                     header("Location: loggedinpage.php");

                 } else {

                     $error = "The email/password combination not found";
                 }

             } else {

                  $error = "The email/password combination not found";
             }
        }
    }

}

?>

<?php include("header.php"); ?>

<div class="container" id="homePageContainer">

    <h1>Secret Journal</h1>

    <p><strong>Store your thoughts permanently and securely.</strong></p>

    <div id="error"><?php if ($error!="") {
            echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';

        } ?></div>

    <form method="post" id = "signUpForm">

        <p>Interested? Sign up now.</p>

        <fieldset class="form-group">

            <input class="form-control" type="email" name="email" placeholder="Your Email">

        </fieldset>

        <fieldset class="form-group">

            <input class="form-control" type="password" name="password" placeholder="Password">

        </fieldset>

        <div class="checkbox">

            <label>

                <input type="checkbox" name="stayLoggedIn" value=1> Stay logged in

            </label>

        </div>

        <fieldset class="form-group">

            <input type="hidden" name="signUp" value="1">

            <input class="btn btn-success" type="submit" name="submit" value="Sign Up!">

        </fieldset>

        <p><a class="toggleForms">Log in</a></p>

    </form>

    <form method="post" id = "logInForm">

        <p>Log in using your username and password.</p>

        <fieldset class="form-group">

            <input class="form-control" type="email" name="email" placeholder="Your Email">

        </fieldset>

        <fieldset class="form-group">

            <input class="form-control"type="password" name="password" placeholder="Password">

        </fieldset>

        <div class="checkbox">

            <label>

                <input type="checkbox" name="stayLoggedIn" value=1> Stay logged in

            </label>

        </div>

        <input type="hidden" name="signUp" value="0">

        <fieldset class="form-group">

            <input class="btn btn-success" type="submit" name="submit" value="Log In!">

        </fieldset>

        <p><a class="toggleForms">Sign up</a></p>

    </form>

</div>

<?php include("footer.php"); ?>