<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-ojt-dark leading-tight">
				Preview: {{ $title }}
			</h2>
			<a href="{{ url()->previous() }}" class="text-ojt-primary hover:text-maroon-700 text-sm font-medium">← Back</a>
		</div>
	</x-slot>

	<div class="py-6 sm:py-8">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
				<div class="p-4 flex items-center justify-between border-b">
					<div>
						<div class="text-sm text-gray-600">MIME: <span class="font-medium">{{ $mime }}</span></div>
						@if($useOfficeViewer)
							<div class="text-xs text-gray-500 mt-1">Using Office Web Viewer</div>
						@endif
					</div>
					<div class="space-x-2">
						<a href="{{ $publicUrl }}" target="_blank" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Open Raw</a>
						<a href="{{ route('documents.download', ['submission' => request()->route('submission')]) }}" class="px-3 py-1.5 text-sm bg-ojt-primary text-white rounded hover:bg-maroon-700">Download</a>
					</div>
				</div>
				<div class="h-[80vh]">
					<iframe src="{{ $viewerUrl }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
