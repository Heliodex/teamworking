<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class AddProduct
{
	#[Assert\NotBlank]
	final public string $name;

	final public ?string $description;


	#[Assert\NotBlank]
	final public int $price;


	#[Assert\NotBlank]
	final public int $stock;
}
