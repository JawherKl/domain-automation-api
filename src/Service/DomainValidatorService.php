<?php

namespace App\Service;

class DomainValidatorService
{
    public function validateDNS(string $domainName): bool
    {
        // Exemple de vérification simple pour les enregistrements DNS
        $records = dns_get_record($domainName, DNS_A + DNS_MX + DNS_CNAME);
        return !empty($records);
    }
}
