<?php

namespace TrackingBundle\EventListener;

use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Pimcore\Security\User\TokenStorageUserResolver;
use TrackingBundle\Model\AdminActivity;

class TrackingEventListener implements EventSubscriberInterface
{
    use PimcoreContextAwareTrait;
    private LoggerInterface $logger;
    protected TokenStorageUserResolver $userResolver;

    public function __construct(TokenStorageUserResolver $userResolver,LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->userResolver = $userResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest()) {
            return;
        }
        if (!$this->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_ADMIN)) {
            return;
        }

        $adminUserId = $this->extractAdminUserId();
        $timestamp = new \DateTime();

        $activity = new AdminActivity();
        $activity->setAdminId($adminUserId);
        $activity->setAction($request->attributes->get('_route'));
        $activity->setTimestamp($timestamp);
        $activity->save();



        $params = $this->getParams($request);
        $user = $this->userResolver->getUser();
        $this->logger->info($request->attributes->get('_controller'), [
            $user ? $user->getId() : '0',
            $request->attributes->get('_route'),
            $request->attributes->get('_route_params'),
            $params,
        ],
            ['channel' => 'admin_statistics']
        );
    }

    protected function extractAdminUserId(): ?int
    {
        // Implement your logic to extract the admin user ID.
        // You can use the TokenStorage or other sources to get the admin user ID.
        $user = $this->userResolver->getUser();

        if ($user) {
            return $user->getId();
        }

        return 0; // Set a default value if the admin user is not found.
    }

    protected function getParams(Request $request): array
    {
        $params = [];
        $disallowedKeys = ['_dc', 'module', 'controller', 'action', 'password'];

        // TODO is this enough?
        $requestParams = array_merge(
            $request->query->all(),
            $request->request->all()
        );

        foreach ($requestParams as $key => $value) {
            if (is_json($value)) {
                $value = json_decode($value);
                if (is_array($value)) {
                    array_walk_recursive($value, function (&$item, $key) {
                        if (str_contains((string)$key, 'pass')) {
                            $item = '*************';
                        }
                    });
                }

                $value = json_encode($value);
            }

            if (!in_array($key, $disallowedKeys) && is_string($value)) {
                $params[$key] = (strlen($value) > 40) ? substr($value, 0, 40) . '...' : $value;
            }
        }

        return $params;
    }

}
