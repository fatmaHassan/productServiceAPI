<?php


namespace App\Controller;


use App\Entity\Purchased;
use Src\Models\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\PurchasedRepository;

class ProductsAPIController extends AbstractController
{
    private ProductRepository $productRepository;
    private UserRepository $userRepository;
    private PurchasedRepository $purchasedRepository;

    public function __construct(ProductRepository $productRepository, UserRepository $userRepository, PurchasedRepository $purchasedRepository)
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->purchasedRepository = $purchasedRepository;
    }

    /**
     * Delete a purchase of the authenticated user, by passing a sku as a param
     * @Route("/user/products/{sku}", name="add_invitation", methods={"DELETE"})
     * @param $sku
     * @param Request $request
     * @return JsonResponse
     */

    public function deletePurchase($sku, Request $request): JsonResponse
    {

        if (empty($sku)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->purchasedRepository->deleteAllUserPurchaseBySKU($this->getUser()->getId(), $sku);

        return new JsonResponse(['status' => 'purchases deleted'], Response::HTTP_OK);
    }

    /**
     * show authenticated user profile
     * @Route("/user", name="show_current_user_profile", methods={"GET"})
     * @return JsonResponse
     */
    public function showCurrentUser(): JsonResponse
    {

        return new JsonResponse(array('name' => $this->getUser()->getName()), Response::HTTP_OK);
    }

    /**
     * Fetch and show all products
     * @Route("/products", name="get_all_products", methods={"GET"})
     */
    public function getAllProducts(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),

            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Fetch and show user products
     * @Route("/user/products", name="get_user_products", methods={"GET"})
     */
    public function getUserProducts(): JsonResponse
    {
        $products = $this->purchasedRepository->findUserProducts($this->getUser()->getId());
        /* $data = [];

         foreach ($products as $product) {
             $data[] = [
                 'id' => $product->getId(),
                 'sku' => $product->getSku(),
                 'name' => $product->getName(),

             ];
         }*/

        return new JsonResponse($products, Response::HTTP_OK);
    }

    /**
     * Add a product Purchase for a user
     * @Route("/user/products", name="user_add_purchased", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addUserPurchase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        if (empty($data['sku'])) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $purchased = new Purchased();
        $purchased->setUserId($this->getUser()->getId());
        $purchased->setProductSku($data['sku']);
        $entityManager->persist($purchased);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new JsonResponse($purchased->toArray(), Response::HTTP_OK);
    }
}