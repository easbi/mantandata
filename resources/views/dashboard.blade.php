<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Anomali') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold">Daftar kasus terbaru</h3>
                            <p class="text-sm text-gray-600">Kasus yang telah berhasil diimpor dari Excel / CSV.</p>
                        </div>
                        <a href="{{ route('anomalies.import') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Import File
                        </a>
                    </div>

                    @php
                        $anomalyTypes = \App\Models\AnomalyType::orderBy('nama')->get();
                    @endphp

                    <form method="GET" class="mt-4 flex items-center gap-3">
                        <label for="anomaly_type_id" class="text-sm font-medium text-gray-700">Filter tipe anomali:</label>
                        <select name="anomaly_type_id" id="anomaly_type_id" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">Semua</option>
                            @foreach ($anomalyTypes as $type)
                                <option value="{{ $type->id }}" {{ request('anomaly_type_id') == $type->id ? 'selected' : '' }}>{{ $type->nama }}</option>
                            @endforeach
                        </select>
                    </form>

                    @php
                        $selectedTypeId = request('anomaly_type_id');
                        $cases = \App\Models\AnomalyCase::with(['anomalyType','latestRun'])
                            ->when($selectedTypeId, fn ($query) => $query->where('anomaly_type_id', $selectedTypeId))
                            ->latest('created_at')
                            ->take(10)
                            ->get();
                        $anomalyTypes = \App\Models\AnomalyType::orderBy('nama')->get();
                    @endphp

                    @if ($cases->isEmpty())
                        <p class="mt-4 text-sm text-gray-500">Belum ada data. Silakan import file pertama Anda.</p>
                    @else
                        <div class="overflow-x-auto mt-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Assignment</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Tipe</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Times Seen</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Last Seen</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($cases as $case)
                                        <tr>
                                            <td class="px-3 py-2">
                                                {{ $case->assignment_id }}
                                                <div class="mt-1">
                                                    <a href="{{ route('anomalies.show', $case) }}" class="text-sm text-blue-600 hover:underline">Lihat detail case</a>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">{{ $case->anomalyType->nama ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ str_replace('_', ' ', $case->status_penanganan) }}</td>
                                            <td class="px-3 py-2">{{ $case->times_seen }}</td>
                                            <td class="px-3 py-2">{{ $case->last_seen_at ? $case->last_seen_at->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
