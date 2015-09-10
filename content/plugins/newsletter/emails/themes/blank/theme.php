<html>
    <head>
        <style>
            body {
                font-family: sans-serif;
                font-size: 12px;
                background-color: #ffffff;
            }
        </style>
    </head>
    <body bgcolor="#ffffff">
        <h1>Your awesome title</h1>

        <p>
            Hi {name} (but you should remove that if you're not collecting subscriber names),
        </p>

        <p>
            here the forewords of your shiny new newsletter. Most of the times a simple layout is the best.
        </p>
        <h2>There is more for you!</h2>

        <p>Still not secure to sign up for our premium service? Even with a standard service plan you'll receive our t-shirt!</p>

        <p>
            Goodbye!
        </p>

        <?php include WP_PLUGIN_DIR . '/newsletter/emails/themes/default/social_main.php'; ?>

        <hr>
        <p>
            To unsubscribe <a href="{unsubscription_url}">click here</a>, to edit your subscription
            <a href="{profile_url}">click here</a>.
        </p>
        <p>
            <?php echo $theme_options['main_footer_contact'] ?>
        </p>
        <p>
            <?php echo $theme_options['main_footer_legal'] ?>
        </p>
    </body>
</html>