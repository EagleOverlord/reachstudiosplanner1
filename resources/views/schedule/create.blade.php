<x-layouts.app :title="__('Create Schedule')">
    <div class="max-w-xl mx-auto mt-8">
        <form method="POST" action="{{ route('schedule.store') }}" class="space-y-6 bg-gray-900 p-6 rounded shadow border border-gray-700">
            @csrf
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label for="start" class="block text-sm font-medium text-gray-300">Start Date & Time</label>
                    <input type="datetime-local" name="start" id="start" required class="mt-1 block w-full border-gray-700 bg-gray-800 text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="w-1/2">
                    <label for="end" class="block text-sm font-medium text-gray-300">End Date & Time</label>
                    <input type="datetime-local" name="end" id="end" required class="mt-1 block w-full border-gray-700 bg-gray-800 text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-300 mb-2">Work Location</span>
                <div class="flex items-center space-x-4"> 
                    <label class="inline-flex items-center">
                        <input type="radio" name="location" value="home" class="form-radio text-indigo-400 bg-gray-800 border-gray-700" checked>
                        <span class="ml-2 text-gray-200">Home</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="location" value="office" class="form-radio text-indigo-400 bg-gray-800 border-gray-700">
                        <span class="ml-2 text-gray-200">Office</span>
                    </label>
                </div>
            </div>
            <div>
                <button type="submit" class="w-full py-2 px-4 bg-indigo-700 text-white font-semibold rounded hover:bg-indigo-800">Create Schedule</button>
            </div>
        </form>
    </div>
</x-layouts.app>