<?php

session_start();

set_error_handler(function() {
  throw new Exception('Failed to open authentification file in <code>'. FILE_PASS .'</code>');
});

require 'config.php';

// logout
if (isset($_GET['logout'])) {
  unset($_SESSION['authentificated']);
  session_destroy();
}

// check identification
else if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
  try {
    //*
    $db = json_decode(file_get_contents(FILE_PASS));
    $username = $db->{'user'};
    $password = $db->{'password'};
    /*/
    $username = 'test';
    $password = 'test';
    //*/
    if ($_POST['username'] == $username && $_POST['password'] == $password)
      $_SESSION['authentificated'] = true;
    else
      $_SESSION['message'] = 'Incorrect username or password.';  
  } catch(Exception $e) {
    $_SESSION['message'] = $e->getMessage();
  }
}

header('Location: ' . _url('/', null, false, '&'));
exit();

?>