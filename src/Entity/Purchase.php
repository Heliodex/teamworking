<?php

namespace App\Entity;

use DateTime;

readonly final class Purchase
{
	final public array $products;
	final public DateTime $completed;
	final public int $discount;

	final public function __construct(array $products, DateTime $completed, int $discount)
	{
		$this->products = $products;
		$this->completed = $completed;
		$this->discount = $discount;
	}
}
