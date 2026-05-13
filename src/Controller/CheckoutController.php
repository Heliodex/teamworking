<?php

namespace App\Controller;

use App\Database;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController extends Base
{
	#[Route("/checkout", methods: ["GET", "POST"], name: "checkout")]
	final public function home(Request $request): Response
	{
		$user = $this->user($request);
		if (!$user)
			return $this->redirectToLogin($request);

		$discount = $user->category->value;
		if ($request->isMethod("POST")) {
			Database::completeOrder($user->id, $discount);
			return $this->redirectToRoute("orders", [], 303);
		}

		$cart = Database::getCart($user->id);

		$subtotal = 0;
		foreach ($cart as $item)
			$subtotal += $item->price * $item->quantity;

		$discounttotal = (int) round($subtotal * (1 - $discount / 100));

		$total = (int) round($discounttotal * 1.2); // VAT

		return $this->finish($request, "checkout.html.twig", [
			"cart" => $cart,
			"subtotal" => $subtotal,
			"discounttotal" => $discounttotal,
			"total" => $total,
		]);
	}
}
