<?php

    // head and nav information
    $pageTitle = basename($_SERVER['REQUEST_URI'], ".php");

    // removes the part after the ? for a nicer title
    if (strpos($pageTitle, '?') !== false) {
        $pageTitle = substr($pageTitle, 0, strpos($pageTitle, '?'));
    }
        $pageTitle = str_replace("-", " ", $pageTitle);

    include 'inc/core.php';
    include 'inc/mysql.php';

    $DB = new MySQL();
    $Core = new Core();

    ob_start();

    include 'inc/header.php';
    include 'languages/selector.php';


    $pageSubtitleText = $Core->subTitleText($pageTitle);

    include 'languages/selector.php';


    if(empty($_GET['page'])){
        $pageTitle = 'nieuws';
        $activePage = 'nieuws';
    }
    else if(!empty($_GET['page'])){         //If no page specified
        if($_GET['page'] == 'index'){       //If index specified go to news
            $activePage = 'nieuws';  
            $pageTitle = 'nieuws';
        }
        else {
            $activePage = $_GET['page']; 
        }
    }

    if($_COOKIE['lang'] == 'nl'){
        $langTitle = 'Nederlands';
    }
    else if($_COOKIE['lang'] == 'en'){
        $langTitle = 'Engels';
    }

    $pageSubtitleText = $Core->subTitleText($pageTitle);
    include 'inc/nav.php';

 
    switch($activePage)
        {
            /* Student pagina's */
            case 'nieuws':
                include 'pages/nieuws.php'; //file path of your home/nieuws page
                break;
            case 'vakken':
                include 'pages/vakken.php';
                break;
            case 'docenten':
                include 'pages/docenten.php';
                break;
            case 'docent':
                include 'pages/docent.php';
                break;
            case 'contact':
                include 'pages/contact.php';
                break;
            case 'aanwezigheid':
                include 'pages/aanwezigen.php';
                break;
            case 'login':
                include 'pages/login.php';
                break;
            case 'uitloggen':
                include 'pages/uitloggen.php';
                break;
            /* Docent pagina's */
            case 'nieuwsbeheer':
                include 'pages/docenten/nieuwsBeheer.php';
                break;
            case 'profiel-bewerken':
                include 'pages/docenten/profiel.php';
                break;
            case 'docentbeheer':
                include 'pages/docenten/docentBeheer.php';
                break;
            case 'beschikbaarheid':
                include 'pages/docenten/beschikbaarheid.php';
                break;
            case 'vakkenbeheer':
                include 'pages/docenten/vakkenBeheer.php';
                break;
            case 'opleidingbeheer':
                include 'pages/docenten/opleidingBeheer.php';
                break;
            // disclaimers
            case 'privacyPolicy':
                include 'pages/privacyPolicy.php';
                break;
            case 'termsAndConditions':
                include 'pages/termsAndConditions.php';
                break;
            default:
                include 'pages/404.php'; //If any page that doesn't exists, then get back to home.
            
        }


        echo "</div>";
        
        echo"<div class='pageSidebarBlock'>";

        /*
        echo "<div class='pageSidebarBlock'>";
        include 'inc/sidebar.php';
        echo "</div>"; //closes pageSidebarBlock
        */
        echo "</div>"; //closes devider

    include 'inc/footer.php';
?>