<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Teams Management')" :subheading="__('Manage teams and organize staff members')">
        
        <!-- Success/Info Messages -->
        <div x-data="{ show: false, message: '', type: 'info' }" 
             @team-created.window="show = true; message = $event.detail.message; type = 'success'; setTimeout(() => show = false, 5000)"
             @team-updated.window="show = true; message = $event.detail.message; type = 'success'; setTimeout(() => show = false, 5000)"
             @team-deleted.window="show = true; message = $event.detail.message; type = $event.detail.message.includes('Cannot') ? 'error' : 'success'; setTimeout(() => show = false, 5000)"
             @user-moved.window="show = true; message = $event.detail.message; type = $event.detail.message.includes('not valid') ? 'error' : 'success'; setTimeout(() => show = false, 5000)">
            <div x-show="show" x-transition 
                 :class="{
                     'bg-green-100 border-green-400 text-green-700 dark:bg-green-900 dark:border-green-600 dark:text-green-200': type === 'success',
                     'bg-red-100 border-red-400 text-red-700 dark:bg-red-900 dark:border-red-600 dark:text-red-200': type === 'error',
                     'bg-blue-100 border-blue-400 text-blue-700 dark:bg-blue-900 dark:border-blue-600 dark:text-blue-200': type === 'info'
                 }"
                 class="mb-4 p-4 border rounded">
                <p x-text="message"></p>
            </div>
        </div>

        <!-- Teams Overview -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Teams Overview</h3>
                <flux:button wire:click="showCreateModal" variant="primary" size="sm" class="inline-flex items-center gap-2">
                    <span class="text-lg font-bold leading-none">+</span>
                    <span>Create New Team</span>
                </flux:button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
                @foreach($teamStats as $teamKey => $stats)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer {{ $selectedTeam === $teamKey ? 'ring-2 ring-blue-500 dark:ring-blue-400' : '' }}" 
                         wire:click="selectTeam('{{ $teamKey }}')">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $stats['name'] }}</h4>
                            <div class="flex space-x-1">
                                <flux:button wire:click.stop="showEditModal('{{ $teamKey }}')" size="xs" variant="ghost" class="flex items-center">
                                    <flux:icon.pencil class="size-3" />
                                </flux:button>
                                <flux:button wire:click.stop="showDeleteModal('{{ $teamKey }}')" size="xs" variant="ghost" class="text-red-600 dark:text-red-400 flex items-center">
                                    <flux:icon.trash class="size-3" />
                                </flux:button>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <p>{{ $stats['count'] }} member{{ $stats['count'] !== 1 ? 's' : '' }}</p>
                            <p>{{ $stats['admin_count'] }} admin{{ $stats['admin_count'] !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Team Members Section -->
        @if($selectedTeam)
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                    {{ \App\Models\Team::getTeamName($selectedTeam) }} Team Members
                </h3>
                
                @if(count($teamMembers) > 0)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keys</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($teamMembers as $member)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-blue-500 dark:bg-blue-600 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-white">
                                                                {{ strtoupper(substr($member['name'], 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $member['name'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $member['email'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $member['admin_status'] === 'yes' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                                    {{ $member['admin_status'] === 'yes' ? 'Admin' : 'User' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $member['keys_status'] === 'yes' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ $member['keys_status'] === 'yes' ? 'Has Keys' : 'No Keys' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <flux:button wire:click="showMoveModal({{ $member['id'] }})" size="sm" variant="outline">
                                                    Move to Team
                                                </flux:button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-8 text-center">
                        <div class="text-gray-500 dark:text-gray-400">
                            <flux:icon.users class="mx-auto h-12 w-12 mb-4" />
                            <h3 class="text-lg font-medium mb-2">No team members</h3>
                            <p>This team doesn't have any members yet.</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Create Team Modal -->
        <flux:modal wire:model="showCreateTeamModal" class="space-y-6">
            <div>
                <flux:heading size="lg">Create New Team</flux:heading>
                <flux:subheading>Add a new team to organize your staff members.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Team Key</flux:label>
                    <flux:input wire:model="newTeamKey" placeholder="e.g., marketing" />
                    <flux:description>A unique identifier for the team (lowercase, underscores allowed).</flux:description>
                    @error('newTeamKey') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Team Name</flux:label>
                    <flux:input wire:model="newTeamName" placeholder="e.g., Marketing" />
                    <flux:description>The display name for the team.</flux:description>
                    @error('newTeamName') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <div class="flex space-x-2">
                <flux:button wire:click="createTeam" variant="primary">Create Team</flux:button>
                <flux:button wire:click="closeModals" variant="ghost">Cancel</flux:button>
            </div>
        </flux:modal>

        <!-- Edit Team Modal -->
        <flux:modal wire:model="showEditTeamModal" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Team</flux:heading>
                <flux:subheading>Update the team information.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Team Name</flux:label>
                    <flux:input wire:model="editingTeamName" />
                    @error('editingTeamName') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <div class="flex space-x-2">
                <flux:button wire:click="updateTeam" variant="primary">Update Team</flux:button>
                <flux:button wire:click="closeModals" variant="ghost">Cancel</flux:button>
            </div>
        </flux:modal>

        <!-- Delete Team Modal -->
        <flux:modal wire:model="showDeleteTeamModal" class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Team</flux:heading>
                <flux:subheading>Are you sure you want to delete this team? This action cannot be undone.</flux:subheading>
            </div>

            <div class="flex space-x-2">
                <flux:button wire:click="deleteTeam" variant="danger">Delete Team</flux:button>
                <flux:button wire:click="closeModals" variant="ghost">Cancel</flux:button>
            </div>
        </flux:modal>

        <!-- Move User Modal -->
        <flux:modal wire:model="showMoveUserModal" class="space-y-6">
            <div>
                <flux:heading size="lg">Move User to Team</flux:heading>
                <flux:subheading>Select a team to move this user to.</flux:subheading>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Select Team</flux:label>
                    <flux:select wire:model="moveToTeam" placeholder="Choose a team...">
                        @foreach($teams as $teamKey => $teamName)
                            @if($teamKey !== $selectedTeam)
                                <flux:select.option value="{{ $teamKey }}">{{ $teamName }}</flux:select.option>
                            @endif
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex space-x-2">
                <flux:button wire:click="moveUser" variant="primary">Move User</flux:button>
                <flux:button wire:click="closeModals" variant="ghost">Cancel</flux:button>
            </div>
        </flux:modal>

    </x-settings.layout>
</section>