<?php
/**
 * CBAPIClerk
 */
namespace Infernusophiuchus\CBAPIClerk\Exceptions;

use Exception;

class HandleException extends Exception
{

    const BAD_ANSWER_CODE = -4;
    const BAD_ANSWER_MESSAGE = 'Bad answer from the server was received:';

    const REQUEST_FAILURE_CODE = -5;
    const REQUEST_FAILURE_MESSAGE = 'Authentication salt request failure.';

    const AUTH_FAILURE_CODE = -6;
    const AUTH_FAILURE_MESSAGE = 'Authentication failure.';

    const INVALID_ACTION_CODE = -7;
    const INVALID_ACTION_MESSAGE = 'Invalid action.';

    const INVALID_LIST_CODE = -8;
    const INVALID_LIST_MESSAGE = 'This entity cannot be listed.';

    const INVALID_DETAIL_CODE = -9;
    const INVALID_DETAIL_MESSAGE = 'Invalid table details requested.';

    const INVALID_ID_CODE = -10;
    const INVALID_ID_MESSAGE = 'Invalid Id.';

}
