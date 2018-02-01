<?php

namespace Jimdo\Reports;

class ErrorCodeStore {
    const ERR_EDIT_COMMENT_DENIED = 1;
    const ERR_DELETE_COMMENT_DENIED = 2;

    const ERR_PASSWORD_NOT_NEW = 3;
    const ERR_PASSWORD_WRONG = 4;

    const ERR_PASSWORD_BLACK_LIST = 5;
    const ERR_PASSWORD_LENGTH = 6;
    const ERR_PASSWORD_LOWER_CASE = 7;
    const ERR_PASSWORD_UPPER_CASE = 8;
    const ERR_PASSWORD_NUMBERS = 9;

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
}
