<?php

if( !defined('FILE_CMD') )
    define('FILE_CMD', realpath(dirname(__FILE__) . '/../') . '/raspberry.cmd.out');

$get_cmd = filter_input(INPUT_GET, 'cmd') ?: null;
$get_run = filter_input(INPUT_GET, 'run') ?: false;

$listOfCmd = \lib\cmd\cmd::getListOfCmd();

if( $get_cmd ) {
    if( !isset($listOfCmd[$get_cmd]) )
        throw new lib\FatalError('Comand not found !', sprintf('Sorry, but `%s` command not foud ...', $get_cmd));

    if( $get_run ) {
        ob_clean();
        try {
            set_time_limit(0);
            echo implode(PHP_EOL, (array) \lib\Cmd\Cmd::create($get_cmd)->exec());
        }
        catch(\Exception $e)
        {
            echo 'Error : <em>', $e, '</em>';
        }
        exit();
    }
?>
<div class="container cmd">
  <h1>Command <em><?php echo $listOfCmd[$get_cmd]['name']; ?></em></h1>
  
  <div class="alert alert-block alert-info">
    <p>
      <strong>Description:</strong>
      <?php echo $listOfCmd[$get_cmd]['description']; ?>
    </p>  
  </div>

  <p <?php echo !$listOfCmd[$get_cmd]['runnable'] ? ' class="hide"' : null; ?>>
    <?php if( $listOfCmd[$get_cmd]['refresh'] ): ?> 
    <a id="run-<?php echo $get_cmd; ?>" href="#log" class="btn btn-block btn-large btn-danger"><i class="icon-play-circle"></i> RUN !</a>
    <?php else: ?>
    <a id="run-<?php echo $get_cmd; ?>" href="#log" class="btn btn-block btn-large btn-warning"><i class="icon-play-circle"></i> RUN !</a>
    <?php endif; ?>
  </p>
  <?php //$js[] = '$(\'#run-' . $get_cmd . '\'); endif; ?>

  <br />

  <pre id="log"></pre>
</div>
<?php
  $url = _url('cmd', array('cmd' => $get_cmd, 'run'), false, '&');

  $js[] = '<script>';

  if( !$listOfCmd[$get_cmd]['runnable'] )
      $js[] = '$(document).ready(function() { $(\'#run-' . $get_cmd . '\').click(); });';
  
  $js[] = '  function addToLogs(text, type) { type = type || \'msg\'; $(\'#log\').append(\'<span class="cmd-msg-\' + type + \'">\' + text + "</span>\n"); }';
  $js[] = '  ';
  $js[] = '  $(\'#run-' . $get_cmd . '\').click(function(event) {';
  
  if( $listOfCmd[$get_cmd]['confirm'] )
    $js[] = '    if( !confirm(\'Please note that this command can be dangerous or disable your raspberry for a certain period. Are you really sure?\') ) return false;';
  
  $js[] = '    addToLogs(\'> ' . $get_cmd . ' started !\', \'cmd\');';
  $js[] = '    addToLogs(\'waiting ...\', \'msg\');';
  $js[] = '    $.get(\'' . $url . '\')';
  $js[] = '    .done(function(datas) {';
  $js[] = '      addToLogs(\'> command done !\', \'cmd\');';
  $js[] = '      addToLogs(datas, \'datas\');';

  if( $listOfCmd[$get_cmd]['refresh'] ) {
    $js[] = '      addToLogs(\'> refresh mode activated (try each ' . $listOfCmd[$get_cmd]['refresh'] . ' sec)\');';
    $js[] = '      setInterval(function() {';
    $js[] = '        addToLogs(\'> connection to server ...\', \'cmd\');';
    $js[] = '        $.get(\'' . _url('/', null, false, '&') . '\', function() {';
    $js[] = '          addToLogs(\'REFRESH\', \'cmd\');';
    $js[] = '          window.location.reload();';
    $js[] = '        })';
    $js[] = '        .fail(function() { addToLogs(\'> no response ...\', \'cmd\'); });';
    $js[] = '      }, ' . $listOfCmd[$get_cmd]['refresh']*1000 . ');';
  }

  $js[] = '    })';
  $js[] = '    .fail(function() {';
  $js[] = '      addToLogs(\'> command fail !\');';
  $js[] = '    })';
  $js[] = '    ;';
  $js[] = '    event.preventDefault();';
  $js[] = '  });';
  $js[] = '</script>';
  $javascripts = implode(PHP_EOL, $js);
}
else {
?>
<div class="container cmd">
  <h1>Command list</h1>
  <ul class="list-cmd">
    <?php foreach( $listOfCmd as $name => $infos ): ?>
    <li>
      <a class="btn" href="<?php echo _url('cmd', array('cmd' => $name)); ?>">
        <i class="icon-<?php echo $infos['icon']; ?>"></i>
        <span><?php echo $infos['name']; ?></span>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php
}


?>