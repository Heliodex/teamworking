<?php

namespace App\Controller;

use App\Database;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

final class OrdersController extends Base
{
	#[Route("/orders", methods: ["GET"], name: "orders", options: ["sitemap" => true])]
	final public function home(Request $request): Response
	{
		$user = $this->user($request);
		if (!$user)
			return $this->redirectToLogin($request);

		$cart = Database::getOrders($user->id);

		$total = 0;
		foreach ($cart as $item)
			$total += $item->price * $item->quantity;

		return $this->finish($request, "orders.html.twig", [
			"cart" => $cart,
			"total" => $total,
		]);
	}
}
