<?php

	namespace Core\Domain\Entity\Traits;

	trait MagicMethodsTrait
	{
		public function __get( $name )
		{
			if ( isset( $this->{$name} ) ) {
				return $this->{$name};
			}

			$className = get_class( $this );
			throw new \BadMethodCallException( "Property {$name} not found in class {$className}" );
		}
	}
