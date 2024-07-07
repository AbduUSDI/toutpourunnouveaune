<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #98B46D, #DAE8C5);">
    <a class="navbar-brand" href="index.php">Tout pour un nouveau né - Forum</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="indexforum.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add_thread.php">Créer une discussion</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="threads.php">Toutes les discussions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="my_profile.php">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../contact.php">Nous contacter</a>
            </li>
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Déconnexion</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Inscription</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
