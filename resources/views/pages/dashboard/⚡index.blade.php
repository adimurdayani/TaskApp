<?php

use App\Enum\Role;
use App\Enum\Status;
use App\Models\Task;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    private function taskQuery()
    {
        $query = Task::query();

        if (auth()->user()->role !== Role::ADMIN) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    #[Computed]
    public function statistics(): array
    {
        $query = $this->taskQuery();

        return [
            'total' => (clone $query)->count(),

            'belum_dimulai' => (clone $query)
                ->where('status', Status::BELUM_DIMULAI)
                ->count(),

            'dikerjakan' => (clone $query)
                ->where('status', Status::DIKERJAKAN)
                ->count(),

            'selesai' => (clone $query)
                ->where('status', Status::SELESAI)
                ->count(),
        ];
    }

    #[Computed]
    public function upcomingTasks()
    {
        return $this->taskQuery()
            ->with(['category', 'user'])
            ->whereNot('status', Status::SELESAI)
            ->whereNotNull('due_date')
            ->whereBetween(
                'due_date',
                [
                    now()->startOfDay(),
                    now()->addDays(7)->endOfDay(),
                ]
            )
            ->orderBy('due_date')
            ->limit(10)
            ->get();
    }
};
?>

<div class="w-full">
    @include('partials.heading', [
    'title' => 'Dashboard',
    'subtitle' => 'Ringkasan tugas dan pekerjaan yang mendekati tenggat waktu.',
    ])

    {{-- Statistik --}}
    <div class="mt-6 flex w-full flex-row gap-4">

        {{-- Total --}}
        <flux:card class="w-full">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <flux:text variant="subtle">
                        Total Tugas
                    </flux:text>

                    <flux:heading size="xl" class="mt-2">
                        {{ $this->statistics['total'] }}
                    </flux:heading>

                    <flux:text class="mt-1 text-sm" variant="subtle">
                        Seluruh tugas Anda
                    </flux:text>
                </div>

                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.clipboard-document-list class="size-6" />
                </div>

            </div>
        </flux:card>


        {{-- Belum Dimulai --}}
        <flux:card class="w-full">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <flux:text variant="subtle">
                        Belum Dimulai
                    </flux:text>

                    <flux:heading size="xl" class="mt-2">
                        {{ $this->statistics['belum_dimulai'] }}
                    </flux:heading>

                    <flux:text class="mt-1 text-sm" variant="subtle">
                        Tugas yang belum dikerjakan
                    </flux:text>
                </div>

                <div
                    class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <flux:icon.clock class="size-6" />
                </div>

            </div>
        </flux:card>


        {{-- Dikerjakan --}}
        <flux:card class="w-full">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <flux:text variant="subtle">
                        Dikerjakan
                    </flux:text>

                    <flux:heading size="xl" class="mt-2">
                        {{ $this->statistics['dikerjakan'] }}
                    </flux:heading>

                    <flux:text class="mt-1 text-sm" variant="subtle">
                        Tugas yang sedang berjalan
                    </flux:text>
                </div>

                <div
                    class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <flux:icon.arrow-path class="size-6" />
                </div>

            </div>
        </flux:card>


        {{-- Selesai --}}
        <flux:card class="w-full">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <flux:text variant="subtle">
                        Selesai
                    </flux:text>

                    <flux:heading size="xl" class="mt-2">
                        {{ $this->statistics['selesai'] }}
                    </flux:heading>

                    <flux:text class="mt-1 text-sm" variant="subtle">
                        Tugas yang telah diselesaikan
                    </flux:text>
                </div>

                <div
                    class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                    <flux:icon.check-circle class="size-6" />
                </div>

            </div>
        </flux:card>

    </div>


    {{-- Tugas Mendekati Tenggat --}}
    <div class="mt-6 space-y-4">

        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <flux:heading size="lg">
                    Tugas Mendekati Tenggat
                </flux:heading>

                <flux:text class="mt-1" variant="subtle">
                    Tugas yang memiliki tenggat waktu dalam 7 hari ke depan.
                </flux:text>
            </div>

            <flux:button href="{{ route('task') }}" variant="ghost" icon="arrow-right" wire:navigate>
                Lihat Semua Tugas
            </flux:button>
        </div>


        <flux:card class="overflow-hidden">

            @if ($this->upcomingTasks->isEmpty())

            <div class="flex flex-col items-center justify-center px-6 py-12 text-center">

                <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.calendar-days class="size-6 text-zinc-500" />
                </div>

                <flux:heading size="sm" class="mt-4">
                    Tidak ada tugas yang mendekati tenggat
                </flux:heading>

                <flux:text class="mt-1 max-w-md" variant="subtle">
                    Saat ini tidak terdapat tugas yang memiliki tenggat
                    dalam 7 hari ke depan.
                </flux:text>

            </div>
            @else
            <div class="overflow-x-auto">

                <flux:table class="w-full table-fixed">

                    <flux:table.columns>

                        <flux:table.column class="w-[32%]">
                            Tugas
                        </flux:table.column>

                        <flux:table.column class="w-[18%]">
                            Kategori
                        </flux:table.column>

                        <flux:table.column class="w-[15%]">
                            Prioritas
                        </flux:table.column>

                        <flux:table.column class="w-[15%]">
                            Status
                        </flux:table.column>

                        <flux:table.column class="w-[15%]">
                            Tenggat
                        </flux:table.column>

                    </flux:table.columns>


                    <flux:table.rows>

                        @foreach ($this->upcomingTasks as $task)
                        @php
                        $daysLeft = now()
                        ->startOfDay()
                        ->diffInDays($task->due_date->startOfDay(), false);
                        @endphp

                        <flux:table.row :key="$task->id">

                            {{-- Judul --}}
                            <flux:table.cell class="min-w-0">

                                <div class="min-w-0" title="{{ $task->title }}">
                                    <flux:text class="block truncate" variant="strong">
                                        {{ $task->title }}
                                    </flux:text>

                                    @if ($task->description)
                                    <flux:text class="mt-1 block truncate text-sm" variant="subtle">
                                        {{ $task->description }}
                                    </flux:text>
                                    @endif
                                </div>

                            </flux:table.cell>


                            {{-- Kategori --}}
                            <flux:table.cell class="min-w-0">

                                @if ($task->category)
                                <flux:text class="block truncate text-sm"
                                    title="{{ $task->category->name }}">
                                    {{ $task->category->name }}
                                </flux:text>
                                @else
                                <flux:text variant="subtle" class="text-sm">
                                    Tanpa kategori
                                </flux:text>
                                @endif

                            </flux:table.cell>


                            {{-- Priority --}}
                            <flux:table.cell class="whitespace-nowrap">

                                @if ($task->priority instanceof \App\Enum\Priority)
                                @switch($task->priority->value)
                                @case('tinggi')
                                <flux:badge color="red">
                                    {{ $task->priority->label() }}
                                </flux:badge>
                                @break

                                @case('sedang')
                                <flux:badge color="amber">
                                    {{ $task->priority->label() }}
                                </flux:badge>
                                @break

                                @default
                                <flux:badge color="zinc">
                                    {{ $task->priority->label() }}
                                </flux:badge>
                                @endswitch
                                @else
                                <flux:badge color="zinc">
                                    {{ $task->priority }}
                                </flux:badge>
                                @endif

                            </flux:table.cell>


                            {{-- Status --}}
                            <flux:table.cell class="whitespace-nowrap">

                                @if ($task->status instanceof \App\Enum\Status)
                                @switch($task->status->value)
                                @case('dikerjakan')
                                <flux:badge color="blue">
                                    {{ $task->status->label() }}
                                </flux:badge>
                                @break

                                @case('belum_dimulai')
                                <flux:badge color="amber">
                                    {{ $task->status->label() }}
                                </flux:badge>
                                @break

                                @default
                                <flux:badge color="zinc">
                                    {{ $task->status->label() }}
                                </flux:badge>
                                @endswitch
                                @else
                                <flux:badge color="zinc">
                                    {{ $task->status }}
                                </flux:badge>
                                @endif

                            </flux:table.cell>


                            {{-- Tenggat --}}
                            <flux:table.cell class="whitespace-nowrap">

                                <div class="space-y-1">

                                    <flux:text class="text-sm">
                                        {{ $task->due_date->format('d M Y') }}
                                    </flux:text>

                                    @if ($daysLeft === 0)
                                    <flux:badge color="red">
                                        Hari ini
                                    </flux:badge>
                                    @elseif ($daysLeft === 1)
                                    <flux:badge color="red">
                                        Besok
                                    </flux:badge>
                                    @elseif ($daysLeft <= 3)
                                        <flux:badge color="amber">
                                        H-{{ $daysLeft }}
                                        </flux:badge>
                                        @else
                                        <flux:badge color="zinc">
                                            H-{{ $daysLeft }}
                                        </flux:badge>
                                        @endif

                                </div>

                            </flux:table.cell>

                        </flux:table.row>
                        @endforeach

                    </flux:table.rows>

                </flux:table>

            </div>

            @endif

        </flux:card>

    </div>

</div>
