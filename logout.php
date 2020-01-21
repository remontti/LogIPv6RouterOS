<?php
  session_start();
  $_SESSION = array();
  session_destroy();
  header('location: ' . dirname( $_SERVER['SERVER_NAME'] ) . '/');
  exit;
?>