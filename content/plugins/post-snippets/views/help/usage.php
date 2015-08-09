<h2><?php _e('Usage', $td); ?></h2>


<h3><?php _e('Title', $td); ?></h3>
<p>
<?php _e('Give the snippet a title that helps you identify it in the post editor. This also becomes the name of the shortcode if you enable that option.', $td); ?>
</p>


<h3><?php _e('Variables', $td); ?></h3>
<p>
<?php _e('A comma separated list of custom variables you can reference in your snippet. A variable can also be assigned a default value that will be used in the insert window by using the equal sign, variable=default.', $td); ?>
</p>

<p>
    <strong><?php _e('Example', $td); ?></strong>
    <pre><code>url,name,role=user,title</code></pre>
</p>


<h3><?php _e('Snippet', $td); ?></h3>
<p>
<?php _e('This is the block of text, HTML or PHP to insert in the post or as a shortcode. If you have entered predefined variables you can reference them from the snippet by enclosing them in {} brackets.', $td); ?>
</p>

<p><strong><?php _e('Example', $td); ?></strong></p>
<p><?php _e('To reference the variables in the example above, you would enter {url} and {name}. So if you enter this snippet:', $td); ?></p>
<p><?php _e('So if you enter this snippet:', $td); ?></p>

<pre><code>This is the website of &lt;a href="{url}"&gt;{name}&lt;/a&gt;</code></pre>

<p><?php _e('You will get the option to replace url and name on insert if they are defined as variables.', $td); ?></p>


<h3><?php _e('Description', $td); ?></h3>
<p>
<?php _e('An optional description for the Snippet. If filled out, the description will be displayed in the snippets insert window in the post editor.', $td); ?>
</p>


<h3><?php _e('Shortcode', $td); ?></h3>
<p>
<?php _e('When enabling the shortcode checkbox, the snippet is no longer inserted directly but instead inserted as a shortcode. The obvious advantage of this is of course that you can insert a block of text or code in many places on the site, and update the content from one single place.', $td); ?>
</p>

<p>
<?php _e('The name to use the shortcode is the same as the title of the snippet (spaces are not allowed). When inserting a shortcode snippet, the shortcode and not the content will be inserted in the post.', $td); ?>
</p>

<p>
<?php _e('If you enclose the shortcode in your posts, you can access the enclosed content by using the variable {content} in your snippet. The {content} variable is reserved, so don\'t use it in the variables field.', $td); ?>
</p>

<h3><?php _e('PHP Code', $td); ?></h3>
<p>
<?php _e('See the dedicated help section for information about PHP shortcodes.', $td); ?>
</p>

<h3>wptexturize</h3>
<p>
<?php printf(__('Before the shortcode is outputted, it can optionally be formatted with %s, to transform quotes to smart quotes, apostrophes, dashes, ellipses, the trademark symbol, and the multiplication symbol.', $td), '<a href="http://codex.wordpress.org/Function_Reference/wptexturize">wptexturize</a>'); ?>
</p>
