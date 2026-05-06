<?php

namespace App\Controller;

use App\Database;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController extends Base
{
	#[Route("/checkout", name: "checkout")]
	final public function home(Request $request): Response
	{
		$user = $this->user($request);
		if (!$user)
			return $this->redirectToRoute("login");

		if ($request->isMethod("POST")) {
			return $this->redirectToRoute("home");
		}

		$cart = Database::getCart($user->id);

		$total = 0;
		foreach ($cart as $item)
			$total += $item->price * $item->quantity;

		return $this->finish($request, "checkout.html.twig", [
			"cart" => $cart,
			"total" => $total,
		]);
	}
}
