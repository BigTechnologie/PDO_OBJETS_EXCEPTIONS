<?php

session_start(); // On demarre la session
session_destroy();// On detruit la session
header('Location: ' . $router->url('login')); 
exit();