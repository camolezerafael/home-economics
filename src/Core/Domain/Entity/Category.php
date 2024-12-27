<?php

	namespace Core\Domain\Entity;

	use Core\Domain\Entity\Traits\MagicMethodsTrait;
	use Core\Domain\Exception\EntityValidationException;

	class Category
	{
		use MagicMethodsTrait;

		public function __construct(
			protected int|null    $id = null,
			protected string|null $name = null,
			protected string|null $description = null,
			protected int|null    $userId = null,
		)
		{
			$this->validate();
		}

		public function update( string $name, string $description = '' ): void
		{
			$this->name = $name;

			if ( $description !== '' ) {
				$this->description = $description;
			}

			$this->validate();
		}

		public function validate()
		{
			if ( empty( $this->name ) || strlen($this->name) === 1 ) {
				throw new EntityValidationException("Invalid name");
			}

			if ( !empty( $this->description ) && strlen( $this->description ) < 3 ) {
				throw new EntityValidationException( 'Invalid description' );
			}
		}

	}
