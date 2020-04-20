<?php

namespace App\Controller;

use App\Entity\Note;
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
use App\Normalizer\EntityNormalizer;

class NoteController extends AbstractController {
    
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
     * @Route("/note", name="note_create", methods={"POST"})
     */
    public function actionCreate(Request $request) {
        
        /**
         * 
         * @var Note $note
         * @var Eleve $eleve
         */
        
        $data = $request->getContent();
        $entityManager = $this->getDoctrine()->getManager();
        
        var_dump($request->getContent());
        
        $note = $this->serializer->deserialize($data, Note::class,'json');
        
        var_dump($note);
        
        //$eleve = $entityManager->getRepository(Eleve::class)->find($note->getEleve()->getId());
        //$note->setEleve($eleve);
        
        $entityManager->persist($note);
        $entityManager->flush();
        
        return new Response('', Response::HTTP_CREATED);
    }
    
}
