<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\Address\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AddressController
 * @Route("/address")
 */
class AddressController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ChatterInterface
     */
    private $slack;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface        $logger,
        ChatterInterface       $slack
    ) {
        $this->em     = $em;
        $this->logger = $logger;
        $this->slack  = $slack;
    }

    /**
     * @Route("/add")
     */
    public function add(
        Request $request
    ) {
        $form = $this->createForm(AddressType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $this->em->persist($address);
            $this->em->flush();

            $this->slack->send(new ChatMessage('New AddressBook entry added: ' . $address->getName()));
            $this->logger->info('New AddressBook entry added: ' . $address->getName());
            $this->addFlash('success', 'New AddressBook entry added: ' . $address->getName());

            return $this->redirectToRoute('app_address_list');
        }

        $data['form'] = $form;

        return $this->renderForm('address/add.html.twig', $data);
    }

    /**
     * @Route("/edit/{address}")
     */
    public function edit(
        Address $address,
        Request $request
    ) {
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // send sms to new addition with MY phone number
            // send an email to new addition with MY email address
            $this->slack->send(new ChatMessage('AddressBook entry edited: ' . $address->getName()));
            $this->logger->info('Saved: ' . $address->getName());
            $this->addFlash('success', 'Saved: ' . $address->getName());

            return $this->redirectToRoute('app_address_list');
        }

        $data['form'] = $form;
        $data['data'] = [
            'address' => $address,
        ];

        return $this->renderForm('address/edit.html.twig', $data);
    }

    /**
     * @Route("/list")
     */
    public function list()
    {

        $data['data'] = [
            'addresses' => $this->em->getRepository(Address::class)->findAll(),
        ];

        return $this->render('address/list.html.twig', $data);

    }

    /**
     * @Route("/view/{address}")
     */
    public function view(Address $address)
    {
        $data['data'] = [
            'address' => $address,
        ];

        return $this->render('address/view.html.twig', $data);
    }

}
