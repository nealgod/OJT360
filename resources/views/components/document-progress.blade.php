@props(['studentId' => null])

@php
    // Determine which student to show progress for
    $targetStudentId = $studentId ?? Auth::id();
    
    // Get all required documents
    $requirements = \App\Models\DocumentRequirement::where('is_required', true)
        ->where('is_active', true)
        ->get();
    
    // Split into Pre and Post
    $preReqs = $requirements->where('type', 'pre_placement');
    $postReqs = $requirements->where('type', 'post_placement');
    
    // Helper function to calculate stats
    $calculateStats = function($reqs) use ($targetStudentId) {
        $total = $reqs->count();
        $completed = 0;
        
        if ($total === 0) return ['total' => 0, 'completed' => 0, 'percentage' => 100];

        foreach ($reqs as $req) {
            $submission = \App\Models\StudentDocumentSubmission::where('student_user_id', $targetStudentId)
                ->where('document_requirement_id', $req->id)
                ->exists();
            
            if ($submission) {
                $completed++;
            }
        }
        
        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => round(($completed / $total) * 100)
        ];
    };

    $preStats = $calculateStats($preReqs);
    $postStats = $calculateStats($postReqs);
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-gray-200 p-4 shadow-sm']) }}>
    <h3 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">Document Progress</h3>

    <!-- Pre-Placement Section -->
    <div class="mb-4">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-medium text-gray-700">Pre-Placement</span>
            <span class="text-xs font-bold {{ $preStats['percentage'] == 100 ? 'text-green-600' : 'text-ojt-primary' }}">{{ $preStats['percentage'] }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-500 ease-out {{ $preStats['percentage'] == 100 ? 'bg-green-500' : 'bg-ojt-primary' }}" 
                 style="width: {{ $preStats['percentage'] }}%"></div>
        </div>
    </div>

    <!-- Post-Placement Section -->
    <div>
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-medium text-gray-700">Post-Placement</span>
            <span class="text-xs font-bold {{ $postStats['percentage'] == 100 ? 'text-green-600' : 'text-ojt-primary' }}">{{ $postStats['percentage'] }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-500 ease-out {{ $postStats['percentage'] == 100 ? 'bg-green-500' : 'bg-ojt-primary' }}" 
                 style="width: {{ $postStats['percentage'] }}%"></div>
        </div>
    </div>

    @if(Auth::user()->isCoordinator())
        <div class="mt-4 pt-3 border-t border-gray-100 text-center">
            <a href="{{ route('documents.index', ['student_id' => $targetStudentId]) }}" class="text-xs font-medium text-ojt-primary hover:text-maroon-700 hover:underline inline-flex items-center">
                View Student Documents
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    @endif
</div>
