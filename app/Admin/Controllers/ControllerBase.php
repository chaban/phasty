<?php namespace Phasty\Admin\Controllers;

use \Phalcon\Mvc\Controller;
use Phasty\Common\Traits\RestControllerTrait;

/**
 * Class ControllerBase
 * @package Phasty\Http\Controllers\Admin
 */
class ControllerBase extends Controller {

    use RestControllerTrait;

    const CODE_WRONG_ARGS = 'GEN-FUBARGS';
    const CODE_NOT_FOUND = 'GEN-LIKETHEWIND';
    const CODE_INTERNAL_ERROR = 'GEN-AAAGGH';
    const CODE_UNAUTHORIZED = 'GEN-MAYBGTFO';
    const CODE_FORBIDDEN = 'GEN-GTFO';
    const CODE_INVALID_MIME_TYPE = 'GEN-UMWUT';

    protected function initialize() {

    }


}
