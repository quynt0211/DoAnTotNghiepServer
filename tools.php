<?php
/**
 * Created by PhpStorm.
 * User: buiph
 * Date: 9/5/2015
 * Time: 7:15 PM
 */
ini_set('max_execution_time', 0);
date_default_timezone_set("Asia/Saigon");
session_start();
header('Content-Type: text/html; charset=utf-8');
//header('Content-Type: text/event-stream');
// recommended to prevent caching of event data.
header('Cache-Control: no-cache');

require 'soccerway.php';

if(isset($_GET['action']))
    $action = $_GET['action'];
elseif(isset($argv[1]))
    $action = $argv[1];
if(!$action) {
    die('Không có quyền truy cập.');
}
else{
    $soccerway = new CrawlSoccerData();
    $soccerway->showMsg = true;
    switch($action){
        case 'club-domestic':
            $soccerway->getClubDomesticLeagues();
            break;
        case 'club-international':
            $soccerway->getClubInternationalLeagues();
            break;
        case 'nation':
            $soccerway->getNationLeagues();
            break;
        case 'nation-teams':
            $soccerway->getNationTeams();
            break;
        case 'league':
            $soccerway->getLeague();
            break;
        case 'update-league':
            $soccerway->updateLeagueInfo();
            break;
        case 'league-match':
            if(isset($_GET['league']))
                $soccerway->getLeagueMatch(1, (int)$_GET['league']);
            else
                $soccerway->getLeagueMatches();
            break;
        case 'league-table':
            if(isset($_GET['id']))
                $soccerway->getLeaguesTables($_GET['id']);
            else
                $soccerway->getLeaguesTables();
            break;
        case 'table':
            if(!$_GET['league'])
                die('Vui lòng cung cấp thông tin giải đấu');
            $league = $_GET['league'];
            $soccerway->getLeagueTables(1, (int)$league);
            break;
        case 'player':
            if(isset($_GET['id']))
                $soccerway->getTeamsPlayers($_GET['id']);
            else
                $soccerway->getTeamsPlayers();
            break;
        case 'update-team':
            $soccerway->getListTeamsInfo();
            break;
        case 'get-players-info':
            if(isset($_GET['id']))
                $soccerway->getPlayersInfo($_GET['id']);
            else
                $soccerway->getPlayersInfo();
            break;
        case 'get-players-career':
            if(isset($_GET['id']))
                $soccerway->getPlayersCareer($_GET['id']);
            else
                $soccerway->getPlayersCareer();
            break;
        case 'country-info':
            $soccerway->updateCountryInfo();
            break;
        case 'update-team-venue':
            $soccerway->getTeamsVenue();
            break;
        case 'update-match':
            if(isset($_GET['id']))
                $soccerway->updateMatch($_GET['id']);
            else
                $soccerway->updateMatch();
            break;
        case 'live-score':
            $soccerway->liveScore();
            break;
        case 'get-round':
            if(isset($_GET['id']))
                $soccerway->getLeagueRound($_GET['id']);
            else
                $soccerway->getLeagueRound();
            break;
        case 'get-rounds-matches':
            if(isset($_GET['id']))
                $soccerway->getLeagueRoundsMatches($_GET['id']);
            else
                $soccerway->getLeagueRoundsMatches();
            break;
        case 'get-round-matches':
            if(!$_GET['round'])
                die('Vui lòng cung cấp vòng\bảng đấu');
            $soccerway->getLeagueRoundMatches($_GET['round']);
            break;
        case 'get-odds-data':
            $soccerway->getOddData();
            break;
        case 'get-match-detail':
            if(isset($_GET['id']))
                $soccerway->getMatchDetailInfo($_GET['id']);
            else
                $soccerway->getMatchDetailInfo();
            break;
        case 'get-match-detail-live':
            if(isset($_GET['id']))
                $soccerway->getMatchDetailActivity($_GET['id']);
            else
                $soccerway->getMatchDetailActivity();
            break;
        case 'get-matches-by-date':
            $soccerway->getMatchesByDate();
            break;
        case 'get-round-group':
            $soccerway->getLeagueRoundGroup();
            break;
        case 'get-team-trophies':
            if(isset($_GET['id']))
                $soccerway->getTeamTrophies($_GET['id']);
            else
                $soccerway->getTeamTrophies();
            break;
        case 'update-country':
            $soccerway->updateCountryAlias();
            break;
        case 'update-match-venue':
            $soccerway->getMatchVenue();
            break;
        case 'update-venue':
            $soccerway->updateVenue();
            break;
    }
}
