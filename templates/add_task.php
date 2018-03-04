<div class="modal">
    <a href="/">
        <button class="modal__close" type="button" name="button">Закрыть</button>
    </a>
    <h2 class="modal__heading">Добавление задачи</h2>
    <form class="form" method="post" action="index.php" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>
            <input
                class="form__input <?= (isset($errors["name"])) ? "form__input--error" : "" ?>"
                type="text"
                name="name"
                id="name"
                value="<?= (isset($_POST["name"])) ? $_POST["name"] : ""; ?>"
                placeholder="Введите название"
            >
            <?php if (isset($errors["name"])): ?>
                <p class="form__message">
                    <?= $errors["name"]; ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>
            <select
                class="
                    form__input
                    form__input--select
                    <?= (isset($errors["project"])) ? "form__input--error" : "" ?>
                "
                name="project"
                id="project"
            >
                <option value="">---</option>
                <?php foreach ($projects as $project): ?>
                    <option
                        value="<?= (isset($project["name"])) ? $project["name"] : ""; ?>"
                        <?php if (isset($_POST["project"])): ?>
                            <?= ($_POST["project"] === $project["name"]) ? "selected" : ""; ?>
                        <?php endif; ?>
                    >
                        <?= (isset($project["name"])) ? $project["name"] : ""; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors["project"])): ?>
                <p class="form__message">
                    <?= $errors["project"]; ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>
            <input
                class="form__input form__input--date"
                type="date"
                name="date"
                id="date"
                value="<?= (isset($_POST["date"])) ? $_POST["date"] : ""; ?>"
                placeholder="Введите дату в формате ДД.ММ.ГГГГ"
            >
        </div>
        <div class="form__row">
            <label class="form__label" for="preview">Файл</label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">
                <label class="button button--transparent" for="preview">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>
        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="add_task" value="Добавить">
        </div>
    </form>
</div>
