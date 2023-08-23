<?php
    include_once "bd_connection.php";

/**
 * Отправляет запрос на создание поста
 * @return bool
 */
function create($database)
{
    if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['content']) || empty($_POST['author-id']))
    {
        return false;
    }

    $authorId = $_POST['author-id'];
    $date = date('Y-m-d H:i:s', time());

    $query = 'INSERT INTO Post (Title, Description, Content, Author, Date) VALUES (?,?,?,?,?)';
    $stmt = $database->prepare($query);
    $stmt->bind_param('sssss', $_POST['title'], $_POST['description'], $_POST['content'], $authorId, $date);

    return $stmt->execute() === true;
}

/**
 * Отправляет запрос на обновление поста
 * @return bool
 */
function edit($database)
{
    if (empty($_GET['id']))
    {
        return false;
    }

    if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['content']))
    {
        return false;
    }

    $editableId = $_GET['id'];
    $date = date('Y-m-d H:i:s', time());

    $query = 'UPDATE Post SET Title = ?, Description = ?, Content = ?, Date = ? WHERE Id = ?';
    $stmt = $database->prepare($query);
    $stmt->bind_param('sssss', $_POST['title'], $_POST['description'], $_POST['content'], $date, $editableId);

    return $stmt->execute() === true;
}

/**
 * Отправляет запрос на удаление поста
 * @return bool
 */
function delete($database)
{
    if (empty($_GET['id']))
    {
        return false;
    }

    $deletableId = $_GET['id'];

    $query = 'DELETE FROM Post WHERE Id = ?';
    $stmt = $database->prepare($query);
    $stmt->bind_param('s', $deletableId);

    return $stmt->execute() === true;
}

/**
 * Отправляет запрос на удаление всех постов
 * @return bool
 */
function delete_all($database)
{
    $query = 'DELETE FROM Post';
    $stmt = $database->prepare($query);

    return $stmt->execute() === true;
}

function comment($database)
{
    if (empty($_POST['post']) || empty($_POST['author-id']) || empty($_POST['comment-text']))
    {
        return false;
    }

    $date = date('Y-m-d H:i:s', time());
    $query = 'INSERT INTO Comment (Post, Author, Text, Date) VALUES (?,?,?,?)';
    $stmt = $database->prepare($query);
    $stmt->bind_param('ssss', $_POST['post'], $_POST['author-id'], $_POST['comment-text'], $date);

    return $stmt->execute() === true;
}

    if (empty($_GET['act']))
    {
        header('HTTP/1.0 403 Forbidden');
        exit();
    }

    $status = false;

    switch ($_GET['act'])
    {
        case 'create': $status = create($db); break;
        case 'edit': $status = edit($db); break;
        case 'delete': $status = delete($db); break;
        case 'delete-all': $status = delete_all($db); break;
        case 'comment': $status = comment($db); break;
        default: break;
    }

    if (!$status)
    {
        echo 'Ошибка выполнения действия на сервере!';
        exit();
    }

    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'posts.php';
    header("Location: https://$host$uri/$extra");
    exit();
?>