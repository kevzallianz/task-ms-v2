@extends('layouts.user-layout')

@section('user-content')
<main class="flex flex-col gap-6 max-w-3xl mx-auto py-6 px-4">

    {{-- Page Header --}}
    <article>
        <h1 class="text-2xl font-semibold text-primary flex items-center gap-2">
            <x-heroicon-o-user-circle class="w-6 h-6" />
            My Profile
        </h1>
        <p class="text-sm text-gray-600 mt-1">Manage your account information and security settings.</p>
    </article>

    {{-- Tabs --}}
    <div class="border-b border-secondary/30">
        <nav class="-mb-px flex gap-6" id="profileTabs">
            <button type="button" data-tab="profile"
                class="profile-tab pb-3 text-sm font-medium border-b-2 transition-colors border-primary text-primary">
                Profile Info
            </button>
            <button type="button" data-tab="password"
                class="profile-tab pb-3 text-sm font-medium border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700">
                Change Password
            </button>
        </nav>
    </div>

    {{-- Profile Info Tab --}}
    <div id="tab-profile" class="profile-tab-panel space-y-6">
        <div class="bg-white border border-secondary/30 rounded-lg p-6">
            <h2 class="text-base font-semibold text-foreground mb-5">Profile Information</h2>

            <form id="profileForm" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Avatar --}}
                <div class="flex items-center gap-5">
                    <div class="relative">
                        <div id="avatarPreview"
                            class="w-20 h-20 rounded-full bg-primary/20 flex items-center justify-center text-2xl font-bold text-primary overflow-hidden border-2 border-primary/30">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-full h-full object-cover" id="avatarImg" />
                            @else
                                <span id="avatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <label for="avatarInput" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-primary border border-primary/30 rounded-lg hover:bg-primary/5 transition">
                                <x-heroicon-o-camera class="w-4 h-4" />
                                Change Photo
                            </label>
                            <input id="avatarInput" type="file" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp" class="hidden" />
                            @if($user->avatar)
                            <button type="button" id="removeAvatarBtn"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition">
                                <x-heroicon-o-trash class="w-4 h-4" />
                                Remove Photo
                            </button>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">JPG, PNG or WebP. Max 2MB.</p>
                        <span class="text-xs text-red-500 hidden" id="avatarError"></span>
                    </div>
                </div>

                {{-- Name --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="profileName" class="text-sm font-medium text-foreground">Full Name <span class="text-red-500">*</span></label>
                        <input id="profileName" type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="100" required
                            class="w-full mt-1 rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                        <span class="text-xs text-red-500 hidden" id="profileNameError"></span>
                    </div>

                    <div>
                        <label for="profileUsername" class="text-sm font-medium text-foreground">Username <span class="text-red-500">*</span></label>
                        <input id="profileUsername" type="text" name="username" value="{{ old('username', $user->username) }}" maxlength="50" required
                            class="w-full mt-1 rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                        <span class="text-xs text-red-500 hidden" id="profileUsernameError"></span>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="profileEmail" class="text-sm font-medium text-foreground">Email Address <span class="text-red-500">*</span></label>
                    <input id="profileEmail" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="150" required
                        class="w-full mt-1 rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                    <span class="text-xs text-red-500 hidden" id="profileEmailError"></span>
                </div>

                {{-- Bio --}}
                <div>
                    <label for="profileBio" class="text-sm font-medium text-foreground">Bio</label>
                    <textarea id="profileBio" name="bio" rows="3" maxlength="500" placeholder="Tell a little about yourself..."
                        class="w-full mt-1 rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20 resize-none">{{ old('bio', $user->bio) }}</textarea>
                    <div class="flex justify-between mt-0.5">
                        <span class="text-xs text-red-500 hidden" id="profileBioError"></span>
                        <span class="text-xs text-gray-400 ml-auto" id="bioCharCount">{{ strlen($user->bio ?? '') }}/500</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" id="profileSubmitBtn"
                        class="px-5 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                        <x-heroicon-o-check class="w-4 h-4" />
                        <span id="profileSubmitText">Save Changes</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Account Info (read-only) --}}
        <div class="bg-white border border-secondary/30 rounded-lg p-6">
            <h2 class="text-base font-semibold text-foreground mb-4">Account Details</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Role</dt>
                    <dd class="mt-1 font-medium text-foreground capitalize">{{ $user->role }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Member Since</dt>
                    <dd class="mt-1 font-medium text-foreground">{{ $user->created_at->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Campaign</dt>
                    <dd class="mt-1 font-medium text-foreground">{{ $user->campaignMember?->campaign?->name ?? 'Not assigned' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Updated</dt>
                    <dd class="mt-1 font-medium text-foreground">{{ $user->updated_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Change Password Tab --}}
    <div id="tab-password" class="profile-tab-panel hidden space-y-6">
        <div class="bg-white border border-secondary/30 rounded-lg p-6">
            <h2 class="text-base font-semibold text-foreground mb-5">Change Password</h2>

            <form id="passwordForm" class="space-y-5">
                @csrf

                <div>
                    <label for="currentPassword" class="text-sm font-medium text-foreground">Current Password <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input id="currentPassword" type="password" name="current_password" maxlength="255" required
                            class="w-full rounded-lg border border-secondary/30 px-3 py-2 pr-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                        <button type="button" class="toggle-pw absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600" data-target="currentPassword">
                            <x-heroicon-o-eye class="w-4 h-4 eye-icon" />
                            <x-heroicon-o-eye-slash class="w-4 h-4 eye-slash-icon hidden" />
                        </button>
                    </div>
                    <span class="text-xs text-red-500 hidden" id="currentPasswordError"></span>
                </div>

                <div>
                    <label for="newPassword" class="text-sm font-medium text-foreground">New Password <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input id="newPassword" type="password" name="password" maxlength="255" required
                            class="w-full rounded-lg border border-secondary/30 px-3 py-2 pr-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                        <button type="button" class="toggle-pw absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600" data-target="newPassword">
                            <x-heroicon-o-eye class="w-4 h-4 eye-icon" />
                            <x-heroicon-o-eye-slash class="w-4 h-4 eye-slash-icon hidden" />
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters.</p>
                    <span class="text-xs text-red-500 hidden" id="newPasswordError"></span>
                </div>

                <div>
                    <label for="confirmPassword" class="text-sm font-medium text-foreground">Confirm New Password <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input id="confirmPassword" type="password" name="password_confirmation" maxlength="255" required
                            class="w-full rounded-lg border border-secondary/30 px-3 py-2 pr-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary/20" />
                        <button type="button" class="toggle-pw absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600" data-target="confirmPassword">
                            <x-heroicon-o-eye class="w-4 h-4 eye-icon" />
                            <x-heroicon-o-eye-slash class="w-4 h-4 eye-slash-icon hidden" />
                        </button>
                    </div>
                    <span class="text-xs text-red-500 hidden" id="confirmPasswordError"></span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" id="passwordSubmitBtn"
                        class="px-5 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition flex items-center gap-2">
                        <x-heroicon-o-lock-closed class="w-4 h-4" />
                        <span id="passwordSubmitText">Update Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</main>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab switching ──────────────────────────────────────────────
    const tabs = document.querySelectorAll('.profile-tab');
    const panels = document.querySelectorAll('.profile-tab-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;

            tabs.forEach(t => {
                const active = t.dataset.tab === target;
                t.classList.toggle('border-primary', active);
                t.classList.toggle('text-primary', active);
                t.classList.toggle('border-transparent', !active);
                t.classList.toggle('text-gray-500', !active);
            });

            panels.forEach(p => p.classList.toggle('hidden', p.id !== 'tab-' + target));
        });
    });

    // ── Remove avatar ──────────────────────────────────────────────
    const removeAvatarBtn = document.getElementById('removeAvatarBtn');
    removeAvatarBtn?.addEventListener('click', function () {
        if (!confirm('Remove your profile photo?')) return;
        this.disabled = true;
        this.textContent = 'Removing…';

        fetch('{{ route("user.profile.avatar.remove") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                showToast('error', data.message || 'Failed to remove photo.');
                this.disabled = false;
                this.innerHTML = '<svg class="w-4 h-4" ...></svg> Remove Photo';
            } else {
                showToast('success', data.message || 'Photo removed.');
                setTimeout(() => location.reload(), 800);
            }
        })
        .catch(() => showToast('error', 'An unexpected error occurred.'));
    });

    // ── Avatar preview ─────────────────────────────────────────────
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    avatarInput?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            document.getElementById('avatarError').textContent = 'Image must be under 2MB.';
            document.getElementById('avatarError').classList.remove('hidden');
            this.value = '';
            return;
        }
        document.getElementById('avatarError').classList.add('hidden');
        const reader = new FileReader();
        reader.onload = e => {
            avatarPreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover" />`;
        };
        reader.readAsDataURL(file);
    });

    // ── Bio char counter ───────────────────────────────────────────
    const bioTextarea = document.getElementById('profileBio');
    const bioCharCount = document.getElementById('bioCharCount');
    bioTextarea?.addEventListener('input', function () {
        bioCharCount.textContent = this.value.length + '/500';
    });

    // ── Toggle password visibility ─────────────────────────────────
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            this.querySelector('.eye-icon').classList.toggle('hidden', !isText);
            this.querySelector('.eye-slash-icon').classList.toggle('hidden', isText);
        });
    });

    // ── Helper: show field errors ──────────────────────────────────
    function clearErrors(prefix, fields) {
        fields.forEach(f => {
            const el = document.getElementById(prefix + f + 'Error');
            if (el) { el.textContent = ''; el.classList.add('hidden'); }
        });
    }

    function showErrors(errors, prefix) {
        Object.entries(errors).forEach(([field, messages]) => {
            // map dotted field names (e.g. password_confirmation) to camelCase ids
            const key = field.replace(/_([a-z])/g, (_, c) => c.toUpperCase());
            const el = document.getElementById(prefix + key.charAt(0).toUpperCase() + key.slice(1) + 'Error')
                    || document.getElementById(prefix + key + 'Error');
            if (el) { el.textContent = messages[0]; el.classList.remove('hidden'); }
        });
    }

    // ── Profile form ───────────────────────────────────────────────
    const profileForm = document.getElementById('profileForm');
    profileForm?.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors('profile', ['Name', 'Username', 'Email', 'Bio']);
        document.getElementById('avatarError').classList.add('hidden');

        const btn = document.getElementById('profileSubmitBtn');
        const txt = document.getElementById('profileSubmitText');
        btn.disabled = true; txt.textContent = 'Saving…';

        const formData = new FormData(profileForm);

        fetch('{{ route("user.profile.update") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                       'Accept': 'application/json' },
            body: formData,
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                if (data.errors) showErrors(data.errors, 'profile');
                else showToast('error', data.message || 'Failed to save.');
            } else {
                showToast('success', data.message || 'Profile updated!');
                    setTimeout(() => location.reload(), 800);
            }
        })
        .catch(() => showToast('error', 'An unexpected error occurred.'))
        .finally(() => { btn.disabled = false; txt.textContent = 'Save Changes'; });
    });

    // ── Password form ──────────────────────────────────────────────
    const passwordForm = document.getElementById('passwordForm');
    passwordForm?.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors('', ['currentPassword', 'newPassword', 'confirmPassword']);

        const btn = document.getElementById('passwordSubmitBtn');
        const txt = document.getElementById('passwordSubmitText');
        btn.disabled = true; txt.textContent = 'Updating…';

        const formData = new FormData(passwordForm);

        fetch('{{ route("user.profile.password") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                       'Accept': 'application/json' },
            body: formData,
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                if (data.errors) {
                    const map = {
                        current_password: 'currentPasswordError',
                        password: 'newPasswordError',
                        password_confirmation: 'confirmPasswordError',
                    };
                    Object.entries(data.errors).forEach(([field, msgs]) => {
                        const el = document.getElementById(map[field]);
                        if (el) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
                    });
                } else {
                    showToast('error', data.message || 'Failed to update password.');
                }
            } else {
                showToast('success', data.message || 'Password updated!');
                passwordForm.reset();
            }
        })
        .catch(() => showToast('error', 'An unexpected error occurred.'))
        .finally(() => { btn.disabled = false; txt.textContent = 'Update Password'; });
    });

});
</script>
