<?php
namespace MeadSteve\Raygun4php;

class Message
{
    protected $OccurredOn;
    protected $Details;

    public function __construct($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        $this->OccurredOn = gmdate("Y-m-d\TH:i:s", $timestamp);
        $this->Details = new MessageDetails();
    }

    public function setException($exception)
    {
        $this->Details->MachineName = gethostname();
        $this->Details->Error = new ExceptionMessage($exception);
        $this->Details->Request = new RequestMessage();
        $this->Details->Environment = new EnvironmentMessage();
        $this->Details->Client = new ClientMessage();
    }

    public function setTags(array $tags)
    {
        $this->Details->Tags = $tags;
    }

    public function setUserCustomData(array $userCustomData)
    {
        $this->Details->UserCustomData = $userCustomData;
    }

    public function setVersion($version)
    {
        $this->Details->Version = $version;
    }

    public function setContext(Identifier $context)
    {
        $this->Details->Context = $context;
    }

    public function setUser(Identifier $user)
    {
        $this->Details->User = $user;
    }

    public function getAsJson()
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array(
            'OccurredOn' => $this->$OccurredOn,
            'Details' => $this->Details
        );
    }
}
