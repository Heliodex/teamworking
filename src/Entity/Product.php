<?php

namespace App\Entity;

use DateTime;

final class Product extends AddProduct
{
	final public string $id;
	final public DateTime $created;
	final public int $quantity;
	final public bool $inCart;

	final public function __construct(string $id, DateTime $created, string $name, ?string $description, int $price, int $stock, int $quantity, bool $inCart)
	{
		$this->id = $id;
		$this->created = $created;
		$this->name = $name;
		$this->description = $description;
		$this->price = $price;
		$this->stock = $stock;
		$this->quantity = $quantity;
		$this->inCart = $inCart;
	}
}
