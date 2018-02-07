<?php
function set_template ($template, $data) {
    if (file_exists($template)) {
        extract($data);
        ob_start();
        require_once($template);
        return ob_get_clean();
    }
    return "";
}
?>
