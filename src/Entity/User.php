<?php

namespace App\Entity;

use App\MemberCategory;
use DateTime;

readonly final class User
{
	final public string $id;
	final public DateTime $created;
	private DateTime $createdAt; // must exist
	final public string $forename;
	final public string $surname;
	final public string $street;
	final public string $town;
	final public string $postcode;
	final public MemberCategory $category;
	final public string $email;
	final public string $password;
	final public ?int $cartSize;

	final public function __construct(
		string $id,
		DateTime $created,
		string $forename,
		string $surname,
		string $street,
		string $town,
		string $postcode,
		MemberCategory $category,
		string $email,
		string $password,
		?int $cartSize = null
	) {
		$this->id = $id;
		$this->created = $created;
		$this->forename = $forename;
		$this->surname = $surname;
		$this->street = $street;
		$this->town = $town;
		$this->postcode = $postcode;
		$this->category = $category;
		$this->email = $email;
		$this->password = $password;
		$this->cartSize = $cartSize;
	}
}
