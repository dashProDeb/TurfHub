<?php
// logout.php — POST: Destroy session
require_once __DIR__ . '/../config/helpers.php';

session_unset();
session_destroy();

jsonResponse(['success' => true]);
