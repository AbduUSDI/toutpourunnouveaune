<?php

// Fichier pour détruire la session en cours et rediriger vers index.php

session_start();
session_destroy();
header('Location: /Portfolio/toutpourunnouveaune/home');
exit;
