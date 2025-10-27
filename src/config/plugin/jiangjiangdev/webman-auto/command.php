<?php

use Jiangjiangdev\WmAuto\Commands\AdminCreateCommand;
use Jiangjiangdev\WmAuto\Commands\AuthCreateCommand;
use Jiangjiangdev\WmAuto\Commands\ContentResetCommand;
use Jiangjiangdev\WmAuto\Commands\ResourceCreateCommand;

return [
    AuthCreateCommand::class,
    ContentResetCommand::class,
    ResourceCreateCommand::class,
    AdminCreateCommand::class,
];
