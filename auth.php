<?php
include_once 'bd_connection.php';

if (!empty($_GET['action']) && $_GET['action'] === 'logout')
{
    OnUserLogout();
    exit();
}

if (empty($_POST['action']) || empty($_POST['login']) || empty($_POST['password']))
{
    header('HTTP/1.0 403 Forbidden');
    exit();
}

$login = $_POST['login'];
$password = $_POST['password'];

switch ($_POST['action'])
{
    case 'register':
        if (empty($_POST['nickname']))
        {
            break;
        }

        $nickname = $_POST['nickname'];
        $avatar = null;

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0)
        {
            $upload_path = '/user_avatar/';

            if (!is_dir(__DIR__ . $upload_path))
            {
                mkdir(__DIR__ . $upload_path);
            }

            $arr = explode('.', $_FILES['avatar']['name']);
            $extension = end($arr);
            $upload_path = $upload_path . basename($_FILES['avatar']['tmp_name']) . '.' . $extension;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . $upload_path))
            {
                $avatar = $upload_path;
            }
        }

        $query = 'INSERT INTO User (Login, Password, Nickname, Avatar) VALUES (?,?,?,?)';
        $stmt = $db->prepare($query);
        $stmt->bind_param('ssss', $login, $password, $nickname, $avatar);

        if (!$stmt->execute())
        {
            echo 'Ошибка на сервере: '.$stmt->error;
            break;
        }

        OnUserRegister();
        break;
    case 'login':
        $query = 'SELECT * FROM User WHERE Login = ? AND Password = ?';
        $stmt = $db->prepare($query);
        $stmt->bind_param('ss', $login, $password);

        if (!$stmt->execute())
        {
            echo 'Ошибка на сервере: '.$stmt->error;
            break;
        }

        $result = $stmt->get_result();

        if (!$result)
        {
            echo 'Ошибка при считывании результата';
            break;
        }

        $row = $result->fetch_assoc();

        if (is_null($row))
        {
            echo 'Не найдена запись из БД';
            break;
        }

        OnUserLogin($row['Id'], $row['Nickname'], $row['Grade']);
        break;
    default:
        break;
}

/**
 * Вызывается при успешной авторизации пользователя
 * @param string $id Идентификатор пользователя
 * @param string $nickname Никнейм пользователя
 * @param string $grade Разрешения пользователя
 * @return void
 */
function OnUserLogin($id, $nickname, $grade)
{
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'posts.php';
    session_start();
    $_SESSION['user-id'] = $id;
    $_SESSION['user-nickname'] = $nickname;
    $_SESSION['user-grade'] = $grade;
    header("Location: https://$host$uri/$extra");
    exit();
}

/**
 * Вызывается при успешной регистрации пользователя
 * @return void
 */
function OnUserRegister()
{
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php?action=login';
    header("Location: https://$host$uri/$extra");
    exit();
}

/**
 * Вызывается при попытке выхода из системы пользователя
 * @return void
 */
function OnUserLogout()
{
    session_start();
    session_destroy();
    unset($_SESSION);

    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = '';
    header("Location: https://$host$uri/$extra");
    exit();
}
?>
