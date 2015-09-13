<?php
// This is an alternative subscription message showing page. Variables are already initialized
// on the original page which includes this one.
?>
<html>
    <head>
        <style type="text/css">
            body {
                font-family: verdana;
                background-color: #ddd;
                font-size: 12px;
            }
            #container {
                border: 1px solid #aaa;
                border-radius: 5px;
                background-color: #fff;
                margin: 40px auto;
                width: 600px;
                padding: 20px
            }
            h1 {
                font-size: 24px;
                font-weight: normal;
                border-bottom: 1px solid #aaa;
                margin-top: 0;
            }
            h2 {
                font-size: 20px;
            }
            th, td {
                font-size: 12px;
            }
            th {
                padding-right: 10px;
                text-align: right;
                vertical-align: middle;
                font-weight: normal;
            }
        </style>
    </head>

    <body>
        <?php if (!empty($alert)) { ?>
        <script>
            alert("<?php echo addslashes($alert); ?>");
        </script>
        <?php } ?>
        
        <div id="container">
            <h1><?php echo get_option('blogname'); ?></h1>
            <?php echo $message; ?>
        </div>
    </body>
</html>