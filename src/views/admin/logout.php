<?php

session_start();
session_destroy();
header('Location: /Portfolio/toutpourunnouveaune/login');
exit;
