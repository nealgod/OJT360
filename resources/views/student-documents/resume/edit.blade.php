<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Edit Resume') }}
            </h2>
            <a href="{{ route('student-documents.index') }}" class="text-ojt-primary hover:text-maroon-700">
                ← Back to Documents
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('student-documents.resume.update', $resume) }}" id="resumeForm" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Personal Information -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="personal_info_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" id="personal_info_name" name="personal_info[name]" 
                                   value="{{ old('personal_info.name', $resume->personal_info['name'] ?? '') }}"
                                   required class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            @error('personal_info.name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="personal_info_job_title" class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                            <input type="text" id="personal_info_job_title" name="personal_info[job_title]"
                                   value="{{ old('personal_info.job_title', $resume->personal_info['job_title'] ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                                   placeholder="e.g., Student, Aspiring Professional, OJT Trainee">
                            @error('personal_info.job_title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="personal_info_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="personal_info_email" name="personal_info[email]"
                                   value="{{ old('personal_info.email', $resume->personal_info['email'] ?? '') }}"
                                   required class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            @error('personal_info.email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="personal_info_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" id="personal_info_phone" name="personal_info[phone]"
                                   value="{{ old('personal_info.phone', $resume->personal_info['phone'] ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            @error('personal_info.phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="personal_info_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" id="personal_info_address" name="personal_info[address]"
                                   value="{{ old('personal_info.address', $resume->personal_info['address'] ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            @error('personal_info.address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-1">Profile Image</label>
                            @if($resume->profile_image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($resume->profile_image) }}" alt="Current profile image" class="w-24 h-24 object-cover rounded-lg border border-gray-300">
                                    <p class="text-xs text-gray-500 mt-1">Current image</p>
                                </div>
                            @endif
                            <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/webp"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-ojt-primary file:text-white hover:file:bg-maroon-700">
                            <p class="mt-1 text-sm text-gray-500">Upload a new profile photo (JPG, PNG, WEBP, max 5MB). Leave empty to keep current image.</p>
                            @error('profile_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Objective/Summary -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-2">Objective / Professional Summary</h3>
                    <textarea id="objective" name="objective" rows="3" maxlength="250"
                              class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                              placeholder="Short 1–3 sentence summary (max 250 characters)">{{ old('objective', $resume->objective ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Maximum 250 characters.</p>
                    @error('objective')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Education -->
                <div class="bg-white shadow sm:rounded-lg p-6" x-data="{
                    showSeniorHigh: {{ (isset($resume->education[1]) && !empty($resume->education[1]['institution'])) ? 'true' : 'false' }},
                    showJuniorHigh: {{ (isset($resume->education[2]) && !empty($resume->education[2]['institution'])) ? 'true' : 'false' }},
                    showElementary: {{ (isset($resume->education[3]) && !empty($resume->education[3]['institution'])) ? 'true' : 'false' }},
                    addNextEducation() {
                        if (!this.showSeniorHigh) {
                            this.showSeniorHigh = true;
                        } else if (!this.showJuniorHigh) {
                            this.showJuniorHigh = true;
                        } else if (!this.showElementary) {
                            this.showElementary = true;
                        }
                    }
                }">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ojt-dark">Education</h3>
                        <button type="button" @click="addNextEducation()" 
                                x-show="!showSeniorHigh || !showJuniorHigh || !showElementary"
                                class="text-sm text-ojt-primary hover:text-maroon-700">+ Add Education</button>
                    </div>
                    
                    <!-- College/University (Always visible, cannot be removed) -->
                    <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-700 mb-3">
                            College/University
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                                <input type="text" name="education[0][institution]" 
                                       value="{{ old('education.0.institution', $resume->education[0]['institution'] ?? '') }}"
                                       placeholder="e.g., Eastern Visayas State University"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Degree/Course</label>
                                <input type="text" name="education[0][degree]" 
                                       value="{{ old('education.0.degree', $resume->education[0]['degree'] ?? '') }}"
                                       placeholder="e.g., Bachelor of Science in Information Technology"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <input type="text" name="education[0][department]" 
                                       value="{{ old('education.0.department', $resume->education[0]['department'] ?? '') }}"
                                       placeholder="e.g., College of Engineering"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                                <select name="education[0][year_level]" 
                                        class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                    <option value="">Select Year Level</option>
                                    @foreach(($yearLevels ?? []) as $code => $label)
                                        <option value="{{ $label }}" {{ (old('education.0.year_level', $resume->education[0]['year_level'] ?? '') == $label) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="education[0][type]" value="college">
                    </div>

                    <!-- Senior High School (Can be shown/hidden) -->
                    <div x-show="showSeniorHigh" x-transition class="mb-4 p-4 border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-semibold text-gray-700">
                                Senior High School
                            </h4>
                            <button type="button" @click="showSeniorHigh = false" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                                <input type="text" name="education[1][institution]" 
                                       value="{{ old('education.1.institution', $resume->education[1]['institution'] ?? '') }}"
                                       placeholder="e.g., Tacloban National High School"
                                       :disabled="!showSeniorHigh"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Strand</label>
                                <input type="text" name="education[1][strand]" 
                                       value="{{ old('education.1.strand', $resume->education[1]['strand'] ?? '') }}"
                                       placeholder="e.g., STEM, ABM, HUMSS, TVL"
                                       :disabled="!showSeniorHigh"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Year/Period</label>
                                <input type="text" name="education[1][year_period]" 
                                       value="{{ old('education.1.year_period', $resume->education[1]['year_period'] ?? '') }}"
                                       placeholder="e.g., 2018-2020"
                                       :disabled="!showSeniorHigh"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="education[1][type]" :value="showSeniorHigh ? 'senior_high' : ''">

                    <!-- Junior High School (Can be shown/hidden) -->
                    <div x-show="showJuniorHigh" x-transition class="mb-4 p-4 border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-semibold text-gray-700">
                                Junior High School
                            </h4>
                            <button type="button" @click="showJuniorHigh = false" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                                <input type="text" name="education[2][institution]" 
                                       value="{{ old('education.2.institution', $resume->education[2]['institution'] ?? '') }}"
                                       placeholder="e.g., Tacloban City National High School"
                                       :disabled="!showJuniorHigh"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Year/Period</label>
                                <input type="text" name="education[2][year_period]" 
                                       value="{{ old('education.2.year_period', $resume->education[2]['year_period'] ?? '') }}"
                                       placeholder="e.g., 2014-2018"
                                       :disabled="!showJuniorHigh"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="education[2][type]" :value="showJuniorHigh ? 'junior_high' : ''">

                    <!-- Elementary (Can be shown/hidden) -->
                    <div x-show="showElementary" x-transition class="mb-4 p-4 border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-semibold text-gray-700">
                                Elementary
                            </h4>
                            <button type="button" @click="showElementary = false" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                                <input type="text" name="education[3][institution]" 
                                       value="{{ old('education.3.institution', $resume->education[3]['institution'] ?? '') }}"
                                       placeholder="e.g., Tacloban Central Elementary School"
                                       :disabled="!showElementary"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Year/Period</label>
                                <input type="text" name="education[3][year_period]" 
                                       value="{{ old('education.3.year_period', $resume->education[3]['year_period'] ?? '') }}"
                                       placeholder="e.g., 2008-2014"
                                       :disabled="!showElementary"
                                       class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="education[3][type]" :value="showElementary ? 'elementary' : ''">
                </div>

                <!-- Work Experience -->
                <div class="bg-white shadow sm:rounded-lg p-6" x-data="{ experiences: {{ json_encode(old('work_experience', $resume->work_experience ?? [['company' => '', 'position' => '', 'start_date' => '', 'end_date' => '', 'description' => '']])) }} }">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ojt-dark">Work Experience</h3>
                        <button type="button" @click="experiences.push({company: '', position: '', start_date: '', end_date: '', description: ''})"
                                class="text-sm text-ojt-primary hover:text-maroon-700">+ Add Experience</button>
                    </div>
                    <template x-for="(exp, index) in experiences" :key="index">
                        <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Experience <span x-text="index + 1"></span></span>
                                <button type="button" @click="experiences.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                    <input type="text" x-model="exp.company" :name="`work_experience[${index}][company]`"
                                           placeholder="e.g., Company Name or Organization"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                    <input type="text" x-model="exp.position" :name="`work_experience[${index}][position]`"
                                           placeholder="e.g., Intern, Assistant, Trainee"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="text" x-model="exp.start_date" :name="`work_experience[${index}][start_date]`" 
                                           placeholder="e.g., 01/2024"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="text" x-model="exp.end_date" :name="`work_experience[${index}][end_date]`" 
                                           placeholder="e.g., 06/2024 or Present"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea x-model="exp.description" :name="`work_experience[${index}][description]`" rows="3"
                                              placeholder="Describe your responsibilities and achievements..."
                                              class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Skills -->
                <div class="bg-white shadow sm:rounded-lg p-6" x-data="{ skills: {{ json_encode(old('skills', $resume->skills ?? [''])) }} }">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ojt-dark">Skills</h3>
                        <button type="button" @click="skills.push('')"
                                class="text-sm text-ojt-primary hover:text-maroon-700">+ Add Skill</button>
                    </div>
                    <div class="text-xs text-gray-500 mb-3">Add your technical skills, soft skills, and languages</div>
                    <template x-for="(skill, index) in skills" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" x-model="skills[index]" :name="`skills[${index}]`"
                                   class="flex-1 rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                                   placeholder="e.g., Communication, Teamwork, Microsoft Office, English">
                            <button type="button" @click="skills.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                        </div>
                    </template>
                </div>

                <!-- Certifications -->
                <div class="bg-white shadow sm:rounded-lg p-6" x-data="{ certifications: {{ json_encode(old('certifications', $resume->certifications ?? [['name' => '']])) }} }">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ojt-dark">Certifications</h3>
                        <button type="button" @click="certifications.push({name: ''})"
                                class="text-sm text-ojt-primary hover:text-maroon-700">+ Add Certification</button>
                    </div>
                    <div class="text-xs text-gray-500 mb-3">Add professional certifications, licenses, or training programs</div>
                    <template x-for="(cert, index) in certifications" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" x-model="certifications[index].name" :name="`certifications[${index}][name]`"
                                   class="flex-1 rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                                   placeholder="e.g., First Aid Training, TESDA NC II, Seminar Certificate">
                            <button type="button" @click="certifications.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                        </div>
                    </template>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('student-documents.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700">
                        Update Resume
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
