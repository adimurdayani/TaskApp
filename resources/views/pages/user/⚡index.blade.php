<?php

use App\Enum\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Daftar Akun')] class extends Component {
    use \Livewire\WithPagination;

    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public ?string $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = '';

    public ?string $successMessage = '';
    public ?string $errorMessage = '';

    public ?User $selectedUser = null;

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function users()
    {
        return User::query()->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)->paginate(5);
    }

    public function edit(string $id)
    {
        abort_unless(auth()->check(), 403);
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role instanceof \App\Enum\Role ? $user->role->value : (string) $user->role;
        $this->password = '';
        $this->resetValidation();
        $this->modal('detail')->close();
        $this->modal('edit')->show();
    }

    public function save(): void
    {
        $this->validate(
            [
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'email' => ['required', 'email', Rule::unique(User::class, 'email')->ignore($this->userId)],
                'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:8', Password::default()],
                'role' => ['required', Rule::enum(Role::class)],
            ],
            [
                'name.required' => 'Nama lengkap wajib diisi.',
                'name.min' => 'Nama lengkap minimal 3 karakter.',
                'name.max' => 'Nama lengkap maksimal 255 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Email tidak valid.',
                'email.unique' => 'Email lengkap sudah digunakan.',
                'password.required' => 'Password tidak valid.',
                'password.min' => 'Password minimal 8 karakter.',
                'role.required' => 'Role kategori tidak valid.',
                'role.string' => 'Role harus berupa teks.',
                'role.max' => 'Role maksimal 255 karakter.',
            ],
        );

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if (filled($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            $user = User::query()->findOrFail($this->userId);

            $user->update($data);

            $this->successMessage = 'Akun berhasil diperbarui.';

            $this->reset(['userId', 'name', 'email', 'password', 'role']);

            $this->resetValidation();

            $this->modal('edit')->close();

            return;
        }

        User::query()->create([...$data, 'password' => Hash::make($this->password)]);

        $this->successMessage = 'Akun berhasil ditambahkan.';

        $this->reset(['name', 'email', 'password', 'role']);

        $this->resetValidation();

        $this->modal('tambah')->close();
    }

    public function delete(string $id): void
    {
        abort_unless(auth()->check(), 403);

        $user = User::query()->findOrFail($id);

        $hasTasks = Task::query()->where('user_id', $user->id)->exists();

        if ($hasTasks) {
            $this->errorMessage = 'Akun tidak dapat dihapus karena masih digunakan oleh satu atau lebih tugas.';
            return;
        }

        $user->delete();

        $this->successMessage = 'Akun berhasil dihapus.';
    }

    public function showDetail(string $id): void
    {
        abort_unless(auth()->check(), 403);

        $user = User::query()->findOrFail($id);

        $this->selectedUser = $user;

        $this->modal('detail')->show();
    }

    public function closeDetail(): void
    {
        $this->selectedUser = null;

        $this->modal('detail')->close();
    }
};
?>

<div>
    @include('partials.heading', ['title' => 'Daftar Akun', 'subtitle' => 'Kelola data akun pengguna'])
    <flux:card class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Tabel data akun pengguna</flux:heading>
                <flux:text class="mt-1 text-sm">
                    Kelola dan pantau akun pengguna.
                    Anda dapat menambahkan akun baru, memperbarui informasi akun.
                </flux:text>
            </div>
            <flux:modal.trigger name="tambah">
                <flux:button variant="primary" color="blue" icon="plus">Tambah Akun</flux:button>
            </flux:modal.trigger>
        </div>

        @if ($successMessage)
        <div x-data x-init="setTimeout(() => $wire.set('successMessage', null), 4000)" class="mb-6">
            <flux:callout variant="success" icon="check-circle" heading="Berhasil" class="mb-6">
                {{ $successMessage }}
            </flux:callout>
        </div>
        @endif

        @if ($errorMessage)
        <div x-data x-init="setTimeout(() => $wire.set('errorMessage', null), 8000)" class="mb-6">
            <flux:callout variant="warning" icon="exclamation-circle" heading="Peringatan" class="mb-6">
                {{ $errorMessage }}
            </flux:callout>
        </div>
        @endif

        <flux:table :paginate="$this->users" class="w-full table-fixed">
            <flux:table.columns>
                <flux:table.column class="w-[25%]">
                    Nama Pengguna
                </flux:table.column>

                <flux:table.column class="w-[25%]">
                    Email
                </flux:table.column>

                <flux:table.column class="w-[15%]">
                    Role
                </flux:table.column>

                <flux:table.column class="w-[12%] text-center">
                    Jumlah Tugas
                </flux:table.column>

                <flux:table.column class="w-[13%] whitespace-nowrap" sortable :sorted="$sortBy === 'created_at'"
                    :direction="$sortDirection" wire:click="sort('created_at')">
                    Terdaftar
                </flux:table.column>

                <flux:table.column class="w-[10%] text-right">
                    Aksi
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->users as $user)
                <flux:table.row :key="$user->id">

                    {{-- Nama --}}
                    <flux:table.cell class="min-w-0">
                        <div class="min-w-0 max-w-full" title="{{ $user->name }}">
                            <flux:text class="block truncate" variant="strong">
                                {{ $user->name }}
                            </flux:text>
                        </div>
                    </flux:table.cell>

                    {{-- Email --}}
                    <flux:table.cell class="min-w-0">
                        <div class="min-w-0 max-w-full" title="{{ $user->email }}">
                            <flux:text class="block truncate text-sm">
                                {{ $user->email }}
                            </flux:text>
                        </div>
                    </flux:table.cell>

                    {{-- Role --}}
                    <flux:table.cell class="whitespace-nowrap">
                        @if ($user->role instanceof \App\Enum\Role)
                        @switch($user->role->value)
                        @case('admin')
                        <flux:badge color="red">
                            {{ $user->role->label() }}
                        </flux:badge>
                        @break

                        @case('user')
                        <flux:badge color="blue">
                            {{ $user->role->label() }}
                        </flux:badge>
                        @break

                        @default
                        <flux:badge color="zinc">
                            {{ $user->role->label() }}
                        </flux:badge>
                        @endswitch
                        @else
                        <flux:badge color="zinc">
                            {{ $user->role }}
                        </flux:badge>
                        @endif
                    </flux:table.cell>

                    {{-- Jumlah Tugas --}}
                    <flux:table.cell class="text-center">
                        <flux:badge color="zinc">
                            {{ $user->tasks->count() }}
                        </flux:badge>
                    </flux:table.cell>

                    {{-- Tanggal Terdaftar --}}
                    <flux:table.cell class="whitespace-nowrap">
                        <flux:text class="text-sm">
                            {{ $user->created_at?->format('d M Y') ?? '-' }}
                        </flux:text>
                    </flux:table.cell>

                    {{-- Aksi --}}
                    <flux:table.cell class="whitespace-nowrap">
                        <div class="flex">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />

                                <flux:menu>

                                    {{-- Detail --}}
                                    <flux:menu.item icon="eye" wire:click="showDetail('{{ $user->id }}')">
                                        Lihat
                                    </flux:menu.item>

                                    {{-- Edit --}}
                                    <flux:menu.item icon="pencil" wire:click="edit('{{ $user->id }}')">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    {{-- Hapus --}}
                                    @if ((string) auth()->id() !== (string) $user->id)
                                    <flux:menu.item wire:click="delete('{{ $user->id }}')"
                                        wire:confirm="Apakah Anda yakin ingin menghapus akun {{ $user->name }}?"
                                        variant="danger" icon="trash">
                                        Hapus
                                    </flux:menu.item>
                                    @else
                                    <flux:menu.item disabled icon="lock-closed">
                                        Tidak dapat menghapus akun sendiri
                                    </flux:menu.item>
                                    @endif

                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </flux:table.cell>

                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

    </flux:card>
    @include('pages.user.includes.modal')
</div>
