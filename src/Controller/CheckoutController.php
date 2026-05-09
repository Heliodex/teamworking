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

		if ($request->isMethod("POST"))
			return $this->redirectToHome($request);

		$cart = Database::getCart($user->id, false);

		$total = 0;
		foreach ($cart as $item)
			$total += $item->price * $item->quantity;

		return $this->finish($request, "checkout.html.twig", [
			"cart" => $cart,
			"total" => $total,
		]);
	}
}
