<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #98B46D, #DAE8C5);">
    <a class="navbar-brand" href="/Portfolio/toutpourunnouveaune/forum">Tout pour un nouveau né - Forum</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="/Portfolio/toutpourunnouveaune/home">Retour sur tout-pour-un-nouveau-ne</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Portfolio/toutpourunnouveaune/forum">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Portfolio/toutpourunnouveaune/forum/threads/add">Créer une discussion</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Portfolio/toutpourunnouveaune/forum/threads">Toutes les discussions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Portfolio/toutpourunnouveaune/forum/profile">Mon profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Portfolio/toutpourunnouveaune/forum/contact">Nous contacter</a>
            </li>
            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/Portfolio/toutpourunnouveaune/forum/logout">Déconnexion</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="/Portfolio/toutpourunnouveaune/forum/login">Connexion</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
