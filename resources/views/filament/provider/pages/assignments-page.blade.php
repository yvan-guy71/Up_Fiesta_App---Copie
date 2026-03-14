@section('title', 'Mes Assignations')

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Stats Header -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">En attente</p>
                        <p class="text-2xl font-bold">{{ $this->assignments->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="text-yellow-500 text-3xl">⏳</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Acceptées</p>
                        <p class="text-2xl font-bold">{{ $this->assignments->where('status', 'accepted')->count() }}</p>
                    </div>
                    <div class="text-blue-500 text-3xl">✓</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Rejetées</p>
                        <p class="text-2xl font-bold">{{ $this->assignments->where('status', 'rejected')->count() }}</p>
                    </div>
                    <div class="text-red-500 text-3xl">✕</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Complétées</p>
                        <p class="text-2xl font-bold">{{ $this->assignments->where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="text-green-500 text-3xl">✔</div>
                </div>
            </div>
        </div>

        <!-- Assignments List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Mes Assignations</h2>
            </div>

            @if($this->assignments->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-400 text-lg">Aucune assignation pour le moment</div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Budget</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->assignments as $assignment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $assignment->serviceRequest->subject }}
                                        </div>
                                        @if($assignment->serviceRequest->category)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $assignment->serviceRequest->category->name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $assignment->serviceRequest->user->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($assignment->serviceRequest->budget, 0) }} XOF
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            @if($assignment->status === 'pending')
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($assignment->status === 'accepted')
                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($assignment->status === 'rejected')
                                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($assignment->status === 'completed')
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @endif
                                        ">
                                            @switch($assignment->status)
                                                @case('pending')
                                                    En attente
                                                    @break
                                                @case('accepted')
                                                    Acceptée
                                                    @break
                                                @case('rejected')
                                                    Rejetée
                                                    @break
                                                @case('completed')
                                                    Complétée
                                                    @break
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        @if($assignment->isPending())
                                            <button
                                                wire:click="accept({{ $assignment->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                                                title="Accepter"
                                            >
                                                ✓
                                            </button>
                                            <button
                                                wire:click="openRejectModal({{ $assignment->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition"
                                                title="Rejeter"
                                            >
                                                ✕
                                            </button>
                                        @elseif($assignment->isAccepted())
                                            <button
                                                wire:click="complete({{ $assignment->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition"
                                                title="Marquer comme complété"
                                            >
                                                ✔
                                            </button>
                                        @else
                                            <span class="text-gray-500 text-xs">Aucune action</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    @if($showRejectModal && $selectedAssignment)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rejeter l'assignation</h3>
                </div>

                <div class="px-6 py-4">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Veuillez fournir une raison pour rejeter cette assignation :
                    </p>

                    <textarea
                        wire:model="rejectionReason"
                        placeholder="Expliquez pourquoi vous rejetez cette assignation..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        rows="4"
                    ></textarea>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                    <button
                        wire:click="closeRejectModal"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition"
                    >
                        Annuler
                    </button>
                    <button
                        wire:click="reject"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                    >
                        Rejeter
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
