<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <?php echo e(__('My Supervised Students')); ?>

                </h2>
                <p class="text-sm text-gray-500">
                    Track everyone you’ve accepted and jump back into their profiles or letters.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('supervisor.students.search')); ?>"
                   class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Accept Another Student
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if($students->count()): ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $profile = $student->studentProfile;
                            $company = $profile?->company;
                            $latestLetter = $student->acceptanceLetters->first();
                        ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <?php echo $student->getAvatarHtml('w-14 h-14'); ?>

                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-gray-900"><?php echo e($student->name); ?></h3>
                                            <?php if($profile?->student_id): ?>
                                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded-full">
                                                    <?php echo e($profile->student_id); ?>

                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-600 space-x-2">
                                            <?php if($profile?->course): ?>
                                                <span><?php echo e($profile->course); ?></span>
                                            <?php endif; ?>
                                            <?php if($profile?->department): ?>
                                                <span class="text-gray-400">•</span>
                                                <span><?php echo e($profile->department); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($company): ?>
                                            <div class="text-sm text-gray-500 mt-1">
                                                <span class="font-medium text-gray-700">Company:</span>
                                                <?php echo e($company->name); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <a href="<?php echo e(route('supervisor.students.view', $student->id)); ?>"
                                       class="inline-flex items-center px-4 py-2 bg-ojt-primary text-white text-sm font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Details
                                    </a>
                                </div>
                            </div>
                            <!-- Hours Tracking Section -->
                            <?php
                                $completedMinutes = $student->attendanceLogs()->sum('minutes_worked');
                                $completedHours = round(($completedMinutes ?? 0) / 60, 1);
                                $requiredHours = $latestLetter?->total_hours ?? $profile?->required_hours ?? 0;
                                $percentage = $requiredHours > 0 ? round(($completedHours / $requiredHours) * 100, 1) : 0;
                            ?>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                                            <p class="text-xs text-green-700 mb-1">Hours Completed</p>
                                            <p class="text-2xl font-bold text-green-900"><?php echo e(number_format($completedHours, 1)); ?></p>
                                        </div>
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                                            <p class="text-xs text-blue-700 mb-1">Required Hours</p>
                                            <p class="text-2xl font-bold text-blue-900">
                                                <?php if($requiredHours): ?>
                                                    <?php echo e(number_format($requiredHours)); ?>

                                                <?php else: ?>
                                                    —
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <?php if($requiredHours > 0): ?>
                                            <div class="text-sm">
                                                <p class="text-gray-500">Progress</p>
                                                <p class="text-lg font-bold text-ojt-primary"><?php echo e($percentage); ?>%</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right text-sm">
                                        <p class="text-gray-500">Status</p>
                                        <p class="font-medium text-gray-900">
                                            <?php echo e($profile?->ojt_status ? ucfirst($profile->ojt_status) : 'In Progress'); ?>

                                        </p>
                                    </div>
                                </div>
                                
                                <?php if($requiredHours > 0): ?>
                                    <div class="mb-3">
                                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-ojt-primary h-2 rounded-full transition-all duration-300" 
                                                 style="width: <?php echo e(min($percentage, 100)); ?>%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <?php echo e(number_format(max(0, $requiredHours - $completedHours), 1)); ?> hours remaining
                                        </p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Start Date</p>
                                        <p class="font-medium text-gray-900">
                                            <?php if($latestLetter?->start_date): ?>
                                                <?php echo e($latestLetter->start_date->format('M d, Y')); ?>

                                            <?php else: ?>
                                                <span class="text-gray-400">Not set</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Last Attendance</p>
                                        <p class="font-medium text-gray-900">
                                            <?php
                                                $lastAttendance = $student->attendanceLogs()->latest('work_date')->first();
                                            ?>
                                            <?php if($lastAttendance): ?>
                                                <?php echo e($lastAttendance->work_date->format('M d, Y')); ?>

                                            <?php else: ?>
                                                <span class="text-gray-400">No logs yet</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-6">
                    <?php echo e($students->links()); ?>

                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg border border-dashed border-gray-300 p-10 text-center">
                    <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">No supervised students yet</h3>
                    <p class="text-gray-500 mt-2">
                        Once you accept a student, they’ll appear here for quick access to their details and documents.
                    </p>
                    <div class="mt-6">
                        <a href="<?php echo e(route('supervisor.students.search')); ?>"
                           class="inline-flex items-center px-5 py-3 bg-ojt-primary text-white font-medium rounded-lg hover:bg-maroon-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Accept a Student
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>

<?php /**PATH C:\xampp\htdocs\ojt360\resources\views/supervisor/students/index.blade.php ENDPATH**/ ?>