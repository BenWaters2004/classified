<?php

namespace App\Services\Screening;

use App\Models\ScreeningRequest;
use App\Models\ScreeningRequestForm;
use Illuminate\Support\Facades\DB;

class CandidateSectionResolver
{
    /**
     * Resolve all forms required by the checks belonging to a
     * screening request.
     *
     * If multiple checks require the same form, that form appears once.
     */
    public function requiredSections($screeningRequestId)
    {
        $screeningRequestId = (int) $screeningRequestId;

        $requirements = DB::table('screening_request_checks as src')

            ->join(
                'screening_check_types as sct',
                'sct.id',
                '=',
                'src.check_type_id'
            )

            ->join(
                'screening_check_form_requirements as scfr',
                'scfr.check_type_id',
                '=',
                'src.check_type_id'
            )

            ->where(
                'src.screening_request_id',
                $screeningRequestId
            )

            /*
             * A cancelled check should no longer make its forms required.
             */
            ->where(
                'src.status',
                '<>',
                'cancelled'
            )

            ->where(
                'sct.active',
                1
            )

            ->select([
                'scfr.form_key',
                'scfr.required',
                'scfr.sort_order',

                'sct.code as check_code',
                'sct.name as check_name',
            ])

            ->orderBy('scfr.sort_order')
            ->get();

        $sections = [];

        foreach ($requirements as $requirement) {

            $formKey = $requirement->form_key;

            if (!isset($sections[$formKey])) {

                $sections[$formKey] = [
                    'form_key' => $formKey,

                    'required' =>
                        (bool) $requirement->required,

                    'sort_order' =>
                        (int) $requirement->sort_order,

                    'check_codes' => [],

                    'checks' => [],
                ];
            }

            /*
             * If any check requires this section, the section is required.
             */
            if ((bool) $requirement->required) {
                $sections[$formKey]['required'] = true;
            }

            /*
             * Use the earliest requested position.
             */
            $sections[$formKey]['sort_order'] = min(
                $sections[$formKey]['sort_order'],
                (int) $requirement->sort_order
            );

            if (
                !in_array(
                    $requirement->check_code,
                    $sections[$formKey]['check_codes'],
                    true
                )
            ) {
                $sections[$formKey]['check_codes'][] =
                    $requirement->check_code;

                $sections[$formKey]['checks'][] = [
                    'code' => $requirement->check_code,
                    'name' => $requirement->check_name,
                ];
            }
        }

        $sections = array_values($sections);

        usort(
            $sections,
            function ($a, $b) {

                if ($a['sort_order'] === $b['sort_order']) {
                    return strcmp(
                        $a['form_key'],
                        $b['form_key']
                    );
                }

                return $a['sort_order'] <=> $b['sort_order'];
            }
        );

        return $sections;
    }

    /**
     * Synchronise the required sections into screening_request_forms.
     *
     * This never deletes progress.
     *
     * If a check is later removed, its unused forms are simply marked
     * required = 0 rather than deleted.
     */
    public function syncRequestForms($screeningRequestId)
    {
        $screeningRequestId = (int) $screeningRequestId;

        $request = ScreeningRequest::find(
            $screeningRequestId
        );

        if (!$request) {
            return [];
        }

        $sections = $this->requiredSections(
            $screeningRequestId
        );

        $requiredKeys = [];

        DB::transaction(function () use (
            $screeningRequestId,
            $sections,
            &$requiredKeys
        ) {

            foreach ($sections as $section) {

                $formKey = $section['form_key'];

                $requiredKeys[] = $formKey;

                $form = ScreeningRequestForm::firstOrCreate(
                    [
                        'screening_request_id' =>
                            $screeningRequestId,

                        'form_key' =>
                            $formKey,
                    ],
                    [
                        'status' =>
                            'not_started',

                        'required' =>
                            $section['required'] ? 1 : 0,

                        'sort_order' =>
                            $section['sort_order'],
                    ]
                );

                /*
                 * Update requirement metadata without touching progress.
                 */
                $changed = false;

                if (
                    (bool) $form->required !==
                    (bool) $section['required']
                ) {
                    $form->required =
                        $section['required'] ? 1 : 0;

                    $changed = true;
                }

                if (
                    (int) $form->sort_order !==
                    (int) $section['sort_order']
                ) {
                    $form->sort_order =
                        (int) $section['sort_order'];

                    $changed = true;
                }

                if ($changed) {
                    $form->save();
                }
            }

            /*
             * Forms which were previously required but are no longer
             * needed are retained for audit/history purposes.
             */
            $query = ScreeningRequestForm::where(
                'screening_request_id',
                $screeningRequestId
            );

            if (!empty($requiredKeys)) {

                $query->whereNotIn(
                    'form_key',
                    $requiredKeys
                );
            }

            $query->update([
                'required' => 0,
            ]);
        });

        return $this->sectionsWithProgress(
            $screeningRequestId
        );
    }

    /**
     * Return resolved sections with candidate progress attached.
     */
    public function sectionsWithProgress($screeningRequestId)
    {
        $screeningRequestId = (int) $screeningRequestId;

        $requirements = $this->requiredSections(
            $screeningRequestId
        );

        $progress = ScreeningRequestForm::where(
            'screening_request_id',
            $screeningRequestId
        )
            ->get()
            ->keyBy('form_key');

        $sections = [];

        foreach ($requirements as $section) {

            $form = $progress->get(
                $section['form_key']
            );

            $section['id'] =
                $form ? (int) $form->id : null;

            $section['status'] =
                $form
                    ? $form->status
                    : 'not_started';

            $section['started_at'] =
                $form
                    ? $form->started_at
                    : null;

            $section['completed_at'] =
                $form
                    ? $form->completed_at
                    : null;

            $sections[] = $section;
        }

        return $sections;
    }

    /**
     * Determine whether all required V2 forms are complete.
     */
    public function allRequiredFormsComplete($screeningRequestId)
    {
        $remaining = ScreeningRequestForm::where(
            'screening_request_id',
            (int) $screeningRequestId
        )
            ->where('required', 1)
            ->where('status', '<>', 'completed')
            ->count();

        return $remaining === 0;
    }

    /**
     * Return progress figures for dashboards/sidebar.
     */
    public function progress($screeningRequestId)
    {
        $forms = ScreeningRequestForm::where(
            'screening_request_id',
            (int) $screeningRequestId
        )
            ->where('required', 1)
            ->get();

        $total = $forms->count();

        $completed = $forms
            ->where('status', 'completed')
            ->count();

        $percentage = $total > 0
            ? (int) round(
                ($completed / $total) * 100
            )
            : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'remaining' => max(
                0,
                $total - $completed
            ),
            'percentage' => $percentage,
        ];
    }
}