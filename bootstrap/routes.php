<?php

declare(strict_types=1);

/** @var $this \Support\Routing\Router */

$this->post('api/user', App\Controllers\User\Create::class);
$this->post('api/auth', App\Controllers\User\Auth::class);
$this->put('api/user', App\Controllers\User\Update::class);
$this->get('api/user', App\Controllers\User\Read::class);
$this->delete('api/user', App\Controllers\User\Delete::class);
$this->get('api/list', App\Controllers\User\Filter::class);
