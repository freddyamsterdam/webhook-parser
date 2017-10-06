<?php

use FreddyAmsterdam\WebhookParser;
use YourSystem\HypotheticalResourceController; // This is purely hypothetical, remove or replace as needed.
use YourSystem\HypotheticalErrorHandler; // This is purely hypothetical, remove or replace as needed.

try {
    $options = [
      'hook param' => 'cool-parameter' // This indicates which query parameter to look at to determine which hook was called.
    ];
    $webhookParser = new WebhookParser($options);
    $data = $webhookParser->execute();

    if (isset($data['error'])) {
        throw new Exception("Do something with this error: {$data['error']}");
    }

    $controller = new HypotheticalResourceController($data['hook']); // Pass called hook to HypotheticalResourceController.
    $controller->doSomethingCoolWithWebhookData($data); // Do something with the data.
} catch (Exception $e) {
    new HypotheticalErrorHandler($e->getMessage()); // Doing something with error
}

die();
