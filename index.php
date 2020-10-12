<?php
// head and nav information

include 'inc/mysql.php';
include 'inc/header.php';
include 'inc/nav.php';
include 'inc/sidebar.php';

$DB = new MySQL;

    if(!isset($_GET['page']) || $_GET['page'] == ''){
        $page = 'nieuws'; //If no page specified
    } else {
        $page = $_GET['page'];
    }

    switch($page)
        {
            case 'databasetest':
                include 'pages/databasetest.php'; //file path of your home/nieuws page
                break;
            case 'nieuws':
                include 'pages/nieuws.php'; //file path of your home/nieuws page
                break;
            case 'vakken':
                include 'pages/vakken.php';
                break;
            case 'docenten':
                include 'pages/docenten.php';
                break;
            case 'contact':
                include 'pages/contact.php';
                break;
            case 'login':
                include 'pages/login.php';
                break;
            case 'gebruikers':
                include 'pages/gebruikers.php';
                break;
            case 'uploadNieuws':
                include 'pages/uploadNieuws.php';
                break;
            case 'docentenEdit':
                include 'pages/docentenEdit.php';
                break;
            default:
                include 'pages/404.php'; //If any page that doesn't exists, then get back to home.
        }
    
include 'inc/footer.php';

?>
