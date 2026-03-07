<?php

namespace App\Services;

class CustomerAuthService
{
    public function allAccounts(): array
    {
        return array_values(config('customers', []));
    }

    public function attempt(string $username, string $password): ?array
    {
        foreach ($this->allAccounts() as $account) {
            if (
                ($account['username'] ?? '') === $username &&
                ($account['password'] ?? '') === $password
            ) {
                return $this->publicProfile($account);
            }
        }

        return null;
    }

    public function findById(string $id): ?array
    {
        foreach ($this->allAccounts() as $account) {
            if (($account['id'] ?? '') === $id) {
                return $this->publicProfile($account);
            }
        }

        return null;
    }

    private function publicProfile(array $account): array
    {
        return [
            'id' => (string) ($account['id'] ?? ''),
            'username' => (string) ($account['username'] ?? ''),
            'name' => (string) ($account['name'] ?? ''),
            'contact' => (string) ($account['contact'] ?? ''),
            'address' => (string) ($account['address'] ?? ''),
        ];
    }
}
