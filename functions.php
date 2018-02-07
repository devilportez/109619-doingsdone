<?php
function set_template ($template, $data) {
    if (file_exists($template)) {
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        ob_start();
        require_once($template);
        return ob_get_clean();
    }
    return "";
}
?>
