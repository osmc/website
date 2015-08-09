<?php
/**
 * View Handling.
 *
 * @author   Johan Steen <artstorm at gmail dot com>
 * @link     http://johansteen.se/
 */
class PostSnippets_View
{
    /**
     * Render a View.
     *
     * @param  string  $view
     * @param  array   $data
     * @return string
     */
    public static function render($view, $data = null)
    {
        // Handle data
        ($data) ? extract($data) : null;

        ob_start();
        include(plugin_dir_path(__FILE__).'../../views/'.$view.'.php');
        $view = ob_get_contents();
        ob_end_clean();

        return $view;
    }
}
