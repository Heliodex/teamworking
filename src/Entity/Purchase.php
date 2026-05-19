<?php

namespace App\Entity;

use DateTime;

final class Purchase
{
	final public array $products;
	final public readonly DateTime $completed;
	final public readonly int $discount;

	final public function __construct(array $products, DateTime $completed, int $discount)
	{
		$this->products = $products;
		$this->completed = $completed;
		$this->discount = $discount;
	}

	final public function price(): int
	{
		$total = 0;
		foreach ($this->products as $product)
			$total += $product->price * $product->quantity;

		return $total;
	}
}
