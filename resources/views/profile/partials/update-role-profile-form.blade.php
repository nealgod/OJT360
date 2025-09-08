<section>
    <header>
        <h2 class="text-lg font-medium text-ojt-dark">
            @if($user->isStudent())
                {{ __('Student Information') }}
            @elseif($user->isCoordinator())
                {{ __('Coordinator Information') }}
            @elseif($user->isSupervisor())
                {{ __('Supervisor Information') }}
            @endif
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            @if($user->isStudent())
                {{ __('Complete your student profile information.') }}
            @elseif($user->isCoordinator())
                {{ __('Update your coordinator profile information.') }}
            @elseif($user->isSupervisor())
                {{ __('Update your supervisor profile information.') }}
            @endif
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        @if($user->isStudent())
            <!-- Student Profile Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="student_id" :value="__('Student ID')" />
                    <x-text-input id="student_id" name="student_id" type="text" class="mt-1 block w-full" 
                        :value="old('student_id', $profile->student_id ?? '')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
                </div>

                <div>
                    <x-input-label for="department" :value="__('Department')" />
                    <select id="department" name="department" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm" required>
                        <option value="">Select Department</option>
                        @foreach(config('departments.departments') as $dept => $data)
                            <option value="{{ $dept }}" 
                                {{ old('department', $profile->department ?? '') == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('department')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="course" :value="__('Course')" />
                    <select id="course" name="course" class="mt-1 block w-full border-gray-300 focus:border-ojt-primary focus:ring-ojt-primary rounded-md shadow-sm" required>
                        <option value="">Select Course</option>
                        @if(old('department', $profile->department ?? ''))
                            @foreach(config('departments.departments')[old('department', $profile->department ?? '')]['courses'] ?? [] as $course => $hours)
                                <option value="{{ $course }}" 
                                    {{ old('course', $profile->course ?? '') == $course ? 'selected' : '' }}>
                                    {{ $course }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('course')" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" 
                        :value="old('phone', $profile->phone ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
            </div>

        @elseif($user->isCoordinator())
            <!-- Coordinator Profile Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="employee_id" :value="__('Employee ID')" />
                    <x-text-input id="employee_id" name="employee_id" type="text" class="mt-1 block w-full" 
                        :value="old('employee_id', $profile->employee_id ?? '')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('employee_id')" />
                </div>

                <div>
                    <x-input-label :value="__('Department')" />
                    <div class="mt-1 block w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-700">
                        {{ optional($profile->department)->name ?? ($profile->department ?? '—') }}
                    </div>
                </div>

                <div>
                    <x-input-label :value="__('Program / Course')" />
                    <div class="mt-1 block w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-700">
                        {{ optional($profile->program)->name ?? '—' }}
                    </div>
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" 
                        :value="old('phone', $profile->phone ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
            </div>

        @elseif($user->isSupervisor())
            <!-- Supervisor Profile Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="employee_id" :value="__('Employee ID')" />
                    <x-text-input id="employee_id" name="employee_id" type="text" class="mt-1 block w-full" 
                        :value="old('employee_id', $profile->employee_id ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('employee_id')" />
                </div>

                <div>
                    <x-input-label for="position" :value="__('Position')" />
                    <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" 
                        :value="old('position', $profile->position ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('position')" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" 
                        :value="old('phone', $profile->phone ?? '')" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
            </div>
        @endif

        <!-- Profile Image Upload -->
        <div>
            <x-input-label for="profile_image" :value="__('Profile Picture')" />
            <div class="mt-2 flex items-center space-x-4">
                @if($profile && $profile->profile_image)
                    <img src="{{ Storage::url($profile->profile_image) }}" alt="Current profile" class="w-20 h-20 rounded-full object-cover border-2 border-ojt-primary">
                @else
                    <div class="w-20 h-20 bg-ojt-primary rounded-full flex items-center justify-center text-white text-xl font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <input id="profile_image" name="profile_image" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-ojt-primary file:text-white hover:file:bg-maroon-700" accept="image/*" />
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-ojt-primary hover:bg-maroon-700">
                {{ __('Save Profile') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600"
                >{{ __('Profile saved successfully.') }}</p>
            @endif
        </div>
    </form>

    @if($user->isStudent())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departmentSelect = document.getElementById('department');
            const courseSelect = document.getElementById('course');
            const departments = @json(config('departments.departments'));

            departmentSelect.addEventListener('change', function() {
                const selectedDepartment = this.value;
                courseSelect.innerHTML = '<option value="">Select Course</option>';
                
                if (selectedDepartment && departments[selectedDepartment] && departments[selectedDepartment].courses) {
                    Object.keys(departments[selectedDepartment].courses).forEach(function(course) {
                        const option = document.createElement('option');
                        option.value = course;
                        option.textContent = course;
                        courseSelect.appendChild(option);
                    });
                }
            });

            // Trigger change event on page load if department is already selected
            if (departmentSelect.value) {
                departmentSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endif
</section>
