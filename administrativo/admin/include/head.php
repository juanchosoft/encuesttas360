<?php
ob_start(); // ← Agregar esta línea al inicio
session_start();
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

      <!-- ===============================================-->
      <!--    Document Title & Favicons-->
      <!-- ===============================================-->
      <title>ESTADISTICAS360</title>

      <!-- Favicons -->
      <link rel="apple-touch-icon" sizes="57x57" href="assets/img/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="assets/img/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="assets/img/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="assets/img/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="assets/img/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="assets/img/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="assets/img/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="assets/img/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon-16x16.png">
<link rel="manifest" href="/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">

      <!-- ===============================================-->
      <!--    Stylesheets-->
      <!-- ===============================================-->
      <link href="vendors/dropzone/dropzone.css" rel="stylesheet">
      <link href="vendors/flatpickr/flatpickr.min.css" rel="stylesheet" />
      <link rel="stylesheet" href="assets/css/style.css">

    <meta name="msapplication-TileImage" content="assets/img/favicons/favicon.png">
    <meta name="theme-color" content="#000000">
    <script src="vendors/simplebar/simplebar.min.js"></script>
    <script src="assets/js/config.js"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link href="vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
    <script>
      var phoenixIsRTL = window.config.config.phoenixIsRTL;
      if (phoenixIsRTL) {
        var linkDefault = document.getElementById('style-default');
        var userLinkDefault = document.getElementById('user-style-default');
        linkDefault.setAttribute('disabled', true);
        userLinkDefault.setAttribute('disabled', true);
        document.querySelector('html').setAttribute('dir', 'rtl');
      } else {
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
      }
    </script>
    <script src="/vendors/dropzone/dropzone-min.js"></script>
    <link href="vendors/leaflet/leaflet.css" rel="stylesheet">
    <link href="vendors/leaflet.markercluster/MarkerCluster.css" rel="stylesheet">
    <link href="vendors/leaflet.markercluster/MarkerCluster.Default.css" rel="stylesheet">
    
  </head>
