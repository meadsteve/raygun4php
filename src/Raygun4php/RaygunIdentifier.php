<?php
namespace MeadSteve\Raygun4php;

class RaygunIdentifier
{
		public $Identifier;

		public function __construct($id)
		{
			$this->Identifier = $id;
		}
}