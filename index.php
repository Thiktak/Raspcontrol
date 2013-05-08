<?php

namespace lib;

spl_autoload_extensions('.php');
spl_autoload_register();

session_start();

require 'config.php';

// param page
$get_page = filter_input(INPUT_GET, 'page') ?: 'home';
$get_page = strtr($get_page, array(DIRECTORY_SEPARATOR => null, '\\' => null));

// authentification
if (isset($_SESSION['authentificated']) && $_SESSION['authentificated']) {
  $display = true;
  function is_active($page) {
    if ($page == $_GET['page'])
      echo ' class="active"';
  }
}
else {
  $get_page = 'login';
  $display = false;
}

$page = 'pages'. DIRECTORY_SEPARATOR . $get_page. '.php';
$page = file_exists($page) ? $page : 'pages'. DIRECTORY_SEPARATOR .'404.php';

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Raspcontrol</title>
    <meta name="author" content="Nicolas Devenet" />
    <meta name="robots" content="noindex, nofollow, noarchive" />
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
    <link rel="icon" type="image/png" href="img/favicon.ico" />
    <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet" />
    <link href="css/raspcontrol.css" rel="stylesheet" media="screen" />
  </head>

  <body>

    <header>
      <div class="container">
        <a href="<?php _url('/'); ?>"><img src="img/raspcontrol.png" alt="rbpi" /></a>
        <h1><?php _link('Raspcontrol', '/'); ?></h1>
        <h2>The Raspberry Pi Control Center</h2>
      </div>
    </header>

    <?php if ($display) : ?>

    <div class="navbar navbar-static-top navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <div class="nav-collapse collapse">
			  <ul class="nav">
				<li<?php is_active('home'); ?>><a href="<?php _url('/'); ?>"><i class="icon-home icon-white"></i> Home</a></li>
        <li<?php is_active('details'); ?>><a href="<?php _url('details'); ?>"><i class="icon-search icon-white"></i> Details</a></li>
        <li<?php is_active('infos'); ?>><a href="<?php _url('infos'); ?>"><i class="icon-tasks icon-white"></i> Stats</a></li>
        <li<?php is_active('cmd'); ?>><a href="<?php _url('cmd'); ?>"><i class="icon-play icon-white"></i> Commands</a></li>
			  </ul>
			  <ul class="nav pull-right">
				<li><a href="login.php?logout"><i class="icon-off icon-white"></i> Logout</a></li>
			  </ul>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>

    <div id="content">
      <?php if (isset($_SESSION['message'])) { ?>
      <div class="container">
        <div class="alert alert-error">
          <strong>Oups!</strong> <?php echo $_SESSION['message']; ?>
        </div>
      </div>
      <?php unset($_SESSION['message']); } ?>
      
<?php
try {
  include $page;
}
catch( InternalError $err ) {
  echo <<<EOL
  <div class="container error">
    <h1>{$err->getTitle()}</h1>
    <div class="alert alert-block alert-{$err->getMessageType()}">
      {$err->getMessage()}
    </div>
  </div>
EOL;
}
?>

    </div> <!-- /content -->

    <footer>
      <div class="container">
        <p>Powered by <a href="https://github.com/Bioshox/Raspcontrol">Raspcontrol</a>.</p>
        <p>Sources are available on <a href="https://github.com/Bioshox/Raspcontrol">Github</a>.</p>
      </div>
    </footer>

    <script src="http://code.jquery.com/jquery-latest.js"></script>
  	<script src="js/bootstrap.min.js"></script>
    <?php echo isset($javascripts) ? $javascripts : null; ?>
  </body>
</html>
