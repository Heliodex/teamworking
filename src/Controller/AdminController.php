<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends Base
{
	#[Route("/admin", name: "admin")]
	final public function admin(Request $request): Response
	{
		$user = $this->user($request);
		if (!$user)
			return $this->redirectToRoute("home");
		if (!$user->admin) {
			// todo: return 401
		}


		return $this->finish($request, "admin.html.twig", [
			"user" => $user,
		]);
	}
}