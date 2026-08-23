<?php

namespace App\Services;

/**
 * Simulated National Hospital Registry service.
 *
 * In production this would call a government/insurance registry API.
 * For the MediFund demo we ship an offline registry table so the
 * fraud engine can evidence-check every claimed hospital.
 */
class HospitalRegistryService
{
    /** Mocked registry entries: normalised name => registration data */
    const REGISTRY = [
        'square hospital'                    => ['reg_no' => 'BD-HOSP-0117', 'city' => 'Dhaka',    'tier' => 'tertiary'],
        'nicvd'                              => ['reg_no' => 'BD-HOSP-0042', 'city' => 'Dhaka',    'tier' => 'specialised'],
        'national institute of cardiovascular diseases' => ['reg_no' => 'BD-HOSP-0042', 'city' => 'Dhaka', 'tier' => 'specialised'],
        'united hospital'                    => ['reg_no' => 'BD-HOSP-0203', 'city' => 'Dhaka',    'tier' => 'tertiary'],
        'evercare hospital'                  => ['reg_no' => 'BD-HOSP-0221', 'city' => 'Dhaka',    'tier' => 'tertiary'],
        'apollo hospital'                    => ['reg_no' => 'BD-HOSP-0221', 'city' => 'Dhaka',    'tier' => 'tertiary'],
        'bsmmu'                              => ['reg_no' => 'BD-HOSP-0001', 'city' => 'Dhaka',    'tier' => 'tertiary'],
        'bangabandhu sheikh mujib medical university' => ['reg_no' => 'BD-HOSP-0001', 'city' => 'Dhaka', 'tier' => 'tertiary'],
        'dhaka medical college hospital'     => ['reg_no' => 'BD-HOSP-0002', 'city' => 'Dhaka',    'tier' => 'tertiary'],
        'chittagong medical college hospital'=> ['reg_no' => 'BD-HOSP-0106', 'city' => 'Chattogram','tier' => 'tertiary'],
        'mount adora hospital'               => ['reg_no' => 'BD-HOSP-0311', 'city' => 'Sylhet',   'tier' => 'secondary'],
        'ibrahim cardiac'                    => ['reg_no' => 'BD-HOSP-0155', 'city' => 'Dhaka',    'tier' => 'specialised'],
        'labaid special hospital'            => ['reg_no' => 'BD-HOSP-0189', 'city' => 'Dhaka',    'tier' => 'tertiary'],
    ];

    /**
     * Look up a hospital claim against the registry.
     *
     * @param  string|null $hospitalName
     * @return array{registered: bool, matched_name: ?string, reg_no: ?string, tier: ?string}
     */
    public static function verify(?string $hospitalName): array
    {
        $normalised = self::normalize($hospitalName);

        if ($normalised === '') {
            return ['registered' => false, 'matched_name' => null, 'reg_no' => null, 'tier' => null];
        }

        /* exact hit */
        if (isset(self::REGISTRY[$normalised])) {
            $entry = self::REGISTRY[$normalised];
            return [
                'registered'   => true,
                'matched_name' => ucwords($normalised),
                'reg_no'       => $entry['reg_no'],
                'tier'         => $entry['tier'],
            ];
        }

        /* partial containment either way */
        foreach (self::REGISTRY as $name => $entry) {
            if (str_contains($normalised, $name) || str_contains($name, $normalised)) {
                return [
                    'registered'   => true,
                    'matched_name' => ucwords($name),
                    'reg_no'       => $entry['reg_no'],
                    'tier'         => $entry['tier'],
                ];
            }
        }

        return ['registered' => false, 'matched_name' => null, 'reg_no' => null, 'tier' => null];
    }

    public static function normalize(?string $name): string
    {
        $name = strtolower(trim((string) $name));
        $name = preg_replace('/[^a-z0-9 ]+/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', trim((string) $name));

        return $name ?? '';
    }
}
