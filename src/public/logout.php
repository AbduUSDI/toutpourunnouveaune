<?php

session_start();
session_destroy();
header('Location: https://www.abduusdi.fr/toutpourunnouveaune/login');
exit;
