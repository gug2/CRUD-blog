<?php
    session_start();

    if (empty($_SESSION['user-id']))
    {
        header('HTTP/1.0 403 Forbidden');
        exit();
    }
?>
<div class="list-group p-2 border me-0 d-none d-md-block col-md-4">
    <div class="list-group-item bg-light">
        <h6 class="text-center mb-0">Новейшие публикации</h6>
    </div>
    <?php
        include_once 'bd_connection.php';

        $query = 'SELECT Post.Id, Title, User.Nickname AS Author, Date FROM Post LEFT JOIN User ON Author = User.Id ORDER BY Date DESC LIMIT 5';
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) :
    ?>
    <div class="list-group-item">
        <a href="viewpost.php?id=<?php echo $row['Id'];?>" class="mb-2"
        style="color:inherit;text-decoration:underline;">
            <?php echo $row['Title'];?>
        </a>
        <div class="d-flex justify-content-between gap-3 text-muted">
            <small><?php echo $row['Author'];?></small>
            <small><?php echo $row['Date'];?></small>
        </div>
    </div>
    <?php
        endwhile;

        if (!empty($_SESSION['user-grade']) && $_SESSION['user-grade'] === 'administrator') :
    ?>
    <div class="container py-2 sticky-top bg-dark text-white" id="admin-panel">
        <p>Админ панелька</p>
        <div class="d-grid gap-3">
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#create-post-modal-id">Добавить пост</button>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete-all-posts-modal-id">Удалить все</button>
        </div>
    </div>

    <div class="modal fade" id="delete-all-posts-modal-id" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="post-action.php?act=delete-all" method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Удаление</h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p>Вы уверены, что хотите удалить все посты?</p>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </div>
            </form>
        </div>
    </div>
    <?php
        elseif (empty($_SESSION['user-grade'])):
    ?>
    <div class="list-group-item">
        <button class="btn btn-warning col-12" data-bs-toggle="modal" data-bs-target="#create-post-modal-id">Добавить пост</button>
    </div>
    <?php
        endif;
    ?>
    <div class="modal fade" id="create-post-modal-id" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="post-action.php?act=create" method="post" class="modal-content">
                <input type="hidden" name="author-id" value="<?php echo $_SESSION['user-id'];?>">
                <div class="modal-header">
                    <h5 class="modal-title">Добавление</h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" name="title" placeholder="Название" required>
                    <input type="text" class="form-control mb-3" name="description" placeholder="Описание" required>
                    <div class="form-floating">
                        <textarea class="form-control" name="content" placeholder="" required></textarea>
                        <label>Начните писать здесь..</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Опубликовать</button>
                </div>
            </form>
        </div>
    </div>
</div>