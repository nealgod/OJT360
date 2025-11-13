<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
                {{ __('Create Resume') }}
            </h2>
            <a href="{{ route('resume.index') }}" class="text-ojt-primary hover:text-maroon-700">
                ← Back to Resume
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('resume.store') }}" id="resumeForm" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Personal Information -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="personal_info_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" id="personal_info_name" name="personal_info[name]" 
                                   value="{{ old('personal_info.name', $defaultData['personal_info']['name'] ?? '') }}"
                                   required class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            @error('personal_info.name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="personal_info_job_title" class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                            <input type="text" id="personal_info_job_title" name="personal_info[job_title]"
                                   value="{{ old('personal_info.job_title', $defaultData['personal_info']['job_title'] ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                                   placeholder="e.g., Aspiring Engineer, Future Educator, Creative Professional">
                            @error('personal_info.job_title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="personal_info_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="personal_info_email" name="personal_info[email]"
                                   value="{{ old('personal_info.email', $defaultData['personal_info']['email'] ?? '') }}"
                                   required class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            @error('personal_info.email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="personal_info_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" id="personal_info_phone" name="personal_info[phone]"
                                   value="{{ old('personal_info.phone', $defaultData['personal_info']['phone'] ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            @error('personal_info.phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="personal_info_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" id="personal_info_address" name="personal_info[address]"
                                   value="{{ old('personal_info.address', $defaultData['personal_info']['address'] ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                            @error('personal_info.address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-1">Profile Image</label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-ojt-primary file:text-white hover:file:bg-maroon-700">
                            <p class="mt-1 text-sm text-gray-500">Upload your profile photo (JPG, PNG, max 2MB)</p>
                            @error('profile_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Objective/Summary -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Objective / Professional Summary</h3>
                    <textarea id="objective" name="objective" rows="4"
                              class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                              placeholder="Write a brief summary of your career objectives or professional summary...">{{ old('objective', '') }}</textarea>
                    @error('objective')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Education -->
                <div class="bg-white shadow sm:rounded-lg p-6" x-data="{ educations: {{ json_encode(old('education', $defaultData['education'] ?? [['institution' => '', 'degree' => '', 'department' => '', 'year' => '']])) }} }">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ojt-dark">Education</h3>
                        <button type="button" @click="educations.push({institution: '', degree: '', department: '', year: ''})"
                                class="text-sm text-ojt-primary hover:text-maroon-700">+ Add Education</button>
                    </div>
                    <template x-for="(edu, index) in educations" :key="index">
                        <div class="mb-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Education <span x-text="index + 1"></span></span>
                                <button type="button" @click="educations.splice(index, 1)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Institution</label>
                                    <input type="text" x-model="edu.institution" :name="`education[${index}][institution]`"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Degree/Course</label>
                                    <input type="text" x-model="edu.degree" :name="`education[${index}][degree]`"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                    <input type="text" x-model="edu.department" :name="`education[${index}][department]`"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                    <input type="text" x-model="edu.year" :name="`education[${index}][year]`"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Work Experience -->
                <div class="bg-white shadow sm:rounded-lg p-6" x-data="{ experiences: {{ json_encode(old('work_experience', [['company' => '', 'position' => '', 'start_date' => '', 'end_date' => '', 'description' => '']])) }} }">
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
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                    <input type="text" x-model="exp.position" :name="`work_experience[${index}][position]`"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="text" x-model="exp.start_date" :name="`work_experience[${index}][start_date]`" placeholder="MM/YYYY"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="text" x-model="exp.end_date" :name="`work_experience[${index}][end_date]`" placeholder="MM/YYYY or Present"
                                           class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea x-model="exp.description" :name="`work_experience[${index}][description]`" rows="3"
                                              class="w-full rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Skills -->
                <div class="bg-white shadow sm:rounded-lg p-6" x-data="{ skills: {{ json_encode(old('skills', [''])) }} }">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ojt-dark">Skills</h3>
                        <button type="button" @click="skills.push('')"
                                class="text-sm text-ojt-primary hover:text-maroon-700">+ Add Skill</button>
                    </div>
                    <template x-for="(skill, index) in skills" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" x-model="skills[index]" :name="`skills[${index}]`"
                                   class="flex-1 rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                                   placeholder="Enter a skill">
                            <button type="button" @click="skills.splice(index, 1)" class="text-red-500 hover:text-red-700">Remove</button>
                        </div>
                    </template>
                </div>

                <!-- Certifications -->
                <div class="bg-white shadow sm:rounded-lg p-6" x-data="{ certifications: {{ json_encode(old('certifications', [['name' => '']])) }} }">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-ojt-dark">Certifications</h3>
                        <button type="button" @click="certifications.push({name: ''})"
                                class="text-sm text-ojt-primary hover:text-maroon-700">+ Add Certification</button>
                    </div>
                    <template x-for="(cert, index) in certifications" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" x-model="certifications[index].name" :name="`certifications[${index}][name]`"
                                   class="flex-1 rounded-lg border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary"
                                   placeholder="Enter certification name">
                            <button type="button" @click="certifications.splice(index, 1)" class="text-red-500 hover:text-red-700">Remove</button>
                        </div>
                    </template>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('resume.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-ojt-primary text-white rounded-lg hover:bg-maroon-700">
                        Save Resume
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>



