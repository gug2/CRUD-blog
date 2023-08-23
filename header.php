<?php
    session_start();
?>

<header>
    <div class="d-flex p-2 px-4 justify-content-between bg-dark text-white">
        <h1 class="display-5 m-0 ms-4 user-select-none">CRUD блог</h1>
        <?php
            if (!empty($_SESSION['user-id']) && !empty($_SESSION['user-nickname'])):
        ?>
        <div class="d-flex flex-row align-items-center m-0 gap-3">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-sm-inline"><?php echo $_SESSION['user-nickname'];?></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li class="dropdown-item">
                        <small class="text-muted ">Account ID - <?php echo $_SESSION['user-id'];?></small>
                    </li>
                    <li class="dropdown-item">Настройки</li>
                    <a href="auth.php?action=logout" class="dropdown-item">Выйти</a>
                </ul>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
            </svg>
        </div>
        <?php
            endif;
        ?>
    </div>
</header>