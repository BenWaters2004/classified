@php
    $screeningSettingsService = app(
        \App\Services\Screening\OrganisationScreeningService::class
    );

    $screeningCheckGroups =
        $screeningSettingsService->groupedCatalogue();

    $enabledScreeningChecks =
        $screeningSettingsService->enabledCodes(
            $organisationDetails->id
        );
@endphp


<div class="panel panel-default" style="margin-top: 25px;">

    <div class="panel-heading">
        <strong>Screening Checks</strong>
    </div>

    <div class="panel-body">

        <p class="text-muted" style="margin-bottom: 20px;">
            Select the screening checks this organisation is permitted
            to request.
        </p>

        <div class="alert alert-info">
            <strong>Candidate Portal V2:</strong>
            These settings are being prepared for the new screening
            request system. The existing Allowed Application Types
            settings remain active until the new request flow is enabled.
        </div>

        {{-- Marker allows the controller to distinguish:
             "no checks selected" from "this form did not contain
             V2 screening settings". --}}
        <input
            type="hidden"
            name="screeningChecksPresent"
            value="1"
        >

        @foreach($screeningCheckGroups as $group)

            <div
                class="screening-check-group"
                style="
                    margin-bottom: 25px;
                    padding-bottom: 15px;
                    border-bottom: 1px solid #eeeeee;
                "
            >

                <h4 style="margin-bottom: 15px;">
                    {{ $group['name'] }}
                </h4>

                <div class="row">

                    @foreach($group['checks'] as $check)

                        <div class="col-md-6">

                            <div
                                class="checkbox"
                                style="
                                    border: 1px solid #e5e5e5;
                                    border-radius: 4px;
                                    padding: 12px 15px 12px 35px;
                                    margin-top: 0;
                                    margin-bottom: 10px;
                                "
                            >

                                <label style="display: block;">

                                    <input
                                        type="checkbox"
                                        name="screeningChecks[]"
                                        value="{{ $check['code'] }}"

                                        @if(
                                            in_array(
                                                $check['code'],
                                                $enabledScreeningChecks,
                                                true
                                            )
                                        )
                                            checked
                                        @endif
                                    >

                                    <strong>
                                        {{ $check['name'] }}
                                    </strong>

                                    @if(!empty($check['description']))

                                        <div
                                            class="text-muted"
                                            style="
                                                margin-top: 4px;
                                                font-weight: normal;
                                            "
                                        >
                                            {{ $check['description'] }}
                                        </div>

                                    @endif

                                </label>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @endforeach

    </div>
</div>