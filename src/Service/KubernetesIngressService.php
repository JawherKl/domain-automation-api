<?php

namespace App\Service;

class KubernetesIngressService
{
    public function createIngress(string $domainName): void
    {
        // Replace dots with hyphens and ensure lowercase
        $validServiceName = strtolower(str_replace('.', '-', $domainName));
        
        // Prepare the Ingress YAML
        $ingressYaml = [
            'apiVersion' => 'networking.k8s.io/v1',
            'kind' => 'Ingress',
            'metadata' => ['name' => "ingress-{$validServiceName}"],
            'spec' => [
                'rules' => [
                    [
                        'host' => $validServiceName,
                        'http' => [
                            'paths' => [
                                [
                                    'path' => '/',
                                    'pathType' => 'Prefix',
                                    'backend' => [
                                        'service' => [
                                            'name' => $validServiceName,
                                            'port' => ['number' => 80],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Convert the array to YAML
        $yaml = yaml_emit($ingressYaml);

        // Save the YAML to a temporary file
        file_put_contents('/tmp/ingress.yaml', $yaml);

        // Apply the Ingress using kubectl
        shell_exec("kubectl apply -f /tmp/ingress.yaml");
    }
}
