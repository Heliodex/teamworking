<?php

namespace App\Controller;

use App\Database;
use App\Log;
use App\Entity\AddProduct;
use App\Form\Type\AddProductType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends Base
{
	#[Route("/admin", methods: ["GET", "POST"], name: "admin")]
	final public function admin(Request $request): Response
	{
		$user = $this->user($request);
		if (!$user)
			return $this->redirectToHome($request);
		if (!$user->admin)
			throw new AccessDeniedHttpException("You do not have permission to access this page.", null, 403);

		$addProduct = new AddProduct();
		$form = $this->createForm(AddProductType::class, $addProduct);

		$finish = fn() => $this->finish($request, "admin.html.twig", [
			"form" => $form,
		]);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$id = Database::addProduct($addProduct);
			if (!$id) {
				$form->addError(new FormError("Failed to add product"));
				return $finish();
			}

			// write to file
			$imageDir = dirname(__DIR__, 2) . "/var/images";
			if (!is_dir($imageDir))
				mkdir($imageDir, 0755, true);

			$addProduct->image->move($imageDir, (string) $id);

			Log::info("Added product with ID $id");
			$this->addFlash("success", "Product added successfully.");
		}

		return $finish();
	}
}