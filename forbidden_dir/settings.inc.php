<?php

/*
 * set server specific constants
 */
if (
    (!empty($_SERVER['class']))
    &&
    (!empty($_SERVER['class']['constants']))
){
    $_SERVER['class']['constants']->version_number='2022.07.04';//live update for each release
    $_SERVER['class']['constants']->version_name='RC2.0';//live update for each release
    $_SERVER['class']['constants']->server_name='lakebed';//live update for each release
    $_SERVER['class']['constants']->environment='dev';//live update for each release
}
