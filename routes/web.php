<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/candidate_portal.php';


// Route::get('/', function () {
//     return view('manager.welcome');
// });
Route::get('/', [\App\Http\Controllers\FrontEnd::class, 'homePage']);

Route::get('/login', 'Login@index')->name('login');
Route::post('/login', 'Login@authenticate');
Route::post('/loginmfa', 'Login@test_mfa');
Route::get('/logout', 'Login@logout');
Route::post('/forgotPassword', 'Login@forgotPassword');

#Registration
Route::get('/register/{accessUrlCode?}', 'Register@index');
Route::get('/applicant', 'Register@index');
Route::post('/register', 'Register@authenticate');
Route::post('/register/signupCheckCode', 'Register@signupCheckCode');
Route::post('/register/createAccount', 'Register@createApplicantAccount');


Route::get('/applicant/dashboard', 'Applicant@dashboard');
Route::get('/applicant/faq', 'Applicant@faq');
Route::get('/applicant/editProfile', 'Applicant@editProfile');
Route::post('/applicant/updateUserDetails', 'Applicant@updateUserDetails');

Route::get('/applicant/logout', 'Applicant@logout');
Route::post('/applicant/forgotPassword', 'Applicant@forgotPassword');
Route::get('/applicant/modifyDBSApplication/{applicationID}', 'Applicant@modifyDBSApplication');
Route::get('/applicant/modifyBPSSApplication/{applicationID}', 'Applicant@modifyBPSSApplication');
Route::get('/applicant/downloadBPSSPDF/{applicationID}', 'Applicant@downloadBPSSPDF');
Route::get('/applicant/generateBPSSPDF/{applicationID}', 'Applicant@generateBPSSPDF');

//Route::get('/applicant/viewApplicationStatus/{applicationID}', 'Applicant@viewApplicationStatus');

Route::get('/applicant/reviewBPSSApplication/{applicationID}', 'Applicant@reviewBPSSApplication');
Route::get('/applicant/bpssStep1', 'Applicant@bpssStep1');
Route::post('/applicant/savebpssStep1', 'Applicant@savebpssStep1');
Route::get('/applicant/savebpssStep1', 'Applicant@bpssStep1');

Route::get('/applicant/bpssStep1Extra', 'Applicant@bpssStep1Extra');
Route::post('/applicant/savebpssStep1Extra', 'Applicant@savebpssStep1Extra');
Route::get('/applicant/savebpssStep1Extra', 'Applicant@bpssStep1Extra');

Route::get('/applicant/bpssStep2', 'Applicant@bpssStep2');
Route::post('/applicant/savebpssStep2', 'Applicant@savebpssStep2');
Route::get('/applicant/savebpssStep2', 'Applicant@bpssStep2');
Route::post('/applicant/addPassportDetails', 'Applicant@addPassportDetails');
Route::post('/applicant/removePassportDetails', 'Applicant@removePassportDetails');
Route::post('/applicant/addCitizenshipCountry', 'Applicant@addCitizenshipCountry');
Route::post('/applicant/removeCitizenshipCountry', 'Applicant@removeCitizenshipCountry');

Route::get('/applicant/bpssStep3', 'Applicant@bpssStep3');
Route::post('/applicant/savebpssStep3', 'Applicant@savebpssStep3');
Route::get('/applicant/savebpssStep3', 'Applicant@bpssStep3');
Route::post('/applicant/addEmploymentDetails', 'Applicant@addEmploymentDetails');
Route::post('/applicant/removeEmploymentDetails', 'Applicant@removeEmploymentDetails');
Route::post('/applicant/addUnemploymentDetails', 'Applicant@addUnemploymentDetails');
Route::post('/applicant/removeUnemploymentDetails', 'Applicant@removeUnemploymentDetails');

Route::get('/applicant/bpssStep4', 'Applicant@bpssStep4');
Route::post('/applicant/savebpssStep4', 'Applicant@savebpssStep4');
Route::get('/applicant/savebpssStep4', 'Applicant@bpssStep4');

Route::get('/applicant/bpssStep5', 'Applicant@bpssStep5');
Route::post('/applicant/savebpssStep5', 'Applicant@savebpssStep5');
Route::get('/applicant/savebpssStep5', 'Applicant@bpssStep5');

Route::get('/applicant/bpssStep6', 'Applicant@bpssStep6');
Route::post('/applicant/savebpssStep6', 'Applicant@savebpssStep6');
Route::get('/applicant/savebpssStep6', 'Applicant@bpssStep6');

Route::get('/applicant/submitBPSSApplication', 'Applicant@submitBPSSApplication');

Route::get('/applicant/reviewDBSApplication/{applicationID}', 'Applicant@reviewDBSApplication');
Route::get('/applicant/dbsReset', 'Applicant@dbsReset');
Route::get('/applicant/dbsStep1', 'Applicant@dbsStep1');
Route::get('/applicant/dbsStep2', 'Applicant@dbsStep2');
Route::get('/applicant/dbsStep3', 'Applicant@dbsStep3');
Route::get('/applicant/dbsStep4', 'Applicant@dbsStep4');
Route::get('/applicant/dbsStep5', 'Applicant@dbsStep5');
Route::get('/applicant/submitApplication', 'Applicant@submitApplication');

Route::post('/applicant/saveStep1Data', 'Applicant@saveStep1Data');
Route::post('/applicant/saveStep2Data', 'Applicant@saveStep2Data');
Route::post('/applicant/saveStep3Data', 'Applicant@saveStep3Data');
Route::post('/applicant/saveStep4Data', 'Applicant@saveStep4Data');
Route::post('/applicant/saveStep5Data', 'Applicant@saveStep5Data');
Route::post('/applicant/submitApplication', 'Applicant@submitApplication');

Route::get('/applicant/saveStep1Data', 'Applicant@dbsStep1');
Route::get('/applicant/saveStep2Data', 'Applicant@dbsStep2');
Route::get('/applicant/saveStep3Data', 'Applicant@dbsStep3');
Route::get('/applicant/saveStep4Data', 'Applicant@dbsStep4');
Route::get('/applicant/saveStep5Data', 'Applicant@dbsStep5');
Route::get('/applicant/submitApplication', 'Applicant@submitApplication');

Route::post('/applicant/addOtherNames', 'Applicant@addOtherNames');
Route::post('/applicant/removeName', 'Applicant@removeName');
Route::post('/applicant/addPreviousAddress', 'Applicant@addPreviousAddress');
Route::post('/applicant/removePreviousAddress', 'Applicant@removePreviousAddress');
Route::post('/applicant/uploadSupportingDocument', 'Applicant@uploadSupportingDocument');
Route::get('/applicant/removeSupportingDocument/{id}', 'Applicant@removeSupportingDocument');

Route::post('/applicant/sendReferenceEmail', 'Applicant@sendReferenceEmail');
Route::get('/reference/{refcode?}', 'Applicant@submitReference');
Route::post('/reference/submitReferenceDetails', 'Applicant@submitReferenceDetails');

#Admin
Route::get('/admin/logo/{filename}', function ($filename) {
    $path = storage_path('app/logos/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->middleware('auth'); // Add more middleware if needed
Route::post('/admin/deleteLogo', [\App\Http\Controllers\Admin::class, 'deleteOrganisationLogo'])->name('admin.deleteLogo');
Route::get('/admin', 'Adminoperator@futureDashboard');
Route::get('/admin/dashboard', 'Adminoperator@futureDashboard');
Route::post('/admin/saveOrganisation', 'Admin@saveOrganisation');
Route::get('/admin/editOrganisation/{id}', 'Admin@editOrganisation');
Route::post('/admin/deleteOrganisation', 'Admin@deleteOrganisation');
Route::get('/admin/dbsReport', 'Admin@dbsReport');
Route::get('/admin/settings/organisation', 'Admin@organisationSettings')->name('admin.organisationSettings');
Route::post('/admin/settings/organisation/update', 'Admin@organisationSettingsUpdate');
Route::post('/admin/settings/organisation/email/save', 'Admin@saveOrganisationEmail')->name('admin.organisationSettings.saveEmail');
Route::get('/admin/settings/organisation/load-email', function (Request $request) {
    $template = \DB::table('organisation_emails')
        ->where('organisation_id', $request->org)
        ->where('template_key', $request->key)
        ->first();
    
    $defaultHtml = view('email_templates.dbs_registration_request')->with([
        'registrationLink' => '#',
    ])->render();

    // Replace other dynamic values with placeholders
    $defaultHtml = str_replace('{{ env("APP_NAME") }}', 'Get ClassifIeD', $defaultHtml);
    $defaultHtml = str_replace('{{ env(\'APP_NAME\') }}', 'Get ClassifIeD', $defaultHtml);
    $defaultHtml = str_replace('{{ URL::to(\'/images/logo_getclassifiedBackground.jpg\') }}', '/images/logo_getclassifiedBackground.jpg', $defaultHtml);
    $defaultHtml = str_replace('{{ env(\'APP_URL\') }}login', '#', $defaultHtml);
    $defaultHtml = str_replace('{{ date(\'Y\') }}', date('Y'), $defaultHtml);

    return response()->json([
        'custom_content' => $template->custom_content ?? '',
        'use_default' => $template->use_default ?? true,
        'default_content' => $defaultHtml,
    ]);
});


Route::get('/notifications/', 'Notifications@index');
Route::post('/notifications/dismissNotification', 'Notifications@dismissNotification');
Route::get('/notifications/viewNotification/{id}', 'Notifications@viewNotification');
Route::get('/notifications/resolveNotification/{id}', 'Notifications@resolveNotification');
#Users
Route::get('/users', 'User@viewUsersList');
Route::get('/users/applicants', 'User@viewApplicantsList');
Route::get('/users/consolidatedSearch', 'Adminoperator@allApplications');
Route::get('/users/consolidatedSearch/{applicationStatus?}', 'Adminoperator@allApplications');
Route::post('/users/updateUserRole', 'User@updateUserRole');
Route::post('/users/updateUserOrganisation', 'User@updateUserOrganisation');
Route::get('/users/viewProfile/{id?}', 'User@viewProfile');
Route::get('/users/editProfile/{id}', 'User@editProfile');
Route::post('/users/updateUserDetails', 'User@updateUserDetails');
Route::post('/users/deleteUser', 'User@deleteUser');
Route::get('/users/addUser', 'User@addUser');
Route::post('/users/saveUser', 'User@saveUser');
Route::post('/users/purgeUser', 'User@purgeUser');
Route::get('/forgotPassword', 'Login@forgotPassword');
Route::post('/forgotPassword', 'Login@sendResetLink');
Route::get('/passwordReset/{resetCode?}', 'Login@passwordReset');
Route::get('/updatePassword', 'Login@index');
Route::post('/updatePassword', 'Login@updatePassword');

Route::post('/users/markUsersToDelete', 'User@markUsersToDelete');

#Site Users
Route::get('/siteuser', 'Adminoperator@futureDashboard');
Route::get('/siteuser/dashboard', 'Adminoperator@futureDashboard');

#Applications
Route::get('/applications', 'Applications@searchApplications');
Route::get('/applications/pendingRequests', 'Applications@pendingRequests');
Route::post('/applications/removeRequest', 'Applications@removeRequest');
Route::post('/applications/resendRequest', 'Applications@resendRequest');
Route::get('/applications/searchApplications', 'Applications@searchApplications');
Route::get('/applications/newApplicationRequest', 'Applications@newApplicationRequest');
Route::post('/applications/sendApplicationRequest', 'Applications@sendApplicationRequest');
Route::get('/applications/viewApplicant/{applicantID}', 'Applications@viewApplicant');
Route::get('/applications/viewApplicationDetails/{applicationID}', 'Applications@viewApplicationDetails');
Route::post('/applications/updateGDPRStatus', 'Applications@updateGDPRStatus');
Route::get('/applications/reviewBPSSVRDetails/{applicantID}/{option?}', 'Applications@reviewBPSSVRDetails');
Route::post('/applications/updateBPSSVRDetails', 'Applications@updateBPSSVRDetails');
Route::post('/applications/addDocumentVerifiedDetails', 'Applications@addDocumentVerifiedDetails');
Route::post('/applications/removeDocumentVerifiedDetails', 'Applications@removeDocumentVerifiedDetails');
Route::get('/applications/downloadBPSSVRPDF/{applicationID}', 'Applications@downloadBPSSVRPDF');
Route::get('/applications/loadReferenceLog/{vrID}', 'Applications@loadReferenceLog');
Route::get('/applications/downloadReference/{referenceFormId}', 'Applications@downloadReference');

Route::post('/applications/markApplicationForReview', 'Applications@markApplicationForReview');
Route::post('/applications/markApplicationCompleteSite', 'Applications@markApplicationCompleteSite');
Route::post('/applications/resetVrCompletion', 'Applications@resetVrCompletion');

Route::post('/applications/submitToDBS', 'Applications@submitToDBS');

Route::get('/applications/getApplicationResult/{applicationID}', 'Applications@int023GetApplicationResult');
Route::get('/applications/getApplicationResultForm', 'Applications@int023GetApplicationResultForm');
Route::post('/applications/getApplicationResultForm', 'Applications@int023GetApplicationResultFormProcess');
Route::get('/applications/checkApplicationsStatus/{applicationID}', 'Applications@int025CheckApplicationsStatus');

Route::post('/applications/updateChecklist', [\App\Http\Controllers\Applications::class, 'updateChecklist'])->name('updateChecklist');
#Admin Operator

Route::get('/adminoperator', 'Adminoperator@futureDashboard');
Route::get('/adminoperator/dashboard', 'Adminoperator@futureDashboard');


Route::get('/adminoperator/newApplicationRequest', 'Adminoperator@newApplicationRequest');
Route::post('/adminoperator/sendApplicationRequest', 'Adminoperator@sendApplicationRequest');
Route::get('/adminoperator/applicants', 'Adminoperator@applicants');
Route::get('/adminoperator/applicants/{applicationStatus?}', 'Adminoperator@applicants');
Route::get('/adminoperator/viewApplicant/{applicantID}', 'Adminoperator@viewApplicant');
Route::get('/adminoperator/editApplicant/{applicantID}', 'Adminoperator@editApplicant');
Route::post('/adminoperator/updateUserFiles/', 'Adminoperator@updateUserFiles');
Route::post('/adminoperator/removeFile/', 'Adminoperator@removeFile');
Route::post('/adminoperator/addUserFiles', [\App\Http\Controllers\Adminoperator::class, 'addUserFiles'])->name('addUserFiles');
Route::post('/adminoperator/saveChecklist', [\App\Http\Controllers\Adminoperator::class, 'saveChecklist'])->name('saveChecklist');
Route::get('/applications/completedApplications', 'Adminoperator@completedApplications');
Route::post('/applications/completedApplicationsData', 'Adminoperator@completedApplicationsData');
Route::get('/applications/inprogressApplications', 'Adminoperator@inprogressApplications');
Route::get('/applications/allApplications', 'Adminoperator@allApplications');
Route::post('/applications/allApplicationsData', 'Adminoperator@allApplicationsData');
Route::post('/applications/returnVR', 'Adminoperator@returnVR')->name('applications.returnVR');

#Messaging
Route::post('/applications/sendRegistrationSms', [ApplicationsController::class, 'sendRegistrationSms'])
    ->name('applications.sendRegistrationSms');
Route::get('/superuser/candidate-messaging', [\App\Http\Controllers\Adminoperator::class, 'candidateMessagingIndex'])
        ->name('candidateMessaging.index');
Route::post('/superuser/candidate-messaging/compose', [\App\Http\Controllers\Adminoperator::class, 'candidateMessagingCompose'])
        ->name('candidateMessaging.compose');
Route::post('/superuser/candidate-messaging/send', [\App\Http\Controllers\Adminoperator::class, 'candidateMessagingSend'])
        ->name('candidateMessaging.send');



#Help Center
Route::get('/help/helpCentre', 'Infopages@helpCenter');
#CronJobs
Route::get('/cron/checkDBS/{checkType?}', 'Cronjobs@checkDBS');
Route::get('/cron/generateDbsSubmissionReportCronTask', 'Cronjobs@generateDbsSubmissionReportCronTask');
Route::get('/cron/sendEmailsFromQueue', 'Cronjobs@sendEmailsFromQueue');

#Reports
Route::get('/reports/applicationsReport', 'Reports@applicationsReport');
Route::post('/reports/generateApplicationsReport', 'Reports@generateApplicationsReport');
Route::get('/reports/trackerReport', 'Reports@trackerReport');
Route::post('/reports/generateTrackerReport', 'Reports@generateTrackerReport');
Route::post('/reports/generateDbsSubmissionReport', 'Reports@generateDbsSubmissionReport');

#Download
Route::get('/downloadfile/{resourceType}/{resourceId}/{preview?}', 'Controller@downloadResource');
Route::get('/mergertest', 'Controller@mergertest');

#Cookies
Route::post('/setGdprCookie', 'Controller@setGdprCookie');


#Temp
Route::get('/users/getConsolidateSerachAjax', 'Adminoperator@allApplications');

#Registeration email change
#Route::post('/applications/updateEmail', 'Applicant@updateEmail');

#Yoti Identity check
Route::get('/applicant/yoti', [\App\Http\Controllers\Applicant::class, 'yotiPage']);
Route::get('/error', [\App\Http\Controllers\Applicant::class, 'yotiError']);
Route::get('/success', [\App\Http\Controllers\Applicant::class, 'yotiSuccess']);
Route::get('/applicant/yotiDisclaimer', [\App\Http\Controllers\Applicant::class, 'yotiStart']);
Route::get('/yoti-report/{id}', [\App\Http\Controllers\Applicant::class, 'showYotiReport'])->name('yoti.report');

Route::get('/candidate/idv/iframe', [\App\Http\Controllers\Applicant::class, 'candidateIdvIframe'])
    ->name('candidate.idv.iframe');

Route::get('/yoti/report/{userID}/pdf', [\App\Http\Controllers\Applicant::class, 'downloadYotiPdf'])
    ->name('yoti.report.pdf');


Route::get('/yoti/sendBack/{id}', [\App\Http\Controllers\Applicant::class, 'yotiSendBack']);

Route::post('/superuser/mass-email/send', 'Admin@massEmailsend')->name('massEmail.send');

Route::get('/Home', [\App\Http\Controllers\FrontEnd::class, 'homePage']);

#Blog
Route::get('/Blog', [\App\Http\Controllers\FrontEnd::class, 'blogPage'])->name('blog.index');
Route::post('/admin/blog/store', [\App\Http\Controllers\Admin::class, 'storeBlogPost'])->name('admin.blog.store');
Route::get('/admin/createBlog', function () {
    return view('admin.createBlog');
});
Route::get('/blog/{slug}', [\App\Http\Controllers\FrontEnd::class, 'viewBlogPost'])->name('blog.view');
Route::get('/admin/blog/manage', [\App\Http\Controllers\Admin::class, 'manageBlogPosts'])->name('admin.blog.manage');
Route::get('/admin/blog/edit/{id}', [\App\Http\Controllers\Admin::class, 'editBlogPost'])->name('admin.blog.edit');
Route::post('/admin/blog/update/{id}', [\App\Http\Controllers\Admin::class, 'updateBlogPost'])->name('admin.blog.update');
Route::post('/admin/blog/delete/{id}', [\App\Http\Controllers\Admin::class, 'deleteBlogPost'])->name('admin.blog.delete');



Route::get('/ContactUs', function () {
    return view('FrontEnd.contactUs');
});
Route::get('/Contact/accessibility-feedback', function () {
    return view('FrontEnd.Contact.accessibilityFeedback');
});
Route::get('/AboutUs', function () {
    return view('FrontEnd.AboutUs.aboutUs');
});
Route::get('/Candidates', function () {
    return view('FrontEnd.candidates');
});
Route::get('/Clients', function () {
    return view('FrontEnd.clients');
});
Route::get('/Services', function () {
    return view('FrontEnd.services');
});
Route::get('/sitemap', [App\Http\Controllers\SitemapController::class, 'sitemapHtml'])->name('sitemap');
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.xml');

Route::get('/AboutUs/security-at-classified', function () {
    return view('FrontEnd.AboutUs.security-at-classified');
});
Route::get('/Resources/glossary', function () {
    return view('FrontEnd.Resources.glossary');
});
Route::get('/AboutUs/Certifications', function () {
    return view('FrontEnd.AboutUs.certs');
});
Route::get('/Resources', function () {
    return view('FrontEnd.Resources.resources');
});

Route::get('/Services/BPSS', function () {
    return view('FrontEnd.Services.bpssCheck');
});
Route::get('/Services/DBS-Basic', function () {
    return view('FrontEnd.Services.dbsBasic');
});
Route::get('/Services/Digital-ID-Verification', function () {
    return view('FrontEnd.Services.digitalIDverification');
});
Route::get('/Services/right-to-work', function () {
    return view('FrontEnd.Services.RTW');
});
Route::get('/Services/academic-history', function () {
    return view('FrontEnd.Services.academic');
});
Route::get('/Services/employment-history', function () {
    return view('FrontEnd.Services.employment');
});
Route::get('/Services/personal-references', function () {
    return view('FrontEnd.Services.personal');
});

Route::get('/finalReport/review/{id}', [\App\Http\Controllers\Reports::class, 'reviewFinalReport']);
Route::get('/finalReport/generate/{id}', [\App\Http\Controllers\Reports::class, 'generateFinalReport']);



/*
Route::get('/candidate/welcome', [\App\Http\Controllers\candidate::class, 'candidateWelcome'])->name('candidate.welcome');
Route::get('/candidate/personalDetails', [\App\Http\Controllers\candidate::class, 'candidatePersonalDetails'])->name('candidate.personal.edit');
Route::post('/candidate/personal-details', [\App\Http\Controllers\candidate::class, 'savePersonal'])->name('candidate.personal.save');
Route::get('/candidate/DBS-basic', [\App\Http\Controllers\candidate::class, 'candidateDBSbasic'])->name('candidate.dbsbasic.edit');
Route::post('/candidate/DBS-basic-Save', [\App\Http\Controllers\candidate::class, 'saveBasicDBS'])->name('candidate.dbsbasic.save');
Route::get('/candidate/identityVerification', [\App\Http\Controllers\candidate::class, 'candidateIdentityVerification'])->name('candidate.identity.edit');
Route::get('/candidate/employmentHistory', [\App\Http\Controllers\candidate::class, 'candidateEmploymentHistory'])->name('candidate.employment.edit');
Route::post('/candidate/employmentHistory-save', [\App\Http\Controllers\candidate::class, 'saveEmploymentHistory'])->name('candidate.employment.save');
Route::get('/candidate/academicHistory', [\App\Http\Controllers\candidate::class, 'candidateAcademicHistory'])->name('candidate.academic.edit');
Route::post('/candidate/academicHistory-save', [\App\Http\Controllers\candidate::class, 'saveAcademicHistory'])->name('candidate.academic.save');
Route::get('/candidate/personalReferences', [\App\Http\Controllers\candidate::class, 'candidatePersonalReferences'])->name('candidate.references.personal.edit');
Route::post('/candidate/personalReferences-save', [\App\Http\Controllers\candidate::class, 'savePersonalReferences'])->name('candidate.references.personal.save');
Route::get('/candidate/supportingDocs', [\App\Http\Controllers\candidate::class, 'candidateSupportingDocs'])->name('candidate.supporting.edit');
Route::post('/candidate/markSupportingComplete', [\App\Http\Controllers\candidate::class, 'markSupportingComplete'])->name('candidate.supporting.complete');
Route::get('/candidate/supportingDocument/{id}/download', [\App\Http\Controllers\candidate::class, 'downloadSupportingDocument'])->name('candidate.supporting.download');
Route::post('/candidate/deleteSupportingDocument', [\App\Http\Controllers\candidate::class, 'deleteSupportingDocument'])->name('candidate.supporting.delete');
Route::get('/candidate/saveAndSubmit', [\App\Http\Controllers\candidate::class, 'candidateSaveAndSubmit'])->name('candidate.review');
Route::post('/candidate/submit', [\App\Http\Controllers\candidate::class, 'candidateSubmit'])->name('candidate.submit');
Route::get('/candidate/help', [\App\Http\Controllers\candidate::class, 'candidateHelp']);
Route::get('/candidate/dashboard', [\App\Http\Controllers\candidate::class, 'candidateDashboard'])->name('candidate.dashboard');
*/

#bulk upload
Route::post('/adminoperator/previewApplicantsFromFile', 'Applications@previewApplicantsFromFile');
Route::post('/adminoperator/sendBulkApplicationRequest', 'Applications@sendBulkApplicationRequest')->name('bulk.application.submit');

Route::get(
    '/applications/pendingRequests/{id}/edit',
    'Adminoperator@editPendingApplicant'
)->name('applications.pending.edit');

Route::post(
    '/applications/pendingRequests/{id}/update',
    'Adminoperator@updatePendingApplicant'
)->name('applications.pending.update');