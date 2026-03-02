<form id="forgot-form" method="POST" action="{{ route('password.email') }}" class="w-full bg-white border border-foreground/20 rounded-2xl p-8 flex flex-col gap-6">
    @csrf
    <div class="space-y-1">
        <h1 class="text-2xl font-semibold text-primary">Forgot your password?</h1>
        <p class="text-sm text-foreground/80">Enter your email and we'll send a password reset link.</p>
    </div>

    <div class="flex flex-col gap-4">
        <x-input-field label="Email" type="email" name="email" placeholder="you@company.com" value="{{ old('email') }}" />
    </div>

    <x-button text="Send reset link" class="w-full py-2.5 rounded-lg text-base font-medium" />

    <a href="{{ route('home') }}" class="text-secondary hover:underline text-sm text-center">Back to home</a>
</form>
<script>
    $('#forgot-form').on('submit', function(event) {
        event.preventDefault();
        const form = $(this);
        const btn = form.find('button[type=submit]');
        const originalText = btn.text();
        btn.prop('disabled', true).text('Sending...');
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
                showToast('success', response.message || 'If that email exists, a reset link was sent.');
                btn.prop('disabled', false).text(originalText);
            },
            error: function(xhr) {
                const message = (xhr.responseJSON && (xhr.responseJSON.message || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors).flat().join(' ')))) || 'An error occurred. Please try again.';
                showToast('error', message);
                btn.prop('disabled', false).text(originalText);
            }
        });
    });
</script>
