<?php

namespace App\Services;

use function Laravel\Prompts\text;

require_once base_path('app/Services/ripcord.php');

class OdooService
{
    protected $url;
    protected $db;
    protected $username;
    protected $password;
    protected $uid;
    protected $common;
    protected $object;

    public function __construct()
    {
        $this->url = config('odoo.url');
        $this->db = config('odoo.db');
        $this->username = config('odoo.username');
        $this->password = config('odoo.password');

        $this->common = \ripcord::client("{$this->url}/xmlrpc/2/common");
        $this->uid = $this->common->authenticate($this->db, $this->username, $this->password, []);
        $this->object = \ripcord::client("{$this->url}/xmlrpc/2/object");
    }



    public function createContact(string $name, string $phone, string $email = null, array $extra = [])
    {
        $data = array_merge([
            'name'          => $name,
            'phone'         => $phone,
            'email'         => $email,
            'company_type'  => 'person',
            'is_company'    => false,
        ], $extra);


        $data = array_filter($data, fn($value) => !is_null($value) && $value !== '');

        return $this->object->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            'res.partner',
            'create',
            [$data]
        );
    }

    public function syncContact(string $number, array $data)
    {
        $variants = $this->generateNumberVariants($number);

        foreach ($variants as $variant) {
            $found = $this->object->execute_kw(
                $this->db,
                $this->uid,
                $this->password,
                'res.partner',
                'search_read',
                [[
                    ['phone', '=', $variant]
                ]],
                ['fields' => ['id'], 'limit' => 1]
            );

            if (!empty($found)) {
                $id = $found[0]['id'];

                $this->object->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'res.partner',
                    'write',
                    [[$id], $data]
                );

                return ['status' => 'updated', 'id' => $id];
            }
        }

        $data['phone'] = $number;

        $newId = $this->object->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            'res.partner',
            'create',
            [$data]
        );

        return ['status' => 'created', 'id' => $newId];
    }

    public function searchContactByNumber(string $rawNumber)
    {
        $variants = $this->generateNumberVariants($rawNumber);
        foreach ($variants as $number) {
            try {
                $results = $this->object->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'res.partner',
                    'search_read',
                    [[
                        '|',
                        ['phone', '=', $number],
                        ['mobile', '=', $number]
                    ]],
                    ['fields' => ['id', 'name', 'phone', 'mobile', 'email'], 'limit' => 1]
                );
                if (!empty($results)) {
                    return $results[0];
                }
            } catch (\Exception $e) {
                $results = $this->object->execute_kw(
                    $this->db,
                    $this->uid,
                    $this->password,
                    'res.partner',
                    'search_read',
                    [[
                        ['phone', '=', $number]
                    ]],
                    ['fields' => ['id', 'name', 'phone', 'email'], 'limit' => 1]
                );
                if (!empty($results)) {
                    return $results[0];
                }
            }
        }

        return null;
    }

    public function updateContact(int $id, array $data): bool
    {
        $data = array_filter($data, fn($value) => !is_null($value) && $value !== '');
        $result = $this->object->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            'res.partner',
            'write',
            [[$id], $data]
        );
        return $result !== false;
    }

    public function deleteContact(int $id): bool
    {
        return $this->object->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            'res.partner',
            'unlink',
            [[$id]]
        );
    }

    private function generateNumberVariants(string $number): array
    {
        $variants = [];

        $clean = preg_replace('/[^\d+]/', '', $number);

        if (strpos($clean, '+') !== 0) {
            if (strlen($clean) == 11 && substr($clean, 0, 1) == '0') {
                $variants[] = $clean;
                $variants[] = '+88' . ltrim($clean, '0');
            } else {
                $variants[] = $clean;
            }
        } else {
            $variants[] = $clean;
            if (substr($clean, 0, 3) === '+88') {
                $variants[] = '0' . substr($clean, 3);
            }
        }

        return array_unique($variants);
    }
}
