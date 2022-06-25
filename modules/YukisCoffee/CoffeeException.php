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
    // PHP is really annoying ugh...
    // - protected $file
    // - protected $line
    // Crash PHP 8, saying they must be typed
    // - protected string $file
    // - protected int $line
    // Crash PHP 7, saying they must be untyped
    // Fix: Remove the redefinition here and just
    // inherit from Exception.
    public $message = "Unknown exception";
    private $string;
    protected $code = 0;
    private $trace;
    public $exceptionName;

    protected static $beautifulError = true;

    public function __construct($message = null, $code = 0)
    {
        $this->exceptionName = get_class($this);

        if (!$message)
        {
            $this->message = "Unknown {$this->exceptionName}";
        }

        parent::__construct($message, $code);
    }

    /**
     * Create a new exception from a previously thrown
     * one.
     * 
     * Useful for renaming exceptions.
     * 
     * @param Throwable $exception
     * @return CoffeeException
     */
    public static function from($exception)
    {
        $ceInstance = new static(
            $exception -> getMessage(),
            $exception -> getCode()
        );

        $ceInstance -> _setFile( $exception -> getFile() );
        $ceInstance -> _setLine( $exception -> getLine() );
        $ceInstance -> _setTrace( $exception -> getTrace() );

        return $ceInstance;
    }

    public function _setFile($a)
    {
        $this->file = $a;
    }

    public function _setLine($a)
    {
        $this->line = $a;
    }

    public function _setTrace($a)
    {
        $this->trace = $a;
    }

    public function __toString()
    {
        if (self::$beautifulError)
        {
            // More readable crash screen if uncaught.
            echo 
                "<div class=\"yukiscoffee-uncaught-error-container\">" .
                    "<h1>Fatal error</h1>" .
                    "Uncaught <b>{$this->exceptionName}</b>: {$this->message}<br><br>" .
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
        else
        {
            return parent::__toString();
        }
    }

    public static function enableBeautifulError($status = true)
    {
        self::$beautifulError = $status;
    }

    public static function disableBeautifulError()
    {
        return self::enableBeautifulError(false);
    }
}