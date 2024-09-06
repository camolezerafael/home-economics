<?php

	namespace Tests\Unit\Domain\Entity;

	use Core\Domain\Entity\Category;
	use Core\Domain\Exception\EntityValidationException;
	use Tests\TestCase;

	class CategoryUnitTest extends TestCase {

		public function testAttributes(): void
		{
			$category = new Category(
				id: 1,
				name:'New Expense',
				description: 'The new expense category',
				userId: 1
			);

			$this->assertEquals('New Expense', $category->name);
			$this->assertEquals('The new expense category', $category->description);
			$this->assertEquals(1, $category->userId);

		}

		public function testUpdate(){
			$id = random_int(1,9999);
			$userId = random_int(1,9999);

			$category = new Category(
				id: $id,
				name:'New Expense',
				description: 'The new expense category',
				userId: $userId
			);

			$category->update(
				name:'Expense Edited',
				description:'Changed Category Description'
			);

			$this->assertEquals('Expense Edited', $category->name);
			$this->assertEquals('Changed Category Description', $category->description);
		}

		public function testExceptionName(){
			try {
				$category = new Category(
					name:'E',
					description:'Category Description'
				);

				$this->assertTrue( false);

			}catch (\Throwable $th){
				$this->assertInstanceOf(EntityValidationException::class, $th);
			}
		}

	}
