<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spipu\Html2Pdf\Html2Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class Admin extends Controller
{

	/*
    |--------------------------------------------------------------------------
    | Admin Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling the Admin logic
    |
    */

    /**
     * index fallback
     *
     * 
     */

    public function index()
    {
        return redirect("/login");
        
    }

    /**
     * save organisation details
     * 
     * @param  int  $id
     */

    public function saveOrganisation(Request $request)
    {
        if(!$this->checkAccess('superuser')) return redirect("/login");
        
        $requestVars = $request->all();
        $validateArray = [
            'organisationName'  => 'required|max:255',
            'organisationEmail' => 'required|email|max:255',
        ];

        $this->validate($request, $validateArray); 

        $dbsEmailRow = \DB::table('settings')
            ->where('settingName', 'consent_responsible_body_email')
            ->value('settingValue');

        $organisationDetails = [
            'organisationName'      => $requestVars['organisationName'],
            'organisationStatus'    => 1,
            'designatedEmailList'   => $requestVars['organisationEmail'],
            'organisationEmail'     => $dbsEmailRow,
            'passwordLength'        => 14,
            'lowercaseCharacters'   => 1,
            'uppercaseCharacters'   => 1,
            'specialCharacters'     => 1,
            'mfaAuthSiteUser'       => 1,
            'mfaAuthUser'           => 1,
            'totalCompleted'        => 0,
        ];
       
        $newOrganisationID = \DB::table('organisations')->insertGetId($organisationDetails);
        if (!empty($newOrganisationID) && is_numeric($newOrganisationID)){
            return redirect("/admin/settings/organisation"); 
        } else {
            return back()->withInput();
        }
    }

    /**
     * Edit organisation profile
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function editOrganisation($organisationID)
    {
        if(!isset($organisationID) || empty($organisationID) || !$this->checkAccess('superuser')) return redirect("/login");

        $organisationDetails = \DB::table('organisations')->select('organisations.*')->where(['id' => $organisationID])->first();
        if(!isset($organisationDetails->id) || empty($organisationDetails->id)) return redirect("/login");
       
        return view('admin.editOrganisation', ['organisationDetails' => $organisationDetails]);
        
    }

    public function deleteOrganisationLogo(Request $request)
    {
        $organisationID = $request->input('organisationID');
        $org = \DB::table('organisations')->where('id', $organisationID)->first();

        if ($org && $org->logo) {
            $logoPath = storage_path("app/logos/{$org->logo}");
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }

            \DB::table('organisations')->where('id', $organisationID)->update(['logo' => null]);
        }

        return redirect()->back()->with('success', 'Logo deleted successfully.');
    }




    /**
     * Edit user profile
     * 
     * @param   \Illuminate\Http\Reques
     * @return json
     */

   public function deleteOrganisation(Request $request)
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) return redirect("/login");
        $requestVars = $request->all();
        if ($requestVars['organisationID'] > 0){
            $testDelete = \DB::table('organisations')->where('id', '=', $requestVars['organisationID'])->delete();
            if ($testDelete>0){
                echo 1;                    
            } else echo 0;
        } else echo 0;
        
    }

    public function dbsReport()
    {   
        if (!Auth::check() || !$this->checkAccess('superuser')) return redirect("/login");

        $availableOrganisations = \DB::table('organisations')
            ->select('*')
            ->where(['organisations.organisationStatus'=>1])
            ->get();

        //get search result
        $organisations = \DB::table('organisations')->select('organisations.*')->get();
        return view('admin.dbsReport', ['availableOrganisations' =>$availableOrganisations]);
        
    }

    public function storeBlogPost(Request $request)
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) return redirect("/");
        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'keywords' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);


        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/blog_images'), $filename);
            $imagePath = 'uploads/blog_images/' . $filename;
        }

        $description = Str::limit(strip_tags($validated['content']), 200, '...');

        $slug = Str::slug($validated['title']);


        // Insert blog post into DB
        \DB::table('blog_posts')->insert([
            'title' => $validated['title'],
            'slug' => $slug,
            'keywords' => $validated['keywords'],
            'description' => $description,
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Blog post created successfully!');
    }

    public function manageBlogPosts()
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) return redirect("/");
        $posts = \DB::table('blog_posts')->orderBy('published_at', 'desc')->get();
        return view('admin.manageBlogs', compact('posts'));
    }

    public function deleteBlogPost($id)
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) return redirect("/");
        $post = \DB::table('blog_posts')->where('id', $id)->first();

        if ($post && $post->image_path && file_exists(public_path($post->image_path))) {
            @unlink(public_path($post->image_path));
        }

        \DB::table('blog_posts')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Blog post and image deleted successfully.');
    }

    public function editBlogPost($id)
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) return redirect("/");
        $post = \DB::table('blog_posts')->where('id', $id)->first();

        if (!$post) {
            abort(404);
        }

        return view('admin.editBlogPost', compact('post'));
    }

    public function updateBlogPost(Request $request, $id)
    {
        if (!Auth::check() || !$this->checkAccess('superuser')) return redirect("/");
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'keywords' => 'required|string',
            'content' => 'required|string',
            'published_at' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $post = \DB::table('blog_posts')->where('id', $id)->first();
        $imagePath = $post->image_path;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($imagePath && file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            }

            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/blog_images'), $filename);
            $imagePath = 'uploads/blog_images/' . $filename;
        }

        $description = \Str::limit(strip_tags($validated['content']), 200, '...');

        $slug = Str::slug($validated['title']);

        \DB::table('blog_posts')->where('id', $id)->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'keywords' => $validated['keywords'],
            'description' => $description,
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'published_at' => $validated['published_at'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.blog.manage')->with('success', 'Blog post updated successfully.');
    }


    public function organisationSettings(Request $request) {
        if (!$this->checkAccess('siteuser')) return redirect("/login");

        $currentUserRoles = $this->getCurrentUserRoles();
        $userOrganisations = $this->getUserOrganisations();

        $query = \DB::table('organisations')->select('*');
        if (!in_array('superuser', $currentUserRoles)) {
            $query->whereIn('id', $userOrganisations);
        }

        $organisations = $query->get();

        // Determine selected org ID (default to first accessible)
        $selectedOrgId = $request->query('org', Auth::user()->organisationID ?? $organisations->first()->id ?? null);

        // Check access
        if (!in_array('superuser', $currentUserRoles) && !in_array($selectedOrgId, $userOrganisations)) {
            abort(403, 'Unauthorized access to organisation.');
        }

        $organisationDetails = \DB::table('organisations')->where('id', $selectedOrgId)->first();

        $organisationAdmins = \DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.userID')
            ->where('organisationID', $selectedOrgId)
            ->where('userType', 'admin')
            ->select('users.*', 'user_roles.role')
            ->get();


        return view('admin.organisationSettings', [
            'organisations' => $organisations,
            'selectedOrgId' => $selectedOrgId,
            'currentUserRoles' => $currentUserRoles,
            'organisationDetails' => $organisationDetails,
            'organisationAdmins' => $organisationAdmins
        ]);
    }



    public function organisationSettingsUpdate(Request $request)
    {
        if (!$this->checkAccess('siteuser')) return redirect("/login");

        $request->validate([
            'organisationID' => 'required|exists:organisations,id',
            'organisationName' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'mfaAuthUser' => 'required|int',
            'brand_color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'allowedAppTypes' => 'nullable|array',
            'allowedAppTypes.*' => 'string|in:DBS,BPSS,YOTI,BPSS_REVAL_ONSITE,BPSS_REVAL_OFFSITE',
            'permanent_employee_renewal_years' => 'required|integer|min:1|max:100',
            'contractor_renewal_years'         => 'required|integer|min:1|max:100',
            'data_retention_years'             => 'required|integer|min:2|max:100',
            'screeningChecksPresent' => 'nullable|in:1',
            'screeningChecks' => 'nullable|array',
            'screeningChecks.*' => 'string|exists:screening_check_types,code',
        ]);

        $organisationID = $request->input('organisationID');
        $currentUserRoles = $this->getCurrentUserRoles();

        $updateData = [];

        // Only allow name change for superusers
        if (in_array('superuser', $currentUserRoles)) {
            $updateData['organisationName'] = $request->input('organisationName');
            $updateData['mfaAuthUser'] = $request->input('mfaAuthUser');
            $updateData['allowed_app_types'] = json_encode($request->input('allowedAppTypes', []));
            $updateData['data_retention_years'] = $request->input('data_retention_years');
        }

        // Handle logo upload
        if ($request->input('deleteLogo') == '1') {
            $existing = \DB::table('organisations')->where('id', $request->organisationID)->first();
            if ($existing && $existing->logo) {
                \Storage::delete('logos/' . $existing->logo);
            }

            $updateData['logo'] = NULL;
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->storeAs('logos', $filename); // Stored in storage/app/logos

            // Delete old logo if exists
            $existing = \DB::table('organisations')->where('id', $organisationID)->first();
            if ($existing && $existing->logo) {
                \Storage::delete('logos/' . $existing->logo);
            }

            $updateData['logo'] = $filename;
        }

        $hex = $request->input('brand_color', '#C55359');
        $hex = strtoupper(ltrim($hex, '#'));
        $updateData['brand_color'] = '#'.$hex;

        $updateData['permanent_employee_renewal_years'] = $request->input('permanent_employee_renewal_years');
        $updateData['contractor_renewal_years'] = $request->input('contractor_renewal_years');
        
        // Update organisation
        if (!empty($updateData)) {
            \DB::table('organisations')->where('id', $organisationID)->update($updateData);
        }

        /*
        |--------------------------------------------------------------------------
        | Candidate Portal V2 Screening Checks
        |--------------------------------------------------------------------------
        |
        | screeningChecksPresent is deliberately separate from screeningChecks.
        |
        | If the user unchecks every option there will be no screeningChecks[]
        | value in the request, but screeningChecksPresent will still exist.
        |
        | If an old page or another process posts to this controller without the
        | V2 fields, we don't touch the V2 configuration at all.
        |
        */

        if ($request->has('screeningChecksPresent')) {

            $screeningChecks = $request->input(
                'screeningChecks',
                []
            );

            if (!is_array($screeningChecks)) {
                $screeningChecks = [];
            }

            app(
                \App\Services\Screening\OrganisationScreeningService::class
            )->syncOrganisationChecks(
                $organisationID,
                $screeningChecks
            );
        }

        return redirect()->back()->with('success', 'Organisation settings updated.');
    }

    public function saveOrganisationEmail(Request $request)
    {
        $request->validate([
            'organisationID' => 'required|exists:organisations,id',
            'template_key' => 'required|string',
            'use_default' => 'required|boolean',
            'custom_content' => 'nullable|string'
        ]);

        \DB::table('organisation_emails')->updateOrInsert(
            [
                'organisation_id' => $request->organisationID,
                'template_key' => $request->template_key,
            ],
            [
                'use_default' => $request->use_default,
                'custom_content' => $request->use_default ? null : $request->custom_content,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Email template saved.');
    }


}