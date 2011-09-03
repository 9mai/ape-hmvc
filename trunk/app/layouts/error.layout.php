<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<title>Error <?php echo $type_msg; ?></title>
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
        <?php echo $content; ?>
      </div>
  </div>
  
</body>
</html>