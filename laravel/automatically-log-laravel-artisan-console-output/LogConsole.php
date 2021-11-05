<?php
// @see: https://stackoverflow.com/questions/48167834/how-to-remove-all-formatting-from-the-output-of-a-console-command-written-in-php

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\{Event, Log};
use Symfony\Component\Console\Helper\Helper;

Event::listen(function (CommandFinished $event) {
    Log::info(
        Helper::removeDecoration(
            $event->output->getFormatter(),
            $event->output->fetch(),
        )
    );
});