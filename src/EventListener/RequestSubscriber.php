<?php

// namespace App\EventListener;


// use Psr\Log\LoggerInterface;
// use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\HttpKernel\Event\ResponseEvent;
// use Symfony\Component\HttpKernel\KernelEvents;

// class RequestSubscriber implements EventSubscriberInterface
// {

//     private LoggerInterface $logger;
    


//     public function __construct(LoggerInterface $logger)
//     {
//         $this->logger=$logger;
//     }

//     public static function getSubscribedEvents(): array
//     {
//         return [
//         KernelEvents::RESPONSE => 'onKernelRequest',
//     ];
//     }

//     public function onKernelRequest(ResponseEvent $event): void
//     {
//         $this->logger->debug(
//            "successfully"
//         );

//     }
// }