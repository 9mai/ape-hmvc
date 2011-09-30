<?php
header("Expires: Sat, 1 Jan 2000 00:00:01 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: private, no-cache, no-store, must-revalidate, proxy-revalidate, max-age=0, s-maxage=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if($headers = Registry::get('_SITE_HEADERS')){
	foreach($headers as $header){
		header($header);
	}
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo Registry::get('_SITE_TITLE', Loader::loadConfig('core', 'site_name')); ?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="<?php echo Loader::loadConfig('core', 'base_url'); ?>" />

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
      }
    </style>

    <?php
        if($js_external = Registry::get('_SITE_JS_EXTERNAL')){
            foreach($js_external as $js){
                echo '<script src="'.$js.'" type="text/javascript"></script>'."\n";
            }
        }
        if($js_inline = Registry::get('_SITE_JS_INLINE')){
            echo '<script type="text/javascript">'."\n";
            foreach($js_inline as $js){
                echo $js."\n";
            }
            echo '</script>'."\n";
        }
        if($scripts = Registry::get('_SITE_JS')){
            foreach($scripts as $js){
                echo '<script src="js/'.$js.'?'.$cache_key.'" type="text/javascript"></script>'."\n";
            }
        }
        if($styles = Registry::get('_SITE_CSS')){
            foreach($styles as $css){
                echo '<link href="css/'.$css.'?'.$cache_key.'" rel="stylesheet" type="text/css" />'."\n";
            }
        }
        unset($headers, $js_external, $js_inline, $scripts, $styles);
    ?>
    
    <link rel="shortcut icon" href="images/favicon.ico">
    <!-- Le fav and touch icons
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
    -->
  </head>

  <body>

    <div class="topbar">
      <div class="fill">
        <div class="container">
          <a class="brand" href="#">Project name</a>
          <ul class="nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>This is the default layout.</h1>
          <p>You can have as many layouts as you want. Think of them as view containers. See the docs for more examples.</p>
          <div id="test-content">
              <h2>View Data:</h2>
              <?php echo $view; ?>
              <em>From: /app/modules/home/views/default.view.php</em>
              <hr />
              <h2>Controller "Simple Assignment" data:</h2>
              <p><?php echo $content; /* Alternatively Registry::get('main_content') */ ?></p>
              <em>From: /app/modules/home/controllers/default.controller.php</em>
          </div>
          
          <p><em>/app/layouts/default.layout.php</em></p>
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span6">
          <h2>Heading</h2>
          <p>Etiam porta sem malesuada magna mollis euismod. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
        <div class="span5">
          <h2>Heading</h2>
           <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
       </div>
        <div class="span5">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
      </div>

      <footer>
        <p>&copy; Company 2011 <!--debug--></p>
      </footer>

    </div> <!-- /container -->

  </body>
</html>