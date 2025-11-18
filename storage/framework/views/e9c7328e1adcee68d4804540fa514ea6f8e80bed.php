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
            <div class="flex items-center space-x-4">
                <a href="<?php echo e(route('coord.students.index')); ?>" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-ojt-dark leading-tight">Student Details</h2>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Student Header -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
                <div class="flex items-start space-x-6">
                    <!-- Student Avatar -->
                    <div class="flex-shrink-0">
                        <?php if($student->getProfile() && $student->getProfile()->profile_image): ?>
                            <img class="h-20 w-20 rounded-full object-cover border-4 border-ojt-primary" 
                                 src="<?php echo e(Storage::url($student->getProfile()->profile_image)); ?>" 
                                 alt="<?php echo e($student->name); ?>">
                        <?php else: ?>
                            <div class="h-20 w-20 rounded-full <?php echo e($student->getAvatarColor()); ?> flex items-center justify-center border-4 border-gray-200">
                                <span class="text-white font-bold text-2xl"><?php echo e(substr($student->name, 0, 1)); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Student Info -->
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900"><?php echo e($student->name); ?></h1>
                        <p class="text-gray-600">Student ID: <?php echo e($student->studentProfile?->student_id ?? 'N/A'); ?></p>
                        <p class="text-gray-600"><?php echo e($student->studentProfile?->course ?? 'N/A'); ?></p>
                        <p class="text-gray-600"><?php echo e($student->studentProfile?->department ?? 'N/A'); ?></p>
                        
                        <?php
                            $status = $student->studentProfile?->ojt_status ?? 'pending';
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800'
                            ];
                        ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($statusColors[$status] ?? 'bg-gray-100 text-gray-800'); ?> mt-2">
                            <?php echo e(ucfirst($status)); ?>

                        </span>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex-shrink-0">
                        <div class="flex space-x-3">
                            <form method="POST" action="<?php echo e(route('coord.students.update-status', $student)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <select name="ojt_status" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-ojt-primary focus:border-ojt-primary">
                                    <option value="pending" <?php echo e($status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                    <option value="active" <?php echo e($status == 'active' ? 'selected' : ''); ?>>Active</option>
                                    <option value="completed" <?php echo e($status == 'completed' ? 'selected' : ''); ?>>Completed</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                $milestones = [
                    ['label' => 'Pre-Placement', 'complete' => (bool) $student->studentProfile?->preplacement_complete, 'note' => $student->studentProfile?->preplacement_complete ? 'Checklist done' : 'Waiting submissions'],
                    ['label' => 'Company', 'complete' => (bool) $derivedCompanyName, 'note' => $derivedCompanyName ?? 'Not assigned'],
                    ['label' => 'Supervisor', 'complete' => (bool) $student->studentProfile?->supervisor_id, 'note' => $student->studentProfile?->supervisor?->name ?? 'Not assigned'],
                    ['label' => 'Activation', 'complete' => $student->studentProfile?->ojt_status === 'active', 'note' => ucfirst($student->studentProfile?->ojt_status ?? 'Pending')],
                ];
            ?>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <?php $__currentLoopData = $milestones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $milestone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500"><?php echo e($milestone['label']); ?></p>
                            <p class="text-sm font-semibold text-ojt-dark">
                                <?php echo e($milestone['complete'] ? 'Complete' : 'Pending'); ?>

                            </p>
                            <?php if(!empty($milestone['note'])): ?>
                                <p class="text-xs text-gray-500 mt-1"><?php echo e($milestone['note']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center justify-center w-10 h-10 rounded-full <?php echo e($milestone['complete'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'); ?>">
                            <?php if($milestone['complete']): ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <?php else: ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Attendance Overview -->
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Attendance Overview</h3>
                                <p class="text-sm text-gray-500">Latest logs with photos and punctuality checks.</p>
                                </div>
                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                <div><span class="font-semibold text-ojt-dark"><?php echo e($attendanceStats['total_days']); ?></span> days logged</div>
                                <div><span class="font-semibold text-green-600"><?php echo e($attendanceStats['completed_days']); ?></span> completed</div>
                                <div><span class="font-semibold text-yellow-600"><?php echo e($attendanceStats['missing_checkout']); ?></span> pending out</div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Date</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Time In</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Time Out</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Hours</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Photos</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php $__empty_1 = true; $__currentLoopData = $student->attendanceLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $late = false;
                                            // Late detection removed - can be added back using acceptance letter data if needed
                                        ?>
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900"><?php echo e($log->work_date?->format('M d, Y') ?? '—'); ?></td>
                                            <td class="px-3 py-2 text-gray-700"><?php echo e($log->time_in_formatted ?? '—'); ?></td>
                                            <td class="px-3 py-2 text-gray-700"><?php echo e($log->time_out_formatted ?? '—'); ?></td>
                                            <td class="px-3 py-2">
                                                <?php if($log->minutes_worked): ?>
                                                    <span class="inline-flex items-center gap-1 text-sm font-semibold text-ojt-primary">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <?php echo e(round($log->minutes_worked / 60, 1)); ?>h
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-gray-400 text-sm">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="flex items-center gap-2">
                                                    <?php if($log->photo_in_path): ?>
                                                        <a href="<?php echo e(Storage::url($log->photo_in_path)); ?>" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            In
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if($log->photo_out_path): ?>
                                                        <a href="<?php echo e(Storage::url($log->photo_out_path)); ?>" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium bg-green-50 text-green-700 border border-green-200 rounded-md hover:bg-green-100 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            Out
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if(!$log->photo_in_path && !$log->photo_out_path): ?>
                                                        <span class="text-xs text-gray-400">No photos</span>
                                                    <?php endif; ?>
                                            </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <?php if(!$log->time_in): ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Missing Time-In</span>
                                                <?php elseif(!$log->time_out): ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Needs Time-Out</span>
                                                <?php elseif($late): ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Late</span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">On Time</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">No attendance logs yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                                    </div>
                                </div>

                    <!-- Reports Overview -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                                <div>
                                <h3 class="text-lg font-semibold text-gray-900">Reports Overview</h3>
                                <p class="text-sm text-gray-500">Recent submissions with quick access.</p>
                                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                <div><span class="font-semibold text-ojt-dark"><?php echo e($reportStats['total_reports']); ?></span> total</div>
                                <div><span class="font-semibold text-ojt-dark"><?php echo e($reportStats['this_week']); ?></span> this week</div>
                                    </div>
                                </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Date</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Summary</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wide">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php $__empty_1 = true; $__currentLoopData = $student->dailyReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900"><?php echo e($report->work_date?->format('M d, Y') ?? '—'); ?></td>
                                            <td class="px-3 py-2 text-gray-700"><?php echo e(Str::limit($report->summary, 80) ?: 'No summary provided'); ?></td>
                                            <td class="px-3 py-2">
                                                <a href="<?php echo e(route('reports.show', $report)); ?>" target="_blank" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-medium">
                                                    View Report
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-500">No reports submitted yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Company & Supervisor Summary -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Placement Summary</h3>
                            <?php if($companySource): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <?php echo e(ucfirst($companySource)); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-4 text-sm text-gray-700">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-ojt-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-1">Company</p>
                                    <p class="font-medium text-ojt-dark">
                                        <?php echo e($derivedCompanyName ?? 'Not assigned'); ?>

                                    </p>
                                    <?php if($derivedCompanyAddress): ?>
                                        <div class="flex items-start gap-1.5 mt-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <p class="text-xs text-gray-500"><?php echo e($derivedCompanyAddress); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3 text-xs text-gray-500 border-t border-gray-100 pt-4">
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Hours Completed</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        <?php echo e(number_format($student->studentProfile?->completed_hours ?? 0)); ?>

                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Required Hours</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        <?php if($acceptance?->total_hours): ?>
                                            <?php echo e(number_format($acceptance->total_hours)); ?>

                                        <?php elseif($student->studentProfile?->required_hours): ?>
                                            <?php echo e(number_format($student->studentProfile->required_hours)); ?>

                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="uppercase tracking-wide">Activation</span>
                                    <span class="text-sm text-ojt-dark font-semibold">
                                        <?php echo e(ucfirst($student->studentProfile?->ojt_status ?? 'pending')); ?>

                                    </span>
                                </div>
                            </div>
                            <?php if($student->studentProfile?->supervisor): ?>
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor</p>
                                    <p class="mt-1 font-medium text-ojt-dark"><?php echo e($student->studentProfile->supervisor->name); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e($student->studentProfile->supervisor->email); ?></p>
                                <?php else: ?>
                                    <div class="border-t border-gray-100 pt-4">
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Supervisor</p>
                                        <p class="mt-1 text-gray-400">Not assigned</p>
                                    </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>


                    <!-- Supervisor Assignment Section -->
                    <div id="supervisor-assignment" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ open: <?php echo e($student->studentProfile?->supervisor ? 'false' : 'true'); ?> }">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Supervisor Assignment</h3>
                            <div class="flex items-center gap-2">
                                <?php if($student->studentProfile?->supervisor): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Assigned</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">⚠ Pending</span>
                                <?php endif; ?>
                                <button type="button" @click="open = !open" class="inline-flex items-center px-2.5 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <span x-show="!open">Show</span>
                                    <span x-show="open">Hide</span>
                                </button>
                            </div>
                        </div>

                        <!-- Current assignment -->
                        <div class="mb-4" x-show="open">
                            <?php if($student->studentProfile?->supervisor): ?>
                                <div class="bg-gradient-to-r from-ojt-accent/10 to-ojt-primary/5 border border-ojt-accent/30 rounded-lg p-4">
                                    <div class="flex items-start gap-4">
                                        <!-- Supervisor Avatar -->
                                        <div class="flex-shrink-0">
                                            <?php if($student->studentProfile->supervisor->supervisorProfile?->profile_image): ?>
                                                <img src="<?php echo e(Storage::url($student->studentProfile->supervisor->supervisorProfile->profile_image)); ?>" 
                                                     alt="<?php echo e($student->studentProfile->supervisor->name); ?>" 
                                                     class="w-16 h-16 rounded-full object-cover border-2 border-ojt-accent shadow-sm">
                                            <?php else: ?>
                                                <div class="w-16 h-16 <?php echo e($student->studentProfile->supervisor->getAvatarColor()); ?> rounded-full flex items-center justify-center text-white text-xl font-bold shadow-sm">
                                                    <?php echo e(substr($student->studentProfile->supervisor->name, 0, 1)); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Supervisor Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                                    Assigned
                                                </span>
                                            </div>
                                            <h4 class="text-base font-semibold text-ojt-dark mb-1"><?php echo e($student->studentProfile->supervisor->name); ?></h4>
                                            
                                            <?php if($student->studentProfile->supervisor->supervisorProfile?->position): ?>
                                                <p class="text-sm text-gray-600 mb-2"><?php echo e($student->studentProfile->supervisor->supervisorProfile->position); ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="space-y-1.5">
                                                <!-- Email -->
                                                <div class="flex items-center text-sm text-gray-700">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    <a href="mailto:<?php echo e($student->studentProfile->supervisor->email); ?>" class="hover:text-ojt-primary">
                                                        <?php echo e($student->studentProfile->supervisor->email); ?>

                                                    </a>
                                                </div>
                                                
                                                <!-- Phone -->
                                                <?php if($student->studentProfile->supervisor->supervisorProfile?->phone): ?>
                                                    <div class="flex items-center text-sm text-gray-700">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                        </svg>
                                                        <a href="tel:<?php echo e($student->studentProfile->supervisor->supervisorProfile->phone); ?>" class="hover:text-ojt-primary">
                                                            <?php echo e($student->studentProfile->supervisor->supervisorProfile->phone); ?>

                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <!-- Company -->
                                                <?php if($student->studentProfile->supervisor->supervisorProfile?->company): ?>
                                                    <div class="flex items-start text-sm text-gray-700 mt-2 pt-2 border-t border-ojt-accent/20">
                                                        <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        <div>
                                                            <p class="font-medium"><?php echo e($student->studentProfile->supervisor->supervisorProfile->company->name); ?></p>
                                                            <?php if($student->studentProfile->supervisor->supervisorProfile->company->address): ?>
                                                                <p class="text-xs text-gray-500 mt-0.5"><?php echo e($student->studentProfile->supervisor->supervisorProfile->company->address); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800">No supervisor assigned yet</p>
                                            <p class="text-xs text-yellow-700 mt-1">Student can submit supervisor details, or you can assign an existing supervisor below.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <!-- Student-submitted details -->
                        <?php if(isset($latestProposal) && $latestProposal): ?>
                                <div class="bg-ojt-accent/10 border border-ojt-accent/30 rounded-lg p-3 mb-4" x-show="open">
                                <p class="text-sm text-ojt-accent font-medium mb-2">📝 Supervisor Details Submitted by Student:</p>
                                <p class="text-sm text-ojt-dark"><strong>Name:</strong> <?php echo e($latestProposal->proposed_name ?? 'Not provided'); ?></p>
                                <p class="text-sm text-ojt-dark"><strong>Email:</strong> <?php echo e($latestProposal->proposed_email ?? 'Not provided'); ?></p>
                                <?php if($latestProposal->notes): ?>
                                    <button type="button" onclick="document.getElementById('proposalNotes').classList.toggle('hidden')" class="mt-2 text-xs text-ojt-accent underline">Show notes</button>
                                    <div id="proposalNotes" class="hidden mt-2 text-xs text-ojt-dark bg-ojt-accent/10 p-2 rounded"><?php echo e($latestProposal->notes); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Assignment Options -->
                        <div class="space-y-3" x-show="open">
                            <!-- Option 1: Create from student proposal or placement info -->
                            <?php
                                $hasSupervisorInfo = false;
                                $supervisorName = null;
                                $supervisorEmail = null;
                                
                                if (isset($latestProposal) && $latestProposal && $latestProposal->proposed_name && $latestProposal->proposed_email) {
                                    $hasSupervisorInfo = true;
                                    $supervisorName = $latestProposal->proposed_name;
                                    $supervisorEmail = $latestProposal->proposed_email;
                                }
                            ?>
                            
                            <?php if($hasSupervisorInfo): ?>
                                <div class="border border-blue-200 rounded-lg p-3">
                                    <h4 class="text-sm font-medium text-ojt-dark mb-2">Option 1: Create Supervisor Account</h4>
                                    <p class="text-xs text-gray-600 mb-2">Create a new supervisor account using the details submitted by the student.</p>
                                    <div class="text-xs text-gray-700 mb-3 space-y-1">
                                        <p><strong>Name:</strong> <?php echo e($supervisorName); ?></p>
                                        <p><strong>Email:</strong> <?php echo e($supervisorEmail); ?></p>
                                    </div>
                                    <form method="POST" action="<?php echo e(route('coord.students.assign-supervisor', $student)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="action" value="create_from_proposal">
                                        <button type="submit" class="bg-ojt-primary text-white px-3 py-1 rounded text-sm hover:bg-maroon-700 transition-colors">
                                            Create Account & Assign
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\ojt360\resources\views/coord/students/show.blade.php ENDPATH**/ ?>