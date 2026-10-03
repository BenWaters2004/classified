<?php

namespace App\Services\Screening;

use App\Models\ScreeningCheckType;
use Illuminate\Support\Facades\DB;

class OrganisationScreeningService
{
    /**
     * Display names and ordering for the screening check categories.
     */
    protected $categoryLabels = [
        'criminal_record' => 'Criminal Record Checks',
        'identity' => 'Identity Verification',
        'pre_employment' => 'Pre-employment Screening',
        'financial_screening' => 'Financial / Sanctions Screening',
    ];

    /**
     * Get all active screening check types.
     */
    public function catalogue()
    {
        return ScreeningCheckType::where('active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Return the catalogue grouped for the organisation settings page.
     */
    public function groupedCatalogue()
    {
        $checks = $this->catalogue();

        $groups = [];

        foreach ($checks as $check) {
            $category = $check->category;

            if (!isset($groups[$category])) {
                $groups[$category] = [
                    'key' => $category,
                    'name' => isset($this->categoryLabels[$category])
                        ? $this->categoryLabels[$category]
                        : ucwords(str_replace('_', ' ', $category)),
                    'checks' => [],
                ];
            }

            $groups[$category]['checks'][] = [
                'id' => (int) $check->id,
                'code' => $check->code,
                'name' => $check->name,
                'provider' => $check->provider,
                'description' => $check->description,
            ];
        }

        /*
         * Keep categories in the order defined above.
         * Any future unknown categories are placed afterwards.
         */
        $ordered = [];

        foreach ($this->categoryLabels as $category => $label) {
            if (isset($groups[$category])) {
                $ordered[$category] = $groups[$category];
                unset($groups[$category]);
            }
        }

        foreach ($groups as $category => $group) {
            $ordered[$category] = $group;
        }

        return $ordered;
    }

    /**
     * Determine whether this organisation has explicitly been configured
     * using the V2 screening architecture.
     *
     * Important:
     * Even disabled rows count as configuration. This allows an organisation
     * to intentionally have zero checks enabled.
     */
    public function hasV2Configuration($organisationId)
    {
        return DB::table('organisation_screening_checks')
            ->where('organisation_id', (int) $organisationId)
            ->exists();
    }

    /**
     * Get the V2 screening check codes enabled for an organisation.
     *
     * If the organisation has not yet been configured in V2, we fall back
     * to the legacy allowed_app_types field.
     */
    public function enabledCodes($organisationId)
    {
        $organisationId = (int) $organisationId;

        if ($this->hasV2Configuration($organisationId)) {
            return DB::table('organisation_screening_checks as osc')
                ->join(
                    'screening_check_types as sct',
                    'sct.id',
                    '=',
                    'osc.check_type_id'
                )
                ->where('osc.organisation_id', $organisationId)
                ->where('osc.enabled', 1)
                ->where('sct.active', 1)
                ->orderBy('sct.sort_order')
                ->pluck('sct.code')
                ->map(function ($code) {
                    return (string) $code;
                })
                ->values()
                ->all();
        }

        return $this->legacyEnabledCodes($organisationId);
    }

    /**
     * Check whether a specific screening check is enabled for an organisation.
     */
    public function isEnabled($organisationId, $checkCode)
    {
        return in_array(
            $checkCode,
            $this->enabledCodes($organisationId),
            true
        );
    }

    /**
     * Save the complete V2 screening configuration.
     *
     * A row is stored for every active check, including disabled checks.
     * This is intentional: it means "configured with zero enabled checks"
     * can be distinguished from "not migrated to V2 yet".
     */
    public function syncOrganisationChecks($organisationId, array $selectedCodes)
    {
        $organisationId = (int) $organisationId;

        $selectedCodes = array_values(
            array_unique(
                array_filter(
                    array_map('strval', $selectedCodes)
                )
            )
        );

        $checkTypes = ScreeningCheckType::where('active', 1)
            ->orderBy('sort_order')
            ->get();

        /*
         * Silently ignore codes that do not exist in the catalogue.
         * This prevents a modified POST request creating arbitrary records.
         */
        $validCodes = $checkTypes
            ->pluck('code')
            ->map(function ($code) {
                return (string) $code;
            })
            ->all();

        $selectedCodes = array_values(
            array_intersect($selectedCodes, $validCodes)
        );

        DB::transaction(function () use (
            $organisationId,
            $selectedCodes,
            $checkTypes
        ) {
            foreach ($checkTypes as $checkType) {
                DB::table('organisation_screening_checks')
                    ->updateOrInsert(
                        [
                            'organisation_id' => $organisationId,
                            'check_type_id' => $checkType->id,
                        ],
                        [
                            'enabled' => in_array(
                                $checkType->code,
                                $selectedCodes,
                                true
                            ) ? 1 : 0,

                            'updated_at' => now(),
                        ]
                    );
            }
        });

        return $this->enabledCodes($organisationId);
    }

    /**
     * Initialise one organisation using its current allowed_app_types value.
     *
     * Existing V2 configuration is never overwritten.
     */
    public function initialiseFromLegacy($organisationId)
    {
        $organisationId = (int) $organisationId;

        if ($this->hasV2Configuration($organisationId)) {
            return $this->enabledCodes($organisationId);
        }

        $legacyCodes = $this->legacyEnabledCodes($organisationId);

        $this->syncOrganisationChecks(
            $organisationId,
            $legacyCodes
        );

        return $this->enabledCodes($organisationId);
    }

    /**
     * Read the existing allowed_app_types JSON.
     */
    public function legacyAllowedApplicationTypes($organisationId)
    {
        $organisation = DB::table('organisations')
            ->select([
                'id',
                'allowed_app_types',
            ])
            ->where('id', (int) $organisationId)
            ->first();

        if (!$organisation) {
            return [];
        }

        if (empty($organisation->allowed_app_types)) {
            return [];
        }

        $decoded = json_decode(
            $organisation->allowed_app_types,
            true
        );

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(
            array_unique(
                array_map('strval', $decoded)
            )
        );
    }

    /**
     * Convert current organisation permissions into V2 check codes.
     *
     * Legacy values:
     *
     * DBS
     * BPSS
     * YOTI
     * BPSS_REVAL_ONSITE
     * BPSS_REVAL_OFFSITE
     */
    public function legacyEnabledCodes($organisationId)
    {
        $legacyTypes = $this->legacyAllowedApplicationTypes(
            $organisationId
        );

        $checks = [];

        if (in_array('DBS', $legacyTypes, true)) {
            $checks[] = 'DBS_BASIC';
        }

        /*
         * Onsite/offsite revalidation are workflow variants of BPSS,
         * not separate screening check products in the new architecture.
         */
        if (
            in_array('BPSS', $legacyTypes, true) ||
            in_array('BPSS_REVAL_ONSITE', $legacyTypes, true) ||
            in_array('BPSS_REVAL_OFFSITE', $legacyTypes, true)
        ) {
            $checks[] = 'BPSS';
        }

        if (in_array('YOTI', $legacyTypes, true)) {
            $checks[] = 'YOTI_IDV';
        }

        return array_values(
            array_unique($checks)
        );
    }
}