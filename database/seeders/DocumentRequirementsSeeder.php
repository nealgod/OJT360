<?php

namespace Database\Seeders;

use App\Models\DocumentRequirement;
use Illuminate\Database\Seeder;

class DocumentRequirementsSeeder extends Seeder
{
    public function run(): void
    {
        $requirements = [
            // Pre-deployment Requirements
            [
                'name' => 'Copy of Certificate of Registration',
                'description' => 'Current semester certificate of registration',
                'type' => 'pre_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit a clear copy of your current semester certificate of registration.',
            ],
            [
                'name' => 'Copy of Report of Grades',
                'description' => 'Report of grades with notation "qualified to enroll in OJT/Practicum/Internship course"',
                'type' => 'pre_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit your report of grades with the required notation from your academic advisor.',
            ],
            [
                'name' => 'Application Letter and PDS/Resume',
                'description' => 'Application letter and Personal Data Sheet or Resume',
                'type' => 'pre_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'doc', 'docx'],
                'max_file_size_mb' => 3,
                'instructions' => 'Submit both your application letter and updated resume/PDS in one document or separate files.',
            ],
            [
                'name' => 'Medical Certificate',
                'description' => 'Medical clearance from a licensed physician',
                'type' => 'pre_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 5,
                'instructions' => 'Medical certificate must be dated within 6 months and issued by a licensed physician.',
            ],
            [
                'name' => 'Notarized Parent\'s Consent Form',
                'description' => 'Notarized consent form from parent or guardian',
                'type' => 'pre_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit a notarized consent form from your parent or guardian. The form must be properly notarized.',
            ],
            [
                'name' => 'Insurance Certificate',
                'description' => 'Proof of insurance coverage (SSS, PhilHealth, or private insurance)',
                'type' => 'pre_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 3,
                'instructions' => 'Submit a copy of your insurance certificate or card.',
            ],
            [
                'name' => 'Certificates of Participation/Attendance',
                'description' => 'Certificates from required pre-deployment activities (Anti-Sexual Harassment, Food and Sanitation, etc.)',
                'type' => 'pre_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit certificates from all required seminars and training sessions.',
            ],
            [
                'name' => 'Letter of Acceptance (Optional)',
                'description' => 'Letter of acceptance from training company (if available)',
                'type' => 'pre_placement',
                'is_required' => false,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 3,
                'instructions' => 'Submit if you have already received a letter of acceptance from your training company.',
            ],
            [
                'name' => 'Recommendation',
                'description' => 'Recommendation letter from faculty or previous employer',
                'type' => 'pre_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 3,
                'instructions' => 'Submit a recommendation letter from a faculty member or previous employer.',
            ],

            // Post-deployment Requirements
            [
                'name' => 'Documentation Report',
                'description' => 'Comprehensive documentation of OJT activities and achievements',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'doc', 'docx'],
                'max_file_size_mb' => 10,
                'instructions' => 'Submit a detailed documentation report of your OJT activities, projects, and achievements.',
            ],
            [
                'name' => 'Company Profile',
                'description' => 'Profile of the training company',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'doc', 'docx'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit a company profile or brochure of your training company.',
            ],
            [
                'name' => 'Weekly Accomplishment Report',
                'description' => 'Weekly reports of accomplishments during OJT',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'doc', 'docx'],
                'max_file_size_mb' => 5,
                'instructions' => 'Compile all your weekly accomplishment reports into one document.',
            ],
            [
                'name' => 'OJT Learning Experience Journal',
                'description' => 'Personal journal documenting learning experiences',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'doc', 'docx'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit your learning experience journal documenting your OJT journey.',
            ],
            [
                'name' => 'Pertinent Documents',
                'description' => 'Other relevant documents from OJT experience',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 10,
                'instructions' => 'Submit any other relevant documents from your OJT experience.',
            ],
            [
                'name' => 'Personal Data Sheet or Resume',
                'description' => 'Updated PDS or resume after OJT',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'doc', 'docx'],
                'max_file_size_mb' => 3,
                'instructions' => 'Submit your updated resume or PDS reflecting your OJT experience.',
            ],
            [
                'name' => 'Application Letter',
                'description' => 'Application letter for OJT (post-placement copy)',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'doc', 'docx'],
                'max_file_size_mb' => 3,
                'instructions' => 'Submit a copy of your application letter for OJT.',
            ],
            [
                'name' => 'Letter of Acceptance (Post-placement)',
                'description' => 'Letter of acceptance from training company (final copy)',
                'type' => 'post_placement',
                'is_required' => false,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 3,
                'instructions' => 'Submit the final letter of acceptance from your training company.',
            ],
            [
                'name' => 'Recommendation Letter',
                'description' => 'Recommendation letter from supervisor or company',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 3,
                'instructions' => 'Submit a recommendation letter from your supervisor or training company.',
            ],
            [
                'name' => 'Certificate of Completion (Photocopy)',
                'description' => 'Photocopy of certificate of completion from company',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit a clear photocopy of your certificate of completion.',
            ],
            [
                'name' => 'Supervisor\'s Evaluation Form',
                'description' => 'Sealed evaluation form from supervisor in signed envelope',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit the supervisor\'s evaluation form sealed in a long mailing envelope with signature across the tab.',
            ],
            [
                'name' => 'Authenticated Copy of DTR',
                'description' => 'Authenticated Daily Time Record',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 5,
                'instructions' => 'Submit an authenticated copy of your Daily Time Record.',
            ],
            [
                'name' => 'Photo Documentation',
                'description' => 'Photos documenting OJT activities and workplace',
                'type' => 'post_placement',
                'is_required' => true,
                'file_types' => ['pdf', 'jpg', 'jpeg', 'png', 'zip'],
                'max_file_size_mb' => 10,
                'instructions' => 'Submit photos documenting your OJT activities, workplace, and key moments.',
            ],
            [
                'name' => 'Other Documents Not Specified',
                'description' => 'Any other documents required by the school',
                'type' => 'post_placement',
                'is_required' => false,
                'file_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
                'max_file_size_mb' => 10,
                'instructions' => 'Submit any other documents that may be required by your school.',
            ],
        ];

        foreach ($requirements as $requirement) {
            DocumentRequirement::create($requirement);
        }
    }
}
