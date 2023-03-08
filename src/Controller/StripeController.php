<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }

    public function frontendStripeAction()
    {
        return $this->render('@Stripe/stripe/FrontStripe.html.twig');
    }

    

    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, MailerInterface $mailer)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $payment_intent = Stripe\Charge::create([
            "amount" =>  100,
            "currency" => "usd",
            "source" => $request->request->get('stripeToken'),
            "description" => "Binaryboxtuts Payment Test"
        ]);

        

        //        if ($payment_intent->status == 'requires_confirmation') {
        //          $payment_intent->confirm();
        //    }
        if ($payment_intent->status == 'succeeded') {
            // Payment succeeded, send confirmation email
            // Send a payment confirmation email
            $email = (new Email())
                ->from('chemkhihejer11@gmail.com')
                ->to('safouene.cs@gmail.com')
                ->subject('Payment Confirmation')
                ->text('Your payment has been processed successfully.');

            $mailer->send($email);

            echo 'Payment successful. Confirmation email sent.';
        } else {
            echo 'Payment failed.';
        }

        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        //return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);

        // Send a payment confirmation email
        $email = (new Email())
            ->from('chemkhihejer11@gmail.com')
            ->to('chemkhihejer11@gmail.com')
            ->subject('Payment Confirmation')
            ->text('Your payment has been processed successfully.');

        $mailer->send($email);

        // Add a flash message to indicate that the payment was successful
        $this->addFlash(
            'success',
            'Payment Successful!'
        );

        // Redirect the user back to the Stripe payment page
        return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
    }
}
