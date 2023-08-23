<?php
    session_start();

    include_once 'bd_connection.php';

    if (!isset($_GET['id']))
    {
        echo 'Нет доступа!';
        exit();
    }

    $postId = $_GET['id'];

    $query = 'SELECT Post.Id, Title, Description, Content, User.Nickname AS Author, Date, Views, Likes, Reposts, Favours FROM Post LEFT JOIN User ON Author = User.Id LEFT JOIN PostStatistics ON Post.Id = PostStatistics.Post WHERE Post.Id = ?';
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $postId);

    if (!$stmt->execute())
    {
        echo 'Ошибка выполнения запроса к БД: '.$stmt->error;
        exit();
    }

    $result = $stmt->get_result();

    if (!$result)
    {
        echo 'Ошибка получения результата запроса.';
        exit();
    }

    $row = $result->fetch_assoc();

    if (is_null($row))
    {
        header('HTTP/1.0 404 Not Found');
        exit();
    }

/**
 * Обновляем счетчик просмотров
 */
    $query2 = 'UPDATE PostStatistics SET Views = ? WHERE Post = ?';
    $stmt = $db->prepare($query2);
    $updatedViews = $row['Views'] + 1;
    $stmt->bind_param('ss', $updatedViews, $row['Id']);
    $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <title><?php echo $row['Title'];?></title>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
<?php include 'header.php';?>
<div class="container my-5 col-12 row mx-auto justify-content-between">
    <div class="d-grid gap-4 col-md-7 py-3 align-self-start bg-light border">
        <?php
            $authorOrAdminFlag = (!empty($_SESSION['user-grade']) && $_SESSION['user-grade'] === 'administrator')
                                 || (!empty($_SESSION['user-nickname']) && $_SESSION['user-nickname'] === $row['Author']);

            if ($authorOrAdminFlag && !empty($_GET['mode']) && strtolower($_GET['mode']) === 'edit') :
        ?>
            <script>
                document.addEventListener('DOMContentLoaded', () =>
                {
                    let textarea = document.querySelector('.card-text textarea');
                    textarea.style.height = textarea.scrollHeight + 'px';
                });
            </script>
            <div class="card">
                <form action="post-action.php?act=edit&id=<?php echo $row['Id'];?>" method="post" class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-2 gap-2">
                        <span class="text-muted">id: <?php echo $row['Id'];?></span>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                    <input type="text" class="form-control mb-3" placeholder="Название" name="title" required value="<?php echo $row['Title'];?>">
                    <input type="text" class="form-control mb-3" placeholder="Описание" name="description" required value="<?php echo $row['Description'];?>">
                    <div class="card-text">
                        <textarea class="form-control" placeholder="" required name="content"><?php echo $row['Content'];?></textarea>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-2 gap-2">
                        <span class="text-muted">id: <?php echo $row['Id'];?></span>
                        <?php
                            if ((!empty($_SESSION['user-grade']) && $_SESSION['user-grade'] === 'administrator')
                            || (!empty($_SESSION['user-nickname']) && $_SESSION['user-nickname'] === $row['Author'])):
                        ?>
                        <a class="btn btn-outline-dark" href="viewpost.php?id=<?php echo $row['Id'];?>&mode=edit">Редактировать</a>
                        <button type="button" class="btn btn-danger">Удалить</button>
                        <?php
                            endif;
                        ?>
                    </div>
                    <h5 class="card-title"><?php echo $row['Title'];?></h5>
                    <p class="card-text">
                        <small class="text-muted">Изменено: <?php echo $row['Date'];?>, Автор: <?php echo $row['Author'];?></small>
                    </p>
                    <div class="card-text"><?php echo $row['Content'];?></div>
                </div>
                <div class="card-footer d-flex flex-column flex-md-row gap-3">
                    <small class="text-muted">Просмотры: <?php echo $row['Views'];?></small>
                    <small class="text-muted">Нравится: <?php echo $row['Likes'];?></small>
                    <small class="text-muted">Поделились: <?php echo $row['Reposts'];?></small>
                    <small class="text-muted">Избранное: <?php echo $row['Favours'];?></small>
                </div>
            </div>
        <?php endif;?>

        <!-- Блок комментариев !-->
        <div class="d-grid gap-1">
            <?php
                $query = 'SELECT Comment.*, User.Nickname FROM Comment LEFT JOIN User ON Comment.Author = User.Id WHERE Comment.Post = ? ORDER BY Date';
                $stmt = $db->prepare($query);
                $stmt->bind_param('s', $_GET['id']);

                if (!$stmt->execute())
                {
                    echo 'Ошибка выполнения запроса к БД: '.$stmt->error;
                    exit();
                }

                $comment_result = $stmt->get_result();

                if (!$comment_result)
                {
                    echo 'Ошибка получения результата запроса.';
                    exit();
                }

                if ($comment_result->num_rows == 0):
            ?>
            <div class="card">
                <div class="card-body">
                    <p class="text-center m-0">Комментариев пока нет!</p>
                </div>
            </div>
            <?php
                endif;
                while ($comment_row = $comment_result->fetch_assoc()):
            ?>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-center mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                        <div class="d-grid">
                            <?php
                                $query = 'SELECT Grade FROM User WHERE Nickname = ?';
                                $stmt = $db->prepare($query);
                                $stmt->bind_param('s', $comment_row['Nickname']);
                                $stmt->execute();
                                $adminResult = $stmt->get_result();
                                $adminRow = $adminResult->fetch_assoc();

                                if (!empty($adminRow['Grade'])):
                            ?>
                                <div class="d-flex flex-row gap-3 align-items-center">
                                    <span><?php echo $comment_row['Nickname'];?></span>
                                    <p class="bg-danger rounded-2 text-white px-2 m-0">Администратор</p>
                                </div>
                            <?php
                                else:
                            ?>
                            <span><?php echo $comment_row['Nickname'];?></span>
                            <?php
                                endif;
                            ?>
                            <small class="text-muted"><?php echo $comment_row['Date'];?></small>
                        </div>
                    </div>
                    <div class="card-text"><?php echo $comment_row['Text'];?></div>
                </div>
            </div>
            <!-- Форма комментария !-->
            <?php
                endwhile;
                if (!empty($_SESSION['user-id'])):
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', () =>
                {
                    let ta = document.querySelector('#comment-text-id');
                    let btn = document.querySelector('#comment-send-btn');
                    ta.addEventListener('input', () =>
                    {
                        ta.style.height = '42px';
                        ta.style.height = ta.scrollHeight + 'px';

                        if (ta.value.length === 0)
                        {
                            btn.classList.add('disabled');
                        }
                        else
                        {
                            btn.classList.remove('disabled');
                        }
                    });
                });
            </script>
            <div class="card">
                <form action="post-action.php?act=comment" method="post" class="card-body d-flex justify-content-between">
                    <input type="hidden" name="post" value="<?php echo $row['Id'];?>">
                    <input type="hidden" name="author-id" value="<?php echo $_SESSION['user-id'];?>">
                    <textarea name="comment-text" id="comment-text-id" class="form-control me-2" placeholder="Введите комментарий"></textarea>
                    <button class="btn disabled align-self-start" id="comment-send-btn" title="Отправить" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="currentColor" class="bi bi-arrow-right-square-fill" viewBox="0 0 16 16">
                            <path d="M0 14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v12zm4.5-6.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5a.5.5 0 0 1 0-1z"/>
                        </svg>
                    </button>
                </form>
            </div>
            <?php
                endif;
            ?>
        </div>
    </div>
    <?php include 'side-panel.php';?>
</div>
<?php include 'html/footer.html';?>
</body>
</html>