<?php
    session_start();

    if (empty($_SESSION['user-nickname']))
    {
        header('HTTP/1.0 403 Forbidden');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <title>CRUD - Посты</title>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
<script src="js/posts.js"></script>
<?php include 'header.php';?>
<div class="container my-5 col-12 row mx-auto justify-content-between">
    <div class="d-grid gap-4 col-md-7 py-3 bg-light border">
        <?php
            include_once 'bd_connection.php';

            $query = 'SELECT Post.Id, Title, Description, Preview, User.Nickname AS Author, Date, Views, Likes, Reposts, Favours FROM Post LEFT JOIN User ON Author = User.Id LEFT JOIN PostStatistics ON Post.Id = PostStatistics.Post ORDER BY Date DESC';
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) :
        ?>
            <h5 class="text-muted m-auto">Пока ничего нет!</h5>
        <?php
            endif;
            while ($row = $result->fetch_assoc()) :
        ?>
        <div class="card" id="<?php echo $row['Id'];?>">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-2 gap-2">
                    <span class="text-muted">id: <?php echo $row['Id'];?></span>
                    <?php
                        if ((!empty($_SESSION['user-grade']) && $_SESSION['user-grade'] === 'administrator')
                        || (!empty($_SESSION['user-nickname']) && $_SESSION['user-nickname'] === $row['Author'])):
                    ?>
                    <a class="btn btn-outline-dark" href="viewpost.php?id=<?php echo $row['Id'];?>&mode=edit">Редактировать</a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete-post-modal-id" data-bs-post-id="<?php echo $row['Id'];?>">Удалить</button>
                    <?php
                        endif;
                    ?>
                </div>
                <a href="viewpost.php?id=<?php echo $row['Id'];?>">
                    <h5 class="card-title"><?php echo $row['Title'];?></h5>
                </a>
                <p class="card-text"><?php echo $row['Description'];?></p>
                <p class="card-text">
                    <small class="text-muted">Изменено: <?php echo $row['Date'];?>, Автор: <?php echo $row['Author'];?></small>
                </p>
            </div>
            <img src="<?php echo $row['Preview'];?>" class="card-img-bottom ratio ratio-16x9 bg-secondary" alt="card-img-bottom">
            <div class="card-footer d-flex flex-column flex-md-row gap-3">
                <small class="text-muted">Просмотры: <?php echo $row['Views'];?></small>
                <small class="text-muted">Нравится: <?php echo $row['Likes'];?></small>
                <small class="text-muted">Поделились: <?php echo $row['Reposts'];?></small>
                <small class="text-muted">Избранное: <?php echo $row['Favours'];?></small>
            </div>
        </div>
        <?php
            endwhile;
        ?>
    </div>
    <?php include 'side-panel.php';?>
</div>

<div class="modal fade" id="delete-post-modal-id" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Удаление</h5>
                <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены что хотите удалить пост?</p>
            </div>
            <div class="modal-footer">
                <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-danger">Удалить</button>
            </div>
        </form>
    </div>
</div>

<?php include 'html/footer.html';?>
</body>
</html>