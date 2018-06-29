<?php

App::uses('AuthFailedException', 'Service/AuthServiceException');

class AuthMismatchException extends AuthFailedException{};
