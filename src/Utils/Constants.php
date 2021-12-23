<?php

namespace App\Utils;

class Constants
{
    const  ADMIN_EMAIL = "renaudstrifler@gmail.com";


    const  ROLE_TEST_USER = "ROLE_TEST_USER";
    const  ROLE_TEST_ADMIN = "ROLE_TEST_ADMIN";
    const  ROLE_USER = "ROLE_USER";
    const  ROLE_ADMIN = "ROLE_ADMIN";


    const  MENU_DASHBOARD = "MENU_DASHBOARD";
    const  MENU_ANNONCE = "MENU_ANNONCE";
    const  MENU_SONORISATION = "MENU_SONORISATION";
    const  MENU_ADMIN = "MENU_ADMIN";
    const  MENU_COMPTE = "MENU_COMPTE";


    const  MENU_MANAGER = [
        self::ROLE_TEST_USER =>[ self::MENU_DASHBOARD,self::MENU_ANNONCE ,self::MENU_SONORISATION,self::MENU_COMPTE],
        self::ROLE_TEST_ADMIN =>[ self::MENU_DASHBOARD,self::MENU_ANNONCE ,self::MENU_SONORISATION,self::MENU_ADMIN,self::MENU_COMPTE],
        self::ROLE_USER =>[ self::MENU_DASHBOARD,self::MENU_ANNONCE ,self::MENU_SONORISATION,self::MENU_COMPTE],
        self::ROLE_ADMIN =>[ self::MENU_DASHBOARD,self::MENU_ANNONCE ,self::MENU_SONORISATION,self::MENU_ADMIN,self::MENU_COMPTE],
    ];
}