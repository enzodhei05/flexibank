<?php

namespace App\Controller;

use App\Entity\CardOrder;
use App\Form\BankCardOrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    // Route pour afficher la page de gestion des cartes
    #[Route('/cartes', name: 'app_cartes')]
    public function index(): Response
    {
        return $this->render('card/index.html.twig');
    }

    // Route pour afficher le formulaire de commande de carte
    #[Route('/cartes/commander', name: 'app_cartes_commander')]
    public function orderCard(Request $request, EntityManagerInterface $em): Response
    {
        // Vérification que l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');  // Rediriger vers la page de connexion si non connecté
        }

        $cardOrder = new CardOrder();
        $form = $this->createForm(BankCardOrderType::class, $cardOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Assigner l'email de l'utilisateur connecté à la commande
                $cardOrder->setCustomerEmail($this->getUser()->getEmail()); // Ajout automatique de l'email

                // Persister la commande dans la base de données
                try {
                    $em->persist($cardOrder);
                    $em->flush();

                    $this->addFlash('success', 'Votre commande a été enregistrée avec succès.');
                    return $this->redirectToRoute('app_cartes_suivi');  // Redirection vers la page de suivi
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de votre commande.');
                }
            } else {
                $this->addFlash('error', 'Une erreur est survenue, veuillez vérifier les informations.');
            }
        }

        return $this->render('card/commander_ma_carte.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour afficher le statut de la demande de carte
    #[Route('/cartes/suivi', name: 'app_cartes_suivi')]
    public function cardStatus(EntityManagerInterface $em): Response
    {
        // Vérification que l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');  // Rediriger vers la page de connexion si non connecté
        }

        // Récupérer les commandes de carte pour l'utilisateur connecté
        $cardOrders = $em->getRepository(CardOrder::class)->findBy([
            'customerEmail' => $this->getUser()->getEmail()
        ], [
            'createdAt' => 'DESC'
        ]);

        if (!$cardOrders) {
            $this->addFlash('warning', 'Aucune commande trouvée.');
        }

        return $this->render('card/suivre_ma_demande.html.twig', [
            'cardOrders' => $cardOrders,
        ]);
    }

    // Route pour afficher les informations de la carte
    #[Route('/cartes/ma-carte', name: 'app_ma_carte')]
    public function myCard(EntityManagerInterface $em): Response
    {
        // Vérification que l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');  // Rediriger vers la page de connexion si non connecté
        }

        // Récupérer la commande de carte pour l'utilisateur connecté
        $cardOrder = $em->getRepository(CardOrder::class)->findOneBy([
            'customerEmail' => $this->getUser()->getEmail()
        ]);

        if (!$cardOrder) {
            throw $this->createNotFoundException('Aucune commande trouvée pour cet utilisateur.');
        }

        return $this->render('card/ma_carte.html.twig', [
            'cardOrder' => $cardOrder,
        ]);
    }
}
