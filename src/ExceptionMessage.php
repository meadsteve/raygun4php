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
            $line = new ExceptionTraceLineMessage();
            if (array_key_exists("file", $trace))
            {
            $line->FileName = $trace["file"];
            }
            if (array_key_exists("class", $trace))
            {
            $line->ClassName = $trace["class"];
            }
            if (array_key_exists("function", $trace))
            {
            $line->MethodName = $trace["function"];
            }
            if (array_key_exists("line", $trace))
            {
            $line->LineNumber = $trace["line"];
            }
            $lines[] = $line;
         }
        $this->StackTrace = $lines;
    }
}
