<form id="reset-form" method="POST" action="{{ route('password.update') }}" class="w-full bg-white border border-foreground/20 rounded-2xl p-8 flex flex-col gap-6">
    @csrf
    <input type="hidden" name="token" value="{{ $token ?? old('token') }}">

    <div class="space-y-1">
        <h1 class="text-2xl font-semibold text-primary">Reset password</h1>
        <p class="text-sm text-foreground/80">Choose a strong password to secure your account.</p>
    </div>

    <div class="flex flex-col gap-4">
        <x-input-field label="Email" type="email" name="email" placeholder="you@company.com" value="{{ old('email') }}" />
        <x-input-field label="New password" type="password" name="password" placeholder="••••••••" />
        <x-input-field label="Confirm password" type="password" name="password_confirmation" placeholder="••••••••" />
    </div>

    <x-button text="Reset password" class="w-full py-2.5 rounded-lg text-base font-medium" />

    <a href="{{ route('home') }}" class="text-secondary hover:underline text-sm text-center">Back to home</a>
</form>
<script>
    $('#reset-form').on('submit', function(event) {
        event.preventDefault();
        const form = $(this);
        const btn = form.find('button[type=submit]');
        const originalText = btn.text();
        btn.prop('disabled', true).text('Resetting...');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                showToast('success', response.message || 'Password reset successful.');
                btn.prop('disabled', false).text(originalText);
                if (response.redirect) {
                    setTimeout(() => window.location.href = response.redirect, 900);
                } else {
                    setTimeout(() => window.location.href = '/password/reset/success', 900);
                }
            },
            error: function(xhr) {
                const message = (xhr.responseJSON && (xhr.responseJSON.message || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors).flat().join(' ')))) || 'An error occurred. Please try again.';
                showToast('error', message);
                btn.prop('disabled', false).text(originalText);
            }
        });
    });
</script>
