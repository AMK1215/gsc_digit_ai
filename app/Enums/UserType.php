<?php

namespace App\Enums;

enum UserType: int
{
    case Master = 10;
    case Admin = 20;
    case Agent = 30;
    case Player = 40;
    case SystemWallet = 50;

    public static function usernameLength(UserType $type)
    {
        return match ($type) {
            self::Master => 1,
            self::Admin => 2,
            self::Agent => 3,
            self::Player => 4,
            self::SystemWallet => 5
        };
    }

    public static function childUserType(UserType $type)
    {
        return match ($type) {
            self::Master => self::Admin,
            self::Admin => self::Agent,
            self::Agent => self::Player,
            self::Player => self::Player
        };
    }
}
