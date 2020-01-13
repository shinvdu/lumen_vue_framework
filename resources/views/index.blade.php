<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="format-detection" content="telephone=no, email=no"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />
    <title>Lumen Vue Framework</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <!-- weui引入样式 -->
    <link rel="stylesheet" href="./css/theme_default/weui.min.css" />
    <link rel="stylesheet" href="./js/styles.css?ver=<?php echo $md5['css_md5']?>" />
</head>
<body oncontextmenu="return false;" onselectstart="return false">
<div id="app"></div>
<script type="text/javascript">
</script>
<script src="./jweixin-1.2.0.js?ver=<?php echo $md5['jweixin_md5']?>"></script>
<script src="./js/client-vendor-bundle.js?ver=<?php echo $md5['vendor_bundle_md5'] ?>"></script>
<script src="./gz_resource/client-bundle.js?ver=<?php echo $md5['client_bundle_md5'] ?>"></script>
</body>
</html>