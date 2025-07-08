<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Users Management')" :subheading="__('View all users and their team assignments')">
        <div class="space-y-6 max-w-none w-full">
            <!-- Success Message -->
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded dark:bg-green-800 dark:border-green-600 dark:text-green-200">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Search and Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <flux:input 
                    wire:model.live="search" 
                    placeholder="Search users..."
                    class="w-full"
                />
                
                <flux:select wire:model.live="teamFilter" placeholder="Filter by team">
                    <flux:select.option value="">All Teams</flux:select.option>
                    <flux:select.option value="slt">Senior Leadership Team</flux:select.option>
                    <flux:select.option value="mobile">Mobile</flux:select.option>
                    <flux:select.option value="front_end">Front End</flux:select.option>
                    <flux:select.option value="back_end">Back End</flux:select.option>
                    <flux:select.option value="design">Design</flux:select.option>
                    <flux:select.option value="e_commerce">E-Commerce</flux:select.option>
                    <flux:select.option value="bdm">Business Development Manager</flux:select.option>
                </flux:select>
                
                <flux:select wire:model.live="statusFilter" placeholder="Filter by status">
                    <flux:select.option value="">All Status</flux:select.option>
                    <flux:select.option value="yes">Admins</flux:select.option>
                    <flux:select.option value="no">Users</flux:select.option>
                </flux:select>
                
                <flux:select wire:model.live="keysFilter" placeholder="Filter by keys">
                    <flux:select.option value="">All Keys</flux:select.option>
                    <flux:select.option value="yes">Has Keys</flux:select.option>
                    <flux:select.option value="no">No Keys</flux:select.option>
                </flux:select>
            </div>

            <!-- Users Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[200px]">
                                    Name
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[220px]">
                                    Email
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[180px]">
                                    Team
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[100px]">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                                    Keys Access
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[120px]">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                @if($editingUser === $user->id)
                                    <!-- Edit Mode -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <flux:input 
                                            wire:model="editName" 
                                            class="w-full text-sm min-w-[180px]"
                                            placeholder="Name"
                                        />
                                        @error('editName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <flux:input 
                                            wire:model="editEmail" 
                                            type="email"
                                            class="w-full text-sm min-w-[200px]"
                                            placeholder="Email"
                                        />
                                        @error('editEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <flux:select wire:model="editTeam" class="w-full text-sm min-w-[160px]">
                                            <flux:select.option value="slt">Senior Leadership Team</flux:select.option>
                                            <flux:select.option value="mobile">Mobile</flux:select.option>
                                            <flux:select.option value="front_end">Front End</flux:select.option>
                                            <flux:select.option value="back_end">Back End</flux:select.option>
                                            <flux:select.option value="design">Design</flux:select.option>
                                            <flux:select.option value="e_commerce">E-Commerce</flux:select.option>
                                            <flux:select.option value="bdm">Business Development Manager</flux:select.option>
                                        </flux:select>
                                        @error('editTeam') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <flux:select wire:model="editAdminStatus" class="w-full text-sm min-w-[90px]">
                                            <flux:select.option value="no">User</flux:select.option>
                                            <flux:select.option value="yes">Admin</flux:select.option>
                                        </flux:select>
                                        @error('editAdminStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <flux:select wire:model="editKeysStatus" class="w-full text-sm min-w-[110px]">
                                            <flux:select.option value="no">No Keys</flux:select.option>
                                            <flux:select.option value="yes">Has Keys</flux:select.option>
                                        </flux:select>
                                        @error('editKeysStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <flux:button 
                                                wire:click="saveUser" 
                                                variant="primary"
                                                size="sm"
                                            >
                                                Save
                                            </flux:button>
                                            <flux:button 
                                                wire:click="cancelEdit" 
                                                variant="outline"
                                                size="sm"
                                            >
                                                Cancel
                                            </flux:button>
                                        </div>
                                    </td>
                                @else
                                    <!-- View Mode -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $user->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $this->getTeamBadgeColor($user->team) }}">
                                            {{ $this->getTeamDisplayName($user->team) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $user->admin_status === 'yes' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                            {{ $user->admin_status === 'yes' ? 'Admin' : 'User' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $user->keys_status === 'yes' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                            {{ $user->keys_status === 'yes' ? 'Has Keys' : 'No Keys' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        <flux:button 
                                            wire:click="editUser({{ $user->id }})" 
                                            variant="outline"
                                            size="sm"
                                        >
                                            Edit
                                        </flux:button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @endif

            <!-- Summary Stats -->
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $users->total() }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Users</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ App\Models\User::where('admin_status', 'yes')->count() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Admins</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ App\Models\User::where('keys_status', 'yes')->count() }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">With Keys</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ App\Models\User::distinct('team')->count('team') }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Teams</div>
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>
</section>
