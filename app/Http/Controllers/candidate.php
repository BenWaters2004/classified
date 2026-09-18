<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class candidate extends Controller
{
    private const NOT_REQUIRED = 0;
    private const INCOMPLETE = 1;
    private const COMPLETE = 2;

    private array $columnCache = [];

    /* -----------------------------------------------------------------
     | Shared helpers
     | -----------------------------------------------------------------
     */

    private function requireCandidate(): int
    {
        if (!Auth::check()) {
            abort(401);
        }

        $user = Auth::user();

        // The existing application uses userType=applicant for candidates.
        if (isset($user->userType) && $user->userType !== 'applicant') {
            abort(403);
        }

        return (int) $user->id;
    }

    private function columns(string $table): array
    {
        if (!array_key_exists($table, $this->columnCache)) {
            $this->columnCache[$table] = Schema::hasTable($table)
                ? Schema::getColumnListing($table)
                : [];
        }

        return $this->columnCache[$table];
    }

    private function onlyExistingColumns(string $table, array $payload): array
    {
        $columns = array_flip($this->columns($table));
        return array_intersect_key($payload, $columns);
    }

    private function firstExistingColumn(string $table, array $candidates): ?string
    {
        $columns = array_flip($this->columns($table));

        foreach ($candidates as $candidate) {
            if (isset($columns[$candidate])) {
                return $candidate;
            }
        }

        return null;
    }

    private function putFirstExistingColumn(string $table, array &$payload, array $candidates, $value): void
    {
        $column = $this->firstExistingColumn($table, $candidates);
        if ($column !== null) {
            $payload[$column] = $value;
        }
    }

    private function baseUser(int $userId)
    {
        $user = DB::table('users')
            ->join('organisations', 'organisations.id', '=', 'users.organisationID')
            ->select(
                'users.*',
                'organisations.organisationName',
                'organisations.logo',
                'organisations.brand_color'
            )
            ->where('users.id', $userId)
            ->first();

        abort_unless($user, 404);
        return $user;
    }

    private function ensureCandidateForms($user)
    {
        $form = DB::table('candidateForms')->where('userID', $user->id)->first();

        if (!$form) {
            $yoti = (int) ($user->useYoti ?? 0);

            DB::table('candidateForms')->insert([
                'userID'          => $user->id,
                'aboutYou'        => self::INCOMPLETE,
                'basicDBS'        => ((int) ($user->dbsApplication ?? 0) === 1) ? self::INCOMPLETE : self::NOT_REQUIRED,
                'YotiVerifcation' => $yoti === 0 ? self::NOT_REQUIRED : ($yoti === 2 ? self::COMPLETE : self::INCOMPLETE),
                'employmentRef'   => ((int) ($user->bpssApplication ?? 0) === 1) ? self::INCOMPLETE : self::NOT_REQUIRED,
                'academicRef'     => ((int) ($user->bpssApplication ?? 0) === 1) ? self::INCOMPLETE : self::NOT_REQUIRED,
                'personalRef'     => ((int) ($user->bpssApplication ?? 0) === 1) ? self::INCOMPLETE : self::NOT_REQUIRED,
                'supportingDocs'  => self::INCOMPLETE,
                'complete'        => 0,
            ]);

            $form = DB::table('candidateForms')->where('userID', $user->id)->first();
        }

        // Keep requirements in step with the application's requested checks without
        // resetting sections that the candidate has already completed.
        $updates = [];

        $dbsRequired = (int) ($user->dbsApplication ?? 0) === 1;
        if (!$dbsRequired && (int) $form->basicDBS !== self::NOT_REQUIRED) {
            $updates['basicDBS'] = self::NOT_REQUIRED;
        } elseif ($dbsRequired && (int) $form->basicDBS === self::NOT_REQUIRED) {
            $updates['basicDBS'] = self::INCOMPLETE;
        }

        $bpssRequired = (int) ($user->bpssApplication ?? 0) === 1;
        foreach (['employmentRef', 'academicRef', 'personalRef'] as $key) {
            if (!$bpssRequired && (int) $form->{$key} !== self::NOT_REQUIRED) {
                $updates[$key] = self::NOT_REQUIRED;
            } elseif ($bpssRequired && (int) $form->{$key} === self::NOT_REQUIRED) {
                $updates[$key] = self::INCOMPLETE;
            }
        }

        $yoti = (int) ($user->useYoti ?? 0);
        if ($yoti === 0) {
            $updates['YotiVerifcation'] = self::NOT_REQUIRED;
        } elseif ($yoti === 2) {
            $updates['YotiVerifcation'] = self::COMPLETE;
        } elseif ((int) $form->YotiVerifcation === self::NOT_REQUIRED) {
            $updates['YotiVerifcation'] = self::INCOMPLETE;
        }

        if (!empty($updates)) {
            DB::table('candidateForms')->where('userID', $user->id)->update($updates);
            $form = DB::table('candidateForms')->where('userID', $user->id)->first();
        }

        return $form;
    }

    private function ensureApplication($user)
    {
        $application = DB::table('applications')->where('userID', $user->id)->first();

        if (!$application) {
            $payload = $this->onlyExistingColumns('applications', [
                'userID'            => $user->id,
                'organisationID'    => $user->organisationID ?? null,
                'createdOn'         => now(),
                'createdBy'         => $user->id,
                'forename'          => $user->firstName ?? '',
                'surname'           => $user->lastName ?? '',
                'presentSurname'    => $user->lastName ?? '',
                'application_email' => $user->email ?? '',
                'contact_number'    => $user->phoneNumber ?? '',
                'applicationStatus' => 0,
            ]);

            $id = DB::table('applications')->insertGetId($payload);
            $application = DB::table('applications')->where('id', $id)->first();
        }

        return $application;
    }

    private function ensureBpssApplication($user)
    {
        if (!Schema::hasTable('bpss_applications')) {
            return null;
        }

        $application = DB::table('bpss_applications')->where('userID', $user->id)->first();

        if (!$application && (int) ($user->bpssApplication ?? 0) === 1) {
            $payload = $this->onlyExistingColumns('bpss_applications', [
                'userID'          => $user->id,
                'organisationID'  => $user->organisationID ?? null,
                'start_date'      => now(),
                'createdOn'       => now(),
                'applicationStatus' => 0,
            ]);

            $id = DB::table('bpss_applications')->insertGetId($payload);
            $application = DB::table('bpss_applications')->where('id', $id)->first();
        }

        return $application;
    }

    private function mergeUserAndApplication($user, $application): object
    {
        $data = (array) $user;
        $userId = $user->id;
        $userCompleted = $user->completed ?? 0;

        if ($application) {
            foreach ((array) $application as $key => $value) {
                if ($key === 'id') {
                    $data['applicationID'] = $value;
                } else {
                    $data[$key] = $value;
                }
            }
        }

        // Do not allow joined application columns to hide the user identity/status.
        $data['id'] = $userId;
        $data['userID'] = $userId;
        $data['completed'] = $userCompleted;

        return (object) $data;
    }

    private function context(bool $needApplication = true, bool $needBpss = false): array
    {
        $userId = $this->requireCandidate();
        $user = $this->baseUser($userId);
        $forms = $this->ensureCandidateForms($user);
        $application = $needApplication ? $this->ensureApplication($user) : DB::table('applications')->where('userID', $userId)->first();
        $bpss = $needBpss ? $this->ensureBpssApplication($user) : null;
        $userDetails = $this->mergeUserAndApplication($user, $application);

        return compact('userId', 'user', 'forms', 'application', 'bpss', 'userDetails') + [
            'brandColor' => $user->brand_color ?? '#C55359',
        ];
    }

    private function lockedResponse($user, $forms)
    {
        if ((int) ($forms->complete ?? 0) === 1 || (int) ($user->completed ?? 0) === 1) {
            return redirect()->route('candidate.dashboard');
        }

        return null;
    }

    private function nextIncompleteRoute($forms, string $currentKey): string
    {
        $steps = [
            'aboutYou'        => 'candidate.personal.edit',
            'basicDBS'        => 'candidate.dbsbasic.edit',
            'YotiVerifcation' => 'candidate.identity.edit',
            'employmentRef'   => 'candidate.employment.edit',
            'academicRef'     => 'candidate.academic.edit',
            'personalRef'     => 'candidate.references.personal.edit',
            'supportingDocs'  => 'candidate.supporting.edit',
        ];

        $keys = array_keys($steps);
        $start = array_search($currentKey, $keys, true);
        $ordered = [];

        if ($start !== false) {
            $ordered = array_merge(array_slice($keys, $start + 1), array_slice($keys, 0, $start + 1));
        } else {
            $ordered = $keys;
        }

        foreach ($ordered as $key) {
            if ((int) ($forms->{$key} ?? self::NOT_REQUIRED) === self::INCOMPLETE) {
                return $steps[$key];
            }
        }

        return 'candidate.submit.review';
    }

    private function ymd($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function valueFrom($row, array $names, $default = null)
    {
        if (!$row) {
            return $default;
        }

        foreach ($names as $name) {
            if (property_exists($row, $name) && $row->{$name} !== null) {
                return $row->{$name};
            }
        }

        return $default;
    }

    private function hasContinuousCoverage(array $intervals, Carbon $from, Carbon $to): bool
    {
        $normalised = [];

        foreach ($intervals as $interval) {
            if (empty($interval[0]) || empty($interval[1])) {
                continue;
            }

            try {
                $start = Carbon::parse($interval[0])->startOfDay();
                $end = Carbon::parse($interval[1])->endOfDay();
            } catch (\Throwable $e) {
                continue;
            }

            if ($end->lt($from) || $start->gt($to)) {
                continue;
            }

            if ($start->lt($from)) {
                $start = $from->copy();
            }
            if ($end->gt($to)) {
                $end = $to->copy();
            }

            $normalised[] = [$start, $end];
        }

        if (empty($normalised)) {
            return false;
        }

        usort($normalised, fn ($a, $b) => $a[0]->timestamp <=> $b[0]->timestamp);

        $cursor = $from->copy();
        foreach ($normalised as [$start, $end]) {
            if ($start->gt($cursor->copy()->addDay())) {
                return false;
            }

            if ($end->gt($cursor)) {
                $cursor = $end->copy();
            }

            if ($cursor->gte($to)) {
                return true;
            }
        }

        return $cursor->gte($to);
    }

    private function rowsOverlap(array $rows): bool
    {
        $count = count($rows);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                try {
                    $aStart = Carbon::parse($rows[$i]['from'])->startOfDay();
                    $aEnd = Carbon::parse($rows[$i]['to'])->endOfDay();
                    $bStart = Carbon::parse($rows[$j]['from'])->startOfDay();
                    $bEnd = Carbon::parse($rows[$j]['to'])->endOfDay();
                } catch (\Throwable $e) {
                    continue;
                }

                if ($aStart->lte($bEnd) && $bStart->lte($aEnd)) {
                    return true;
                }
            }
        }

        return false;
    }

    /* -----------------------------------------------------------------
     | Welcome / personal details
     | -----------------------------------------------------------------
     */

    public function candidateWelcome()
    {
        $ctx = $this->context(true, true);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        return view('candidate.candidateWelcome', [
            'userDetails'    => $ctx['userDetails'],
            'formTitle'      => 'Welcome',
            'RequiredChecks' => $ctx['forms'],
            'brandColor'     => $ctx['brandColor'],
        ]);
    }

    public function candidatePersonalDetails()
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        $titles = DB::table('user_titles')->orderBy('userTitle')->get(['userTitle', 'id']);
        $countries = DB::table('countries')->orderBy('nicename')->get(['id', 'iso', 'iso3', 'name', 'nicename', 'numcode', 'phonecode']);
        $phoneCodes = $countries->pluck('phonecode')->filter()->unique()->sort()->values();

        $prevNames = DB::table('application_extra_names')
            ->where('applicationID', $ctx['application']->id)
            ->orderBy('id')
            ->get(['id', 'applicationID', 'other_forename', 'other_middlename', 'other_surname', 'dateFrom', 'dateTo', 'createdBy', 'createdOn']);

        return view('candidate.candidatePersonalDetails', [
            'userDetails'    => $ctx['userDetails'],
            'formTitle'      => 'Personal Details',
            'RequiredChecks' => $ctx['forms'],
            'titles'         => $titles,
            'countries'      => $countries,
            'phoneCodes'     => $phoneCodes,
            'prevNames'      => $prevNames,
            'brandColor'     => $ctx['brandColor'],
        ]);
    }

    public function savePersonal(Request $request)
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        $countries = DB::table('countries')->get(['iso3', 'phonecode']);
        $iso3Allowed = $countries->pluck('iso3')->filter()->unique()->values()->all();
        $phoneCodesAll = $countries->pluck('phonecode')->filter()->unique()->map(fn ($c) => (string) $c)->values()->all();

        $nameRegex = "/^[\\pL\\pM .’'\\-]+$/u";

        $rules = [
            'title'             => ['required', 'integer', 'exists:user_titles,id'],
            'sex'               => ['required', Rule::in(['male', 'female'])],
            'dob'               => ['required', 'date', 'before_or_equal:today'],
            'birth_town'        => ['required', 'string', 'max:100'],
            'birth_country'     => ['required', Rule::in($iso3Allowed)],
            'birth_nationality' => ['required', 'string', 'max:100'],
            'forename'          => ['required', 'string', 'max:100', 'regex:' . $nameRegex],
            'middlename'        => ['nullable', 'string', 'max:100', 'regex:' . $nameRegex],
            'surname'           => ['required', 'string', 'max:100', 'regex:' . $nameRegex],
            'email'             => ['required', 'email:rfc', 'max:190'],
            'contact_country_code' => ['required', Rule::in($phoneCodesAll)],
            'contact_number'       => ['required', 'string', 'max:20', 'regex:/^[0-9 ()+\-]+$/'],
            'mobile_country_code'  => ['nullable', Rule::in($phoneCodesAll)],
            'mobile_number'        => ['nullable', 'string', 'max:20', 'regex:/^[0-9 ()+\-]+$/'],
            'address_line1' => ['required', 'string', 'max:150'],
            'address_line2' => ['nullable', 'string', 'max:150'],
            'city'          => ['required', 'string', 'max:100'],
            'county'        => ['nullable', 'string', 'max:100'],
            'postcode'      => ['required', 'string', 'max:20'],
            'country'       => ['required', Rule::in($iso3Allowed)],
            'moved_in'      => ['required', 'date', 'before_or_equal:today'],
            'previous_names'               => ['nullable', 'array', 'max:20'],
            'previous_names.*.forename'    => ['nullable', 'string', 'max:100'],
            'previous_names.*.middlename'  => ['nullable', 'string', 'max:100'],
            'previous_names.*.surname'     => ['nullable', 'string', 'max:100'],
            'previous_names.*.from'        => ['nullable', 'date'],
            'previous_names.*.to'          => ['nullable', 'date'],
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($v) use ($request) {
            $dob = $request->filled('dob') ? Carbon::parse($request->input('dob'))->startOfDay() : null;
            $today = now()->endOfDay();

            if ($dob && $request->filled('moved_in')) {
                $movedIn = Carbon::parse($request->input('moved_in'))->startOfDay();
                if ($movedIn->lt($dob)) {
                    $v->errors()->add('moved_in', 'Living here since date cannot be before your date of birth.');
                }
            }

            foreach ((array) $request->input('previous_names', []) as $i => $row) {
                $hasAny = collect(['forename', 'middlename', 'surname', 'from', 'to'])
                    ->contains(fn ($field) => !empty($row[$field]));

                if (!$hasAny) {
                    continue;
                }

                foreach (['forename', 'surname', 'from', 'to'] as $field) {
                    if (empty($row[$field])) {
                        $v->errors()->add("previous_names.$i.$field", ucfirst($field) . ' is required for a previous name.');
                    }
                }

                if (!empty($row['from']) && !empty($row['to'])) {
                    $from = Carbon::parse($row['from'])->startOfDay();
                    $to = Carbon::parse($row['to'])->endOfDay();
                    if ($to->lt($from)) {
                        $v->errors()->add("previous_names.$i.to", 'End date cannot be before start date.');
                    }
                    if ($from->gt($today) || $to->gt($today)) {
                        $v->errors()->add("previous_names.$i.to", 'Previous-name dates cannot be in the future.');
                    }
                    if ($dob && ($from->lt($dob) || $to->lt($dob))) {
                        $v->errors()->add("previous_names.$i.from", 'Previous-name dates cannot be before your date of birth.');
                    }
                }
            }

            if ($request->filled('mobile_number') && !$request->filled('mobile_country_code')) {
                $v->errors()->add('mobile_country_code', 'Please select a country code for the mobile number.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $appId = $ctx['application']->id;
        $contactNumber = preg_replace('/\D+/', '', (string) $request->input('contact_number'));
        $mobileNumber = $request->filled('mobile_number') ? preg_replace('/\D+/', '', (string) $request->input('mobile_number')) : null;

        DB::transaction(function () use ($request, $ctx, $appId, $contactNumber, $mobileNumber) {
            DB::table('users')->where('id', $ctx['userId'])->update($this->onlyExistingColumns('users', [
                'firstName'   => trim($request->input('forename')),
                'lastName'    => trim($request->input('surname')),
                'phoneNumber' => $contactNumber,
            ]));

            DB::table('applications')->where('id', $appId)->update($this->onlyExistingColumns('applications', [
                'title'                       => (int) $request->input('title'),
                'gender'                      => $request->input('sex'),
                'dob'                         => $this->ymd($request->input('dob')),
                'birth_town'                  => trim($request->input('birth_town')),
                'birth_country'               => $request->input('birth_country'),
                'birth_nationality'           => trim($request->input('birth_nationality')),
                'application_email'           => Str::lower(trim($request->input('email'))),
                'forename'                    => trim($request->input('forename')),
                'middlename'                  => trim((string) $request->input('middlename')),
                'presentSurname'              => trim($request->input('surname')),
                'surname'                     => trim($request->input('surname')),
                'contact_number_country_code' => $request->input('contact_country_code'),
                'contact_number'              => $contactNumber,
                'mobile_number_country_code'  => $request->input('mobile_country_code'),
                'mobile_number'               => $mobileNumber,
                'address_line_1'              => trim($request->input('address_line1')),
                'address_line_2'              => trim((string) $request->input('address_line2')),
                'address_town'                => trim($request->input('city')),
                'address_county'              => trim((string) $request->input('county')),
                'address_postcode'            => Str::upper(trim($request->input('postcode'))),
                'address_country'             => $request->input('country'),
                'current_address_from'        => $this->ymd($request->input('moved_in')),
            ]));

            $submitted = collect((array) $request->input('previous_names', []))
                ->filter(function ($row) {
                    return !empty($row['forename']) || !empty($row['middlename']) || !empty($row['surname']) || !empty($row['from']) || !empty($row['to']);
                })
                ->values();

            DB::table('application_extra_names')->where('applicationID', $appId)->delete();

            if ($submitted->isNotEmpty()) {
                $rows = $submitted->map(function ($row) use ($appId, $ctx) {
                    return $this->onlyExistingColumns('application_extra_names', [
                        'applicationID'    => $appId,
                        'other_forename'   => trim((string) ($row['forename'] ?? '')),
                        'other_middlename' => trim((string) ($row['middlename'] ?? '')),
                        'other_surname'    => trim((string) ($row['surname'] ?? '')),
                        'dateFrom'         => $this->ymd($row['from'] ?? null),
                        'dateTo'           => $this->ymd($row['to'] ?? null),
                        'createdBy'        => $ctx['userId'],
                        'createdOn'        => now(),
                    ]);
                })->all();

                DB::table('application_extra_names')->insert($rows);
            }

            DB::table('applications')->where('id', $appId)->update($this->onlyExistingColumns('applications', [
                'previous_names' => $submitted->isNotEmpty() ? 1 : 0,
            ]));

            DB::table('candidateForms')->where('userID', $ctx['userId'])->update(['aboutYou' => self::COMPLETE]);
        });

        $forms = DB::table('candidateForms')->where('userID', $ctx['userId'])->first();
        return redirect()->route($this->nextIncompleteRoute($forms, 'aboutYou'))->with('success', 'Your personal details have been saved.');
    }

    /* -----------------------------------------------------------------
     | Basic DBS
     | -----------------------------------------------------------------
     */

    public function candidateDBSbasic()
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }
        if ((int) $ctx['forms']->basicDBS === self::NOT_REQUIRED) {
            return redirect()->route('candidate.welcome')->with('info', 'A Basic DBS check is not required for this application.');
        }

        $countries = DB::table('countries')->orderBy('nicename')->get(['id', 'iso', 'iso3', 'name', 'nicename']);
        $employmentSectors = DB::table('employment_sectors')->orderBy('employment_sector_name')->get();
        $previousAddresses = DB::table('application_previous_addresses')
            ->where('applicationID', $ctx['application']->id)
            ->orderBy('previous_address_from', 'desc')
            ->get();

        return view('candidate.candidateDBSbasic', [
            'userDetails'       => $ctx['userDetails'],
            'formTitle'         => 'Criminal Record Check',
            'RequiredChecks'    => $ctx['forms'],
            'employmentSectors' => $employmentSectors,
            'countries'         => $countries,
            'previousAddresses' => $previousAddresses,
            'brandColor'        => $ctx['brandColor'],
        ]);
    }


    public function candidateDBSbasicConsentSave(Request $request)
    {
        $ctx = $this->context(true, false);

        if ((int) ($ctx['forms']->basicDBS ?? self::NOT_REQUIRED) === self::NOT_REQUIRED) {
            return response()->json([
                'ok' => false,
                'message' => 'A Basic DBS check is not required for this application.',
            ], 403);
        }

        if ((int) ($ctx['forms']->complete ?? 0) === 1 || (int) ($ctx['user']->completed ?? 0) === 1) {
            return response()->json([
                'ok' => false,
                'message' => 'This application is currently locked and cannot be edited.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'consent_a'   => ['required', 'string', 'max:30'],
            'consent_b'   => ['required', 'string', 'max:30'],
            'consent_c'   => ['required', 'string', 'max:30'],
            'accept_terms'=> ['accepted'],
        ], [
            'consent_a.required'    => 'Please type I CONFIRM to confirm the privacy declaration.',
            'consent_b.required'    => 'Please type I AGREE to agree to the electronic DBS result declaration.',
            'consent_c.required'    => 'Please type I CONFIRM to confirm the applicant declaration.',
            'accept_terms.accepted' => 'Please tick the box to confirm that you accept the declarations above.',
        ]);

        $validator->after(function ($v) use ($request) {
            $normalise = fn ($value) => Str::upper(
                preg_replace('/\s+/', ' ', trim((string) $value))
            );

            if ($normalise($request->input('consent_a')) !== 'I CONFIRM') {
                $v->errors()->add(
                    'consent_a',
                    'Please type I CONFIRM to confirm the privacy declaration.'
                );
            }

            if ($normalise($request->input('consent_b')) !== 'I AGREE') {
                $v->errors()->add(
                    'consent_b',
                    'Please type I AGREE to agree to the electronic DBS result declaration.'
                );
            }

            if ($normalise($request->input('consent_c')) !== 'I CONFIRM') {
                $v->errors()->add(
                    'consent_c',
                    'Please type I CONFIRM to confirm the applicant declaration.'
                );
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Please correct the highlighted consent fields.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $this->onlyExistingColumns('applications', [
            'privacy_policy'           => 1,
            'consent_basic_check'      => 1,
            'declaration_by_applicant' => 1,
            'terms_accepted'           => 1,
        ]);

        DB::table('applications')
            ->where('id', $ctx['application']->id)
            ->update($payload);

        return response()->json([
            'ok' => true,
            'message' => 'Consent saved successfully.',
        ]);
    }

    public function candidateDBSbasicSave(Request $request)
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }
        if ((int) $ctx['forms']->basicDBS === self::NOT_REQUIRED) {
            abort(403);
        }

        $countries = DB::table('countries')->pluck('iso3')->filter()->values()->all();

        $validator = Validator::make($request->all(), [
            'consent_a' => ['required', 'string', 'max:30'],
            'consent_b' => ['required', 'string', 'max:30'],
            'consent_c' => ['required', 'string', 'max:30'],
            'accept_terms' => ['accepted'],
            'purpose' => ['required', Rule::in(['employment'])],
            'employment_sector_id' => ['required', 'integer', 'exists:employment_sectors,id'],
            'position_applied_for' => ['required', 'string', 'max:190'],
            'employer_name' => ['required', 'string', 'max:190'],
            'ni_number' => ['nullable', 'string', 'max:20'],
            'licence_origin' => ['nullable', Rule::in(['0', '1', 0, 1])],
            'driving_licence_number' => ['nullable', 'string', 'max:50'],
            'licence_issue_date' => ['nullable', 'date', 'before_or_equal:today'],
            'passport_country' => ['nullable', Rule::in($countries)],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_issue_date' => ['nullable', 'date', 'before_or_equal:today'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.line1' => ['nullable', 'string', 'max:150'],
            'history.*.line2' => ['nullable', 'string', 'max:150'],
            'history.*.city' => ['nullable', 'string', 'max:100'],
            'history.*.county' => ['nullable', 'string', 'max:100'],
            'history.*.postcode' => ['nullable', 'string', 'max:20'],
            'history.*.country' => ['nullable', Rule::in($countries)],
            'history.*.from' => ['nullable', 'date', 'before_or_equal:today'],
            'history.*.to' => ['nullable', 'date', 'before_or_equal:today'],
            'has_dbs_profile' => ['required', Rule::in(['0', '1', 0, 1])],
            'dbs_profile_id' => ['nullable', 'string', 'max:100'],
            'paper_certificate' => ['required', Rule::in(['0', '1', 0, 1])],
            'paper_address_choice' => ['nullable', Rule::in(['current', 'different'])],
            'paper_recipient' => ['nullable', 'string', 'max:190'],
            'paper_line1' => ['nullable', 'string', 'max:150'],
            'paper_line2' => ['nullable', 'string', 'max:150'],
            'paper_city' => ['nullable', 'string', 'max:100'],
            'paper_county' => ['nullable', 'string', 'max:100'],
            'paper_postcode' => ['nullable', 'string', 'max:20'],
            'paper_country' => ['nullable', Rule::in($countries)],
            'ro_may_view_first' => ['nullable'],
        ]);

        $validator->after(function ($v) use ($request, $ctx) {
            $normaliseConsent = fn ($value) => Str::upper(preg_replace('/\s+/', ' ', trim((string) $value)));

            if ($normaliseConsent($request->input('consent_a')) !== 'I CONFIRM') {
                $v->errors()->add('consent_a', 'Please type I CONFIRM in box A.');
            }
            if ($normaliseConsent($request->input('consent_b')) !== 'I AGREE') {
                $v->errors()->add('consent_b', 'Please type I AGREE in box B.');
            }
            if ($normaliseConsent($request->input('consent_c')) !== 'I CONFIRM') {
                $v->errors()->add('consent_c', 'Please type I CONFIRM in box C.');
            }

            $ni = Str::upper(preg_replace('/\s+/', '', (string) $request->input('ni_number')));
            if ($ni !== '' && !preg_match('/^[A-CEGHJ-PR-TW-Z]{2}\d{6}[A-D]$/', $ni)) {
                $v->errors()->add('ni_number', 'Please enter a valid National Insurance number, for example AB123456C.');
            }

            $dlAny = $request->filled('licence_origin') || $request->filled('driving_licence_number') || $request->filled('licence_issue_date');
            if ($dlAny) {
                foreach (['licence_origin', 'driving_licence_number', 'licence_issue_date'] as $field) {
                    if (!$request->filled($field)) {
                        $v->errors()->add($field, 'Please complete all driving licence fields, or leave all of them blank.');
                    }
                }
            }

            $passportAny = $request->filled('passport_country') || $request->filled('passport_number') || $request->filled('passport_issue_date');
            if ($passportAny) {
                foreach (['passport_country', 'passport_number', 'passport_issue_date'] as $field) {
                    if (!$request->filled($field)) {
                        $v->errors()->add($field, 'Please complete all passport fields, or leave all of them blank.');
                    }
                }
            }

            if ((string) $request->input('has_dbs_profile') === '1' && !$request->filled('dbs_profile_id')) {
                $v->errors()->add('dbs_profile_id', 'Please enter your DBS Profile ID.');
            }

            if ((string) $request->input('paper_certificate') === '1') {
                if (!$request->filled('paper_address_choice')) {
                    $v->errors()->add('paper_address_choice', 'Please choose where the paper certificate should be sent.');
                }

                if ($request->input('paper_address_choice') === 'different') {
                    foreach (['paper_recipient', 'paper_line1', 'paper_city', 'paper_postcode', 'paper_country'] as $field) {
                        if (!$request->filled($field)) {
                            $v->errors()->add($field, 'This field is required for a different certificate address.');
                        }
                    }
                }
            }

            $currentFrom = $ctx['application']->current_address_from ?? null;
            $needsHistory = true;
            if ($currentFrom) {
                try {
                    $needsHistory = Carbon::parse($currentFrom)->gt(now()->subYears(5));
                } catch (\Throwable $e) {
                    $needsHistory = true;
                }
            }

            if ($needsHistory) {
                $history = collect((array) $request->input('history', []))
                    ->filter(fn ($row) => collect($row)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty())
                    ->values();

                if ($history->isEmpty()) {
                    $v->errors()->add('history', 'Please add your previous address history so the last five years are covered.');
                    return;
                }

                $intervals = [];
                if ($currentFrom) {
                    $intervals[] = [$currentFrom, now()->toDateString()];
                }

                foreach ($history as $i => $row) {
                    foreach (['line1', 'city', 'postcode', 'country', 'from', 'to'] as $field) {
                        if (empty($row[$field])) {
                            $v->errors()->add("history.$i.$field", 'This field is required for each previous address.');
                        }
                    }

                    if (!empty($row['from']) && !empty($row['to'])) {
                        $from = Carbon::parse($row['from'])->startOfDay();
                        $to = Carbon::parse($row['to'])->endOfDay();
                        if ($to->lt($from)) {
                            $v->errors()->add("history.$i.to", 'End date cannot be before start date.');
                        }
                        $intervals[] = [$row['from'], $row['to']];
                    }
                }

                if (!$v->errors()->has('history') && !$this->hasContinuousCoverage($intervals, now()->subYears(5)->startOfDay(), now()->endOfDay())) {
                    $v->errors()->add('history', 'Your current and previous addresses must cover the full last five years without gaps.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('show_purpose', true);
        }

        $appId = $ctx['application']->id;
        $paperDifferent = (string) $request->input('paper_certificate') === '1' && $request->input('paper_address_choice') === 'different';

        DB::transaction(function () use ($request, $ctx, $appId, $paperDifferent) {
            $applicationPayload = [
                'privacy_policy'                         => 1,
                'consent_basic_check'                    => 1,
                'declaration_by_applicant'               => 1,
                'terms_accepted'                         => 1,
                'purpose_of_check'                       => $request->input('purpose'),
                'employment_sector'                      => (int) $request->input('employment_sector_id'),
                'position_applied_for'                   => trim($request->input('position_applied_for')),
                'dbs_employer_name'                      => trim($request->input('employer_name')),
                'name_of_employer'                       => trim($request->input('employer_name')),
                'supporting_nino'                        => Str::upper(preg_replace('/\s+/', '', (string) $request->input('ni_number'))),
                'supporting_dln_type'                    => $request->filled('licence_origin') ? (int) $request->input('licence_origin') : null,
                'supporting_dln'                         => trim((string) $request->input('driving_licence_number')) ?: null,
                'supporting_dln_issue_date'              => $this->ymd($request->input('licence_issue_date')),
                'supporting_passport_country'            => $request->input('passport_country') ?: null,
                'supporting_passport'                    => trim((string) $request->input('passport_number')) ?: null,
                'supporting_passport_date'               => $this->ymd($request->input('passport_issue_date')),
                'user_dbs_profile_id_available'          => (int) $request->input('has_dbs_profile'),
                'user_dbs_profile_id'                    => (string) $request->input('has_dbs_profile') === '1' ? trim((string) $request->input('dbs_profile_id')) : null,
                'supporting_paper_certificate'           => (int) $request->input('paper_certificate'),
                'user_paper_certificate_different_address' => $paperDifferent ? 1 : 0,
                'certificate_address_recipient_name'     => $paperDifferent ? trim((string) $request->input('paper_recipient')) : null,
                'certificate_address_line_1'             => $paperDifferent ? trim((string) $request->input('paper_line1')) : null,
                'certificate_address_line_2'             => $paperDifferent ? trim((string) $request->input('paper_line2')) : null,
                'certificate_address_town'               => $paperDifferent ? trim((string) $request->input('paper_city')) : null,
                'certificate_address_county'             => $paperDifferent ? trim((string) $request->input('paper_county')) : null,
                'certificate_address_postcode'           => $paperDifferent ? Str::upper(trim((string) $request->input('paper_postcode'))) : null,
                'certificate_address_country'            => $paperDifferent ? $request->input('paper_country') : null,
                'dbs_consent'                            => $request->has('ro_may_view_first') ? 1 : 0,
            ];

            DB::table('applications')->where('id', $appId)->update($this->onlyExistingColumns('applications', $applicationPayload));

            DB::table('application_previous_addresses')->where('applicationID', $appId)->delete();

            $history = collect((array) $request->input('history', []))
                ->filter(fn ($row) => collect($row)->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty())
                ->values();

            foreach ($history as $row) {
                $payload = $this->onlyExistingColumns('application_previous_addresses', [
                    'applicationID'              => $appId,
                    'previous_address_line_1'    => trim((string) ($row['line1'] ?? '')),
                    'previous_address_line_2'    => trim((string) ($row['line2'] ?? '')),
                    'previous_address_town'      => trim((string) ($row['city'] ?? '')),
                    'previous_address_county'    => trim((string) ($row['county'] ?? '')),
                    'previous_address_postcode'  => Str::upper(trim((string) ($row['postcode'] ?? ''))),
                    'previous_address_country'   => $row['country'] ?? null,
                    'previous_address_from'      => $this->ymd($row['from'] ?? null),
                    'previous_address_to'        => $this->ymd($row['to'] ?? null),
                    'createdBy'                  => $ctx['userId'],
                    'createdOn'                  => now(),
                ]);
                DB::table('application_previous_addresses')->insert($payload);
            }

            DB::table('candidateForms')->where('userID', $ctx['userId'])->update(['basicDBS' => self::COMPLETE]);
        });

        $forms = DB::table('candidateForms')->where('userID', $ctx['userId'])->first();
        return redirect()->route($this->nextIncompleteRoute($forms, 'basicDBS'))->with('success', 'Your Basic DBS details have been saved.');
    }

    /* -----------------------------------------------------------------
     | Identity verification
     | -----------------------------------------------------------------
     */

    public function candidateIdentityVerification()
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }
        if ((int) $ctx['forms']->YotiVerifcation === self::NOT_REQUIRED) {
            return redirect()->route('candidate.welcome')->with('info', 'Digital identity verification is not required for this application.');
        }

        return view('candidate.candidateIDcheck', [
            'userDetails'    => $ctx['userDetails'],
            'formTitle'      => 'Digital Identity Verification',
            'RequiredChecks' => $ctx['forms'],
            'brandColor'     => $ctx['brandColor'],
            'nextRoute'      => route($this->nextIncompleteRoute($ctx['forms'], 'YotiVerifcation')),
        ]);
    }

    /* -----------------------------------------------------------------
     | Employment history
     | -----------------------------------------------------------------
     */

    public function candidateEmploymentHistory()
    {
        $ctx = $this->context(true, true);

        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        if ((int) $ctx['forms']->employmentRef === self::NOT_REQUIRED) {
            return redirect()
                ->route('candidate.welcome')
                ->with('info', 'Employment history is not required for this application.');
        }

        abort_unless($ctx['bpss'], 404);

        $countries = DB::table('countries')
            ->orderBy('nicename')
            ->get(['id', 'iso', 'iso3', 'name', 'nicename']);

        $history = [];

        if (Schema::hasTable('bpss_application_employment_history')) {
            $employmentRows = DB::table('bpss_application_employment_history')
                ->where('applicationID', $ctx['bpss']->id)
                ->orderByDesc('date_from')
                ->get();

            foreach ($employmentRows as $row) {
                $isCurrent = (int) $this->valueFrom(
                    $row,
                    ['is_current', 'current_employment', 'currently_employed'],
                    0
                ) === 1;

                $history[] = [
                    'type'                      => 'employment',
                    'from'                      => $this->ymd($row->date_from ?? null),
                    'to'                        => $isCurrent ? '' : $this->ymd($row->date_to ?? null),
                    'is_current'                => $isCurrent,
                    'company'                   => $row->company_name ?? '',
                    'company_email'             => $row->email_address ?? '',
                    'company_email_unavailable' => (int) $this->valueFrom(
                        $row,
                        ['company_email_unavailable', 'email_unavailable'],
                        0
                    ) === 1,
                    'address'                   => $row->company_address_line ?? '',
                    'town'                      => $row->company_address_town ?? '',
                    'county'                    => $this->valueFrom(
                        $row,
                        ['company_address_county', 'company_address_region', 'company_address_state'],
                        ''
                    ),
                    'postcode'                  => $row->company_address_postcode ?? '',
                    'country'                   => $this->valueFrom(
                        $row,
                        ['company_address_country', 'company_country'],
                        ''
                    ),
                    'contact_referee'           => ((int) ($row->referee_allow_contact ?? 0) === 1) ? 'yes' : 'no',
                    'contact_reason'            => $this->valueFrom(
                        $row,
                        ['referee_contact_reason', 'contact_referee_reason'],
                        ''
                    ),
                    'p60'                       => ((int) ($row->p60_enclosed ?? 0) === 1) ? 'yes' : 'no',
                ];
            }
        }

        if (Schema::hasTable('bpss_application_unemployment')) {
            $gapRows = DB::table('bpss_application_unemployment')
                ->where('applicationID', $ctx['bpss']->id)
                ->orderByDesc('date_from')
                ->get();

            foreach ($gapRows as $row) {
                $benefits = $this->valueFrom(
                    $row,
                    ['claiming_benefits', 'benefits_claimed', 'benefits'],
                    null
                );

                $history[] = [
                    'type'              => 'gap',
                    'from'              => $this->ymd($row->date_from ?? null),
                    'to'                => $this->ymd($row->date_to ?? null),
                    'reason'            => $this->valueFrom(
                        $row,
                        ['reason', 'unemployment_reason', 'reason_for_unemployment'],
                        ''
                    ),
                    'claiming_benefits' => $benefits === null
                        ? ''
                        : (((string) $benefits === '1'
                            || $benefits === 1
                            || Str::lower((string) $benefits) === 'yes') ? 'yes' : 'no'),
                ];
            }
        }

        usort(
            $history,
            fn ($a, $b) => strcmp((string) ($b['from'] ?? ''), (string) ($a['from'] ?? ''))
        );

        return view('candidate.candidateEmploymentHistory', [
            'userDetails'    => $ctx['userDetails'],
            'formTitle'      => 'Employment History',
            'RequiredChecks' => $ctx['forms'],
            'brandColor'     => $ctx['brandColor'],
            'history'        => $history,
            'countries'      => $countries,
        ]);
    }

    public function candidateEmploymentHistorySave(Request $request)
    {
        $ctx = $this->context(true, true);

        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        if ((int) $ctx['forms']->employmentRef === self::NOT_REQUIRED) {
            abort(403);
        }

        abort_unless($ctx['bpss'], 404);

        $request->validate([
            'history_json' => ['required', 'string', 'max:200000'],
        ]);

        try {
            $rows = json_decode(
                $request->input('history_json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'history_json' => 'Employment history could not be read. Please try again.',
                ])
                ->withInput();
        }

        if (!is_array($rows) || empty($rows) || count($rows) > 50) {
            return back()
                ->withErrors([
                    'history_json' => 'Please add your employment history and any other periods for the last five years.',
                ])
                ->withInput();
        }

        $countryCodes = DB::table('countries')
            ->pluck('iso3')
            ->filter()
            ->map(fn ($v) => Str::upper((string) $v))
            ->unique()
            ->values()
            ->all();

        $errors = [];
        $normalised = [];
        $today = now()->endOfDay();

        foreach (array_values($rows) as $i => $row) {
            if (!is_array($row) || !in_array($row['type'] ?? null, ['employment', 'gap'], true)) {
                $errors["history.$i.type"] = 'Each history entry must be employment or another period.';
                continue;
            }

            $type = $row['type'];
            $fromRaw = $row['from'] ?? null;

            if (!$fromRaw) {
                $errors["history.$i.from"] = 'A start date is required for every entry.';
                continue;
            }

            try {
                $from = Carbon::parse($fromRaw)->startOfDay();
            } catch (\Throwable $e) {
                $errors["history.$i.from"] = 'Please enter a valid start date.';
                continue;
            }

            if ($from->gt($today)) {
                $errors["history.$i.from"] = 'A start date cannot be in the future.';
            }

            if ($type === 'employment') {
                $isCurrent = filter_var(
                    $row['is_current'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                $toRaw = $isCurrent ? now()->toDateString() : ($row['to'] ?? null);

                if (!$toRaw) {
                    $errors["history.$i.to"] = 'Enter when this employment ended, or select that you currently work here.';
                    continue;
                }

                try {
                    $to = Carbon::parse($toRaw)->endOfDay();
                } catch (\Throwable $e) {
                    $errors["history.$i.to"] = 'Please enter a valid end date.';
                    continue;
                }

                if ($to->gt($today)) {
                    $errors["history.$i.to"] = 'An end date cannot be in the future.';
                }

                if ($to->lt($from)) {
                    $errors["history.$i.to"] = 'The employment end date cannot be before its start date.';
                }

                $emailUnavailable = filter_var(
                    $row['company_email_unavailable'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                $required = [
                    'company' => 'Employer / company name',
                    'address' => 'Employer address',
                    'town' => 'Town / city',
                    'postcode' => 'Postcode / postal code',
                    'country' => 'Country',
                    'contact_referee' => 'Employer contact permission',
                    'p60' => 'P60 selection',
                ];

                foreach ($required as $field => $label) {
                    if (trim((string) ($row[$field] ?? '')) === '') {
                        $errors["history.$i.$field"] = "$label is required.";
                    }
                }

                $email = Str::lower(trim((string) ($row['company_email'] ?? '')));

                if (!$emailUnavailable && $email === '') {
                    $errors["history.$i.company_email"] =
                        'Enter an employer contact email, or select that you do not have one.';
                } elseif (!$emailUnavailable && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors["history.$i.company_email"] =
                        'Please enter a valid employer email address.';
                }

                $country = Str::upper(trim((string) ($row['country'] ?? '')));
                if ($country !== '' && !in_array($country, $countryCodes, true)) {
                    $errors["history.$i.country"] = 'Please select a valid country.';
                }

                if (!in_array($row['contact_referee'] ?? '', ['yes', 'no'], true)) {
                    $errors["history.$i.contact_referee"] =
                        'Please choose whether this employer may be contacted.';
                }

                $contactReason = trim((string) ($row['contact_reason'] ?? ''));
                if (($row['contact_referee'] ?? '') === 'no' && $contactReason === '') {
                    $errors["history.$i.contact_reason"] =
                        'Please briefly explain why this employer should not be contacted.';
                }

                if (!in_array($row['p60'] ?? '', ['yes', 'no'], true)) {
                    $errors["history.$i.p60"] =
                        'Please choose whether you have uploaded a P60 for this employment.';
                }

                $normalised[] = [
                    'type'                      => 'employment',
                    'from'                      => $from->toDateString(),
                    'to'                        => $to->toDateString(),
                    'is_current'                => $isCurrent,
                    'company'                   => trim((string) ($row['company'] ?? '')),
                    'company_email'             => $emailUnavailable ? '' : $email,
                    'company_email_unavailable' => $emailUnavailable,
                    'address'                   => trim((string) ($row['address'] ?? '')),
                    'town'                      => trim((string) ($row['town'] ?? '')),
                    'county'                    => trim((string) ($row['county'] ?? '')),
                    'postcode'                  => Str::upper(trim((string) ($row['postcode'] ?? ''))),
                    'country'                   => $country,
                    'contact_referee'           => $row['contact_referee'] ?? '',
                    'contact_reason'            => $contactReason,
                    'p60'                       => $row['p60'] ?? '',
                ];
            } else {
                $toRaw = $row['to'] ?? null;

                if (!$toRaw) {
                    $errors["history.$i.to"] = 'An end date is required for this period.';
                    continue;
                }

                try {
                    $to = Carbon::parse($toRaw)->endOfDay();
                } catch (\Throwable $e) {
                    $errors["history.$i.to"] = 'Please enter a valid end date.';
                    continue;
                }

                if ($to->gt($today)) {
                    $errors["history.$i.to"] = 'An end date cannot be in the future.';
                }

                if ($to->lt($from)) {
                    $errors["history.$i.to"] = 'The end date cannot be before the start date.';
                }

                if (trim((string) ($row['reason'] ?? '')) === '') {
                    $errors["history.$i.reason"] =
                        'Please describe what you were doing during this period.';
                }

                if (!in_array($row['claiming_benefits'] ?? '', ['yes', 'no'], true)) {
                    $errors["history.$i.claiming_benefits"] =
                        'Please choose whether you received unemployment-related benefits during this period.';
                }

                $normalised[] = [
                    'type'              => 'gap',
                    'from'              => $from->toDateString(),
                    'to'                => $to->toDateString(),
                    'reason'            => trim((string) ($row['reason'] ?? '')),
                    'claiming_benefits' => $row['claiming_benefits'] ?? '',
                ];
            }
        }

        /*
         * Overlapping employment is valid (for example a full-time role and
         * a weekend job at the same time). Coverage validation below merges
         * overlapping date ranges, so overlaps do not need to be rejected.
         */
        if (empty($errors)) {
            $intervals = array_map(
                fn ($row) => [$row['from'], $row['to']],
                $normalised
            );

            if (!$this->hasContinuousCoverage(
                $intervals,
                now()->subYears(5)->startOfDay(),
                now()->endOfDay()
            )) {
                $errors['history_json'] =
                    'Your employment and other periods must cover the full last five years without unexplained gaps.';
            }
        }

        if (!empty($errors)) {
            return back()
                ->withErrors($errors)
                ->withInput();
        }

        DB::transaction(function () use ($normalised, $ctx) {
            if (Schema::hasTable('bpss_application_employment_history')) {
                DB::table('bpss_application_employment_history')
                    ->where('applicationID', $ctx['bpss']->id)
                    ->delete();
            }

            if (Schema::hasTable('bpss_application_unemployment')) {
                DB::table('bpss_application_unemployment')
                    ->where('applicationID', $ctx['bpss']->id)
                    ->delete();
            }

            foreach ($normalised as $row) {
                if ($row['type'] === 'employment') {
                    $payload = $this->onlyExistingColumns(
                        'bpss_application_employment_history',
                        [
                            'applicationID'                    => $ctx['bpss']->id,
                            'date_from'                        => $row['from'],
                            'date_to'                          => $row['to'],
                            'is_current'                       => $row['is_current'] ? 1 : 0,
                            'company_name'                     => $row['company'],
                            'email_address'                    => $row['company_email'],
                            'company_email_unavailable'        => $row['company_email_unavailable'] ? 1 : 0,
                            'company_address_line'             => $row['address'],
                            'company_address_town'             => $row['town'],
                            'company_address_county'           => $row['county'],
                            'company_address_postcode'         => $row['postcode'],
                            'company_address_country'          => $row['country'],
                            'referee_allow_contact'            => $row['contact_referee'] === 'yes' ? 1 : 0,
                            'referee_contact_reason'           => $row['contact_reason'],
                            'p60_enclosed'                     => $row['p60'] === 'yes' ? 1 : 0,
                            'created_at'                       => now(),
                            'updated_at'                       => now(),
                        ]
                    );

                    DB::table('bpss_application_employment_history')
                        ->insert($payload);
                } else {
                    $payload = $this->onlyExistingColumns(
                        'bpss_application_unemployment',
                        [
                            'applicationID' => $ctx['bpss']->id,
                            'date_from'     => $row['from'],
                            'date_to'       => $row['to'],
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]
                    );

                    $this->putFirstExistingColumn(
                        'bpss_application_unemployment',
                        $payload,
                        ['reason', 'unemployment_reason', 'reason_for_unemployment'],
                        $row['reason']
                    );

                    $this->putFirstExistingColumn(
                        'bpss_application_unemployment',
                        $payload,
                        ['claiming_benefits', 'benefits_claimed', 'benefits'],
                        $row['claiming_benefits'] === 'yes' ? 1 : 0
                    );

                    DB::table('bpss_application_unemployment')
                        ->insert($payload);
                }
            }

            DB::table('candidateForms')
                ->where('userID', $ctx['userId'])
                ->update([
                    'employmentRef' => self::COMPLETE,
                ]);
        });

        $forms = DB::table('candidateForms')
            ->where('userID', $ctx['userId'])
            ->first();

        return redirect()
            ->route($this->nextIncompleteRoute($forms, 'employmentRef'))
            ->with('success', 'Your employment history has been saved.');
    }


    /* -----------------------------------------------------------------
     | Academic history
     | -----------------------------------------------------------------
     */

    public function candidateAcademicHistory()
    {
        $ctx = $this->context(true, true);

        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        if ((int) $ctx['forms']->academicRef === self::NOT_REQUIRED) {
            return redirect()
                ->route('candidate.welcome')
                ->with('info', 'Academic history is not required for this application.');
        }

        abort_unless($ctx['bpss'], 404);

        $countries = DB::table('countries')
            ->orderBy('nicename')
            ->get([
                'id',
                'iso',
                'iso3',
                'name',
                'nicename',
                'phonecode',
            ]);

        $phoneCodes = $countries
            ->pluck('phonecode')
            ->filter()
            ->map(fn ($code) => (string) $code)
            ->unique()
            ->sort(function ($a, $b) {
                if ($a === '44') return -1;
                if ($b === '44') return 1;
                return (int) $a <=> (int) $b;
            })
            ->values();

        $academicHistory = collect();

        if (Schema::hasTable('bpss_application_academic_history')) {
            $academicHistory = DB::table('bpss_application_academic_history')
                ->where('applicationID', $ctx['bpss']->id)
                ->orderByDesc('date_from')
                ->orderByDesc('id')
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => $row->institution_type ?? '',
                        'name' => $row->institution_name ?? '',
                        'course' => $row->course_qualification ?? '',
                        'from' => $this->ymd($row->date_from ?? null),
                        'to' => $this->ymd($row->date_to ?? null),
                        'is_current' => (int) ($row->is_current ?? 0) === 1,

                        'contact_unknown' =>
                            (int) ($row->contact_details_unknown ?? 0) === 1,

                        'contact_name' =>
                            $row->contact_name_department ?? '',

                        'contact_email' =>
                            $row->contact_email ?? '',

                        'contact_phone_country_code' =>
                            $row->contact_phone_country_code ?? '44',

                        'contact_phone' =>
                            $row->contact_phone ?? '',

                        'address_line1' =>
                            $row->address_line1 ?? '',

                        'address_line2' =>
                            $row->address_line2 ?? '',

                        'town' =>
                            $row->town ?? '',

                        'county' =>
                            $row->county ?? '',

                        'postcode' =>
                            $row->postcode ?? '',

                        'country' =>
                            $row->country ?? '',
                    ];
                });
        }

        /*
         * Fallback for installations where the migration has not yet run,
         * or for legacy data that has not been migrated for some reason.
         */
        if ($academicHistory->isEmpty()) {
            $legacy = [];

            if ((int) ($ctx['bpss']->school_data ?? 0) === 1) {
                $legacy[] = [
                    'type' => 'school',
                    'name' => $ctx['bpss']->school_name ?? '',
                    'course' => '',
                    'from' => $this->ymd($ctx['bpss']->school_date_from ?? null),
                    'to' => $this->ymd($ctx['bpss']->school_date_to ?? null),
                    'is_current' => false,
                    'contact_unknown' => false,
                    'contact_name' => $ctx['bpss']->school_contact_name ?? '',
                    'contact_email' => '',
                    'contact_phone_country_code' => '44',
                    'contact_phone' => '',
                    'address_line1' => $ctx['bpss']->school_contact_address ?? '',
                    'address_line2' => '',
                    'town' => '',
                    'county' => '',
                    'postcode' => '',
                    'country' => '',
                ];
            }

            if ((int) ($ctx['bpss']->college_data ?? 0) === 1) {
                $legacy[] = [
                    'type' => 'college',
                    'name' => $ctx['bpss']->college_name ?? '',
                    'course' => '',
                    'from' => $this->ymd($ctx['bpss']->college_date_from ?? null),
                    'to' => $this->ymd($ctx['bpss']->college_date_to ?? null),
                    'is_current' => false,
                    'contact_unknown' => false,
                    'contact_name' => $ctx['bpss']->college_contact_name ?? '',
                    'contact_email' => '',
                    'contact_phone_country_code' => '44',
                    'contact_phone' => '',
                    'address_line1' => $ctx['bpss']->college_contact_address ?? '',
                    'address_line2' => '',
                    'town' => '',
                    'county' => '',
                    'postcode' => '',
                    'country' => '',
                ];
            }

            if ((int) ($ctx['bpss']->university_data ?? 0) === 1) {
                $legacy[] = [
                    'type' => 'university',
                    'name' => $ctx['bpss']->university_name ?? '',
                    'course' => '',
                    'from' => $this->ymd($ctx['bpss']->university_date_from ?? null),
                    'to' => $this->ymd($ctx['bpss']->university_date_to ?? null),
                    'is_current' => false,
                    'contact_unknown' => false,
                    'contact_name' => '',
                    'contact_email' => $ctx['bpss']->university_contact_email ?? '',
                    'contact_phone_country_code' => '44',
                    'contact_phone' => $ctx['bpss']->university_contact_number ?? '',
                    'address_line1' => '',
                    'address_line2' => '',
                    'town' => '',
                    'county' => '',
                    'postcode' => '',
                    'country' => '',
                ];
            }

            $academicHistory = collect($legacy);
        }

        $hasAcademicAnswer = $academicHistory->isNotEmpty()
            ? 'yes'
            : (
                (int) ($ctx['forms']->academicRef ?? self::INCOMPLETE) === self::COMPLETE
                    ? 'no'
                    : null
            );

        return view('candidate.candidateAcademicHistory', [
            'userDetails'       => $ctx['userDetails'],
            'formTitle'         => 'Academic History',
            'RequiredChecks'    => $ctx['forms'],
            'brandColor'        => $ctx['brandColor'],
            'academicHistory'   => $academicHistory,
            'hasAcademicAnswer' => $hasAcademicAnswer,
            'countries'         => $countries,
            'phoneCodes'        => $phoneCodes,
        ]);
    }

    public function candidateAcademicHistorySave(Request $request)
    {
        $ctx = $this->context(true, true);

        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        if ((int) $ctx['forms']->academicRef === self::NOT_REQUIRED) {
            abort(403);
        }

        abort_unless($ctx['bpss'], 404);

        $request->validate([
            'has_academic' => ['required', Rule::in(['yes', 'no'])],
            'academic_json' => ['nullable', 'string', 'max:300000'],
        ], [
            'has_academic.required' =>
                'Please tell us whether you have attended school, college or university during the last five years.',
        ]);

        $hasAcademic = $request->input('has_academic') === 'yes';

        $rows = [];

        if ($hasAcademic) {
            try {
                $rows = json_decode(
                    (string) $request->input('academic_json', '[]'),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            } catch (\Throwable $e) {
                return back()
                    ->withErrors([
                        'academic_json' =>
                            'Your academic history could not be read. Please try again.',
                    ])
                    ->withInput();
            }

            if (!is_array($rows) || empty($rows)) {
                return back()
                    ->withErrors([
                        'academic_json' =>
                            'Please add at least one school, college or university.',
                    ])
                    ->withInput();
            }

            if (count($rows) > 50) {
                return back()
                    ->withErrors([
                        'academic_json' =>
                            'Too many academic history entries were supplied.',
                    ])
                    ->withInput();
            }
        }

        $countries = DB::table('countries')
            ->get(['iso3', 'phonecode']);

        $countryCodes = $countries
            ->pluck('iso3')
            ->filter()
            ->map(fn ($value) => Str::upper((string) $value))
            ->unique()
            ->values()
            ->all();

        $phoneCodes = $countries
            ->pluck('phonecode')
            ->filter()
            ->map(fn ($value) => (string) $value)
            ->unique()
            ->values()
            ->all();

        $errors = [];
        $normalised = [];
        $today = now()->endOfDay();
        $cutoff = now()->subYears(5)->startOfDay();

        if ($hasAcademic) {
            foreach (array_values($rows) as $i => $row) {
                if (!is_array($row)) {
                    $errors["academic.$i.type"] =
                        'This academic history entry could not be read.';
                    continue;
                }

                $type = Str::lower(
                    trim((string) ($row['type'] ?? ''))
                );

                if (!in_array(
                    $type,
                    ['school', 'college', 'university', 'other'],
                    true
                )) {
                    $errors["academic.$i.type"] =
                        'Please select a valid institution type.';
                }

                $name = trim((string) ($row['name'] ?? ''));

                if ($name === '') {
                    $errors["academic.$i.name"] =
                        'Institution name is required.';
                } elseif (mb_strlen($name) > 190) {
                    $errors["academic.$i.name"] =
                        'Institution name is too long.';
                }

                $course = trim((string) ($row['course'] ?? ''));

                if (mb_strlen($course) > 190) {
                    $errors["academic.$i.course"] =
                        'Course or qualification is too long.';
                }

                $fromRaw = $row['from'] ?? null;

                if (!$fromRaw) {
                    $errors["academic.$i.from"] =
                        'Enter when you started attending this institution.';
                    continue;
                }

                try {
                    $from = Carbon::parse($fromRaw)->startOfDay();
                } catch (\Throwable $e) {
                    $errors["academic.$i.from"] =
                        'Please enter a valid start date.';
                    continue;
                }

                if ($from->gt($today)) {
                    $errors["academic.$i.from"] =
                        'The start date cannot be in the future.';
                }

                $isCurrent = filter_var(
                    $row['is_current'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                $toRaw = $isCurrent
                    ? now()->toDateString()
                    : ($row['to'] ?? null);

                if (!$toRaw) {
                    $errors["academic.$i.to"] =
                        'Enter when you stopped attending, or select that you currently attend this institution.';
                    continue;
                }

                try {
                    $to = Carbon::parse($toRaw)->endOfDay();
                } catch (\Throwable $e) {
                    $errors["academic.$i.to"] =
                        'Please enter a valid end date.';
                    continue;
                }

                if ($to->gt($today)) {
                    $errors["academic.$i.to"] =
                        'The end date cannot be in the future.';
                }

                if ($to->lt($from)) {
                    $errors["academic.$i.to"] =
                        'The end date cannot be before the start date.';
                }

                if ($to->lt($cutoff)) {
                    $errors["academic.$i.to"] =
                        'This education ended more than five years ago, so you do not need to include it.';
                }

                $contactUnknown = filter_var(
                    $row['contact_unknown'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                $contactName = trim(
                    (string) ($row['contact_name'] ?? '')
                );

                $contactEmail = Str::lower(
                    trim((string) ($row['contact_email'] ?? ''))
                );

                $contactPhoneCode = preg_replace(
                    '/\D+/',
                    '',
                    (string) ($row['contact_phone_country_code'] ?? '')
                );

                $contactPhone = trim(
                    (string) ($row['contact_phone'] ?? '')
                );

                $contactPhoneDigits = preg_replace(
                    '/\D+/',
                    '',
                    $contactPhone
                );

                $addressLine1 = trim(
                    (string) ($row['address_line1'] ?? '')
                );

                $addressLine2 = trim(
                    (string) ($row['address_line2'] ?? '')
                );

                $town = trim(
                    (string) ($row['town'] ?? '')
                );

                $county = trim(
                    (string) ($row['county'] ?? '')
                );

                $postcode = Str::upper(
                    trim((string) ($row['postcode'] ?? ''))
                );

                $country = Str::upper(
                    trim((string) ($row['country'] ?? ''))
                );

                if (!$contactUnknown) {
                    if (
                        $contactEmail === '' &&
                        $contactPhoneDigits === ''
                    ) {
                        $errors["academic.$i.contact_email"] =
                            'Provide an email address or phone number for the institution, or select that you do not know the contact details.';
                    }

                    if (
                        $contactEmail !== '' &&
                        !filter_var(
                            $contactEmail,
                            FILTER_VALIDATE_EMAIL
                        )
                    ) {
                        $errors["academic.$i.contact_email"] =
                            'Please enter a valid institution email address.';
                    }

                    if ($contactPhoneDigits !== '') {
                        if (
                            $contactPhoneCode === '' ||
                            !in_array(
                                $contactPhoneCode,
                                $phoneCodes,
                                true
                            )
                        ) {
                            $errors["academic.$i.contact_phone_country_code"] =
                                'Please select a valid phone country code.';
                        }

                        if (
                            strlen($contactPhoneDigits) < 5 ||
                            strlen($contactPhoneDigits) > 18
                        ) {
                            $errors["academic.$i.contact_phone"] =
                                'Please enter a valid institution phone number.';
                        }
                    }

                    if ($addressLine1 === '') {
                        $errors["academic.$i.address_line1"] =
                            'Institution address line 1 is required.';
                    }

                    if ($town === '') {
                        $errors["academic.$i.town"] =
                            'Town or city is required.';
                    }

                    if ($postcode === '') {
                        $errors["academic.$i.postcode"] =
                            'Postcode or postal code is required.';
                    }

                    if (
                        $country === '' ||
                        !in_array(
                            $country,
                            $countryCodes,
                            true
                        )
                    ) {
                        $errors["academic.$i.country"] =
                            'Please select a valid country.';
                    }
                } else {
                    /*
                     * Preserve any details the candidate did provide, but
                     * do not require them when they genuinely cannot find
                     * institutional contact information.
                     */
                    if (
                        $contactEmail !== '' &&
                        !filter_var(
                            $contactEmail,
                            FILTER_VALIDATE_EMAIL
                        )
                    ) {
                        $errors["academic.$i.contact_email"] =
                            'Please enter a valid institution email address.';
                    }

                    if (
                        $country !== '' &&
                        !in_array(
                            $country,
                            $countryCodes,
                            true
                        )
                    ) {
                        $errors["academic.$i.country"] =
                            'Please select a valid country.';
                    }
                }

                $normalised[] = [
                    'type' => $type,
                    'name' => $name,
                    'course' => $course,
                    'from' => $from->toDateString(),
                    'to' => $to->toDateString(),
                    'is_current' => $isCurrent,

                    'contact_unknown' =>
                        $contactUnknown,

                    'contact_name' =>
                        $contactName,

                    'contact_email' =>
                        $contactEmail,

                    'contact_phone_country_code' =>
                        $contactPhoneCode,

                    'contact_phone' =>
                        $contactPhone,

                    'address_line1' =>
                        $addressLine1,

                    'address_line2' =>
                        $addressLine2,

                    'town' =>
                        $town,

                    'county' =>
                        $county,

                    'postcode' =>
                        $postcode,

                    'country' =>
                        $country,
                ];
            }
        }

        if (!empty($errors)) {
            return back()
                ->withErrors($errors)
                ->withInput();
        }

        DB::transaction(function () use ($ctx, $hasAcademic, $normalised) {
            if (Schema::hasTable('bpss_application_academic_history')) {
                DB::table('bpss_application_academic_history')
                    ->where('applicationID', $ctx['bpss']->id)
                    ->delete();

                if ($hasAcademic) {
                    foreach ($normalised as $row) {
                        DB::table('bpss_application_academic_history')
                            ->insert([
                                'applicationID' =>
                                    $ctx['bpss']->id,

                                'institution_type' =>
                                    $row['type'],

                                'institution_name' =>
                                    $row['name'],

                                'course_qualification' =>
                                    $row['course'] ?: null,

                                'date_from' =>
                                    $row['from'],

                                'date_to' =>
                                    $row['to'],

                                'is_current' =>
                                    $row['is_current'] ? 1 : 0,

                                'contact_details_unknown' =>
                                    $row['contact_unknown'] ? 1 : 0,

                                'contact_name_department' =>
                                    $row['contact_name'] ?: null,

                                'contact_email' =>
                                    $row['contact_email'] ?: null,

                                'contact_phone_country_code' =>
                                    $row['contact_phone_country_code'] ?: null,

                                'contact_phone' =>
                                    $row['contact_phone'] ?: null,

                                'address_line1' =>
                                    $row['address_line1'] ?: null,

                                'address_line2' =>
                                    $row['address_line2'] ?: null,

                                'town' =>
                                    $row['town'] ?: null,

                                'county' =>
                                    $row['county'] ?: null,

                                'postcode' =>
                                    $row['postcode'] ?: null,

                                'country' =>
                                    $row['country'] ?: null,

                                'created_at' =>
                                    now(),

                                'updated_at' =>
                                    now(),
                            ]);
                    }
                }
            }

            /*
             * Mirror the first entry of each legacy type into the old
             * bpss_applications columns. Existing admin/report code that
             * still reads those columns therefore continues to work.
             */
            $firstSchool = collect($normalised)
                ->firstWhere('type', 'school');

            $firstCollege = collect($normalised)
                ->firstWhere('type', 'college');

            $firstUniversity = collect($normalised)
                ->firstWhere('type', 'university');

            $legacyPayload = [
                'school_data' => $firstSchool ? 1 : 0,
                'school_name' =>
                    $firstSchool['name'] ?? null,

                'school_date_from' =>
                    $firstSchool['from'] ?? null,

                'school_date_to' =>
                    $firstSchool['to'] ?? null,

                'school_contact_name' =>
                    $firstSchool['contact_name'] ?? null,

                'school_contact_address' =>
                    $firstSchool['address_line1'] ?? null,

                'college_data' => $firstCollege ? 1 : 0,
                'college_name' =>
                    $firstCollege['name'] ?? null,

                'college_date_from' =>
                    $firstCollege['from'] ?? null,

                'college_date_to' =>
                    $firstCollege['to'] ?? null,

                'college_contact_name' =>
                    $firstCollege['contact_name'] ?? null,

                'college_contact_address' =>
                    $firstCollege['address_line1'] ?? null,

                'university_data' =>
                    $firstUniversity ? 1 : 0,

                'university_name' =>
                    $firstUniversity['name'] ?? null,

                'university_date_from' =>
                    $firstUniversity['from'] ?? null,

                'university_date_to' =>
                    $firstUniversity['to'] ?? null,

                'university_contact_email' =>
                    $firstUniversity['contact_email'] ?? null,

                'university_contact_number' =>
                    $firstUniversity['contact_phone'] ?? null,
            ];

            DB::table('bpss_applications')
                ->where('id', $ctx['bpss']->id)
                ->update(
                    $this->onlyExistingColumns(
                        'bpss_applications',
                        $legacyPayload
                    )
                );

            DB::table('candidateForms')
                ->where('userID', $ctx['userId'])
                ->update([
                    'academicRef' => self::COMPLETE,
                ]);
        });

        $forms = DB::table('candidateForms')
            ->where('userID', $ctx['userId'])
            ->first();

        return redirect()
            ->route(
                $this->nextIncompleteRoute(
                    $forms,
                    'academicRef'
                )
            )
            ->with(
                'success',
                $hasAcademic
                    ? 'Your academic history has been saved.'
                    : 'You confirmed that you have no academic history to provide from the last five years.'
            );
    }


    /* -----------------------------------------------------------------
     | Personal references
     | -----------------------------------------------------------------
     */

    public function candidatePersonalReferences()
    {
        $ctx = $this->context(true, true);

        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        if ((int) $ctx['forms']->personalRef === self::NOT_REQUIRED) {
            return redirect()
                ->route('candidate.welcome')
                ->with('info', 'Personal references are not required for this application.');
        }

        abort_unless($ctx['bpss'], 404);

        $personalRefs = DB::table('bpss_application_personal_referee')
            ->where('applicationID', $ctx['bpss']->id)
            ->orderBy('id')
            ->limit(2)
            ->get();

        $countries = DB::table('countries')
            ->orderBy('nicename')
            ->get([
                'id',
                'iso',
                'iso3',
                'name',
                'nicename',
                'phonecode',
            ]);

        $phoneCodes = $countries
            ->pluck('phonecode')
            ->filter()
            ->map(fn ($code) => (string) $code)
            ->unique()
            ->sortBy(function ($code) {
                if ($code === '44') {
                    return -1;
                }

                return (int) $code;
            })
            ->values();

        return view('candidate.candidatePersonalReferences', [
            'userDetails'    => $ctx['userDetails'],
            'formTitle'      => 'Personal References',
            'RequiredChecks' => $ctx['forms'],
            'brandColor'     => $ctx['brandColor'],
            'personalRef1'   => $personalRefs->get(0),
            'personalRef2'   => $personalRefs->get(1),
            'countries'      => $countries,
            'phoneCodes'     => $phoneCodes,
        ]);
    }

    public function candidatePersonalReferencesSave(Request $request)
    {
        $ctx = $this->context(true, true);

        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        if ((int) $ctx['forms']->personalRef === self::NOT_REQUIRED) {
            abort(403);
        }

        abort_unless($ctx['bpss'], 404);

        $countries = DB::table('countries')
            ->get(['iso3', 'phonecode']);

        $countryCodes = $countries
            ->pluck('iso3')
            ->filter()
            ->map(fn ($value) => Str::upper((string) $value))
            ->unique()
            ->values()
            ->all();

        $phoneCodes = $countries
            ->pluck('phonecode')
            ->filter()
            ->map(fn ($value) => (string) $value)
            ->unique()
            ->values()
            ->all();

        $rules = [
            'refs' => ['required', 'array', 'size:2'],

            'refs.*.name' => [
                'required',
                'string',
                'min:2',
                'max:120',
            ],

            'refs.*.relationship' => [
                'required',
                'string',
                'min:2',
                'max:80',
                function ($attr, $value, $fail) {
                    $normalised = Str::lower(
                        trim(preg_replace('/\s+/', ' ', (string) $value))
                    );

                    /*
                     * "Business partner" is not treated as a family/romantic
                     * relationship. A plain "partner" still is.
                     */
                    $businessPartner = str_contains($normalised, 'business partner');

                    $blockedRelationships = [
                        'mother',
                        'father',
                        'parent',
                        'mum',
                        'mom',
                        'dad',
                        'brother',
                        'sister',
                        'sibling',
                        'son',
                        'daughter',
                        'child',
                        'wife',
                        'husband',
                        'spouse',
                        'cousin',
                        'aunt',
                        'uncle',
                        'niece',
                        'nephew',
                        'grandmother',
                        'grandfather',
                        'grandparent',
                        'grandma',
                        'grandpa',
                        'mother-in-law',
                        'father-in-law',
                        'brother-in-law',
                        'sister-in-law',
                        'stepmother',
                        'stepfather',
                        'stepsister',
                        'stepbrother',
                        'stepson',
                        'stepdaughter',
                        'boyfriend',
                        'girlfriend',
                        'fiancé',
                        'fiance',
                        'fiancée',
                        'fiancee',
                    ];

                    foreach ($blockedRelationships as $relationship) {
                        if (preg_match(
                            '/(?:^|\b)' . preg_quote($relationship, '/') . '(?:\b|$)/iu',
                            $normalised
                        )) {
                            return $fail(
                                'Please provide a referee who is not a relative or romantic partner.'
                            );
                        }
                    }

                    if (
                        !$businessPartner &&
                        preg_match('/(?:^|\b)partner(?:\b|$)/iu', $normalised)
                    ) {
                        return $fail(
                            'Please provide a referee who is not a relative or romantic partner.'
                        );
                    }
                },
            ],

            'refs.*.email' => [
                'required',
                'email:rfc',
                'max:190',
            ],

            'refs.*.phone_country_code' => [
                'required',
                Rule::in($phoneCodes),
            ],

            'refs.*.phone' => [
                'required',
                'string',
                'min:5',
                'max:25',
                'regex:/^[0-9 ()+\-]+$/',
            ],

            'refs.*.address' => [
                'required',
                'string',
                'min:3',
                'max:190',
            ],

            'refs.*.address_line2' => [
                'nullable',
                'string',
                'max:190',
            ],

            'refs.*.town' => [
                'required',
                'string',
                'min:2',
                'max:120',
            ],

            'refs.*.county' => [
                'nullable',
                'string',
                'max:120',
            ],

            'refs.*.postcode' => [
                'required',
                'string',
                'min:2',
                'max:20',
            ],

            'refs.*.country' => [
                'required',
                Rule::in($countryCodes),
            ],

            'refs.*.known_from' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'refs.*.still_known' => [
                'nullable',
                'boolean',
            ],

            'refs.*.known_to' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
        ];

        /*
         * Referee 1 and referee 2 must be different people.
         */
        $rules['refs.0.email'][] = 'different:refs.1.email';

        $validator = Validator::make(
            $request->all(),
            $rules,
            [],
            [
                'refs.*.name'               => 'referee name',
                'refs.*.relationship'       => 'relationship',
                'refs.*.email'              => 'referee email',
                'refs.*.phone_country_code' => 'phone country code',
                'refs.*.phone'              => 'phone number',
                'refs.*.address'            => 'address',
                'refs.*.address_line2'      => 'address line 2',
                'refs.*.town'               => 'town or city',
                'refs.*.county'             => 'county, state or region',
                'refs.*.postcode'           => 'postcode or postal code',
                'refs.*.country'            => 'country',
                'refs.*.known_from'         => 'known since',
                'refs.*.known_to'           => 'known until',
            ]
        );

        $validator->after(function ($v) use ($request, $ctx) {
            $refs = array_values((array) $request->input('refs', []));

            foreach ($refs as $i => $ref) {
                $stillKnown = filter_var(
                    $ref['still_known'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                $fromRaw = $ref['known_from'] ?? null;
                $toRaw = $stillKnown
                    ? now()->toDateString()
                    : ($ref['known_to'] ?? null);

                if (!$stillKnown && empty($toRaw)) {
                    $v->errors()->add(
                        "refs.$i.known_to",
                        'Enter when you stopped knowing this person, or select that you still know them.'
                    );
                }

                if (!empty($fromRaw) && !empty($toRaw)) {
                    try {
                        $from = Carbon::parse($fromRaw);
                        $to = Carbon::parse($toRaw);

                        if ($to->lt($from)) {
                            $v->errors()->add(
                                "refs.$i.known_to",
                                'Known-until date cannot be before the known-since date.'
                            );
                        }
                    } catch (\Throwable $e) {
                        // Base date validation will display the field error.
                    }
                }
            }

            if (count($refs) === 2) {
                $normalisePhone = function (array $ref): string {
                    $code = preg_replace(
                        '/\D+/',
                        '',
                        (string) ($ref['phone_country_code'] ?? '')
                    );

                    $number = preg_replace(
                        '/\D+/',
                        '',
                        (string) ($ref['phone'] ?? '')
                    );

                    /*
                     * Candidate enters the national number. Strip a national
                     * trunk zero before combining with the international code.
                     */
                    if (str_starts_with($number, '0')) {
                        $number = ltrim($number, '0');
                    }

                    return $code . $number;
                };

                $phone0 = $normalisePhone($refs[0]);
                $phone1 = $normalisePhone($refs[1]);

                if ($phone0 !== '' && $phone0 === $phone1) {
                    $v->errors()->add(
                        'refs.1.phone',
                        'Your two personal referees must be different people.'
                    );
                }

                $email0 = Str::lower(trim((string) ($refs[0]['email'] ?? '')));
                $email1 = Str::lower(trim((string) ($refs[1]['email'] ?? '')));

                if ($email0 !== '' && $email0 === $email1) {
                    $v->errors()->add(
                        'refs.1.email',
                        'Your two personal referees must be different people.'
                    );
                }

                $candidateEmails = collect([
                    $ctx['user']->email ?? null,
                    $ctx['userDetails']->application_email ?? null,
                ])
                    ->filter()
                    ->map(fn ($email) => Str::lower(trim((string) $email)))
                    ->unique();

                foreach ([0, 1] as $i) {
                    $refEmail = Str::lower(
                        trim((string) ($refs[$i]['email'] ?? ''))
                    );

                    if (
                        $refEmail !== '' &&
                        $candidateEmails->contains($refEmail)
                    ) {
                        $v->errors()->add(
                            "refs.$i.email",
                            'A personal referee cannot use your own email address.'
                        );
                    }
                }
            }
        });

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $refs = collect($request->input('refs'))
            ->map(function ($ref) {
                $stillKnown = filter_var(
                    $ref['still_known'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                $phoneCode = preg_replace(
                    '/\D+/',
                    '',
                    (string) ($ref['phone_country_code'] ?? '')
                );

                $localPhone = preg_replace(
                    '/\D+/',
                    '',
                    (string) ($ref['phone'] ?? '')
                );

                if (str_starts_with($localPhone, '0')) {
                    $localPhone = ltrim($localPhone, '0');
                }

                return [
                    'name' => trim(
                        preg_replace('/\s+/', ' ', (string) $ref['name'])
                    ),

                    'relationship' => trim(
                        preg_replace(
                            '/\s+/',
                            ' ',
                            (string) $ref['relationship']
                        )
                    ),

                    'email' => Str::lower(
                        trim((string) $ref['email'])
                    ),

                    'phone_country_code' => $phoneCode,

                    /*
                     * Keep the legacy referee_contact_number column useful by
                     * storing the full international number as digits.
                     */
                    'phone' => $phoneCode . $localPhone,

                    'phone_local' => $localPhone,

                    'address' => trim(
                        preg_replace('/\s+/', ' ', (string) $ref['address'])
                    ),

                    'address_line2' => trim(
                        preg_replace(
                            '/\s+/',
                            ' ',
                            (string) ($ref['address_line2'] ?? '')
                        )
                    ),

                    'town' => trim(
                        preg_replace('/\s+/', ' ', (string) $ref['town'])
                    ),

                    'county' => trim(
                        preg_replace(
                            '/\s+/',
                            ' ',
                            (string) ($ref['county'] ?? '')
                        )
                    ),

                    'postcode' => Str::upper(
                        trim(
                            preg_replace(
                                '/\s+/',
                                ' ',
                                (string) $ref['postcode']
                            )
                        )
                    ),

                    'country' => Str::upper(
                        trim((string) $ref['country'])
                    ),

                    'known_from' => Carbon::parse(
                        $ref['known_from']
                    )->toDateString(),

                    'still_known' => $stillKnown,

                    'known_to' => $stillKnown
                        ? now()->toDateString()
                        : Carbon::parse($ref['known_to'])->toDateString(),
                ];
            })
            ->values();

        /*
         * Five years is preferred, but it is deliberately NON-BLOCKING.
         * Candidates who do not have anyone suitable who has known them for
         * five years can still provide the best available referees.
         */
        $warnings = [];

        foreach ($refs as $i => $ref) {
            $from = Carbon::parse($ref['known_from']);
            $to = Carbon::parse($ref['known_to']);
            $years = $from->diffInDays($to) / 365.25;

            if ($years < 5) {
                $warnings[] =
                    'Reference ' . ($i + 1) .
                    ' has known you for less than 5 years. ' .
                    'This has still been accepted and can be reviewed as your best available referee.';
            }
        }

        DB::transaction(function () use ($ctx, $refs) {
            $existing = DB::table('bpss_application_personal_referee')
                ->where('applicationID', $ctx['bpss']->id)
                ->orderBy('id')
                ->limit(2)
                ->get()
                ->values();

            foreach ([0, 1] as $i) {
                $payload = $this->onlyExistingColumns(
                    'bpss_application_personal_referee',
                    [
                        'applicationID'                    => $ctx['bpss']->id,
                        'referee_name'                     => $refs[$i]['name'],
                        'relationship'                     => $refs[$i]['relationship'],
                        'referee_email'                    => $refs[$i]['email'],
                        'referee_contact_country_code'     => $refs[$i]['phone_country_code'],
                        'referee_contact_number'           => $refs[$i]['phone'],
                        'referee_address_line'             => $refs[$i]['address'],
                        'referee_address_line_2'           => $refs[$i]['address_line2'],
                        'referee_address_town'             => $refs[$i]['town'],
                        'referee_address_county'           => $refs[$i]['county'],
                        'referee_address_postcode'         => $refs[$i]['postcode'],
                        'referee_address_country'          => $refs[$i]['country'],
                        'still_known'                      => $refs[$i]['still_known'] ? 1 : 0,
                        'date_from'                        => $refs[$i]['known_from'],
                        'date_to'                          => $refs[$i]['known_to'],
                        'updated_at'                       => now(),
                    ]
                );

                if (isset($existing[$i])) {
                    DB::table('bpss_application_personal_referee')
                        ->where('id', $existing[$i]->id)
                        ->update($payload);
                } else {
                    $payload = $this->onlyExistingColumns(
                        'bpss_application_personal_referee',
                        $payload + ['created_at' => now()]
                    );

                    DB::table('bpss_application_personal_referee')
                        ->insert($payload);
                }
            }

            DB::table('candidateForms')
                ->where('userID', $ctx['userId'])
                ->update([
                    'personalRef' => self::COMPLETE,
                ]);
        });

        $forms = DB::table('candidateForms')
            ->where('userID', $ctx['userId'])
            ->first();

        $response = redirect()
            ->route(
                $this->nextIncompleteRoute(
                    $forms,
                    'personalRef'
                )
            )
            ->with(
                'success',
                'Your personal references have been saved.'
            );

        if (!empty($warnings)) {
            $response->with('warnings', $warnings);
        }

        return $response;
    }


    /* -----------------------------------------------------------------
     | Supporting documents
     | -----------------------------------------------------------------
     */

    private function allowedDocumentTypes(): array
    {
        return [
            'Passport',
            'Driving licence photocard (UK/IoM/CI)',
            'e-Visa',
            'Biometric residence permit (BRP)',
            'Application Registration Card (ARC)',
            'Birth certificate (within 12 months of birth)',
            'Adoption certificate',
            'Birth certificate (more than 12 months after birth)',
            'Marriage/civil partnership certificate',
            'Driving licence photocard (non-UK)',
            'Driving licence paper (UK pre-2000)',
            'HM Forces ID / Veteran card',
            'Firearms licence',
            'Immigration document/visa/work permit (non-UK)',
            'Mortgage statement',
            'Bank/building society statement',
            'Bank account opening letter',
            'Credit card statement',
            'Financial statement (pension/endowment)',
            'P45',
            'P60',
            'Council Tax statement',
            'Utility bill (not mobile)',
            'Benefit statement',
            'Government/local council entitlement letter',
            'HMRC self-assessment/tax demand letter',
            'EHIC/GHIC',
            'EEA National ID card',
            'Irish Passport Card',
            'PASS card',
            'Letter from school/college (16–19)',
            'Letter of sponsorship (non-UK)',
            'Deed poll / change of name',
            'Other',
        ];
    }

    private function supportingRequirements(int $userId, $forms): array
    {
        $application = DB::table('applications')->where('userID', $userId)->first();
        if (!$application) {
            return [false, 'No application record was found.'];
        }

        $docs = DB::table('application_supporting_documents')
            ->where('applicationID', $application->id)
            ->get(['document_category']);

        if ($docs->isEmpty()) {
            return [false, 'Please upload at least one supporting document.'];
        }

        if ((int) ($forms->basicDBS ?? 0) === self::NOT_REQUIRED) {
            return [true, null];
        }

        $types = $docs->pluck('document_category')->filter()->map(fn ($v) => trim((string) $v))->values();
        $unique = $types->unique()->values();
        if ($unique->count() !== $types->count()) {
            return [false, 'For the DBS ID check, the same document type can only be counted once.'];
        }

        $group1 = collect([
            'Passport', 'Driving licence photocard (UK/IoM/CI)', 'e-Visa', 'Biometric residence permit (BRP)',
            'Application Registration Card (ARC)', 'Birth certificate (within 12 months of birth)', 'Adoption certificate',
        ]);
        $group2a = collect([
            'Birth certificate (more than 12 months after birth)', 'Marriage/civil partnership certificate',
            'Driving licence photocard (non-UK)', 'Driving licence paper (UK pre-2000)', 'HM Forces ID / Veteran card',
            'Firearms licence', 'Immigration document/visa/work permit (non-UK)',
        ]);
        $group2b = collect([
            'Mortgage statement', 'Bank/building society statement', 'Bank account opening letter', 'Credit card statement',
            'Financial statement (pension/endowment)', 'P45', 'P60', 'Council Tax statement', 'Utility bill (not mobile)',
            'Benefit statement', 'Government/local council entitlement letter', 'HMRC self-assessment/tax demand letter',
            'EHIC/GHIC', 'EEA National ID card', 'Irish Passport Card', 'PASS card', 'Letter from school/college (16–19)',
            'Letter of sponsorship (non-UK)',
        ]);

        $g1 = $unique->filter(fn ($type) => $group1->contains($type))->count();
        $g2a = $unique->filter(fn ($type) => $group2a->contains($type))->count();
        $g2b = $unique->filter(fn ($type) => $group2b->contains($type))->count();
        $total = $g1 + $g2a + $g2b;

        $route1 = $g1 >= 1 && $total >= 2;
        $route2 = $g1 === 0 && $g2a >= 1 && ($g2a + $g2b) >= 3;

        if (!$route1 && !$route2) {
            return [false, 'The uploaded documents do not yet meet the DBS identity-document route shown on the page.'];
        }

        $hasPreviousName = DB::table('application_extra_names')->where('applicationID', $application->id)->exists();
        if ($hasPreviousName) {
            $nameChange = $unique->contains('Marriage/civil partnership certificate') || $unique->contains('Deed poll / change of name');
            if (!$nameChange) {
                return [false, 'Please upload evidence of your previous-name change.'];
            }
        }

        return [true, null];
    }

    public function candidateSupportingDocs()
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        $existingDocs = DB::table('application_supporting_documents')
            ->select('id', 'document_name', 'document_type', 'document_category', 'document_path')
            ->where('applicationID', $ctx['application']->id)
            ->orderByDesc('id')
            ->get();

        $hasPrevNames = DB::table('application_extra_names')->where('applicationID', $ctx['application']->id)->exists();

        return view('candidate.candidateSupportingDocs', [
            'userDetails'      => $ctx['userDetails'],
            'formTitle'        => 'Supporting Documents',
            'RequiredChecks'   => $ctx['forms'],
            'applicantID'      => (object) ['id' => $ctx['application']->id],
            'existingDocs'     => $existingDocs,
            'brandColor'       => $ctx['brandColor'],
            'hasPreviousName'  => $hasPrevNames,
        ]);
    }

    public function uploadSupportingDocument(Request $request)
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return response()->json(['status' => 0, 'message' => 'Application is locked.'], 423);
        }

        $validator = Validator::make($request->all(), [
            'new_document_category' => ['required', Rule::in($this->allowedDocumentTypes())],
            'new_document' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 422);
        }

        $file = $request->file('new_document');
        $extension = Str::lower($file->getClientOriginalExtension());
        $storedName = (string) Str::uuid() . '.' . $extension;
        $originalBase = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $displayName = Str::limit(preg_replace('/[^\pL\pN _().\-]/u', '', $originalBase), 150, '');
        if ($displayName === '') {
            $displayName = 'document';
        }

        try {
            Storage::disk('public_images')->putFileAs('supporting_documents', $file, $storedName);

            $payload = $this->onlyExistingColumns('application_supporting_documents', [
                'applicationID'     => $ctx['application']->id,
                'document_name'     => $displayName,
                'document_type'     => $extension,
                'document_category' => $request->input('new_document_category'),
                'document_path'     => $storedName,
                'createdBy'         => $ctx['userId'],
                'createdOn'         => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            DB::table('application_supporting_documents')->insert($payload);
            DB::table('candidateForms')->where('userID', $ctx['userId'])->update(['supportingDocs' => self::INCOMPLETE]);
        } catch (\Throwable $e) {
            try {
                Storage::disk('public_images')->delete('supporting_documents/' . $storedName);
            } catch (\Throwable $ignored) {
            }

            report($e);
            return response()->json(['status' => 0, 'message' => 'The document could not be uploaded.'], 500);
        }

        return response()->json(['status' => 1, 'docName' => $displayName]);
    }

    public function markSupportingComplete(Request $request)
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return response()->json(['ok' => false, 'message' => 'Application is locked.'], 423);
        }

        [$ok, $message] = $this->supportingRequirements($ctx['userId'], $ctx['forms']);

        DB::table('candidateForms')->where('userID', $ctx['userId'])->update([
            'supportingDocs' => $ok ? self::COMPLETE : self::INCOMPLETE,
        ]);

        if (!$ok) {
            return response()->json(['ok' => false, 'message' => $message], 422);
        }

        return response()->json(['ok' => true]);
    }

    public function downloadSupportingDocument($id)
    {
        $userId = $this->requireCandidate();
        $doc = DB::table('application_supporting_documents')->where('id', (int) $id)->first();
        abort_unless($doc, 404);

        $owns = DB::table('applications')
            ->where('id', $doc->applicationID)
            ->where('userID', $userId)
            ->exists();
        abort_unless($owns, 403);

        $path = 'supporting_documents/' . basename($doc->document_path);
        abort_unless(Storage::disk('public_images')->exists($path), 404);

        $extension = pathinfo($doc->document_path, PATHINFO_EXTENSION);
        $downloadName = Str::limit(preg_replace('/[^\pL\pN _().\-]/u', '', (string) ($doc->document_name ?: 'document')), 150, '') . '.' . $extension;

        return Storage::disk('public_images')->download($path, $downloadName, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function deleteSupportingDocument(Request $request)
    {
        $userId = $this->requireCandidate();
        $id = (int) $request->input('id');
        if ($id <= 0) {
            return response()->json(['ok' => false], 422);
        }

        $doc = DB::table('application_supporting_documents')->where('id', $id)->first();
        if (!$doc) {
            return response()->json(['ok' => false], 404);
        }

        $owns = DB::table('applications')
            ->where('id', $doc->applicationID)
            ->where('userID', $userId)
            ->exists();
        if (!$owns) {
            return response()->json(['ok' => false], 403);
        }

        try {
            Storage::disk('public_images')->delete('supporting_documents/' . basename($doc->document_path));
        } catch (\Throwable $e) {
            report($e);
        }

        DB::table('application_supporting_documents')->where('id', $id)->delete();

        $forms = DB::table('candidateForms')->where('userID', $userId)->first();
        if ($forms) {
            [$ok] = $this->supportingRequirements($userId, $forms);
            DB::table('candidateForms')->where('userID', $userId)->update(['supportingDocs' => $ok ? self::COMPLETE : self::INCOMPLETE]);
        }

        return response()->json(['ok' => true]);
    }

    /* -----------------------------------------------------------------
     | Review / submit / help / dashboard
     | -----------------------------------------------------------------
     */

    private function refreshDynamicCompletion(int $userId, $user, $forms): object
    {
        $updates = [];

        if ((int) ($user->useYoti ?? 0) === 2 && (int) ($forms->YotiVerifcation ?? 0) !== self::NOT_REQUIRED) {
            $updates['YotiVerifcation'] = self::COMPLETE;
        }

        if ((int) ($forms->supportingDocs ?? 0) !== self::NOT_REQUIRED) {
            [$docsOk] = $this->supportingRequirements($userId, $forms);
            $updates['supportingDocs'] = $docsOk ? self::COMPLETE : self::INCOMPLETE;
        }

        if (!empty($updates)) {
            DB::table('candidateForms')->where('userID', $userId)->update($updates);
            $forms = DB::table('candidateForms')->where('userID', $userId)->first();
        }

        return $forms;
    }

    private function sectionsFor($forms): array
    {
        $sections = [
            ['key' => 'aboutYou',        'label' => 'Personal Details',              'route' => route('candidate.personal.edit')],
            ['key' => 'basicDBS',        'label' => 'Criminal Record Check',         'route' => route('candidate.dbsbasic.edit')],
            ['key' => 'YotiVerifcation', 'label' => 'Digital Identity Verification', 'route' => route('candidate.identity.edit')],
            ['key' => 'employmentRef',   'label' => 'Employment History',            'route' => route('candidate.employment.edit')],
            ['key' => 'academicRef',     'label' => 'Academic History',              'route' => route('candidate.academic.edit')],
            ['key' => 'personalRef',     'label' => 'Personal References',           'route' => route('candidate.references.personal.edit')],
            ['key' => 'supportingDocs',  'label' => 'Supporting Documents',          'route' => route('candidate.supporting.edit')],
        ];

        return array_values(array_filter($sections, fn ($section) => (int) ($forms->{$section['key']} ?? 0) !== self::NOT_REQUIRED));
    }

    public function candidateSaveAndSubmit()
    {
        $ctx = $this->context(true, false);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        $forms = $this->refreshDynamicCompletion($ctx['userId'], $ctx['user'], $ctx['forms']);
        $sections = $this->sectionsFor($forms);
        $status = [];
        foreach ($sections as $section) {
            $status[$section['key']] = (int) ($forms->{$section['key']} ?? 0);
        }
        $allComplete = collect($sections)->every(fn ($section) => (int) ($forms->{$section['key']} ?? 0) === self::COMPLETE);

        return view('candidate.candidateSaveAndSubmit', [
            'userDetails'    => $ctx['userDetails'],
            'formTitle'      => 'Save and Submit',
            'RequiredChecks' => $forms,
            'brandColor'     => $ctx['brandColor'],
            'sections'       => $sections,
            'status'         => $status,
            'allComplete'    => $allComplete,
        ]);
    }

    public function candidateSubmit(Request $request)
    {
        $ctx = $this->context(true, true);
        if ($locked = $this->lockedResponse($ctx['user'], $ctx['forms'])) {
            return $locked;
        }

        $request->validate([
            'final_declaration' => ['accepted'],
        ], [
            'final_declaration.accepted' => 'You must confirm the final declaration before submitting.',
        ]);

        $forms = $this->refreshDynamicCompletion($ctx['userId'], $ctx['user'], $ctx['forms']);
        foreach (['aboutYou', 'basicDBS', 'YotiVerifcation', 'employmentRef', 'academicRef', 'personalRef', 'supportingDocs'] as $key) {
            $value = (int) ($forms->{$key} ?? self::NOT_REQUIRED);
            if ($value !== self::NOT_REQUIRED && $value !== self::COMPLETE) {
                return back()->with('error', 'Some required sections are incomplete. Please finish them before submitting.');
            }
        }

        DB::transaction(function () use ($ctx) {
            DB::table('candidateForms')->where('userID', $ctx['userId'])->update(['complete' => 1]);

            if ($ctx['application']) {
                DB::table('applications')->where('id', $ctx['application']->id)->update($this->onlyExistingColumns('applications', [
                    'applicationStatus' => 1,
                    'submittedDate' => now(),
                    'submitted_at' => now(),
                ]));
            }

            if ($ctx['bpss']) {
                DB::table('bpss_applications')->where('id', $ctx['bpss']->id)->update($this->onlyExistingColumns('bpss_applications', [
                    'applicationStatus' => 1,
                    'submittedDate' => now(),
                    'submitted_at' => now(),
                ]));
            }
        });

        return redirect()->route('candidate.dashboard');
    }

    public function candidateEditApplication()
    {
        $ctx = $this->context(false, false);

        // Once the screening has been completed by the admin, candidates can no
        // longer reopen the application themselves.
        if ((int) ($ctx['user']->completed ?? 0) === 1) {
            return redirect()
                ->route('candidate.dashboard')
                ->with('error', 'Your screening has already been completed and can no longer be edited.');
        }

        // Only a submitted/under-review application needs to be reopened.
        if ((int) ($ctx['forms']->complete ?? 0) === 1) {
            DB::table('candidateForms')
                ->where('userID', $ctx['userId'])
                ->update(['complete' => 0]);
        }

        return redirect()
            ->route('candidate.welcome')
            ->with('success', 'Your application has been reopened. You can now review and edit your details before submitting it again.');
    }

    public function candidateHelp()
    {
        $ctx = $this->context(true, false);

        return view('candidate.candidateHelp', [
            'userDetails'    => $ctx['userDetails'],
            'formTitle'      => 'Help & Support',
            'RequiredChecks' => $ctx['forms'],
            'brandColor'     => $ctx['brandColor'],
        ]);
    }

    public function candidateDashboard()
    {
        $ctx = $this->context(true, false);
        $forms = $this->refreshDynamicCompletion($ctx['userId'], $ctx['user'], $ctx['forms']);

        if ((int) ($forms->complete ?? 0) !== 1 && (int) ($ctx['user']->completed ?? 0) !== 1) {
            return redirect()->route('candidate.welcome');
        }

        $status = 'not_submitted';
        if ((int) ($ctx['user']->completed ?? 0) === 1) {
            $status = 'complete';
        } elseif ((int) ($forms->complete ?? 0) === 1) {
            $status = 'under_review';
        }

        return view('candidate.candidateDashboard', [
            'userDetails'    => $ctx['userDetails'],
            'brandColor'     => $ctx['brandColor'],
            'status'         => $status,
            'RequiredChecks' => $forms,
        ]);
    }
}
