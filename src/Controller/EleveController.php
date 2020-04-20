<?php

namespace App\Controller;

use App\Entity\Eleve;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\XmlFileLoader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;


class EleveController extends AbstractController {
    
    private $serializer;
    
    public function __construct() {
        
        $encoders = [new JsonEncoder()];
        
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getNom();
            },
            ];

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new ObjectNormalizer($classMetadataFactory, null, null, null, null, null, $defaultContext)];
        
        $this->serializer = new Serializer($normalizers, $encoders);
        
    }
    
    /**
     * @Route("/eleve/{id}", name="eleve_show", methods={"GET"})
     */
    public function actionShow(Eleve $eleve) {
        
        $data_normalized = $this->serializer->normalize($eleve, null, ['groups' => 'details_eleve']);
        $data = $this->serializer->serialize($data_normalized, 'json');
        
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    /**
     * @Route("/eleve/raw/{id}", name="eleve_show_raw", methods={"GET"})
     */
    public function actionShowRaw(Eleve $eleve) {
        
        //$data_normalized = $this->serializer->normalize($eleve, null, ['groups' => 'details_eleve']);
        $data = $this->serializer->serialize($eleve, 'json');
        
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    /**
     * @Route("/eleve", name="eleve_show_list", methods={"GET"})
     */
    public function actionList(Request $request) {
        
        $eleves = $this->getDoctrine()->getRepository(Eleve::class)->findAll();
        
        $data_normalized = $this->serializer->normalize($eleves, null, ['groups' => 'list_eleves']);
        $data = $this->serializer->serialize($data_normalized, 'json');
        
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
        
    }
    
    /**
     * @Route("/eleve", name="eleve_create", methods={"POST"})
     */
    public function actionCreate(Request $request) {
        
        $data = $request->getContent();
        
        $eleve = $this->serializer->deserialize($data, Eleve::class,'json');
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($eleve);
        $entityManager->flush();
        
        return new Response('', Response::HTTP_CREATED);
    }
    
    /**
     * @Route("/eleve/{id}", name="eleve_update", methods={"PUT"})
     */
    public function actionUpdate(Request $request, $id) {
        
        /**
         * 
         * @var Eleve $eleve_unserialized
         * @var Eleve $eleve
         */
        $data = $request->getContent();
        $eleve_unserialized = $this->serializer->deserialize($data, Eleve::class,'json');
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $eleve = $entityManager->getRepository(Eleve::class)->find($id);
        
        $eleve->setNom($eleve_unserialized->getNom());
        $eleve->setPrenom($eleve_unserialized->getPrenom());
        $eleve->setDateNaissance($eleve_unserialized->getDateNaissance());
        
        $entityManager->persist($eleve);
        $entityManager->flush();
        
        return $this->redirectToRoute('eleve_show', [
            'eleve' => $eleve, 'id' => $id
        ]);
        
    }
    
    /**
     * @Route("/eleve/{id}", name="eleve_delete", methods={"DELETE"})
     */
    public function actionDelete($id) {
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $eleve = $entityManager->getRepository(Eleve::class)->find($id);
        
        $entityManager->remove($eleve);
        $entityManager->flush();
        
        return new Response('', Response::HTTP_ACCEPTED);
        
    }
    
    
}
