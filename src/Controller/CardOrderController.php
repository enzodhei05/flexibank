<?php

namespace App\Controller;

use App\Entity\CardOrder;
use App\Form\CardOrderType;
use App\Service\CardGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardOrderController extends AbstractController
{
    private $cardGenerator;

    // Injection du service CardGeneratorService
    public function __construct(CardGeneratorService $cardGenerator)
    {
        $this->cardGenerator = $cardGenerator;
    }

    #[Route('/dashboard', name: 'card_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('card_dashboard');
        }

        return $this->render('card_order/index.html.twig');
    }

    #[Route('/order-card', name: 'order_card', methods: ['GET', 'POST'])]
    public function orderCard(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('order_card');
        }

        $userEmail = $user->getEmail();
        if (!$userEmail) {
            $this->addFlash('error', 'Aucun email trouvé pour cet utilisateur.');
            return $this->redirectToRoute('order_card');
        }

        // Vérifier s'il y a déjà une commande en attente
        $existingOrder = $entityManager->getRepository(CardOrder::class)->findOneBy([
            'userEmail' => $userEmail,
            'status' => 'pending'
        ]);

        if ($existingOrder) {
            $this->addFlash('error', 'Vous avez déjà une commande de carte en attente.');
            return $this->redirectToRoute('order_card');
        }

        // Créer une nouvelle commande de carte
        $cardOrder = new CardOrder();
        $cardOrder->setUserEmail($userEmail);

        // Générer les informations de la carte via le service
        $cardOrder->setCardNumber($this->cardGenerator->generateCardNumber());
        $cardOrder->setCcv($this->cardGenerator->generateCCV());

        // **Passage au format string pour l'expiration**
        $expirationDate = $this->cardGenerator->generateExpirationDate();
        $cardOrder->setExpirationDate($expirationDate);

        // Créer et traiter le formulaire
        $form = $this->createForm(CardOrderType::class, $cardOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cardOrder->setStatus('pending');
            $cardOrder->setOrderDate(new \DateTime());

            // Enregistrer la commande dans la base de données
            $entityManager->persist($cardOrder);
            $entityManager->flush();

            // Ajouter un message de succès
            $this->addFlash('success', 'Votre commande de carte a été passée avec succès !');
            return $this->redirectToRoute('track_card_order');
        }

        return $this->render('card_order/order.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/track-card-order', name: 'track_card_order', methods: ['GET'])]
    public function trackCardOrder(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('track_card_order');
        }

        $userEmail = $user->getEmail();
        $cardOrder = $entityManager->getRepository(CardOrder::class)->findOneBy([
            'userEmail' => $userEmail,
            'status' => 'pending'
        ]);

        if (!$cardOrder) {
            return $this->render('card_order/track.html.twig', [
                'message' => 'Aucune commande en attente.'
            ]);
        }

        return $this->render('card_order/track.html.twig', [
            'cardOrder' => $cardOrder
        ]);
    }

    #[Route('/view-card', name: 'app_view_card')]
    public function viewCard(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cardOrder = $entityManager->getRepository(CardOrder::class)
                           ->findOneBy(['userEmail' => $user->getEmail()]);

        if (!$cardOrder) {
            throw $this->createNotFoundException('Carte non trouvée pour cet utilisateur');
        }

        return $this->render('card_order/view.html.twig', [
            'cardOrder' => $cardOrder
        ]);
    }
}
