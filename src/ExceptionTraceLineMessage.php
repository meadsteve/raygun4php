<?php
namespace MeadSteve\Raygun4php;

class ExceptionTraceLineMessage
{
    public $LineNumber;
    public $ClassName;
    public $FileName;
    public $MethodName;

    public function __construct(array $trace = null)
    {
        $this->FileName = "";
        $this->ClassName = "";
        $this->LineNumber = "0";
        $this->MethodName = "";

        if ($trace) {
            $this->fillFromTrace($trace);
        }
    }

    /**
     * @param string[] $trace
     */
    protected function fillFromTrace(array $trace)
    {
        if (array_key_exists("file", $trace)) {
            $this->FileName = $trace["file"];
        }

        if (array_key_exists("class", $trace)) {
            $this->ClassName = $trace["class"];
        }

        if (array_key_exists("function", $trace)) {
            $this->MethodName = $trace["function"];
        }

        if (array_key_exists("line", $trace)) {
            $this->LineNumber = $trace["line"];
        }
    }
}
