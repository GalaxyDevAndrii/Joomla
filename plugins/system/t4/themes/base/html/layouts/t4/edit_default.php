<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{t4:language}" lang="{t4:language}" dir="{t4:direction}">

<head>
  {t4:system_advancedCodeAfterHead}
  {t4post:head}

  <!--[if lt IE 9]>
    <script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
  <![endif]-->
  <meta name="viewport"  content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes"/>
  <style  type="text/css">
    @-webkit-viewport   { width: device-width; }
    @-moz-viewport      { width: device-width; }
    @-ms-viewport       { width: device-width; }
    @-o-viewport        { width: device-width; }
    @viewport           { width: device-width; }
  </style>
  <meta name="HandheldFriendly" content="true"/>
  <meta name="apple-mobile-web-app-capable" content="YES"/>
  <!-- //META FOR IOS & HANDHELD -->
  {t4:system_advancedCodeBeforeHead}
</head>

<body class="{t4post:bodyclass} t4-edit-layout">
  {t4:system_advancedCodeAfterBody}
  {t4:offcanvas}
  <div class="t4-wrapper">
    <div class="t4-content">
      <div class="t4-content-inner">
        {t4:body}
      </div>
    </div>
  </div>
  {t4:system_advancedCodeBeforeBody}
</body>
</html>
