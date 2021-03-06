<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class FrontPanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index()
    {
        $panier = $this->get('session')->get('panier');

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws \Exception
     * @Route("/panier/ajout-produit", name="add-product")

     */
    public function addToPanier(Request $request, EntityManagerInterface $entityManager)
    {
        $produitId = $request->query->get('produitId');
        $quantite = $request->query->get('quantite');
        $price = $request->query->get('price');
        $image = $request->query->get('image');
        $name = $request->query->get('name');

        $session = $this->get('session');

        if(!$session->get('panier')) {
            $session->set('panier', []);
        }

        $panier = $session->get('panier');

        $panier[$produitId]['quantite'] = isset($panier[$produitId]['quantite']) ? $panier[$produitId]['quantite'] + $quantite : $quantite;
        $panier[$produitId]['price'] = $price;
        $panier[$produitId]['image'] = $image;
        $panier[$produitId]['name'] = $name;

        $quantiteGlobale = 0;
        foreach ($panier as $produit) {
            $quantiteGlobale += $produit['quantite'];
        }

        $session->set('panier', $panier);
        $session->set('quantiteGlobale', $quantiteGlobale);


        return new Response($quantiteGlobale);
    }

    /**
     * @return Response
     * @Route("/panier/reset-panier", name="reset-panier")
     */
    public function resetPanier(){

        $session = $this->get('session');
        $panier= [];
        $session->set('panier', $panier);
        $session->set('quantiteGlobale', 0);
        return new Response();

    }
    //
//        $quantite = $panier[$produitId]['quantite'];
//
//        // Insertion en base
//        /** @var User $user */
//        $user = $this->getUser();
//        $panier = $user->getPanier();
//        if(!$panier) {
//            $panier = new Commande();
//            $panier->setUser($user)->setCreatedAt(new \DateTime('now'));
//        }
//        $entityManager->persist($panier);
//        $entityManager->flush();
//
//        $productRepository = $entityManager->getRepository(Product::class);
//        $product = $productRepository->find($produitId);
//
//        $panierProductProduct = $entityManager->getRepository(LigneCommande::class);
//        $panierProduct = $panierProductProduct->findOneBy([
//            'panier'    => $panier,
//            'product'   => $product,
//        ]);
//
//        $panierProduct = $panierProduct ? : new LigneCommande();
//        $panierProduct
//            ->setPanier($panier)
//            ->setProduct($product)
//            ->setQuantity($quantite)
//        ;
//
//        $entityManager->persist($panierProduct);
//        $entityManager->flush();
}

