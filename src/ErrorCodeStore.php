<?php

namespace Jimdo\Reports;

use Jimdo\Reports\User\PasswordException;

class ErrorCodeStore {
    const ERR_EDIT_COMMENT_DENIED = 1;
    const ERR_DELETE_COMMENT_DENIED = 2;

    const ERR_PASSWORD_NOT_NEW = 3;
    const ERR_PASSWORD_WRONG = 4;

    const ERR_PASSWORDBLACKLIST = 5;
    const ERR_PASSWORDLENGTH = 6;
    const ERR_PASSWORDLOWERCASE = 7;
    const ERR_PASSWORDUPPERCASE = 8;
    const ERR_PASSWORDNUMBERS = 9;

    const ERR_USERNAME_EXISTS = 10;
    const ERR_EMAIL_EXISTS = 11;
    const ERR_USERNAME_EMPTY = 12;
    const ERR_EMAIL_EMPTY = 13;
    const ERR_USERNAME_ADMIN = 14;

    const ERR_VALIDATOR_BOOL = 15;
    const ERR_VALIDATOR_DATE = 16;
    const ERR_VALIDATOR_FLOAT = 17;
    const ERR_VALIDATOR_INT = 18;
    const ERR_VALIDATOR_STRING = 19;

    const PASSWORD_CONFIRMATION_WRONG_MATCHING = 20;

    public static function getCorrectErrorCode(string $constraint) {
        $oClass = new \ReflectionClass(__class__);
        $constants = $oClass->getConstants();

        if (array_key_exists($constraint, $constants)) {
            throw new PasswordException(
                null,
                $constants[$constraint]
            );
        }
    }
}
