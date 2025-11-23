<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-ojt-dark leading-tight">
            Create Monthly Evaluation
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-ojt-dark mb-4">Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Student Name</p>
                        <p class="font-medium text-gray-900">{{ $student->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Student ID</p>
                        <p class="font-medium text-gray-900">{{ $student->studentProfile->student_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Course/Program</p>
                        <p class="font-medium text-gray-900">{{ $student->studentProfile->course ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Department</p>
                        <p class="font-medium text-gray-900">{{ $student->studentProfile->department ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Host Training Establishment</p>
                        <p class="font-medium text-gray-900">
                            @if($acceptance && $acceptance->company)
                                {{ $acceptance->company->name }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Company Address</p>
                        <p class="font-medium text-gray-900">
                            @if($acceptance && $acceptance->company)
                                {{ $acceptance->company->address }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Work Schedule</p>
                        <p class="font-medium text-gray-900">
                            {{ $acceptance->formatted_work_schedule ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Supervisor Name</p>
                        <p class="font-medium text-gray-900">
                            @if($acceptance && $acceptance->immediate_supervisor)
                                {{ $acceptance->immediate_supervisor }}
                            @else
                                {{ Auth::user()->name }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Report For</p>
                        <p class="font-medium text-gray-900">
                            Monthly Progress Report
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('supervisor.evaluations.store') }}">
                @csrf
                
                <input type="hidden" name="student_user_id" value="{{ $student->id }}">

                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Evaluation Period</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Month *</label>
                            <select name="evaluation_month" required 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary">
                                <option value="1" {{ old('evaluation_month', $currentMonth) == 1 ? 'selected' : '' }}>January</option>
                                <option value="2" {{ old('evaluation_month', $currentMonth) == 2 ? 'selected' : '' }}>February</option>
                                <option value="3" {{ old('evaluation_month', $currentMonth) == 3 ? 'selected' : '' }}>March</option>
                                <option value="4" {{ old('evaluation_month', $currentMonth) == 4 ? 'selected' : '' }}>April</option>
                                <option value="5" {{ old('evaluation_month', $currentMonth) == 5 ? 'selected' : '' }}>May</option>
                                <option value="6" {{ old('evaluation_month', $currentMonth) == 6 ? 'selected' : '' }}>June</option>
                                <option value="7" {{ old('evaluation_month', $currentMonth) == 7 ? 'selected' : '' }}>July</option>
                                <option value="8" {{ old('evaluation_month', $currentMonth) == 8 ? 'selected' : '' }}>August</option>
                                <option value="9" {{ old('evaluation_month', $currentMonth) == 9 ? 'selected' : '' }}>September</option>
                                <option value="10" {{ old('evaluation_month', $currentMonth) == 10 ? 'selected' : '' }}>October</option>
                                <option value="11" {{ old('evaluation_month', $currentMonth) == 11 ? 'selected' : '' }}>November</option>
                                <option value="12" {{ old('evaluation_month', $currentMonth) == 12 ? 'selected' : '' }}>December</option>
                            </select>
                            @error('evaluation_month')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year *</label>
                            <select name="evaluation_year" required 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary">
                                @php
                                    $startYear = $internshipStart->year;
                                    $endYear = $internshipEnd->year;
                                @endphp
                                @for($year = $startYear; $year <= $endYear; $year++)
                                    <option value="{{ $year }}" {{ old('evaluation_year', $currentYear) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('evaluation_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Work Assignment(s)</h3>
                    <textarea name="work_assignment" rows="3" required
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-ojt-primary focus:ring-ojt-primary"
                              placeholder="Describe the student's work assignments and responsibilities this month...">{{ old('work_assignment', $acceptance->job_title ?? '') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Describe the tasks and responsibilities assigned to the student</p>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-ojt-dark mb-4">Performance Ratings (1-5)</h3>
                    
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left border">Attribute</th>
                                <th class="px-4 py-3 text-center border">
                                    <div class="font-bold">5</div>
                                    <div class="text-xs font-normal">Excellent</div>
                                </th>
                                <th class="px-4 py-3 text-center border">
                                    <div class="font-bold">4</div>
                                    <div class="text-xs font-normal">Very Satisfactory</div>
                                </th>
                                <th class="px-4 py-3 text-center border">
                                    <div class="font-bold">3</div>
                                    <div class="text-xs font-normal">Satisfactory</div>
                                </th>
                                <th class="px-4 py-3 text-center border">
                                    <div class="font-bold">2</div>
                                    <div class="text-xs font-normal">Fair</div>
                                </th>
                                <th class="px-4 py-3 text-center border">
                                    <div class="font-bold">1</div>
                                    <div class="text-xs font-normal">Needs Improvement</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-gray-100">
                                <td colspan="6" class="px-4 py-2 font-semibold">RELATED SKILLS</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">1. Analytical Skills</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_1" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_1" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_1" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_1" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_1" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">2. Communicative Competence</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_2" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_2" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_2" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_2" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_2" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">3. Leadership Skills</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_3" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_3" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_3" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_3" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_3" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">4. Time Management Skills</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_4" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_4" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_4" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_4" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_4" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">5. Technical Competence</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_5" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_5" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_5" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_5" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_5" value="1" required></td>
                            </tr>
                            
                            <tr class="bg-gray-100">
                                <td colspan="6" class="px-4 py-2 font-semibold">QUALITY OF WORK</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">6. Accuracy and Dependability</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_6" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_6" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_6" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_6" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_6" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">7. Creativity</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_7" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_7" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_7" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_7" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_7" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">8. Multi-Tasking Ability</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_8" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_8" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_8" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_8" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_8" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">9. Productivity/Work Speed</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_9" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_9" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_9" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_9" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_9" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">10. Professionalism</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_10" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_10" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_10" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_10" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_10" value="1" required></td>
                            </tr>

                            <tr class="bg-gray-100">
                                <td colspan="6" class="px-4 py-2 font-semibold">WORK APPROACH</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">11. Adaptability to Change</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_11" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_11" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_11" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_11" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_11" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">12. Attendance and Punctuality</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_12" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_12" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_12" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_12" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_12" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">13. Courtesy and Respect</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_13" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_13" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_13" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_13" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_13" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">14. Professional Grooming</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_14" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_14" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_14" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_14" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_14" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">15. Teamwork</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_15" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_15" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_15" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_15" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_15" value="1" required></td>
                            </tr>
                            
                            <tr class="bg-gray-100">
                                <td colspan="6" class="px-4 py-2 font-semibold">JOB INTEREST</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">16. Adherence to Policies</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_16" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_16" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_16" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_16" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_16" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">17. Attitude towards Work</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_17" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_17" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_17" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_17" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_17" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">18. Work with Colleagues</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_18" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_18" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_18" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_18" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_18" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">19. Initiative</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_19" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_19" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_19" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_19" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_19" value="1" required></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">20. Participation in Activities</td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_20" value="5" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_20" value="4" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_20" value="3" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_20" value="2" required></td>
                                <td class="px-4 py-2 text-center"><input type="radio" name="rating_row_20" value="1" required></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Comments</h3>
                    <textarea name="comments_recommendations" rows="4" maxlength="400" class="w-full rounded-md border-gray-300"></textarea>
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex gap-3 justify-end">
                        <a href="{{ route('supervisor.students.view', $student) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" name="action" value="submit" class="px-6 py-2 bg-ojt-primary rounded-lg text-white hover:bg-maroon-700">
                            Submit Evaluation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
