<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoggedOutTest extends WebTestCase
{
	final public function testIndex(): void
	{
		$client = self::createClient();
		$client->request("GET", "/");
		$this->assertResponseIsSuccessful();
	}

	final public function testLogin(): void
	{
		$client = self::createClient();
		$client->request("GET", "/login");
		$this->assertResponseIsSuccessful();
	}

	final public function testRegister(): void
	{
		$client = self::createClient();
		$client->request("GET", "/register");
		$this->assertResponseIsSuccessful();
	}

	final public function testHome(): void
	{
		$client = self::createClient();
		$client->request("GET", "/home");
		$this->assertResponseRedirects("/login", 302);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	final public function testCart(): void
	{
		$client = self::createClient();
		$client->request("GET", "/cart");
		$this->assertResponseRedirects("/login", 302);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	final public function testOrders(): void
	{
		$client = self::createClient();
		$client->request("GET", "/orders");
		$this->assertResponseRedirects("/login", 302);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	final public function testCheckout(): void
	{
		$client = self::createClient();
		$client->request("GET", "/checkout");
		$this->assertResponseRedirects("/login", 302);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	final public function testProfile(): void
	{
		$client = self::createClient();
		$client->request("GET", "/profile");
		$this->assertResponseRedirects("/login", 302);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	final public function testLogout(): void
	{
		$client = self::createClient();
		$client->request("POST", "/logout");
		$this->assertResponseRedirects("/login", 303);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	final public function test404(): void
	{
		$client = self::createClient();
		$client->request("GET", "/nonexistentpage");
		$this->assertResponseStatusCodeSame(404);
	}
}
