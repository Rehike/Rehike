<?php
namespace YukisCoffee;

interface ICoffeeException
{
    // PHP Exception standard methods:
    public function getMessage();
    public function getCode();
    public function getFile();
    public function getLine();
    public function getTrace();
    public function getTraceAsString();

    public function __toString();
    public function __construct($message = null, $code = 0);
}

abstract class CoffeeException extends \Exception implements ICoffeeException
{
    public $message = "Unknown exception";
    private $string;
    protected $code = 0;
    protected $file;
    protected $line;
    private $trace;
    public $exceptionName;

    public function __construct($message = null, $code = 0)
    {
        $this->exceptionName = get_class($this);

        if (!$message)
        {
            $this->message = "Unknown {$this->exceptionName}";
        }

        parent::__construct($message, $code);
    }

    public function __toString()
    {
        // More readable crash screen if uncaught.
        echo 
            "<div class=\"yukiscoffee-uncaught-error-container\">" .
                "<h1>Fatal error</h1>" .
                "Uncaught <b>GetPropertyAtPathException</b>: {$this->message}<br><br>" .
                "<h1>Technical info</h1>" .
                "<pre>" .
                    "File: {$this->file}:{$this->line}\n\n" .
                    "Stack trace:<div class=\"yc-stack-trace\">{$this->getTraceAsString()}</div>" .
                "</pre>" .
            "</div>" .
            "<style>.yukiscoffee-uncaught-error-container{padding:12px;color:#000;border:4px solid #d31010;background:#fff;font-family:arial,sans-serif}" .
            ".yc-stack-trace{margin-left:12px;padding-left:12px;border-left:2px solid #ccc}" .
            "</style>";
        exit();
    }
}