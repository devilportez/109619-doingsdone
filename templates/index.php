<h2 class="content__main-heading">Список задач</h2>
<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">
    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>
<div class="tasks-controls">
    <nav class="tasks-switch">
        <a
            href="<?= "?filter=all"; ?>"
            class="
                tasks-switch__item
                <?php if (isset($_COOKIE["filter"])): ?>
                    <?= ($_COOKIE["filter"] === "all") ? "tasks-switch__item--active" : ""; ?>
                <?php endif; ?>
            "
        >
            Все задачи
        </a>
        <a
            href="<?= "?filter=today"; ?>"
            class="
                tasks-switch__item
                <?php if (isset($_COOKIE["filter"])): ?>
                    <?= ($_COOKIE["filter"] === "today") ? "tasks-switch__item--active" : ""; ?>
                <?php endif; ?>
            "
        >
            Повестка дня
        </a>
        <a
            href="<?= "?filter=tomorrow"; ?>"
            class="
                tasks-switch__item
                <?php if (isset($_COOKIE["filter"])): ?>
                    <?= ($_COOKIE["filter"] === "tomorrow") ? "tasks-switch__item--active" : ""; ?>
                <?php endif; ?>
            "
        >
            Завтра
        </a>
        <a
            href="<?= "?filter=overdue"; ?>"
            class="
                tasks-switch__item
                <?php if (isset($_COOKIE["filter"])): ?>
                    <?= ($_COOKIE["filter"] === "overdue") ? "tasks-switch__item--active" : ""; ?>
                <?php endif; ?>
            "
        >
            Просроченные
        </a>
    </nav>
    <label class="checkbox">
        <a href="<?= "?show_completed" ?>">
            <input
                class="checkbox__input visually-hidden"
                type="checkbox"
                <?= ($show_complete_tasks) ? "checked" : ""; ?>
            >
            <span class="checkbox__text">Показывать выполненные</span>
        </a>
    </label>
</div>
<table class="tasks">
    <?php foreach ($project_tasks as $task): ?>
        <tr
            class="
                tasks__item task
                <?= ($task["done_date"]) ? "task--completed" : ""; ?>
                <?php if (!isset($task["done_date"])): ?>
                    <?= get_urgent_task($task["deadline"]) ? "task--important" : ""; ?>
                <?php endif; ?>
            "
        >
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <a href="<?= "?toggle_done=" . $task["id"]; ?>">
                        <input
                            class="checkbox__input visually-hidden"
                            type="checkbox"
                            <?= ($task["done_date"]) ? "checked" : ""; ?>
                        >
                        <span class="checkbox__text">
                            <?= htmlspecialchars($task["name"]); ?>
                        </span>
                    </a>
                </label>
            </td>
            <td class="task__file">
                <?php if (!empty($task["file"])): ?>
                    <a class="download-link" href="<?= $task["file"]; ?>">
                        <?= pathinfo($task["file"], PATHINFO_BASENAME); ?>
                    </a>
                <?php endif; ?>
            </td>
            <td class="task__date">
                <?= ($task["deadline"]) ? date("d.m.y", strtotime(htmlspecialchars($task["deadline"]))) : ""; ?>
            </td>
            <td class="task__controls"></td>
        </tr>
    <?php endforeach; ?>
</table>
