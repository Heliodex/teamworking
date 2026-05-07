<?php

namespace App;

use App\Entity\{AddProduct, Login, Product, User};

final class Database
{
	// get path from environment variable
	private static function getPath(): string
	{
		$databaseUrl = $_ENV["DATABASE_URL"];
		if ($databaseUrl === false)
			throw new \RuntimeException("DATABASE_URL environment variable is not set.");

		// replace %APP_DIR% with actual app directory
		$databaseUrl = str_replace("%APP_DIR%", dirname(__DIR__), $databaseUrl);

		// Log::info("Database path after replacement: " . $databaseUrl);

		return $databaseUrl;
	}

	private static ?\PDO $pdo = null;

	final public static function pdo(): \PDO
	{
		if (self::$pdo)
			return self::$pdo;

		$databasePath = self::getPath();

		// check if database file exists, if not create it

		// check if string starts with sqlite:
		if (str_starts_with($databasePath, "sqlite:")) {
			$path = substr($databasePath, 7);
			$dir = dirname($path);
			if (!is_dir($dir))
				mkdir($dir, 0777, true);

			if (!file_exists($path))
				// create empty file
				touch($path);
		}

		self::$pdo = new \PDO(
			$databasePath,
			$_ENV["DATABASE_USER"] ?? null,
			$_ENV["DATABASE_PASSWORD"] ?? null,
			[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
		);

		$initQuery = file_get_contents(__DIR__ . "/init.sql");
		self::$pdo->exec($initQuery);

		return self::$pdo;
	}

	final public static function getRandomNumber(): int
	{
		$stmt = self::pdo()->query("SELECT RANDOM() % 100 AS number");
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		return (int) $row["number"];
	}

	final public static function getUserBySessionId(string $sess): ?User
	{
		$getUserBySessionQuery = file_get_contents(__DIR__ . "/getUserBySession.sql");
		$stmt = self::pdo()->prepare($getUserBySessionQuery);
		$stmt->execute([$sess]);

		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if (!$row)
			return null;

		return new User(
			$row["id"],
			new \DateTime($row["created"]),
			$row["forename"],
			$row["surname"],
			$row["street"],
			$row["town"],
			$row["postcode"],
			MemberCategory::from($row["category"]),
			$row["email"],
			$row["password"],
			(bool) $row["admin"],
			$row["cartSize"],
		);
	}

	final public static function createSession(string $userId): string
	{
		$createSessionQuery = file_get_contents(__DIR__ . "/createSession.sql");
		$stmt = self::pdo()->prepare($createSessionQuery);
		$stmt->execute([$userId]);

		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		return $row["id"];
	}

	final public static function invalidateSession(string $sessionId): void
	{
		$stmt = self::pdo()->prepare("DELETE FROM session WHERE id = ?");
		$stmt->execute([$sessionId]);
	}

	final public static function checkUser(string $email, string $passwordRaw): ?User
	{
		$getUserByEmailQuery = file_get_contents(__DIR__ . "/getUserByEmail.sql");
		$stmt = self::pdo()->prepare($getUserByEmailQuery);
		$stmt->execute([$email]);

		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		if (!$row)
			return null;

		if (!password_verify($passwordRaw, $row["password"]))
			return null;

		return new User(
			$row["id"],
			new \DateTime($row["created"]),
			$row["forename"],
			$row["surname"],
			$row["street"],
			$row["town"],
			$row["postcode"],
			MemberCategory::from($row["category"]),
			$email,
			$row["password"],
		);
	}

	final public static function logInUser(Login $login): ?string
	{
		try {
			$getUserByEmailQuery = file_get_contents(__DIR__ . "/getUserByEmail.sql");
			$stmt = self::pdo()->prepare($getUserByEmailQuery);
			$stmt->execute([$login->email]);

			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
			if (!$row)
				return null;

			if (!password_verify($login->password, $row["password"]))
				return null;

			return self::createSession($row["id"]);
		} catch (\PDOException $e) {
			Log::error("Database error during login: {$e->getMessage()}");
			return null;
		}
	}

	final public static function registerUser(string $forename, string $surname, string $street, string $town, string $postcode, MemberCategory $category, string $email, string $passwordRaw): ?string
	{
		try {
			$registerUserQuery = file_get_contents(__DIR__ . "/registerUser.sql");
			$stmt = self::pdo()->prepare(
				$registerUserQuery
			);
			$stmt->execute([
				$forename,
				$surname,
				$street,
				$town,
				$postcode,
				$category->value,
				$email,
				password_hash($passwordRaw, PASSWORD_ARGON2ID),
			]);

			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
			if (!$row)
				return null;

			return self::createSession($row["id"]);
		} catch (\PDOException $e) {
			Log::error("Database error during registration: {$e->getMessage()}");
			return null;
		}
	}

	final public static function updatePassword(string $userId, string $newPasswordRaw): bool
	{
		try {
			$updatePasswordQuery = file_get_contents(__DIR__ . "/updatePassword.sql");
			$stmt = self::pdo()->prepare($updatePasswordQuery);
			$stmt->execute([
				password_hash($newPasswordRaw, PASSWORD_ARGON2ID),
				$userId,
			]);

			return true;
		} catch (\PDOException $e) {
			Log::error("Database error during password update: {$e->getMessage()}");
			return false;
		}
	}

	final public static function getProducts(string $userId): array
	{
		try {
			$getProductsQuery = file_get_contents(__DIR__ . "/getProducts.sql");
			$stmt = self::pdo()->prepare($getProductsQuery);
			$stmt->execute([$userId]);

			$products = [];
			while ($row = $stmt->fetch(\PDO::FETCH_ASSOC))
				$products[] = new Product(
					$row["id"],
					new \DateTime($row["created"]),
					$row["name"],
					$row["description"],
					$row["price"],
					$row["stock"],
					0,
					$row["inCart"],
				);

			return $products;
		} catch (\PDOException $e) {
			Log::error("Database error during product retrieval: {$e->getMessage()}");
			return [];
		}
	}

	final public static function getCart(string $userId): array
	{
		try {
			$getCartQuery = file_get_contents(__DIR__ . "/getCart.sql");
			$stmt = self::pdo()->prepare($getCartQuery);
			$stmt->execute([$userId]);

			$products = [];
			while ($row = $stmt->fetch(\PDO::FETCH_ASSOC))
				$products[] = new Product(
					$row["id"],
					new \DateTime($row["created"]),
					$row["name"],
					$row["description"],
					$row["price"],
					$row["stock"],
					$row["quantity"],
					true,
				);

			return $products;
		} catch (\PDOException $e) {
			Log::error("Database error during cart retrieval: {$e->getMessage()}");
			return [];
		}
	}

	final public static function changeCart(string $userId, string $productId, bool $add): void
	{
		try {
			$addToCartQuery = file_get_contents(__DIR__ . "/addToCart.sql");
			$removeFromCartQuery = file_get_contents(__DIR__ . "/removeFromCart.sql");

			$stmt = self::pdo()->prepare($add ? $addToCartQuery : $removeFromCartQuery);
			$stmt->execute([$userId, $productId]);
		} catch (\PDOException $e) {
			Log::error("Database error during cart change: {$e->getMessage()}");
		}
	}

	final public static function setCartQuantity(string $userId, string $productId, int $qty): void
	{
		// sending quantity directly removes 1 database query compared to querying for the quantity

		try {
			if ($qty < 1) {
				self::changeCart($userId, $productId, false);
				return;
			}

			// fetch product stock and clamp requested qty to available stock
			$getProductStockQuery = file_get_contents(__DIR__ . "/getProductStock.sql");
			$stockStmt = self::pdo()->prepare($getProductStockQuery);
			$stockStmt->execute([$productId]);

			$productRow = $stockStmt->fetch(\PDO::FETCH_ASSOC);
			if (!$productRow) {
				Log::error("Product not found when setting cart quantity: {$productId}");
				return;
			}

			$finalQty = min($qty, (int) $productRow["stock"]);
			if ($finalQty < 1) {
				self::changeCart($userId, $productId, false);
				return;
			}

			$updateCartQuery = file_get_contents(__DIR__ . "/updateCart.sql");
			$stmt = self::pdo()->prepare($updateCartQuery);
			$stmt->execute([$finalQty, $userId, $productId]);
		} catch (\PDOException $e) {
			Log::error("Database error during cart quantity change: {$e->getMessage()}");
		}
	}

	final public static function addProduct(AddProduct $addProduct): void
	{
		try {
			$addProductQuery = file_get_contents(__DIR__ . "/addProduct.sql");
			$stmt = self::pdo()->prepare($addProductQuery);
			$stmt->execute([
				$addProduct->name,
				$addProduct->description,
				$addProduct->price,
				$addProduct->stock
			]);
		} catch (\PDOException $e) {
			Log::error("Database error during product addition: {$e->getMessage()}");
		}
	}
}
