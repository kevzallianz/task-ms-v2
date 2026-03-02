<form id="register-form" method="POST" action="{{ route('user.register') }}" class="w-full bg-white border border-foreground/20 rounded-2xl p-8 flex flex-col gap-6">
    @csrf
    <div class="space-y-1">
        <h1 class="text-2xl font-semibold text-primary">Create an account</h1>
        <p class="text-sm text-foreground/80">Please fill in the information below to create your account.</p>
    </div>
    <div class="flex flex-col gap-4">
        <x-input-field label="Email Address" type="text" name="email" placeholder="ex. juandelacruz@allianz-synergia.com.ph" value="{{ old('email') }}" />
        <x-input-field label="Full Name" type="text" name="name" placeholder="ex. Juan Dela Cruz" value="{{ old('name') }}" />
        <x-input-field label="Username" type="text" name="username" placeholder="ex. jdelacruz" value="{{ old('username') }}" />
        <x-input-field label="Password" type="password" name="password" placeholder="•••••••••••••••" value="{{ old('password') }}" />
        <x-input-field label="Confirm Password" type="password" name="password_confirmation" placeholder="•••••••••••••••" value="{{ old('password_confirmation') }}" />
    </div>

    <x-button id="register-btn" text="Create Account" class="w-full py-2.5 rounded-lg text-base font-medium" />
    <a href="{{ route('home') }}" class="text-secondary hover:underline text-sm text-center hover:text-primary">Already have an account? Sign in here</a>
    
</form>

<script>
    $('#register-form').on('submit', function(event) {
        event.preventDefault();
        const form = $(this);

        $('#register-btn').attr('disabled', 'disabled');
        $('#register-btn').text('Registering...');
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
                showToast('success', response.message || "You've registered successfully!");
                $('#register-btn').removeAttr('disabled').text('Create Account');
                setTimeout(() => window.location.href = response.redirect, 800);
            },
            error: function(xhr) {
                const message = (xhr.responseJSON.message || 'An error occurred. Please try again.');
                $('#register-btn').removeAttr('disabled').text('Create Account');
                showToast('error', message);
            }
        })
    });
</script>