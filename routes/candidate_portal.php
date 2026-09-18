<?php

use App\Http\Controllers\candidate;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Candidate portal routes
|--------------------------------------------------------------------------
|
| Add the following line to routes/web.php:
|
|     require __DIR__.'/candidate_portal.php';
|
*/

Route::middleware(['auth'])->prefix('candidate')->group(function () {
    Route::get('/welcome', [candidate::class, 'candidateWelcome'])->name('candidate.welcome');

    Route::get('/personalDetails', [candidate::class, 'candidatePersonalDetails'])->name('candidate.personal.edit');
    Route::post('/personalDetails', [candidate::class, 'savePersonal'])->name('candidate.personal.save');

    Route::get('/DBS-basic', [candidate::class, 'candidateDBSbasic'])->name('candidate.dbsbasic.edit');
    Route::post('/DBS-basic', [candidate::class, 'candidateDBSbasicSave'])->name('candidate.dbsbasic.save');
    Route::post('/DBS-basic/consent', [candidate::class, 'candidateDBSbasicConsentSave'])->name('candidate.dbsbasic.consent.save');

    Route::get('/identityVerification', [candidate::class, 'candidateIdentityVerification'])->name('candidate.identity.edit');

    Route::get('/employmentHistory', [candidate::class, 'candidateEmploymentHistory'])->name('candidate.employment.edit');
    Route::post('/employmentHistory', [candidate::class, 'candidateEmploymentHistorySave'])->name('candidate.employment.save');

    Route::get('/academicHistory', [candidate::class, 'candidateAcademicHistory'])->name('candidate.academic.edit');
    Route::post('/academicHistory', [candidate::class, 'candidateAcademicHistorySave'])->name('candidate.academic.save');

    Route::get('/personalReferences', [candidate::class, 'candidatePersonalReferences'])->name('candidate.references.personal.edit');
    Route::post('/personalReferences', [candidate::class, 'candidatePersonalReferencesSave'])->name('candidate.references.personal.save');

    Route::get('/supportingDocs', [candidate::class, 'candidateSupportingDocs'])->name('candidate.supporting.edit');
    Route::post('/supportingDocs/upload', [candidate::class, 'uploadSupportingDocument'])->name('candidate.supporting.upload');
    Route::post('/supportingDocs/complete', [candidate::class, 'markSupportingComplete'])->name('candidate.supporting.complete');
    Route::get('/supportingDocs/{id}/download', [candidate::class, 'downloadSupportingDocument'])->where('id', '[0-9]+')->name('candidate.supporting.download');
    Route::post('/supportingDocs/delete', [candidate::class, 'deleteSupportingDocument'])->name('candidate.supporting.delete');

    Route::get('/saveAndSubmit', [candidate::class, 'candidateSaveAndSubmit'])->name('candidate.submit.review');
    Route::post('/submit', [candidate::class, 'candidateSubmit'])->name('candidate.submit');

    Route::get('/help', [candidate::class, 'candidateHelp'])->name('candidate.help');
    Route::get('/dashboard', [candidate::class, 'candidateDashboard'])->name('candidate.dashboard');
    Route::post('/edit-application', [candidate::class, 'candidateEditApplication'])->name('candidate.application.edit');
});
