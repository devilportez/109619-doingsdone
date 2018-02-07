<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="/" class="tasks-switch__item">Повестка дня</a>
        <a href="/" class="tasks-switch__item">Завтра</a>
        <a href="/" class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <a href="/">
            <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
            <input
                class="checkbox__input visually-hidden"
                type="checkbox"
                <?= ($show_complete_tasks === 1) ? "checked" : ""; ?>
            >
            <span class="checkbox__text">Показывать выполненные</span>
        </a>
    </label>
</div>

<table class="tasks">
    <?php foreach ($tasks as $task): ?>
        <tr
            class="
                tasks__item task
                <?= ($task["is_completed"]) ? "task--completed" : ""; ?>
            "
        >
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input
                        class="checkbox__input visually-hidden"
                        type="checkbox"
                        <?= ($task["is_completed"]) ? "checked" : ""; ?>
                    >
                    <span class="checkbox__text">
                        <?= htmlspecialchars($task["task"]); ?>
                    </span>
                </label>
            </td>
            <td class="task__date"><?= $task["date"]; ?></td>
            <td class="task__controls"></td>
        </tr>
    <?php endforeach; ?>
</table>