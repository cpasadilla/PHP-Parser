<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Embarkation Record') }}
            </h2>
            <a href="{{ route('crew.show', $crew->id ?? '') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Crew
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('crew-embarkations.store') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Crew Selection -->
                            <div>
                                <label for="crew_id" class="block text-sm font-medium text-gray-700">Crew Member *</label>
                                <select name="crew_id" id="crew_id" required 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Crew Member</option>
                                    @foreach($crews as $crewMember)
                                        <option value="{{ $crewMember->id }}" 
                                                {{ (old('crew_id', $crew->id ?? '') == $crewMember->id) ? 'selected' : '' }}>
                                            {{ $crewMember->full_name }} ({{ $crewMember->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('crew_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ship Selection -->
                            <div>
                                <label for="ship_id" class="block text-sm font-medium text-gray-700">Ship *</label>
                                <select name="ship_id" id="ship_id" required 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Ship</option>
                                    @foreach($ships as $ship)
                                        <option value="{{ $ship->id }}" {{ old('ship_id') == $ship->id ? 'selected' : '' }}>
                                            MV EVERWIN STAR {{ $ship->ship_number }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ship_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Embark Date -->
                            <div>
                                <label for="embark_date" class="block text-sm font-medium text-gray-700">Embark Date *</label>
                                <input type="date" name="embark_date" id="embark_date" value="{{ old('embark_date', date('Y-m-d')) }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('embark_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Disembark Date -->
                            <div>
                                <label for="disembark_date" class="block text-sm font-medium text-gray-700">Disembark Date (Optional)</label>
                                <input type="date" name="disembark_date" id="disembark_date" value="{{ old('disembark_date') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('disembark_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Embark Port -->
                            <div>
                                <label for="embark_port" class="block text-sm font-medium text-gray-700">Embark Port</label>
                                <input type="text" name="embark_port" id="embark_port" value="{{ old('embark_port') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="e.g., Manila Port">
                                @error('embark_port')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Disembark Port -->
                            <div>
                                <label for="disembark_port" class="block text-sm font-medium text-gray-700">Disembark Port</label>
                                <input type="text" name="disembark_port" id="disembark_port" value="{{ old('disembark_port') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="e.g., Batanes Port">
                                @error('disembark_port')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mt-6">
                            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Any additional notes about this embarkation...">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('crew.show', $crew->id ?? '') }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Embarkation Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ensure disembark date is after embark date
        document.getElementById('embark_date').addEventListener('change', function() {
            const embarkDate = this.value;
            const disembarkInput = document.getElementById('disembark_date');
            disembarkInput.min = embarkDate;
            
            if (disembarkInput.value && disembarkInput.value < embarkDate) {
                disembarkInput.value = '';
            }
        });
    </script>
</x-app-layout>
