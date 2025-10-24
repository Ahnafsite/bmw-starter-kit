<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

#[Title('Manajemen Pengguna')]
class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Edit User Modal
    public $showEditModal = false;
    public $editUserId = null;
    public $editName = '';
    public $editEmail = '';
    public $editPassword = '';
    public $selectedUser = null;

    // Role Management Modal
    public $showRoleModal = false;
    public $roleUserId = null;
    public $selectedRoles = [];
    public $availableRoles = [];
    public $roleUser = null;

    protected $queryString = ['search', 'page'];

    public function mount()
    {
        $this->availableRoles = Role::all()->pluck('name')->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // Edit User Methods
    public function editUser($userId)
    {
        $this->editUserId = $userId;
        $this->selectedUser = User::find($userId);

        if ($this->selectedUser) {
            $this->editName = $this->selectedUser->name;
            $this->editEmail = $this->selectedUser->email;
            $this->editPassword = '';
            $this->showEditModal = true;
        }
    }

    public function updateUser()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editUserId)],
            'editPassword' => 'nullable|min:8',
        ]);

        $user = User::find($this->editUserId);

        if ($user) {
            $user->name = $this->editName;
            $user->email = $this->editEmail;

            // Only update password if provided
            if (!empty($this->editPassword)) {
                $user->password = Hash::make($this->editPassword);
            }

            $user->save();

            session()->flash('message', 'Pengguna berhasil diperbarui.');
        }

        $this->closeEditModal();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editUserId = null;
        $this->editName = '';
        $this->editEmail = '';
        $this->editPassword = '';
        $this->selectedUser = null;
        $this->resetErrorBag();
    }

    // Role Management Methods
    public function manageRole($userId)
    {
        $this->roleUserId = $userId;
        $this->roleUser = User::with('roles')->find($userId);

        if ($this->roleUser) {
            $this->selectedRoles = $this->roleUser->roles->pluck('name')->toArray();
            $this->showRoleModal = true;
        }
    }

    public function updateRoles()
    {
        $user = User::find($this->roleUserId);

        if ($user) {
            // Sync roles - this will remove old roles and add new ones
            $user->syncRoles($this->selectedRoles);

            session()->flash('message', 'Role pengguna berhasil diperbarui.');
        }

        $this->closeRoleModal();
    }

    public function closeRoleModal()
    {
        $this->showRoleModal = false;
        $this->roleUserId = null;
        $this->selectedRoles = [];
        $this->roleUser = null;
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('roles', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.user-management', [
            'users' => $users,
            'roles' => Role::all()
        ]);
    }
}
