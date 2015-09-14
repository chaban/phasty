<?php namespace Phasty\Common\Traits;

use Phalcon\Http\Response;

trait RestControllerTrait {

    protected $statusCode = 200;

    /**
     * Getter for statusCode
     *
     * @return mixed
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    /*protected function respondWithItem($item, $callback)
    {
    $resource = new Item($item, $callback);

    $rootScope = $this->fractal->createData($resource, 'page');

    return Response::make($rootScope->toArray(), $this->statusCode);
    }

    protected function respondWithCollection($collection, $callback)
    {
    $resource = new Collection($collection, $callback);

    $rootScope = $this->fractal->createData($resource);

    return \Phalcon\Http\Response::json($rootScope->toArray(), $this->statusCode);
    }*/

    /**
     * @param $message
     * @param $errorCode
     * @return Response
     */
    protected function respondWithError($message, $errorCode) {
        if ($this->statusCode === 200) {
            trigger_error(
                "You better have a really good reason for erroring on a 200...",
                E_USER_WARNING
            );
        }

        $error = [
            'code' => $errorCode,
            'http_code' => $this->statusCode,
            'message' => $message,
        ];
        $this->response->setStatusCode($this->getStatusCode(), $errorCode);
        $this->setHeaders();
        $this->response->setContent(json_encode($error, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->response->send();
        exit;
    }

    /**
     * Generates a Response with a 403 HTTP header and a given message.
     * @param $message string
     *
     * @return  Response
     */
    public function errorForbidden($message = 'Forbidden') {
        return $this->setStatusCode(403)->respondWithError($message, self::CODE_FORBIDDEN);
    }

    /**
     * Generates a Response with a 500 HTTP header and a given message.
     * @param $message string
     *
     * @return  Response
     */
    public function errorInternalError($message = 'Internal Error') {
        return $this->setStatusCode(500)->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * Generates a Response with a 404 HTTP header and a given message.
     * @param $message string
     *
     * @return  Response
     */
    public function errorNotFound($message = 'Resource Not Found') {
        return $this->setStatusCode(404)->respondWithError($message, self::CODE_NOT_FOUND);
    }

    /**
     * Generates a Response with a 401 HTTP header and a given message.
     * @param $message string
     *
     * @return  Response
     */
    public function errorUnauthorized($message = 'Unauthorized') {
        return $this->setStatusCode(401)->respondWithError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * Generates a Response with a 400 HTTP header and a given message.
     * @param $message string
     *
     * @return  Response
     */
    public function errorWrongArgs($message = 'Wrong arguments for form provided') {
        return $this->setStatusCode(400)->respondWithError($message, self::CODE_WRONG_ARGS);
    }

    /**
     * Generates a Response with a 200 HTTP header and a given body.
     * @param $data mixed
     *
     * @return  Response
     */
    public function apiOk($data = []){
        $this->response->setStatusCode(200, 'OK');
        $this->setHeaders();
        $this->response->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->response->send();
        exit;
    }

    /**
     * @return Response
     */
    protected function setHeaders(){
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET,PUT,PATCH,POST,DELETE');
        $this->response->setContentType('application/json', 'UTF-8');
        return $this;
    }
}