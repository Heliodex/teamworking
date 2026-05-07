<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends Base
{
	#[Route("/admin", name: "admin")]
	final public function admin(Request $request): Response
	{
		$user = $this->user($request);
		if (!$user)
			return $this->redirectToRoute("home");
		if (!$user->admin)
			throw new AccessDeniedHttpException("You do not have permission to access this page.", null, 403);

		return $this->finish($request, "admin.html.twig", [
			"user" => $user,
		]);
	}
}