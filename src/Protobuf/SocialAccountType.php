<?php

// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: Message.proto

namespace App\Protobuf;

/**
 * Protobuf type <code>App.Protobuf.SocialAccountType</code>.
 */
class SocialAccountType
{
    /**
     * Generated from protobuf enum <code>TIKTOK = 0;</code>.
     */
    public const TIKTOK = 0;

    private static $valueToName = [
        self::TIKTOK => 'TIKTOK',
    ];

    public static function name($value)
    {
        if (!isset(self::$valueToName[$value])) {
            throw new \UnexpectedValueException(sprintf('Enum %s has no name defined for value %s', __CLASS__, $value));
        }

        return self::$valueToName[$value];
    }

    public static function value($name)
    {
        $const = __CLASS__.'::'.strtoupper($name);
        if (!defined($const)) {
            throw new \UnexpectedValueException(sprintf('Enum %s has no value defined for name %s', __CLASS__, $name));
        }

        return constant($const);
    }
}
