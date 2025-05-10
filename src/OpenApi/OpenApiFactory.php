<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;

final class OpenApiFactory implements OpenApiFactoryInterface
{

    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @param array<mixed> $context
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        // Add your server URL
        return $openApi->withServers([
            new Model\Server(
                url: 'http://localhost:8080',
                description: 'Local development server'
            ),
        ]);
    }

}
