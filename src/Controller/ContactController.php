<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function index(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();

            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('contact@paiecash.com')
                ->subject('Message depuis blog Paiecash')
                ->html('<h3>Message envoyé depuis https://www.paiecash.com</h3>'
                    . '<b>Expéditeur :</b> '
                    . $contactFormData['pseudo'] . '<br>'
                    . '<b>E-mail :</b> '
                    . $contactFormData['email']
                    . '<br>' . '<b>Message</b> : <p>'
                    . $contactFormData['message'] . '</p>', 'text/plain');

            $mailer->send($message);

            $message = $translator->trans('Votre message a bien été envoyé ! Merci ! Nous vous répondrons dès que possible.');
            $this->addFlash('message', $message);

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            //'controller_name' => 'ContactController',
            'our_form' => $form->createView(),
            'current_menu' => 'Contact',
        ]);
    }
}
