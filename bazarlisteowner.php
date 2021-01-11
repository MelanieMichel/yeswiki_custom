<?php


use YesWiki\Core\Service\DbService;

if (!defined("WIKINI_VERSION")) {
    die("acc&egrave;s direct interdit");
}

$user = $this->getUser();

if ($user == '' ){
    echo '<div class="alert alert-danger">L\'action "bazarlisteowner" doit être utilisée pour un utilisateur connecté.</div>' ;
} else {
    $username = $this->services->get(DbService::class)->escape(
                preg_replace('/^"(.*)"$/', '$1', json_encode($user['name']))
            ) ;
    $params = $GLOBALS['wiki']->parameter;
    $labelName = 'createur';
    // get queries
    $query = ($params['query']) ?? '' ;
    if (!empty($query)) {
        $queries = explode('|',$query) ;
        switch (count($queries)) {
            case 0:
                $query = '' ;
                break ;
            default:
                $query = '' ;
                $first = true ;
                foreach($queries as $subquery) {
                    if (strpos($subquery,"=") !== false) {                        
                        list($id,$criterion) = implode('=',$query) ;
                        if ($id != $labelName) {
                            $query .= ($first) ? '' : '|' ;
                            $first = false ;
                            $query .= $subquery ;
                        }
                    }
                }
                $query .= '|' ;
        }
    } else {
        $query = '' ;
    }
    $query .= $labelName . '=' . $username ;
    $params['query'] = $query ;
    
    $output = $GLOBALS['wiki']->Action('bazarliste',0,$params) ;
    if (empty($output)) {
        echo '<div class="alert alert-info">Aucune fiche trouvée</div>' ;
    } else {
        echo $output ;
    }
}