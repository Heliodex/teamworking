<?php

namespace App\Tests\Controller;

use App\{Database, MemberCategory};
use App\Entity\{Register, User};
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoggedInTest extends WebTestCase
{
	private static function registerUser(): ?User
	{
		$details = new Register();
		$details->forename = "test";
		$details->surname = "test";
		$details->street = "test";
		$details->town = "test";
		$details->postcode = "test";
		$details->memberCategory = MemberCategory::Bronze;
		$details->email = "testuser@example.com";
		$details->password = "testpassword";

		Database::registerUser($details);
		// Re-fetch to ensure we have a fresh instance
		return Database::checkUser("testuser@example.com", "testpassword");
	}

	private function logIn(KernelBrowser $client): void
	{
		// Create test user if it doesn't exist
		$testUser = Database::checkUser("testuser@example.com", "testpassword");
		if (!$testUser)
			$testUser = self::registerUser();

		// find form and submit with test user credentials
		$crawler = $client->request("GET", "/login");
		$form = $crawler->selectButton("Log in")->form();
		$form["login[email]"] = "testuser@example.com";
		$form["login[password]"] = "testpassword";
		$client->submit($form);
		$this->assertResponseRedirects("/home", 303);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	public function testIndex(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		$client->request("GET", "/");
		// Authenticated users cannot access index page, should redirect to /home
		$this->assertResponseRedirects("/home", 302);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	public function testLogin(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		$client->request("GET", "/login");
		// Authenticated users cannot access login page
		$this->assertResponseRedirects("/home", 302);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	public function testRegister(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		$client->request("GET", "/register");
		// Authenticated users cannot access register page
		$this->assertResponseRedirects("/home", 302);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	public function testHome(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		// After logIn(), make a request to /home
		$client->request("GET", "/home");
		$this->assertResponseIsSuccessful();
	}

	public function testCart(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		$client->request("GET", "/cart");
		$this->assertResponseIsSuccessful();
	}

	public function testOrders(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		$client->request("GET", "/orders");
		$this->assertResponseIsSuccessful();
	}

	public function testCheckout(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		$client->request("GET", "/checkout");
		$this->assertResponseIsSuccessful();
	}

	public function testProfile(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		$client->request("GET", "/profile");
		$this->assertResponseIsSuccessful();
	}

	public function testLogout(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		// After logIn(), make a POST request to /logout
		$client->request("POST", "/logout");
		// User is logged out, should redirect to /login
		$this->assertResponseRedirects("/login", 303);
		$client->followRedirect();
		$this->assertResponseIsSuccessful();
	}

	public function test404(): void
	{
		$client = self::createClient();
		$this->logIn($client);
		$client->request("GET", "/nonexistentpage");
		$this->assertResponseStatusCodeSame(404);
	}
}
