<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Exception\{NotFoundHttpException, HttpException};
use Symfony\Component\Routing\Attribute\Route;

final class ProductImageController extends Base
{
	#[Route("/product/{id}", methods: ["GET"], name: "product", options: ["sitemap" => true])]
	final public function product(Request $request, string $id): Response
	{
		$user = $this->user($request);
		if (!$user)
			return $this->redirectToLogin($request);

		// load file
		$path = dirname(__DIR__, 2) . "/var/images/$id";
		if (!is_file($path))
			throw new NotFoundHttpException("Not Found");

		$data = file_get_contents($path);
		if ($data === false)
			throw new HttpException(500, "Internal Server Error");

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mediaType = finfo_buffer($finfo, $data);

		return new Response($data, 200, [
			"Content-Type" => $mediaType,
		]);
	}
}
