<?php

if( !defined('FILE_STATS') )
    define('FILE_STATS', realpath(dirname(__FILE__) . '/../') . '/raspberry.stats');

if( !file_exists(FILE_STATS) ) {
    $message = sprintf('Check if `%s` exists', FILE_STATS);
    throw new lib\FatalError('rb(&pi;) stat file does not exists !', $message);
}

$js = array();
$js[] = '<script src="js/jquery.flot.min.js"></script>';
$js[] = '<script src="js/jquery.flot.time.min.js"></script>';
$js[] = '<script src="js/jquery-ui-1.10.3.custom.min.js"></script>';
/*
$js[] = '  timezoneJS.timezone.zoneFileBasePath = "tz";';
$js[] = '  timezoneJS.timezone.defaultZoneFile = [];';
$js[] = '  timezoneJS.timezone.init({async: false});';
// */

$g = array('title' => null, 'datas' => array(), 'options' => array());
$g['options']['series']['points']['show'] = true;
$g['options']['series']['points']['radius'] = 2;
$g['options']['xaxis']['mode'] = 'time';
$g['options']['series']['lines']['show'] = true;
$g['options']['yaxis']['min'] = 0;
$g['options']['series']['grid']['hoverable'] = true;
$g['options']['legend']['show'] = true;
$g['options']['legend']['position'] = 'sw'; // @todo up
$g['options']['grid']['hoverable'] = true;

$graphs = array();

$graphs['heat'] = $g;
$graphs['heat']['title'] = 'Heat (°C)';
$graphs['heat']['options']['yaxis']['max'] = 100;
$graphs['heat']['datas'][0]['label'] = '%';
$graphs['heat']['datas'][1]['label'] = '°C';

$graphs['swap']['title'] = 'Swap';
$graphs['swap']['options'] = $graphs['heat']['options'];
$graphs['swap']['datas'][0]['label'] = 'total';
$graphs['swap']['datas'][1]['label'] = 'used';
$graphs['swap']['datas'][2]['label'] = 'free';

$graphs['ram']['title'] = 'RAM';
$graphs['ram']['options'] = $graphs['heat']['options'];
$graphs['ram']['datas'][0]['label'] = '%';
$graphs['ram']['datas'][1]['label'] = 'used';

$graphs['cpu'] = $g;
$graphs['cpu']['title'] = 'CPU';
$graphs['cpu']['options']['yaxis']['min'] = 0;
$graphs['cpu']['datas'][0]['label'] = 'load';

$graphs['net_eth'] = $g;
$graphs['net_eth']['title'] = 'Network';
//$graphs['net_eth']['options']['yaxis'][0]['min'] = 0;
$graphs['net_eth']['datas'][0]['label'] = 'total';
$graphs['net_eth']['datas'][1]['label'] = 'up';
$graphs['net_eth']['datas'][2]['label'] = 'down';
$graphs['net_eth']['datas'][3]['label'] = 'connections';
$graphs['net_eth']['datas'][3]['yaxis'] = 2;
$graphs['net_eth']['options']['yaxes'][0]['min'] = 0;
$graphs['net_eth']['options']['yaxes'][1]['alignTicksWithAxis'] = true;
$graphs['net_eth']['options']['yaxes'][1]['position'] = 'right';

$graphs['users'] = $g;
$graphs['users']['title'] = 'Users';
$graphs['users']['options']['yaxis']['min'] = 0;
$graphs['users']['options']['yaxis']['max'] = 0;


$datas = array();
if( file_exists(FILE_STATS) )
    $datas = (array) json_decode(file_get_contents(FILE_STATS));

$timeMin = min(array_keys($datas));
$timeMax = max(array_keys($datas));

foreach( $datas as $time => $data ) {
    if( isset($data->ram) ) {
        $graphs['ram']['datas'][0]['data'][] = array($time*1000, (int) $data->ram->percentage);
        $graphs['ram']['datas'][1]['data'][] = array($time*1000, (int) $data->ram->used);
    }
    if( isset($data->cpu_heat->degrees) ) {
        $graphs['heat']['datas'][0]['data'][] = array($time*1000, (int) $data->cpu_heat->percentage);
        $graphs['heat']['datas'][1]['data'][] = array($time*1000, (int) $data->cpu_heat->degrees);
    }
    if( isset($data->cpu->loads) ) {
        $graphs['cpu']['datas'][0]['data'][] = array($time*1000, (int) ($data->cpu->loads*100));
    }
    if( isset($data->swap) ) {
        $graphs['swap']['datas'][0]['data'][] = array($time*1000, (int) $data->swap->total);
        $graphs['swap']['datas'][1]['data'][] = array($time*1000, (int) $data->swap->used);
        $graphs['swap']['datas'][2]['data'][] = array($time*1000, (int) $data->swap->free);
    }
    if( isset($data->net_eth) ) {
        $graphs['net_eth']['datas'][0]['data'][] = array($time*1000, (int) $data->net_eth->total);
        $graphs['net_eth']['datas'][1]['data'][] = array($time*1000, (int) $data->net_eth->up);
        $graphs['net_eth']['datas'][2]['data'][] = array($time*1000, (int) $data->net_eth->down);
    }
    if( isset($data->net_connections->connections) ) {
        $graphs['net_eth']['datas'][3]['data'][] = array($time*1000, (int) $data->net_connections->connections);
    }
    if( isset($data->users) ) {
        $nb = count($data->users);
        $graphs['users']['datas'][0]['data'][] = array($time*1000, (int) $nb);
        $graphs['users']['options']['yaxis']['max'] = max($graphs['users']['options']['yaxis']['max'], $nb+1);
    }
}
?>


<div class="container">
  <div id="stat-mode">
    <input type="datetime" id="stat-mode-start" value="<?php echo date('Y-m-d H:i', $timeMin); ?>" />
    <a id="stat-mode-all">all</a>
    <a id="stat-mode-last-hour">last day</a>
    <a id="stat-mode-last-hour">last hour</a>
    <input type="datetime" id="stat-mode-end" value="<?php echo date('Y-m-d H:i', $timeMax); ?>" />
  </div>
<?php

$js[] = '<script>';
$js[] = 'var graphDatas = {};';
$js[] = 'var graphOptions = {};';
foreach( $graphs as $graphName => $graphDatas )
{
    $js[] = 'graphDatas[\'' . $graphName . '\'] = ' . json_encode($graphDatas['datas']) . ';';
    $js[] = 'graphOptions[\'' . $graphName . '\'] = ' . json_encode($graphDatas['options']) . ';';
}

$js[] = '$(document).ready(function () {';
foreach( $graphs as $graphName => $graphDatas )
{
    $title = $graphDatas['title'] ?: $graphName;
    
    $js[] = '  $.plot($(\'#graph-' . $graphName . '\'), graphDatas[\'' . $graphName . '\'], graphOptions[\'' . $graphName . '\']);';

    echo <<<EOL
    <article class="graph">
      <h1>{$title}</h1>
      <div id="graph-{$graphName}" data-name="{$graphName}" class="graph"></div>
    </article>
EOL;
}
$js[] = '});';
?>
</div>

<p style="text-align: center">
  <?php echo _link('Monitor the r(&pi;)', 'infos'); ?>
</p>

<?php

$timeLastHour = strtotime('1 hour ago')*1000;

$js[] = <<< EOL
    function dateFromUTC( dateAsString, ymdDelimiter )
    {
      var pattern = new RegExp( "(\\d{4})" + ymdDelimiter + "(\\d{2})" + ymdDelimiter + "(\\d{2}) (\\d{2}):(\\d{2}):(\\d{2})" );
      var parts = dateAsString.match( pattern );

      return new Date( Date.UTC(
          parseInt( parts[1] )
        , parseInt( parts[2], 10 ) - 1
        , parseInt( parts[3], 10 )
        , parseInt( parts[4], 10 )
        , parseInt( parts[5], 10 )
        , parseInt( parts[6], 10 )
        , 0
      ));
    }
    $('#stat-mode-start').change(function () {
        $('div.graph').each(function() {
            graphName = $(this).attr('data-name');
            datas = graphDatas[graphName];
            options = graphOptions[graphName];

            options.xaxis.min = dateFromUTC($('#stat-mode-start').val(), '-').getTime();
            
            $.plot(this, datas, options);
        });
    });
    $('#stat-mode-end').change(function () {
        $('div.graph').each(function() {
            graphName = $(this).attr('data-name');
            datas = graphDatas[graphName];
            options = graphOptions[graphName];

            options.xaxis.max = dateFromUTC($('#stat-mode-end').val(), '-').getTime();
            
            $.plot(this, datas, options);
        });
    });

    $('#stat-mode-last-hour').click(function () {
        //$('#stat-mode-start').val($.datepicker.formatDate('yy-mm-dd HH:ii', new Date({$timeMin}*1000-3600*1000)));
        //$('#stat-mode-end').val($.datepicker.formatDate('yy-mm-dd', new Date({$timeMax}*1000)));
    });
    $('#stat-mode-all').click(function () {
        $('#stat-mode-start').val($.datepicker.formatDate('yy-mm-dd', new Date({$timeMin}*1000)));
        $('#stat-mode-end').val($.datepicker.formatDate('yy-mm-dd', new Date({$timeMax}*1000)));
    });
EOL;
$js[] = '</script>';
$javascripts = implode(PHP_EOL, $js);

?>

<style>
  div.graph { height: 300px; margin-top: .5em; }
  article.graph { padding: .5em; margin-bottom: 1em; background-color: rgba(200, 200, 255, .2); border-radius: 1em; }
  article.graph h1 { line-height: 1em; font-size: 1.5em; text-align: center; padding-bottom: .5em; border-bottom: 2px solid white; }
  #stat-mode {
    padding: 1em;
    margin-bottom: 1em;
    border-radius: 5px;
    text-align: center;
    background-color: rgba(0, 0, 0, .05);
  }

  #stat-mode input {
    width: 120px;
    font-size: 10px;
    padding: .25em;
    text-align: center;
  }

  #stat-mode input:first-child { margin-top: -.35em; float: left; }
  #stat-mode input:last-child { margin-top: -.35em; float: right; }
  #stat-mode a { margin: 0 .5em; padding: .25em .5em; background-color: white; border-radius: 5px; }
</style>