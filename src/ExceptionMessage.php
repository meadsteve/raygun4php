<?php
namespace MeadSteve\Raygun4php;

class ExceptionMessage
{
    public $Message;
    public $ClassName;
    public $StackTrace = array();
    public $FileName;
    public $Data;

    /**
     * @param \Exception $exception
     */
    public function __construct($exception)
    {
        $exceptionClass = get_class($exception);
        if ($exceptionClass != "ErrorException")
        {
            $this->Message = $exceptionClass.": ".$exception->getMessage();
        }
        else
        {
            $this->Message = "Error: ".$exception->getMessage();
        }
        $this->FileName = baseName($exception->getFile());
        $this->BuildStackTrace($exception);
    }

    /**
     * @param \Exception $exception
     */
    private function BuildStackTrace($exception)
    {
        $traces = $exception->getTrace();
        $lines = array();
        foreach ($traces as $trace)
        {
            $lines[] = new ExceptionTraceLineMessage($trace);
         }
        $this->StackTrace = $lines;
    }
}
