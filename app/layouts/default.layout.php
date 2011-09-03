<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<title>APE HMVC Framework</title>
	<style type="text/css">
		html,body { margin:0; padding:0; background:#444;font:normal 1em Arial, Helvetica, sans-serif;}
        div#container { margin: 40px auto 0 40px; width: 750px; }
        #container-inner { background:#fff; padding:15px;-moz-border-radius: 10px; -webkit-border-radius: 10px; }
        #test-content { border:2px solid #ddd; padding:10px; }
        h2, h3 { font-size:1em; }
        #test-content p { background:#efefef; padding:2px;}
        hr { border:1px solid #444; background:#444; height:1px; }
	</style>
</head>
<body>
  
  <div id="container">
      <div id="container-inner">
          <h1>This is the default layout.</h1>
          <p>You can have as many layouts as you want. Think of them as view containers. See the docs for more examples.</p>
          <div id="test-content">
              <h2>View Data:</h2>
              <?php echo $view; ?>
              <em>From: /app/modules/home/views/default.view.php</em>
              <hr />
              <h2>Controller "Simple Assignment" data:</h2>
              <p><?php echo $content; ?></p>
              <em>From: /app/modules/home/controllers/default.controller.php</em>
          </div>
          
          <p><em>/app/layouts/default.layout.php</em></p>
      </div>
  </div>
  
</body>
</html>