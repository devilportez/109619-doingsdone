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

function get_tasks_amount ($tasks, $project) {
    $count = 0;
    foreach ($tasks as $task) {
        if ($project === "Все") {
            $count = count($tasks);
        }
        if ($task["category"] === $project) {
            $count++;
        }
    }
    return $count;
}

function get_urgent_task ($date) {
    $current_timestamp = time();
    $task_timestamp = strtotime($date);
    $seconds_in_day = 86400;
    $difference = floor(($task_timestamp - $current_timestamp) / $seconds_in_day);
    if ($difference < 1) {
        return true;
    }
    return false;
}

function filter_tasks ($tasks, $project, $show_complete_tasks) {
    $filtered_tasks = [];
    if ($project === "Все") {
        $filtered_tasks = $tasks;
    }
    foreach ($tasks as $key => $task) {
        if ($show_complete_tasks) {
            if ($project === $task["category"]) {
                $filtered_tasks[] = $tasks[$key];
            }
        } else {
            if ($project === "Все" && !$task["is_completed"]) {
                $filtered_tasks[] = $tasks[$key];
            }
            if ($project === $task["category"] && !$task["is_completed"]) {
                $filtered_tasks[] = $tasks[$key];
            }
        }
    }
    return $filtered_tasks;
}
?>
