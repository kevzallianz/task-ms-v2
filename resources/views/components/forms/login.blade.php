<form id="login-form" method="POST" action="{{ route('user.authenticate') }}" class="w-full bg-white border border-foreground/20 rounded-2xl p-8 flex flex-col gap-6">
    @csrf
    <div class="space-y-1">
        <h1 class="text-2xl font-semibold text-primary">Welcome back to {{ config('app.name', 'Laravel') }}</h1>
        <p class="text-sm text-foreground/80">Please enter your credentials to access your account.</p>
    </div>

    <div class="flex flex-col gap-4">
        <x-input-field label="Username" type="text" name="username" placeholder="Enter your username" value="{{ old('username') }}" />
        <x-input-field label="Password" type="password" name="password" placeholder="•••••••••••••••" value="{{ old('password') }}" />
    </div>

    <div class="flex items-center justify-between text-sm">
        <input type="hidden" name="remember" value="0">
        <label class="flex items-center gap-2 text-foreground/80">
            <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
            Remember me
        </label>
        <a href="{{ route('password.request') }}" class="text-secondary hover:underline">Forgot password?</a>
    </div>
    <x-button id="sign-in-btn" text="Sign in" class="w-full py-2.5 rounded-lg text-base font-medium" />
    <a href="{{ route('register') }}" class="text-secondary hover:underline text-sm text-center hover:text-primary">Don't have an account yet? Register here</a>
</form>

<script>
    $('#login-form').on('submit', function(event) {
        event.preventDefault();
        const form = $(this);

        $('#sign-in-btn').attr('disabled', 'disabled');
        $('#sign-in-btn').text('Signing in...');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        $.ajax({
            url: form.attr('action'),
            method: "POST",
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                showToast('success', response.message || "You've login successfully!");
                $('#sign-in-btn').removeAttr('disabled').text('Sign in');
                setTimeout(() => window.location.href = response.redirect, 800);
            },
            error: function(xhr) {
                const message = (xhr.responseJSON.message || 'An error occurred. Please try again.');
                $('#sign-in-btn').removeAttr('disabled').text('Sign in');
                showToast('error', message);
            }
        })
    });
</script>