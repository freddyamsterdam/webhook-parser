<?php

namespace FreddyAmsterdam;

class WebhookParser
{
    private $options = [
      'origin required' => false,
      'allowed origins' => [],
      'allowed content types' => [
        'application/json',
        'application/x-www-form-urlencoded'
      ],
      'hook param' => 'hook'
    ];
    private $data;

    public function __construct(array $options = []) {
        $this->options = array_replace_recursive($this->options, $options);
    }

    public function execute() {
        try {
            $this->checkRequest();
            $this->parseRequest();
            $this->checkDataFormatting();

            return $this->prepareOutput();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function parseRequest() {
        switch ($_SERVER['CONTENT_TYPE']) {
            case 'application/json':
                $this->data = json_decode(trim(file_get_contents("php://input")), true);
                break;
            case 'application/x-www-form-urlencoded':
                $this->data = $_POST;
                break;
        }
    }

    private function checkRequest() {
        if ($this->options['origin required']) {
            if (!isset($_SERVER['HTTP_ORIGIN'])) {
                throw new Exception("Origin must be defined request headers.");
            }

            if (!in_array($_SERVER['HTTP_ORIGIN'], $this->options['allowed origins'])) {
                throw new Exception("Origin {$_SERVER['HTTP_ORIGIN']} not allowed.");
            }
        }

        if (!$_SERVER['REQUEST_METHOD'] == 'POST') {
            throw new Exception("HTTP request method {$_SERVER['REQUEST_METHOD']} not allowed.");
        }

        if (!in_array($_SERVER['CONTENT_TYPE'], $this->options['allowed content types'])) {
            throw new Exception("Content-Type {$_SERVER['CONTENT_TYPE']} not allowed.");
        }
    }

    private function checkDataFormatting() {
        if (!is_array($this->data)) {
            throw new Exception("Request data formatted incorrectly.");
        }
    }

    private function prepareOutput() {
        if ($this->options['origin required']) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        } else {
            header("Access-Control-Allow-Origin: *");
        }

        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");

        $output = [
          'hook' => $_GET[$this->options['hook param']],
          'data' => $this->data
        ];

        return $output;
    }
}
