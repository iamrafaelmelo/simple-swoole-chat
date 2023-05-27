<?php

declare(strict_types=1);

use App\Actions\HomeAction;
use Chat\Route;

Route::get('/', HomeAction::class);
