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
                    <x-text-input id="student_id" type="text" class="mt-1 block w-full bg-gray-50" 
                        :value="old('student_id', $profile->student_id ?? '')" disabled />
                    <input type="hidden" name="student_id" value="{{ old('student_id', $profile->student_id ?? '') }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
                </div>

                <div>
                    <x-input-label :value="__('Department')" />
                    <div class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-gray-700">
                        {{ old('department', $profile->department ?? '') }}
                    </div>
                    <input type="hidden" name="department" value="{{ old('department', $profile->department ?? '') }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('department')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label :value="__('Course / Program')" />
                    <div class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-gray-700">
                        {{ old('course', $profile->course ?? '') }}
                    </div>
                    <input type="hidden" name="course" value="{{ old('course', $profile->course ?? '') }}" />
                    <x-input-error class="mt-2" :messages="$errors->get('course')" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" 
                        :value="old('phone', $profile->phone ?? '')" 
                        placeholder="+63 912 345 6789" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="address" :value="__('Address')" />
                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" 
                        :value="old('address', $profile->address ?? '')" 
                        placeholder="Street, City, Postal Code, Country" />
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
           
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
                <!-- Position (Read-only) & Phone (Editable) on same row -->
                <div>
                    <x-input-label for="position" :value="__('Position')" />
                    <x-text-input id="position" type="text" class="mt-1 block w-full bg-gray-50" 
                        :value="old('position', $profile->position ?? '')" disabled />
                    <input type="hidden" name="position" value="{{ old('position', $profile->position ?? '') }}" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" 
                        :value="old('phone', $profile->phone ?? '')" 
                        placeholder="+63 912 345 6789" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <!-- Company Information (Read-only) -->
                @if($profile && $profile->company)
                <div class="md:col-span-2 border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Company Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('Company Name')" />
                            <div class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-gray-700">
                                {{ $profile->company->name }}
                            </div>
                        </div>

                        <div>
                            <x-input-label :value="__('Company Address')" />
                            <div class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-gray-700">
                                {{ $profile->company->address }}
                            </div>
                        </div>
                    </div>
                    
                    <p class="mt-2 text-xs text-gray-500">Company information is set during registration and cannot be changed here.</p>
                </div>
                @endif
            </div>
        @endif

        <!-- Profile Image Upload -->
        <div>
            <x-input-label for="profile_image" :value="__('Profile Picture (Optional)')" />
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
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB. Leave empty to keep current image or use default avatar.</p>
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
    <!-- Dropdown scripts removed because fields are read-only for students -->
    @endif
</section>
