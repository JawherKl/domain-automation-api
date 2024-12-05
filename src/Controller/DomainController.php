<?php

namespace App\Controller;

use App\Entity\Domain;
use App\Service\DomainValidatorService;
use App\Service\GreenArrowApiService;
use App\Service\KubernetesIngressService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\Attribute\AsController;

class DomainController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private DomainValidatorService $validator;
    private GreenArrowApiService $greenArrow;
    private KubernetesIngressService $k8sIngress;

    public function __construct(
        EntityManagerInterface $entityManager,
        DomainValidatorService $validator,
        GreenArrowApiService $greenArrow,
        KubernetesIngressService $k8sIngress
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->greenArrow = $greenArrow;
        $this->k8sIngress = $k8sIngress;
    }

    /**
     * @Route("/api/domains", name="create_domain", methods={"POST"})
     */
    public function createDomain(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $domainName = $data['name'] ?? '';
        $type = $data['type'] ?? ''; // 'mailing' ou 'tracking'

        if (empty($domainName)) {
            return new JsonResponse(['error' => 'Le nom du domaine est requis.'], 400);
        }

        // Validation DNS
        // Is exicte at hub score
        if (!$this->validator->validateDNS($domainName)) {
            return new JsonResponse(['error' => 'Les enregistrements DNS sont invalides.'], 400);
        }

        // Ajout à la base de données
        $domain = new Domain();
        $domain->setName($domainName);
        $domain->setType($type);
        $domain->setStatus('pending');

        $this->entityManager->persist($domain);
        $this->entityManager->flush();

        // Enregistrement auprès de l'API GreenArrow
        /*if (!$this->greenArrow->registerDomain($domainName, $type)) {
            $domain->setStatus('error');
            $this->entityManager->flush();
            return new JsonResponse(['error' => 'Erreur lors de l\'ajout au serveur GreenArrow.'], 500);
        }*/

        // Configuration de l'Ingress avec Kubernetes
        $this->k8sIngress->createIngress($domainName);

        // Mise à jour du statut (pending) --> seconde cron to active domain
        // traitement des domaines en attente sync et async
        $domain->setStatus('active');
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Domaine créé avec succès.', 'domain' => $domainName], 201);
    }
}

