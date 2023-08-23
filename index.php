<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <title>CRUD блог</title>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
<?php include 'header.php';?>
<?php
    if (!empty($_GET['action']) && strtolower($_GET['action']) === 'register'):
?>
<div class="container">
    <form action="auth.php" method="post" enctype="multipart/form-data" id="reg_panel_id" class="row mt-5 mx-auto col-12 col-md-6">
        <h2 class="text-center p-2 mb-3">Регистрация</h2>
        <input type="hidden" name="action" value="register">
        <div class="nav flex-column nav-pills col-auto" role="tablist" aria-orientation="vertical">
            <a href="/" class="btn btn-outline-dark mb-3">Назад</a>
            <a class="nav-link active" id="v-reg-base-tab" data-bs-toggle="pill" href="#v-reg-base" role="tab" aria-controls="v-reg-base" aria-selected="true">Данные</a>
            <a class="nav-link" id="v-reg-profile-tab" data-bs-toggle="pill" href="#v-reg-profile" role="tab" aria-controls="v-reg-profile" aria-selected="false">Профиль</a>
        </div>
        <div class="tab-content col">
            <div class="tab-pane fade show active" id="v-reg-base" role="tabpanel" aria-labelledby="v-reg-base-tab">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" required id="floatingRegLogin" name="login" placeholder="">
                    <label for="floatingRegLogin">Адрес электронной почты</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" required pattern="^[^\s]{1,}$" maxlength="30" id="floatingRegPassword" name="password" placeholder="">
                    <label for="floatingRegPassword">Пароль</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" required pattern="^[^\s]{1,}$" maxlength="30" id="floatingRegPasswordRepeat" placeholder="">
                    <label for="floatingRegPasswordRepeat">Повторите пароль</label>
                </div>
            </div>
            <div class="tab-pane fade" id="v-reg-profile" role="tabpanel" aria-labelledby="v-reg-profile-tab">
                <div class="input-group mb-3">
                    <span class="input-group-text">@</span>
                    <div class="form-floating flex-fill">
                        <input type="text" class="form-control" required pattern="^[^\s]{1,}$" maxlength="30" id="floatingRegNickname" name="nickname" placeholder="">
                        <label for="floatingRegNickname">Псевдоним</label>
                    </div>
                </div>
                <label for="regAvatarInput" class="form-label">Аватар</label>
                <input class="form-control" type="file" id="regAvatarInput" name="avatar">
            </div>
            <button type="submit" class="btn btn-primary mt-4 col-12" id="submit_register_id">Сохранить</button>
        </div>
    </form>
</div>
<?php
    elseif (!empty($_GET['action']) && strtolower($_GET['action']) === 'login'):
?>
<div class="container">
    <form action="auth.php" method="post" id="login_panel_id" class="mt-5 mx-auto col-12 col-md-4">
        <h2 class="text-center p-2 mb-3">Вход</h2>
        <input type="hidden" name="action" value="login">
        <div class="form-floating mb-3">
            <input type="email" class="form-control" required id="floatingLogLogin" name="login" placeholder="">
            <label for="floatingLogLogin">Адрес электронной почты</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" required pattern="^[^\s]{1,}$" maxlength="30" id="floatingLogPassword" name="password" placeholder="">
            <label for="floatingLogPassword">Пароль</label>
        </div>
        <button type="submit" class="btn btn-primary mt-4 col-12">Войти</button>
        <a href="/" class="btn btn-outline-dark mt-3 col-12">Назад</a>
    </form>
</div>
<?php
    else:
?>
<div class="container position-absolute top-50 start-50 translate-middle">
    <h2 class="text-center p-3">Получите доступ к постам, заведя аккаунт здесь!</h2>
    <div class="d-grid col-12 col-md-4 mx-auto gap-3">
        <a href="index.php?action=register" class="btn btn-primary">Создать аккаунт</a>
        <a href="index.php?action=login" class="btn btn-light">Войти</a>
    </div>
</div>
<?php
    endif;
?>
<?php include 'html/footer.html';?>
</body>
</html>