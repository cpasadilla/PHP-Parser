<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Upload Document') }}
            </h2>
            <a href="{{ route('crew-documents.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                Back to Documents
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('crew-documents.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="crew_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Crew Member *</label>
                                <select name="crew_id" id="crew_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Crew Member</option>
                                    @foreach($crews as $crewMember)
                                        <option value="{{ $crewMember->id }}" 
                                                {{ ($crew && $crew->id == $crewMember->id) || old('crew_id') == $crewMember->id ? 'selected' : '' }}>
                                            {{ $crewMember->full_name }} ({{ $crewMember->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('crew_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Type *</label>
                                <select name="document_type" id="document_type" 
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Document Type</option>
                                    @foreach(\App\Models\CrewDocument::DOCUMENT_TYPES as $key => $label)
                                        <option value="{{ $key }}" 
                                                {{ ($selectedDocumentType ?? old('document_type')) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('document_type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="document_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Name *</label>
                                <input type="text" name="document_name" id="document_name" value="{{ old('document_name') }}" 
                                       placeholder="e.g., Medical Certificate 2024"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('document_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                                <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="document_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document File *</label>
                            <input type="file" name="document_file" id="document_file" 
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-800">
                            <p id="file-format-text" class="mt-1 text-sm text-gray-500 dark:text-gray-400">Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 10MB)</p>
                            @error('document_file')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea name="notes" id="notes" rows="4" 
                                      placeholder="Any additional information about this document..."
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('crew-documents.index') }}" 
                               class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                                Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileInput() {
            const fileInput = document.getElementById('document_file');
            const formatText = document.getElementById('file-format-text');
            const documentType = document.getElementById('document_type').value;
            
            if (documentType === 'id_picture') {
                fileInput.accept = '.jpg,.jpeg,.png';
                formatText.textContent = 'Supported formats: JPG, PNG (Max: 5MB) - Recommended size: 300x400px';
            } else {
                fileInput.accept = '.pdf,.jpg,.jpeg,.png,.doc,.docx';
                formatText.textContent = 'Supported formats: PDF, JPG, PNG, DOC, DOCX (Max: 10MB)';
            }
        }

        document.getElementById('document_type').addEventListener('change', updateFileInput);
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateFileInput);
    </script>
</x-app-layout>
